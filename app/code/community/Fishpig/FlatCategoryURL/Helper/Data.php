<?php
/**
 *
 */
class Fishpig_FlatCategoryURL_Helper_Data extends Mage_Core_Helper_Abstract
{
  /**
   * @const string
   */
  const ATTRIBUTE_NAME = 'flatcategoryurl_enabled';
  
	/*
	 * @var bool
	 */
	protected $isEnabled;
	
	/*
	 * @var bool
	 */
	protected $canIncludeParentPath;
	
	/*
	 * @var bool
	 */
	protected $isAppliedGlobally;
	
	
	/*
	 * Determine whether this feature is enabled
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		if (is_null($this->isEnabled)) {
			$this->isEnabled = Mage::getStoreConfigFlag('flatcategoryurl/settings/enabled');
		}
		
		return $this->isEnabled;
	}
	
	/*
	 * IF true, URLs like cat/subcat.html are converted to cat-subcat.html
	 * Else cat/subcat.html is converted to subcat.html
	 *
	 * @return bool
	 */
	public function canIncludeParentPath()
	{
		if (is_null($this->canIncludeParentPath)) {
			$this->canIncludeParentPath = Mage::getStoreConfigFlag('flatcategoryurl/settings/include_parent_path');
		}
		
		return $this->canIncludeParentPath;
	}
	
	/*
	 * IF true, URLs like cat/subcat.html are converted to cat-subcat.html
	 * Else cat/subcat.html is converted to subcat.html
	 *
	 * @return bool
	 */
	public function isAppliedGlobally()
	{
		if (is_null($this->isAppliedGlobally)) {
			$this->isAppliedGlobally = Mage::getStoreConfigFlag('flatcategoryurl/settings/apply_globally');
		}
		
		return $this->isAppliedGlobally;
	}
	
	/**
   *
   * @param $category
   */
  public function isAllowedCategory($category)
  {
    if (!$this->isEnabled()) {
      return false;
    }

    if ($this->isAppliedGlobally()) {
      return true;
    }

    $attributeModel = Mage::getSingleton('eav/config')->getAttribute('catalog_category', self::ATTRIBUTE_NAME);
    
    if (!$attributeModel->getId()) {
      return false;
    }


    $storeId = (int)Mage::app()->getRequest()->getParam('store');
    $categoryId = (int)(is_object($category) ? $category->getId() : $category);
    $db = Mage::getSingleton('core/resource')->getConnection('core_read');
			
		// Get Admin Value
		$select = $db->select()
		  ->from(['a' => $attributeModel->getBackendTable()], null)
		  ->where('a.attribute_id=?', $attributeModel->getId())
		  ->where('a.entity_id=?', $categoryId)
		  ->where('a.store_id=?', 0);
		
		if ($storeId > 0) {
  		// Join current store value  
  	  $select->joinLeft(
  		  ['s' => $attributeModel->getBackendTable()],
  		  's.attribute_id = a.attribute_id AND a.entity_id = s.entity_id AND s.store_id = ' . (int)Mage::app()->getRequest()->getParam('store'),
  		  null
  	  );
  		  
  	  // Select correct scope value
      $select->columns(['value' => new \Zend_Db_Expr('IFNULL(s.value, a.value)')]);
    }
    else {
      $select->columns('value');
    }

    return (int)$db->fetchOne($select) === 1;
  }
  
  /**
   *
   *
   * @return 
   */
  public function categoryPrepareAjaxResponseObserver(Varien_Event_Observer $observer)
  {
    $response = $observer->getEvent()->getResponse();

    $response->setContent(
      $response->getContent() . $this->_getCategoryAttributeJs()
    );
  }

  /**
   *
   *
   * @return 
   */
  protected function _getCategoryAttributeJs()
  {
    if (!$this->isEnabled() || $this->isAppliedGlobally()) {
      return '<script>$("group_4flatcategoryurl_enabled").up("tr").remove();</script>';
    }

    return '';
  }
}