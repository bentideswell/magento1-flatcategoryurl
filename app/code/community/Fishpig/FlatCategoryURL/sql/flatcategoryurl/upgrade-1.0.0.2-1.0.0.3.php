<?php
/**
 *
 */
$this->startSetup();

$this->addAttribute('catalog_category', Fishpig_FlatCategoryURL_Helper_Data::ATTRIBUTE_NAME, array(
  'group' => 'General Information',
  'input' => 'select',
  'type' => 'int',
  'label' => 'Use Flat URL',
  'source' => 'eav/entity_attribute_source_boolean',
  'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
  'visible' => 1,
  'required' => 0,
  'visible_on_front' => 0,
  'unique' => false,
  'user_defined' => false,
  'default' => '0',
  'is_user_defined' => false,
  'sort_order' => 3,
));

$this->endSetup();
