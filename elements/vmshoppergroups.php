<?php
defined('_JEXEC') or die();
/**
*
* @package VirtueMart
* @subpackage EU Recapitulative Statement
* @copyright Copyright (C) 2015 Open Tools, Reinhold Kainhofer.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*
* http://www.open-tools.net
*/

if (!class_exists('JElementList')) require_once(JPATH_LIBRARIES.DS.'joomla'.DS.'html'.DS.'parameter'.DS.'element'.DS.'list.php');

class JELementVmShopperGroups extends JElementList {

	var $_name = 'vmShopperGroups';

	protected function _getOptions(&$node) {
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