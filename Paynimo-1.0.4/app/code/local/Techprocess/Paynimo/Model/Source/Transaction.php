<?php
/**
* Custom Options for paynimo backend configuration for Transaction Request Type
**/

class Techprocess_Paynimo_Model_Source_Transaction extends Mage_Adminhtml_Block_System_Config_Form_Field

{
    protected $_options;

    public function toOptionArray()
    {
         $trans_req = array(
           array('value' => 'T', 'label' => 'T'),
       );
 
       return $trans_req;
    }
}
?>