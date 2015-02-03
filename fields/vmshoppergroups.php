<?php
defined('_JEXEC') or die();
/**
 *
 * @package    VirtueMart
 * @subpackage Plugins  - Elements
 * @author Valérie Isaksen
 * @link http://www.virtuemart.net
 * @copyright ${PHING.VM.COPYRIGHT}
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id$
 */
JFormHelper::loadFieldClass('list');
jimport('joomla.form.formfield');
if (!class_exists( 'VmConfig' )) 
    require(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
VmConfig::loadConfig();

class JFormFieldVmShopperGroups extends JFormFieldList {

	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	var $type = 'vmShopperGroups';

	protected function getOptions() {
		VmConfig::loadJLang('com_virtuemart_orders', TRUE);

		$options = array();
		$db = JFactory::getDBO();

		$query = 'SELECT `virtuemart_shoppergroup_id` AS value, `shopper_group_name` AS text
                 FROM `#__virtuemart_shoppergroups`
                 WHERE `virtuemart_vendor_id` = 1
                 ORDER BY `ordering` ASC, `virtuemart_shoppergroup_id` ASC ';

		$db->setQuery($query);
		$values = $db->loadObjectList();
		foreach ($values as $value) {
			$options[] = JHtml::_('select.option', $value->value, vmText::_($value->text));
		}

		return $options;
	}

}