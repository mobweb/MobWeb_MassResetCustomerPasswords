<?php

$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$entityTypeId = $setup->getEntityTypeId('customer');
$attributeSetId = $setup->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

/*
 *
 * Add the "Email sent?" attribute
 *
 */
$attributeCode = Mage::helper('mobweb_massresetcustomerpasswords')->customerEmailSentAttributeCode;

$setup->addAttribute('customer', $attributeCode, array(
    'type' => 'int',
    'input' => 'select',
    'frontend_input' => 'select',
    'source' => 'eav/entity_attribute_source_boolean',
    'label' => '"New Password" email sent',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'user_defined' => 0,
    'default' => '0',
    'visible_on_front' => 0,
));

$setup->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    $attributeCode,
    '100'
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', $attributeCode);
$oAttribute->setData('used_in_forms', array('adminhtml_customer')); 
$oAttribute->save();

// Set the default value for all existing customer accounts
function setDefaultValue($args)
{
    $attributeCode = Mage::helper('mobweb_massresetcustomerpasswords')->customerEmailSentAttributeCode;

    $customer = Mage::getModel('customer/customer');
    $customer->setData($args['row']);
    $customer->setData($attributeCode, '0');
    $customer->getResource()->saveAttribute($customer, $attributeCode);
}

$customers = Mage::getModel('customer/customer')->getCollection();
Mage::getSingleton('core/resource_iterator')->walk($customers->getSelect(), array('setDefaultValue'));

/*
 *
 * Add the "Data exported?" attribute
 *
 */
$attributeCode = Mage::helper('mobweb_massresetcustomerpasswords')->customerDataExportedAttributeCode;

$setup->addAttribute('customer', $attributeCode, array(
    'type' => 'int',
    'input' => 'select',
    'frontend_input' => 'select',
    'source' => 'eav/entity_attribute_source_boolean',
    'label' => 'Customer data exported?',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'user_defined' => 0,
    'default' => '0',
    'visible_on_front' => 0,
));

$setup->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    $attributeCode,
    '100'
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', $attributeCode);
$oAttribute->setData('used_in_forms', array('adminhtml_customer')); 
$oAttribute->save();

// Set the default value for all existing customer accounts
function setDefaultValue2($args)
{
    $attributeCode = Mage::helper('mobweb_massresetcustomerpasswords')->customerDataExportedAttributeCode;

    $customer = Mage::getModel('customer/customer');
    $customer->setData($args['row']);
    $customer->setData($attributeCode, '0');
    $customer->getResource()->saveAttribute($customer, $attributeCode);
}

$customers = Mage::getModel('customer/customer')->getCollection();
Mage::getSingleton('core/resource_iterator')->walk($customers->getSelect(), array('setDefaultValue2'));

$setup->endSetup();