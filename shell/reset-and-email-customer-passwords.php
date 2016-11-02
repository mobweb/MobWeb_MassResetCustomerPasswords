<?php

/**
 *
 *
 * @author    Louis Bataillard <info@mobweb.ch>
 * @package    MobWeb_MassResetCustomerPasswords
 * @copyright    Copyright (c) MobWeb GmbH (https://mobweb.ch)
 */
require_once 'abstract.php';

class MobWeb_MassResetCustomerPasswords_Shell extends Mage_Shell_Abstract
{
    public function run()
    {
        // Increase the timeout
        set_time_limit(1000);

        if (isset($this->_args['dryrun']) ) {
            $dryRun = true;
        }

        if (isset($this->_args['domain']) ) {
            $domain = $this->_args['domain'];

            if (!filter_var('http://' . $domain, FILTER_VALIDATE_URL)) {
                echo sprintf('Invalid domain: %s', $domain);
                die();
            }
        }

        if (isset($this->_args['batch']) ) {
            $batch = (int) $this->_args['batch'];
        } else {
            $batch = 1000;
        }

        $emailSentAttributeCode = Mage::helper('mobweb_massresetcustomerpasswords')->customerEmailSentAttributeCode;

        // Load $batch customer accounts that have not been processed yet
        $customers = Mage::getResourceModel('customer/customer_collection')
            ->addAttributeToSelect(array('firstname', 'lastname', $emailSentAttributeCode))
            ->addAttributeToFilter($emailSentAttributeCode, array('eq' => '0'))
            ->setPage(1, $batch);

        // Optionally filter the customers by their email domain
        if (isset($domain)) {
            $customers->addAttributeToFilter('email', array('like' => '%' . $domain));
        }

        $customers->load();

        // Process the customers
        $count = $customers->count();
        $i = 0;
        $this->msg(sprintf('Processing %d customers', $count));

        foreach ($customers as $customer) {

            // Generate the new password
            $newPassword = $customer->generatePassword();
            $customer->changePassword($newPassword);

            if (!isset($dryRun)) {

                // Send the new password
                $customer->sendPasswordReminderEmail();

                // Update the "Email sent" attribute
                $customer->setData($emailSentAttributeCode, '1');

                // Save the updated customer object
                $customer->save();

                // Wait for a second before processing the next batch
                sleep(1);

                // Reset the timeout
                set_time_limit(60);
            }

            $this->msg(sprintf('%d/%d | Password for account %s (%d) set to %s & email sent', ++$i, $count, $customer->getEmail(), $customer->getId(), $newPassword));
        }

        $this->msg(sprintf('Successfully processed %d/%d customers', $i, $count));
    }

    private function msg($msg)
    {
        // Echo the $msg
        echo $msg;
        echo "\n";

        // Save the $msg in the log
        Mage::log($msg, NULL, 'reset-and-email-customer-passwords.log');
    }

    public function usageHelp()
    {
        return "
            Usage:  php -f reset-and-email-customer-passwords.php -- [options]

                --dryrun  Dry run, don't reset passwords or send emails.
                --domain <domain>  Only apply to customer accounts with email addreses from the specified domain.
                --batch <amount>  Limit how many accounts to process. Defaults to 1000.
        ";
    }
}

$shell = new MobWeb_MassResetCustomerPasswords_Shell();
$shell->run();