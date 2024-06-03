<?php
/**
* Custom Options for paynimo backend configuration for WSD Locator Url
**/

class Techprocess_Paynimo_Model_Source_Url extends Mage_Adminhtml_Block_System_Config_Form_Field

{
    protected $_options;

    public function toOptionArray()
    {
         $trans_req = array(
           array('value' => 'test', 'label' => 'TEST'),
           array('value' => 'live', 'label' => 'LIVE'),
       );
 
       return $trans_req;
    }
}
?>