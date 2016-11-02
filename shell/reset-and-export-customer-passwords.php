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

        $dataExportedAttributeCode = Mage::helper('mobweb_massresetcustomerpasswords')->customerDataExportedAttributeCode;

        // Load $batch customer accounts that have not been processed yet
        $customers = Mage::getResourceModel('customer/customer_collection')
            ->addAttributeToSelect(array('gender', 'firstname', 'lastname', 'email', $dataExportedAttributeCode))
            ->addAttributeToFilter($dataExportedAttributeCode, array('eq' => '0'))
            ->setPage(1, $batch);

        // Optionally filter the customers by their email domain
        if (isset($domain)) {
            $customers->addAttributeToFilter('email', array('like' => '%' . $domain));
        }

        $customers->load();

        // Process the customers, collect the customer data
        $customerData = array();
        $count = $customers->count();
        $i = 0;
        $this->msg(sprintf('Processing %d customers', $count));

        foreach ($customers as $customer) {

            // Generate the new password
            $newPassword = Mage::helper('mobweb_massresetcustomerpasswords/customer')->generatePassword();
            $customer->changePassword($newPassword);

            // Update the "Data exported" attribute
            $customer->setData($dataExportedAttributeCode, '1');

            if (!isset($dryRun)) {

                // Save the updated customer object
                $customer->save();

                // Reset the timeout
                set_time_limit(60);
            }

            // Save the customer data
            $customerData[] = array(
                $customer->getData('gender'),
                $customer->getFirstname(),
                $customer->getLastname(),
                $customer->getEmail(),
                $newPassword,
            );

            $this->msg(sprintf('%d/%d | Password for account %s (%d) set to %s', ++$i, $count, $customer->getEmail(), $customer->getId(), $newPassword));
        }

        // Save the customer data into a CSV file
        $io = new Varien_Io_File();
        $filePath = Mage::getBaseDir('var') . DS . 'export' . DS . 'reset-and-export-customer-passwords' . DS . date('y-m-d-h-i-s') . '.csv';
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => dirname($filePath)));
        $io->streamOpen($filePath, 'w+');
        $io->streamLock(true);

        // Loop through data and write each row
        foreach ($customerData as $row) {
            $io->streamWriteCsv($row);
        }

        $this->msg(sprintf('Successfully exported %d/%d customers data to %s', $i, $count, $filePath));
    }

    private function msg($msg)
    {
        // Echo the $msg
        echo $msg;
        echo "\n";

        // Save the $msg in the log
        Mage::log($msg, NULL, 'reset-and-export-customer-passwords.log');
    }

    public function usageHelp()
    {
        return "
            Usage:  php -f reset-and-export-customer-passwords.php -- [options]

                --dryrun  Dry run, don't reset passwords.
                --domain <domain>  Only apply to customer accounts with email addreses from the specified domain.
                --batch <amount>  Limit how many accounts to process. Defaults to 1000.
        ";
    }
}

$shell = new MobWeb_MassResetCustomerPasswords_Shell();
$shell->run();