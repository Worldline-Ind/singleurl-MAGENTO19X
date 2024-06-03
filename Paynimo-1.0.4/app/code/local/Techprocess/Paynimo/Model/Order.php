<?php

class Techprocess_Paynimo_Model_Order extends Mage_Sales_Model_Order
{
    public function getPaymentBlock()
    {
        if (!$this->getPayment()) {
            return false;
        }

        $infoBlock = $this->getPayment()->getInfoBlock();
        if (empty($infoBlock)) {
            $infoBlock = Mage::helper('payment')->getInfoBlock($this->getPayment());
        }

        return $infoBlock;
    }
}