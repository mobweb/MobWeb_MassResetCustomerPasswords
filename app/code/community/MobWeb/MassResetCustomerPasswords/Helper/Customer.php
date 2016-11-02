<?php

/**
 * 
 *
 * @author    Louis Bataillard <info@mobweb.ch>
 * @package    MobWeb_MassResetCustomerPasswords
 * @copyright    Copyright (c) MobWeb GmbH (https://mobweb.ch)
 */
class MobWeb_MassResetCustomerPasswords_Helper_Customer extends Mage_Core_Helper_Data
{
    /**
     * Retrieve random password
     *
     * @param   int $length
     * @return  string
     */
    public function generatePassword($length = 8)
    {
        $chars = Mage_Core_Helper_Data::CHARS_PASSWORD_LOWERS
            . Mage_Core_Helper_Data::CHARS_PASSWORD_UPPERS;
            // . Mage_Core_Helper_Data::CHARS_PASSWORD_DIGITS
            // . Mage_Core_Helper_Data::CHARS_PASSWORD_SPECIALS;
        return Mage::helper('core')->getRandomString($length, $chars);
    }
}