<?php
/*
 *
 */
class Fishpig_FlatCategoryURL_Block_System_Config_Form_Field_Actions extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	/*
	 *
	 *
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
	{
		$element->setScopeLabel('');

		return $this->_getButtonHtml('Reindex Data', $this->getUrl('adminhtml/flatcategoryurl/reindex'))
		. '&nbsp;' . $this->_getButtonHtml('Clean Data', $this->getUrl('adminhtml/flatcategoryurl/reindex'));
	}
	
	/*
	 *
	 *
	 * @param  string $label
	 * @param  string $label
	 * @return string
	 */ 
	protected function _getButtonHtml($label, $url)
	{
		return sprintf(
			'<button type="button" class="button" onclick="setLocation(\'%s\');"><span>%s</span></button>',
			$url,
			$label
		);
	}
}