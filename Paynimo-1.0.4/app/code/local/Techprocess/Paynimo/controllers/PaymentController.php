<?php
/**
 * Paynimo Payment Standard Checkout Controller
 **/

require_once(Mage::getBaseDir() . '/app/code/local/Techprocess/Paynimo/Model/Lib/TransactionRequestBean.php');
require_once(Mage::getBaseDir() . '/app/code/local/Techprocess/Paynimo/Model/Lib/TransactionResponseBean.php');
require_once(Mage::getBaseDir() . '/app/code/local/Techprocess/Paynimo/Model/Standard.php');

class Techprocess_Paynimo_PaymentController extends Mage_Core_Controller_Front_Action {
	
	public function getDebug ()
    {
        return Mage::getSingleton('paynimo/config')->getDebug();
    }
	
	 /**
     * When a customer chooses Paynimo on Checkout/Payment page
     *
     */
	public function redirectAction() 
	{
		$standard = Mage::getModel('paynimo/standard');
		$standard->getRequest();
	}
	
	// The response action is triggered when your gateway sends back a response after processing the customer's payment
	public function responseAction() 
	{
		$data = Mage::app()->getRequest()->getParams();
		$str = $data['msg'];
		$iv = Mage::getStoreConfig('payment/paynimo/paynimo_iv');
		$key = Mage::getStoreConfig('payment/paynimo/paynimo_key');
		$trs = new TransactionResponseBean();
		$trs->setResponsePayload($str);
		$trs->setKey($key);
		$trs->setIv($iv);
		$response = $trs->getResponsePayload();
		$responseDetails = explode('|',$response);
		$responseData = array();
		foreach($responseDetails as $responseDetailsData)
		{
			$data = explode("=",$responseDetailsData);
			$responseData[$data[0]] = $data[1];
		}
		
		$orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
		$order = Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
		
		if($responseData['txn_status'] == 300)
		{
			//success
			$successstatus = Mage::getStoreConfig('payment/paynimo/order_status');
			$order = Mage::getModel('sales/order');
			$order->loadByIncrementId($orderId);
			$order->setState($successstatus, true, 'Gateway has authorized the payment.');
			$order->sendNewOrderEmail();
			$order->setEmailSent(true);
			$order->save();
			Mage::getSingleton('checkout/session')->unsQuoteId();
			Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/success', array('_secure'=>true));
		}
		else
		{
			//failed
			$this->cancelAction();
			Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure'=>true));
		}
	}
	
	 /**
     * When a customer cancel payment from paynimo.
     */
	
	// The cancel action is triggered when an order is to be cancelled
	public function cancelAction() 
	{
        if (Mage::getSingleton('checkout/session')->getLastRealOrderId())
		{
            $order = Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
            if($order->getId()) 
			{
				// Flag the order as 'cancelled' and save it
				$order->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, 'Gateway has declined the payment.')->save();
			}
        }
	}
}