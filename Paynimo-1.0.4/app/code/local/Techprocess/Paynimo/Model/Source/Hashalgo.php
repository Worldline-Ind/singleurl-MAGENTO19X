<?php
/**
* Custom Options for paynimo backend configuration for WSD Locator Url
**/

class Techprocess_Paynimo_Model_Source_Hashalgo extends Mage_Adminhtml_Block_System_Config_Form_Field

{
    protected $_options;

    public function toOptionArray()
    {
         $trans_req = array(
           array('value' => 'SHA3-512', 'label' => 'SHA3-512'),
           array('value' => 'SHA3-256', 'label' => 'SHA3-256'),
       );
 
       return $trans_req;
    }
}
?>