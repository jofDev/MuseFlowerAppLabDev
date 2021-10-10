<?php

namespace Vnecoms\Sms\Controller\Adminhtml\Blocklist;

use Vnecoms\Sms\Controller\Adminhtml\Blocklist\Action;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Backend\App\Action\Context;
use Vnecoms\Sms\Model\RuleFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;

class Save extends Action implements HttpPostActionInterface
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var RuleFactory
     */
    private $ruleFactory;

    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param DataPersistorInterface $dataPersistor
     * @param RuleFactory|null $ruleFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        DataPersistorInterface $dataPersistor,
        RuleFactory $ruleFactory = null
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->ruleFactory = $ruleFactory
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(RuleFactory::class);
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            if (empty($data['rule_id'])) {
                $data['rule_id'] = null;
            }
            try {
                /** @var \Vnecoms\Sms\Model\Rule $model */
                $model = $this->ruleFactory->create();
    
                $id = $this->getRequest()->getParam('rule_id');
                if ($id) {
                    $model->load($id);
                }
    
                $model->addData($data);

                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the rule.'));
                $this->dataPersistor->clear('rule');
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the block.'));
            }

            $this->dataPersistor->set('rule', $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
