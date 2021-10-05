<?php
namespace Vnecoms\Sms\Controller\Login;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Controller\AbstractAccount;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\UserLockedException;

class LoginPost extends \Magento\Framework\App\Action\Action implements HttpPostActionInterface
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    protected $customerAccountManagement;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKeyValidator;

    /**
     * @var AccountRedirect
     */
    protected $accountRedirect;

    /**
     * @var Session
     */
    protected $session;

    /**
     * LoginPost constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param AccountManagementInterface $customerAccountManagement
     * @param Validator $formKeyValidator
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Session $customerSession,
        AccountManagementInterface $customerAccountManagement,
        Validator $formKeyValidator
    ) {
        $this->session = $customerSession;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->formKeyValidator = $formKeyValidator;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $response = new \Magento\Framework\DataObject();
        if ($this->session->isLoggedIn() || !$this->formKeyValidator->validate($this->getRequest())) {
            $response->setData([
                'success' => false
            ]);
        }

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $customer = $this->customerAccountManagement->authenticate($login['username'], $login['password']);
                    $customerObj = $this->_objectManager->create('Magento\Customer\Model\Customer')->updateData($customer);
                    $secureKey = md5($customerObj->getData('email').md5(time().rand(1,1000)));
                    $this->session->setData($secureKey, $customerObj->getData('email'));
                    $response->setData([
                        'success' => true,
                        'mobilenumber' =>  $customerObj->getData('mobilenumber'),
                        'secure_key' =>  $secureKey,
                    ]);
                    return $this->resultJsonFactory->create()->setJsonData($response->toJson());
                } catch (EmailNotConfirmedException $e) {
                    $value = $this->customerUrl->getEmailConfirmationUrl($login['username']);
                    $message = __(
                        'This account is not confirmed. <a href="%1">Click here</a> to resend confirmation email.',
                        $value
                    );
                } catch (UserLockedException $e) {
                    $message = __(
                        'The account sign-in was incorrect or your account is disabled temporarily. '
                        . 'Please wait and try again later.'
                    );
                } catch (AuthenticationException $e) {
                    $message = __(
                        'The account sign-in was incorrect or your account is disabled temporarily. '
                        . 'Please wait and try again later.'
                    );
                } catch (LocalizedException $e) {
                    $message = $e->getMessage();
                } catch (\Exception $e) {
                    // PA DSS violation: throwing or logging an exception here can disclose customer password
                    $this->messageManager->addError(
                        __('An unspecified error occurred. Please contact us for assistance.')
                    );
                } finally {
                    if (isset($message)) {
                        $this->messageManager->addError($message);
                        $this->session->setUsername($login['username']);
                    }
                }
            } else {
                $this->messageManager->addError(__('A login and a password are required.'));
            }
        }

        $response->setData([
            'success' => false
        ]);
        return $this->resultJsonFactory->create()->setJsonData($response->toJson());
    }
}
