<?php

/**
 * 
 *
 * @author    Louis Bataillard <info@mobweb.ch>
 * @package    MobWeb_MassResetCustomerPasswords
 * @copyright    Copyright (c) MobWeb GmbH (https://mobweb.ch)
 */
class MobWeb_MassResetCustomerPasswords_Helper_Data extends Mage_Core_Helper_Data
{
    public $customerEmailSentAttributeCode = 'password_reset_email_sent';
    public $customerDataExportedAttributeCode = 'customer_data_exported';
}