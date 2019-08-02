<?php
/*
 *
 */
class Fishpig_FlatCategoryURL_Model_Catalog_Url extends Mage_Catalog_Model_Url
{  
	/**
	 * Get unique category request path
	 *
	 * @param Varien_Object $category
	 * @param string $parentPath
	 * @return string
	 */
	public function getCategoryRequestPath($category, $parentPath)
	{
  	$helper = Mage::helper('flatcategoryurl');
  	
  	if (!$helper->isEnabled() || !$helper->isAllowedCategory($category)) {
    	return parent::getCategoryRequestPath($category, $parentPath);
  	}

		$storeId = $category->getStoreId();
		$idPath  = $this->generatePath('id', null, $category);
	
		if (isset($this->_rewrites[$idPath])) {
			$this->_rewrite = $this->_rewrites[$idPath];
			$existingRequestPath = $this->_rewrites[$idPath]->getRequestPath();
		}
	
		if ($category->getUrlKey() == '') {
			$urlKey = $this->getCategoryModel()->formatUrlKey($category->getName());
		}
		else {
			$urlKey = $this->getCategoryModel()->formatUrlKey($category->getUrlKey());
		}
	
		$categoryUrlSuffix = $this->getCategoryUrlSuffix($storeId);

		if ($helper->canIncludeParentPath()) {
			if (null === $parentPath) {
				$parentPath = $this->getResource()->getCategoryParentPath($category);
			}
			elseif ($parentPath == '/') {
				$parentPath = '';
			}

			$parentPath = Mage::helper('catalog/category')->getCategoryUrlPath($parentPath, true, $storeId);
		}
		else {
			$parentPath = '';
		}			

		$requestPath = $parentPath . $urlKey;
		$regexp = '/^' . preg_quote($requestPath, '/') . '(\-[0-9]+)?' . preg_quote($categoryUrlSuffix, '/') . '$/i';

		if (isset($existingRequestPath) && preg_match($regexp, $existingRequestPath)) {
			return $existingRequestPath;
		}
	
		$fullPath = $requestPath . $categoryUrlSuffix;
		
		if ($parentPath) {
			$fullPath = trim(preg_replace('/[-]{2,}/', '_', str_replace('/', '-', $fullPath)), '-');
		}	
		
		if ($this->_deleteOldTargetPath($fullPath, $idPath, $storeId)) {
			return $requestPath;
		}
	
		return $this->getUnusedPathByUrlKey($storeId, $fullPath, $this->generatePath('id', null, $category), $urlKey);
	}
    
	/*
	 * We don't allow category product rewrites
	 *
	 * @param  Varien_Object $category
	 * @param  $parentPath = null
	 * @param  $refreshProducts = true
	 * @return $this
	 */ 
	protected function _refreshCategoryRewrites(Varien_Object $category, $parentPath = null, $refreshProducts = true)
	{
  	$helper = Mage::helper('flatcategoryurl');

		if (!$helper->isEnabled() || !$helper->isAllowedCategory($category)) {
  		return parent::_refreshCategoryRewrites($category, $parentPath, $refreshProducts);
    }

 		return parent::_refreshCategoryRewrites($category, $parentPath, false);    
	}

	/**
	 * We don't allow category product rewrites
	 *
	 * @param  Varien_Object $category
	 * @return $this
	 */
	protected function _refreshCategoryProductRewrites(Varien_Object $category)
	{
  	$helper = Mage::helper('flatcategoryurl');
  	
		if (!$helper->isEnabled() || !$helper->isAllowedCategory($category)) {
			return parent::_refreshCategoryProductRewrites($category);
		}
		
		return $this;
	}
	
	/*
	 * We don't allow category product rewrites
	 *
	 * @param  Varien_Object $product
	 * @param  Varien_Object $category
	 * @return $this
	 */
	protected function _refreshProductRewrite(Varien_Object $product, Varien_Object $category)
	{
  	$helper = Mage::helper('flatcategoryurl');
  	
		if (!$helper->isEnabled() || !$helper->isAllowedCategory($category)) {
			return parent::_refreshProductRewrite($product, $category);
		}

		if ((int)$category->getId() !== (int)$this->getStoreRootCategory($category->getStoreId())->getId()) {
			return $this;
		}
		
		return parent::_refreshProductRewrite($product, $category);
	}
}
