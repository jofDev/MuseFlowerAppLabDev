<?php 

namespace Sadadqar\Magento\Controller\Payment;

use Sadadqar\Magento\Model\Config;
use Sadadqar\Magento\Model\PaymentMethod;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;

use Magento\Sales\Model\Order\Payment\Transaction\Builder as TransactionBuilder;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Sales\Model\Order;

class Webhook extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface, HttpGetActionInterface, HttpPostActionInterface
{
    /**
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    protected $order;

    protected $logger;
	
	protected $transactionRepository;
	
	protected $sKeyId;
	
	protected $sKeySecret;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Sadadqar\Magento\Model\Config $config
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     */

    	public function createCsrfValidationException(
        RequestInterface $request
	    ): ?InvalidRequestException {
	        return null;
	    }

	    public function validateForCsrf(RequestInterface $request): ?bool
	    {
	        return true;
	    }

    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Sadadqar\Magento\Model\Config $config,
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Psr\Log\LoggerInterface $logger
    ) 
    {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->config = $config;
		
        $this->sKeyId                = $this->config->getConfigData(Config::KEY_PUBLIC_KEY);
        $this->sKeySecret             = $this->config->getConfigData(Config::KEY_PRIVATE_KEY);

        $this->order           = $order;
        $this->logger          = $logger;
    }

    /**
     * Processes the incoming webhook
     */
    public function execute()
    {       
        $post = $this->getPostData(); 

        $writer 	= new \Zend\Log\Writer\Stream(BP . '/var/log/SadadQAR.log');
	    $logger 	= new \Zend\Log\Logger();
	    $logger->addWriter($writer);
	    $logger->info('Response -:'.print_r($post,1));
        

        $this->logger->warning("SadadQAR Webhook processing started.");
		
		$orderId = $post['ORDERID'];
        $responseDescription =$post['RESPMSG'];
		
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

		$collection = $objectManager->create('Magento\Sales\Model\Order'); 
		$order = $collection->loadByIncrementId($orderId);
		$payment = $order->getPayment();
		
		$this->transactionRepository=$objectManager->create('Magento\Sales\Api\TransactionRepositoryInterface');
		
		$transaction = $this->transactionRepository->getByTransactionId(
						"-1",
						$payment->getId(),
						$order->getId()
				);	
		
		if ($post['RESPCODE'] == 01 or $post['RESPCODE'] == 1) {
			$ordTotal=round($order->getGrandTotal(), 2);
			
			$checksum_response = $post['checksumhash'];
                        unset($post['checksumhash']);
                        $sadad_id                   = $this->sKeyId;
                        $sadad_secrete_key          = trim($this->sKeySecret);
                        $data_repsonse              = array();
                        $data_repsonse['postData']  = $post;
                        $data_repsonse['secretKey'] = trim($this->sKeySecret);
                        $key                        = $sadad_secrete_key . $sadad_id;
			
			if($this->verifychecksum_eFromStr(json_encode($data_repsonse), $key, $checksum_response) === "TRUE" && $post['TXNAMOUNT']>=$ordTotal && $post['STATUS'] == 'TXN_SUCCESS')
			{
				$payment_id=$post['transaction_number'];
				$orderState = Order::STATE_PROCESSING;
				$order->setState($orderState)->setStatus(Order::STATE_PROCESSING);
								
				
				if($transaction)
				{
					 $transaction->setTxnId($payment_id);
					$transaction->setAdditionalInformation(  
						"SadadQA Transaction Id",$payment_id
					);
					$transaction->setAdditionalInformation(  
						"status","successful"
					);
					$transaction->setIsClosed(1);
					$transaction->save(); 
					
				}
				
				$payment->addTransactionCommentsToOrder(
					$transaction,
				   "Transaction is completed successfully"
				);
				$payment->setParentTransactionId(null); 
				//php bin/magento module:enable Sadadqar_Magento --clear-static-content
				# send new email
				$order->setCanSendNewEmailFlag(true);
				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
				$objectManager->create('Magento\Sales\Model\OrderNotifier')->notify($order);
				
				
				$payment->save();
				$order->save();	

		        $this->logger->warning("SadadQAR Webhook processing completed.");
				
				$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
				$successUrl=$storeManager->getStore()->getBaseUrl();
				
				?>
		        <script>
					window.location.href='<?php echo $successUrl; ?>checkout/onepage/success';
		        </script>
		<?php	}
			else
			{
				$payment_id='NA';
				$transaction->setTxnId($payment_id);
				$transaction->setAdditionalInformation(  
						"SadadQA Transaction Id",$payment_id
					);
				$transaction->setAdditionalInformation(  
						"status","successful"
					);
				$transaction->setIsClosed(1);
				$transaction->save();
				$payment->addTransactionCommentsToOrder(
					$transaction,
					"Payment for transaction failed."
				);
				
				$order->cancel();

				$payment->setParentTransactionId(null);
				$payment->save();
				$order->save();


		        $this->logger->warning("SadadQAR Webhook processing failed.");
				
				$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
				$successUrl=$storeManager->getStore()->getBaseUrl();
				
				?>
		        <script>
					window.location.href='<?php echo $successUrl; ?>checkout/onepage/failure';
		        </script>
		<?php
			}
		}else{
			$payment_id='NA';
				$transaction->setTxnId($payment_id);
				$transaction->setAdditionalInformation(  
						"SadadQA Transaction Id",$payment_id
					);
				$transaction->setAdditionalInformation(  
						"status","failed"
					);
				$transaction->setIsClosed(1);
				$transaction->save();
				$payment->addTransactionCommentsToOrder(
					$transaction,
					"Payment for transaction failed."
				);
				
				$order->cancel();

				$payment->setParentTransactionId(null);
				$payment->save();
				$order->save();


		        $this->logger->warning("SadadQAR Webhook processing failed.");
				
				$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
				$successUrl=$storeManager->getStore()->getBaseUrl();
				
				?>
		        <script>
					window.location.href='<?php echo $successUrl; ?>checkout/onepage/failure';
		        </script>
	<?php	}
      
     
		exit;
    }


    /**
     * @return Webhook post data as an array
     */
    private function getPostData() : array
    {
        return $_POST;
    }
	
	private function verifychecksum_eFromStr($str, $key, $checksumvalue) {

    $sadad_hash = $this->decrypt_e($checksumvalue, $key);

    $salt = substr($sadad_hash, -4);



    $finalString = $str . "|" . $salt;



    $website_hash = hash("sha256", $finalString);

    $website_hash .= $salt;



    $validFlag = "FALSE";

    if ($website_hash == $sadad_hash) {

        $validFlag = "TRUE";

    } else {

        $validFlag = "FALSE";

    }

    return $validFlag;

}



private function decrypt_e($crypt, $ky) {

    $ky = html_entity_decode($ky);

    $iv = "@@@@&&&&####$$$$";

    $data = openssl_decrypt($crypt, "AES-128-CBC", $ky, 0, $iv);

    return $data;

}
}