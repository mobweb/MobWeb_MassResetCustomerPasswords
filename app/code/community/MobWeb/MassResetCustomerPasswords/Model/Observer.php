<?php

/**
 * 
 *
 * @author    Louis Bataillard <info@mobweb.ch>
 * @package    MobWeb_MassResetCustomerPasswords
 * @copyright    Copyright (c) MobWeb GmbH (https://mobweb.ch)
 */
class MobWeb_MassResetCustomerPasswords_Model_Observer extends Mage_Core_Model_Abstract
{
    public function coreBlockAbstractPrepareLayoutBefore(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();

        if ($block instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction && $block->getRequest()->getControllerName() == 'customer')
        {
            // Add our custom link to the customer mass actions
            $block->addItem('mobweb_massresetcustomerpasswords', array(
                'label' => 'Send new password',
                'url' => $block->getUrl('adminhtml/massresetcustomerpasswords_index')
            ));
        }
    }
}
