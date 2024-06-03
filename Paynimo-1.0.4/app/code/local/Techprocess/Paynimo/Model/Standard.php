<?php
/**
 * Paynimo Standard Checkout Module
 **/

require_once(Mage::getBaseDir() . '/app/code/local/Techprocess/Paynimo/Model/Lib/TransactionRequestBean.php');
require_once(Mage::getBaseDir() . '/app/code/local/Techprocess/Paynimo/Model/Lib/TransactionResponseBean.php');

class Techprocess_Paynimo_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
	protected $_code = 'paynimo';
	protected $_isInitializeNeeded      = true;
	protected $_canUseInternal          = true;
	protected $_canUseForMultishipping  = false;
	protected $_trc;  //TransactionRequestBean

	/**
     * Transaction Request getter for payment module
     */

	public function getRequest()
	{
		if (Mage::getSingleton('customer/session')->isLoggedIn()){
		$customerData = Mage::getSingleton('customer/session')->getCustomer();
		$customerAddressId = Mage::getSingleton('customer/session')->getCustomer()->getDefaultBilling();
		$address = Mage::getModel('customer/address')->load($customerAddressId);
		$customerId = Mage::getSingleton('customer/session')->getId();
		$customerEmail = Mage::helper('customer')->getCustomer()->getData('email');
		$customerMobNumber = $address->getTelephone();
		$customerFirstName = Mage::helper('customer')->getCustomer()->getData('firstname');
		$customerLastName = Mage::helper('customer')->getCustomer()->getData('lastname');
		$customerName = $customerFirstName. " ". $customerLastName;
 		}
		$orderId= Mage::getSingleton('checkout/session')->getLastRealOrderId();
		$order = Mage::getSingleton('sales/order')->loadByIncrementId($orderId);
		$customer = Mage::getSingleton('customer/session')->getCustomer();
	    $iv = Mage::getStoreConfig('payment/paynimo/paynimo_iv');
	    $key = Mage::getStoreConfig('payment/paynimo/paynimo_key');
	    $ReqType = Mage::getStoreConfig('payment/paynimo/paynimo_request_type');
		$MrctCode = Mage::getStoreConfig('payment/paynimo/paynimo_mercode');
		$MrctsCode = Mage::getStoreConfig('payment/paynimo/paynimo_scode');
		$hashalgo = Mage::getStoreConfig('payment/paynimo/paynimo_hashalgo');
		$TpvAccntNo = '';
		$Itc = 'email:'.$customerEmail;
		$MobNo = $customerMobNumber;
		$MrctTxtID = $orderId;
		$CurrencyType = 'INR';
		$ReturnURL = Mage::getBaseUrl().'paynimo/payment/response';
		$Date = date("d-m-Y");
		$TPSLTxnID = 'TXN00'.rand(1,10000);
		$_trc = new TransactionRequestBean;
		$_trc->setMerchantCode($MrctCode);
		$_trc->setAccountNo($TpvAccntNo);
		$_trc->setITC('email:'.$customerEmail);
		$_trc->setEmail($customerEmail);
		$_trc->setUniqueCustomerId($customerId);
		$_trc->setCustomerName($customerName);
		$_trc->setMobileNumber($customerMobNumber);
		$_trc->setIv($iv);
		$_trc->setKey($key);
		$_trc->setRequestType($ReqType);
		$_trc->setMerchantTxnRefNumber($MrctTxtID);
		$_trc->setHashAlgo($hashalgo);
		if(Mage::getStoreConfig('payment/paynimo/paynimo_url') == 'test'){
			$_trc->setWebServiceLocator('https://www.tpsl-india.in/PaymentGateway/TransactionDetailsNew.wsdl');
			$_trc->setBankCode('470');
			$Amount = "1.0";
		}
		else{
			$_trc->setWebServiceLocator('https://www.tpsl-india.in/PaymentGateway/TransactionDetailsNew.wsdl');
			$Amount = round($order->getBaseGrandTotal(),2);
		}
		$_trc->setAmount($Amount);
		$ShoppingCartStr = $MrctsCode.'_'.$Amount.'_0.0';
		$_trc->setShoppingCartDetails($ShoppingCartStr);

		$_trc->setCurrencyCode($CurrencyType);
		$_trc->setReturnURL($ReturnURL);
		$_trc->setTxnDate($Date);
		$_trc->setTPSLTxnID($TPSLTxnID);
		$_trc->setCustId($customerId);
		$responseDetails = $_trc->getTransactionToken();
		$responseDetails = (array)$responseDetails;
		$response = $responseDetails[0];
		header("Location: $response");
		exit;
	}

	/**
     * Return Order place redirect url
     *
     * @return string
     */

	public function getOrderPlaceRedirectUrl()
	{
		return Mage::getUrl('paynimo/payment/redirect', array('_secure' => true));
	}
}