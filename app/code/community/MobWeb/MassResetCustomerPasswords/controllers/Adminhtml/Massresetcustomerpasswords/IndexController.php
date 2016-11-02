<?php

/**
 * 
 *
 * @author    Louis Bataillard <info@mobweb.ch>
 * @package    MobWeb_MassResetCustomerPasswords
 * @copyright    Copyright (c) MobWeb GmbH (https://mobweb.ch)
 */
class MobWeb_MassResetCustomerPasswords_Adminhtml_Massresetcustomerpasswords_IndexController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return true;
    }
    
    public function indexAction()
    {
        // Get the customer IDs from the request
        $customerIds = $this->getRequest()->getParam('customer');

        // Loop through the customer IDs
        $customersProcessed = 0;
        foreach ($customerIds as $customerId) {

            // Try and load the customer object
            $customer = Mage::getModel('customer/customer')->load($customerId);

            // Verify that the customer object has been loaded properly
            if ($customer && !$customer->isObjectNew()) {

                // Generate and send the new password
                $newPassword = Mage::helper('mobweb_massresetcustomerpasswords/customer')->generatePassword();
                $customer->changePassword($newPassword);
                $customer->sendPasswordReminderEmail();

                // Save the updated customer object
                $customer->save();

                // Wait for half a second so as to not send too many emails too quickly
                usleep(0.5 * 1000000);

                // Keep track of how many customers have been processed
                $customersProcessed++;
            }
        }

        // Prepare a success message for the admin
        Mage::getSingleton('core/session')->addSuccess(sprintf('%d customer accounts have been sent a new password.', $customersProcessed));

        // Redirect the user back to the customer list
        $this->_redirect('adminhtml/customer/index');
    }
}