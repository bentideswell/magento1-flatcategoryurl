<?php
/**
 *
 */
class Fishpig_FlatCategoryURL_Adminhtml_FlatcategoryurlController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Determine ACL permissions
	 *
	 * @return bool
	 */
	protected function _isAllowed()
	{
		return true;
	}
	
	/**
	 *
	 *
	 * @return void
	 */
	public function reindexAction()
	{
		try {
			Mage::getModel('index/indexer')->getProcessByCode('catalog_url')->reindexAll();
			
			Mage::getSingleton('adminhtml/session')->addSuccess($this->__('%s index was rebuilt.', 'Catalog URL Rewrites'));			
		}
		catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		}
		
		return $this->_redirect('adminhtml/system_config/edit/section/flatcategoryurl');
	}
	
	/**
	 *
	 *
	 * @return void
	 */
	public function cleanAction()
	{
		try {
			
			$resource   = Mage::getSingleton('core/resource');
			$connection = $resource->getConnection('core_write');
			
			$connection->delete(
				$resource->getTableName('core/url_rewrite'),
				'product_id IS NOT NULL AND category_id IS NOT NULL'
			);
			
			Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Catalog URL Rewrites were cleaned.'));			
		}
		catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		}
		
		return $this->_redirect('adminhtml/system_config/edit/section/flatcategoryurl');
	}
}