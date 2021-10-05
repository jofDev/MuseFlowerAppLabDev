<?php

namespace Sadadqar\Magento\Controller\Payment;

use Sadadqar\Magento\Model\PaymentMethod;
use Magento\Framework\Controller\ResultFactory;
use Sadadqar\Magento\Model\Config;
use Magento\Framework\App\RequestInterface;

class Order  extends \Magento\Framework\App\Action\Action 
{
	protected $quote;

	protected $checkoutSession;
	
	protected $orderFactory;

	protected $_currency = PaymentMethod::CURRENCY;
	
	protected $PageFactory;
	
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Sadadqar\Magento\Model\CheckoutFactory $checkoutFactory,
        \Sadadqar\Magento\Model\Config $config,
		 \Magento\Catalog\Model\Session $catalogSession
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->config = $config;
		

        $this->key_id = $this->config->getConfigData(Config::KEY_PUBLIC_KEY);
        $this->key_secret = $this->config->getConfigData(Config::KEY_PRIVATE_KEY);
		$this->website = $this->config->getConfigData(Config::KEY_WEBSITE);
		$this->checkLang = $this->config->getConfigData(Config::KEY_CHECKLANG);
		$this->checkoutType = $this->config->getConfigData(Config::KEY_CHEKOUTTYPE);
		$this->checkoutType2 = $this->config->getConfigData(Config::KEY_CHECKOUTTYPE2);
		$this->hideLoader = $this->config->getConfigData(Config::KEY_HIDELOAD);
    }

    public function execute()
    {
		
       if(isset($_GET['redirct']) && $_GET['redirct']==1)
	   {
	   		$this->redirectToSadad();
	   }  
	  
	   
    }
	
	protected function redirectToSadad()
	{
		 try 
        {
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$orderId = $this->checkoutSession->getLastOrderId();
			
			$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
			$baseUrll=$storeManager->getStore()->getBaseUrl();
			
			if(!$orderId)
			{
				?>
                <script>
					window.location.href='<?php echo $baseUrll; ?>';
                </script>
                <?php
				exit;
			}
			
			//$baseUrll=str_replace(array("http://www.","https://www."),array("http://","https://"),$baseUrll);
			//$baseUrll=str_replace(array("http://","https://"),array("http://www.","https://www."),$baseUrll);
			
			$order=$objectManager->create('\Magento\Sales\Model\OrderRepository')->get($orderId);
			
			$billing = $order->getBillingAddress();
			$shipping = $order->getShippingAddress();
			$amount = round($order->getGrandTotal(), 2);
			 
			 $payment = $order->getPayment();
			
			$payment->setTransactionId("-1");
			  $payment->setAdditionalInformation(  
				[\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => array("Transaction is yet to complete")]
			);
			$trn = $payment->addTransaction(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE,null,true);
			$trn->setIsClosed(0)->save();
			 $payment->addTransactionCommentsToOrder(
                $trn,
               "The transaction is yet to complete."
            );

            $payment->setParentTransactionId(null);
            $payment->save();
            $order->save();
			

			$redirect_url = $baseUrll.'sadadqa/payment/webhook';
			 


		 //////////////
            $order_id = $order->getIncrementId();
           
            $txntype = '1';
            $ptmoption = '1';
            $currency = 'QAR';
            $purpose = "1";
            $productDescription = 'sadadpay';


            $ip = $_SERVER['REMOTE_ADDR'];

            $email = $order->getCustomerEmail();
            $mobile_no = $billing->getTelephone();
			$txnDate = date('Y-m-d H:i:s');
           
            

            $sadad_checksum_array = array();
			 
            $incVal = 0;
            
			foreach ($order->getAllItems() as $item) {
				
				$itmPrice=round($item->getPrice(),2);
				
				$sadad_checksum_array['productdetail'][$incVal]['order_id']    = $order_id;
				$sadad_checksum_array['productdetail'][$incVal]['itemname']    = $item->getName();
				$sadad_checksum_array['productdetail'][$incVal]['amount']      = $itmPrice;
				$sadad_checksum_array['productdetail'][$incVal]['totalamount'] = $itmPrice*$item->getQtyOrdered();
				$sadad_checksum_array['productdetail'][$incVal]['quantity']    = round($item->getQtyOrdered());
				$sadad_checksum_array['productdetail'][$incVal]['type']        = 'line_item';
				$sadad_checksum_array['productdetail'][$incVal]['item_meta']   = '';
				$incVal++;
			}
      
			
			$sadad_checksum_array['merchant_id']                     = $this->key_id;
            $sadad_checksum_array['ORDER_ID']                        = $order_id;
            $sadad_checksum_array['WEBSITE']                         = $this->website;
            $sadad_checksum_array['TXN_AMOUNT']                      = number_format((float)$amount, 2, '.', '');
            //$sadad_checksum_array['shipping_rate']                   = number_format((float)$order->getShippingAmount(), 2, '.', '');
			$sadad_checksum_array['VERSION']                         = '1.1';
            $sadad_checksum_array['CUST_ID']                         = $email;
            $sadad_checksum_array['EMAIL']                           = $email;
            $sadad_checksum_array['MOBILE_NO']                       = $mobile_no;
            $sadad_checksum_array['MODE']                            = 'yes';
            $sadad_checksum_array['SADAD_WEBCHECKOUT_PAGE_LANGUAGE'] = $this->checkLang;
            $sadad_checksum_array['CALLBACK_URL']                    = $redirect_url;
            $sadad_checksum_array['txnDate']                         = $txnDate;
			
            $chkType = $this->checkoutType;
			$chkLoader = ($this->hideLoader>0)?'YES':'NO';
			$chkType2 = $this->checkoutType2;
			 
            //if webcheckout 2.2, add these fields to checksum
            if ($chkType == 2) {
                $sadad_checksum_array['SADAD_WEBCHECKOUT_HIDE_LOADER'] = $chkLoader;
                $sadad_checksum_array['showdialog']                    = (string) $chkType2;
            }
			$sAry1 = array();
			$sadad_checksum_array1 = array();
			foreach($sadad_checksum_array as $pK => $pV){
				if($pK=='checksumhash') continue;
				if(is_array($pV)){
					$prodSize = sizeof($pV);
					for($i=0;$i<$prodSize;$i++){
						foreach($pV[$i] as $innK => $innV){
							$sAry1[] = "<input type='hidden' name='productdetail[$i][". $innK ."]' value='" . trim($innV) . "'/>";
							$sadad_checksum_array1['productdetail'][$i][$innK]    = trim($innV);
						}
					}
				} else {
					$sAry1[] = "<input type='hidden' name='". $pK ."' id='". $pK ."' value='" . trim($pV) . "'/>";
					$sadad_checksum_array1[$pK]    = trim($pV);
				}
			}
            
            $sadad__checksum_data['postData']  = $sadad_checksum_array1;
            $sadad__checksum_data['secretKey'] = $this->key_secret;
            $checksum                          = $this->getChecksumFromString(json_encode($sadad__checksum_data), $this->key_secret . $this->key_id);
            $sAry1[]                = "<input type='hidden' name='checksumhash' value='" . $checksum . "'/>";
			
           if ($chkType == 1) {
                $action_url = 'https://sadadqa.com/webpurchase';
                
                echo '<form action="' . $action_url . '" method="post" id="sadad_payment_form" name="gosadad">
                    ' . implode('', $sAry1) . '
                    <script type="text/javascript">
                        document.gosadad.submit();
                    </script>
                    
                </form>';
			   exit;
            } else {
				$action_url = 'https://secure.sadadqa.com/webpurchasepage';
                
                echo '<form action="' . $action_url . '" method="post" id="paymentform" name="paymentform" data-link="' . $action_url . '">
                    ' . implode('', $sAry1) . '
                </form>';
                
?>
                
                
                
                
                    <style>
						.modal123 {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 999999; /* Sit on top */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content/Box */
.modal123-content {
  background-color: #fff;
  margin: 20px auto; /* 15% from the top and centered */
  padding: 20px;
  border: 1px solid #888;
  width: 40%; /* Could be more or less, depending on screen size */
}

                        .close-btn{
                            height: auto;
                            width: auto;
                            -webkit-appearance: none !important;
                            background: none !important;
                            border: 0px;
                            float: right;
                            right: 10px;
                            z-index: 11;
                            cursor: pointer;
                            outline: 0px !important;
                            box-shadow: none;
                            top: 15px;
                        }
                        .close, .close:hover{
                            color: #000;
                            font-size:30px;
                        }
                        .modal123-body{
                            padding: 0px;
                            border-radius: 15px;
                        }
                        #onlyiframe{
                         width:100% !important;
                         height:100vh !important;
                         overflow: hidden !important;
                         border:0;
                        
                         top: 0;
                         left: 0;
                         bottom: 0;
                         right: 0;
                              
                        }
                        #includeiframe{
                            height:100vh !important;
                         overflow: hidden !important;
                        border:0;
						width:100%;
                        }
                        .modal-backdrop {
                           background-color: #000 !important;
                        }
						#exampleModal .modal-content{
							background: #fff !important;
						}
                    </style>
                    <!-- Modal -->
                    <div id="container_div_sadad">
						
                    <div class="modal123 fade not_hide_sadad" id="exampleModal" >
                        <div class="modal123-dialog">
                            <div class="modal123-content">
                               
                                <div class="modal123-body">
									 <button type="button" class="close-btn" onClick="closemodal();" aria-label="Close">
                                    <span class="close">&times;</span>
                                </button>
                                    <iframe name="includeiframe" id="includeiframe" frameborder="0" scrolling="no"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                    <iframe name="onlyiframe" id="onlyiframe" border="0" class="not_hide_sadad" frameborder="0" scrolling="no"></iframe>
                    </div>
                    
                    <script type="text/javascript">
					
                        function closemodal()
                        {
							window.location.href = '<?php echo $baseUrll; ?>checkout/#payment';
                            //$('#exampleModal').modal('hide');
                        }
                        jQuery(document).ready(function($){
                        
                                
                             $('iframe').load(function() {
                                    $(this).height( $(this).contents().find("body").height() );
                                    if(this.contentWindow.location=='<?php
                echo $redirect_url;
?>'){
									
									$(this).hide();
									window.location.href = '';
                                        
                                    }
                                });    
                        });
                    </script>

                <?php
			}
			
			
        } 
        catch (\Exception $e) 
        {
			
			echo '<pre>';
			print_r($e->getMessage());
        }
		
		exit;
	}
	
		private function getChecksumFromString($str, $key) {
	
		
	
		$salt = $this->generateSalt_e(4);
	
		$finalString = $str . "|" . $salt;
	
		$hash = hash("sha256", $finalString);
	
		$hashString = $hash . $salt;
	
		$checksum = $this->encrypt_e($hashString, $key);
	
		return $checksum;
	
	}
	
	
	
	private function generateSalt_e($length) {
	
		$random = "";
	
		srand((double) microtime() * 1000000);
	
	
	
		$data = "AbcDE123IJKLMN67QRSTUVWXYZ";
	
		$data .= "aBCdefghijklmn123opq45rs67tuv89wxyz";
	
		$data .= "0FGH45OP89";
	
		for ($i = 0; $i < $length; $i++) {
	
			$random .= substr($data, (rand() % (strlen($data))), 1);
	
		}
	
		return $random;
	
	}



	private function encrypt_e($input, $ky) {
	
	$ky = html_entity_decode($ky);
	
	$iv = "@@@@&&&&####$$$$";
	
	$data = openssl_encrypt($input, "AES-128-CBC", $ky, 0, $iv);
	
	return $data;
	
	}
}
