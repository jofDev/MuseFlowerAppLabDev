<?php
namespace Applab\OrderMailFix\Model\Order\Email\Sender;

use Magento\Sales\Model\Order;

class OrderSender extends \Magento\Sales\Model\Order\Email\Sender\OrderSender {

    public function send(Order $order, $forceSyncMode = false)
    {
        //echo $payment = $order->getPayment()->getMethodInstance()->getCode().'---'.$order->getStatus(); exit(' ----testing');
        $payment = $order->getPayment()->getMethodInstance()->getCode();
        if($payment == 'sadadqa' && !in_array($order->getStatus(), ['processing','complete','closed'])){ 
            return false;
        }
        $order->setSendEmail(true);

        if (!$this->globalConfig->getValue('sales_email/general/async_sending') || $forceSyncMode) {
            if ($this->checkAndSend($order)) {
                $order->setEmailSent(true);
                $this->orderResource->saveAttribute($order, ['send_email', 'email_sent']);
                return true;
            }
        }

        $this->orderResource->saveAttribute($order, 'send_email');

        return false;
    }
}