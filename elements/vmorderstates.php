<?php
defined ('_JEXEC') or die();
/**
 *
 * @package    VirtueMart
 * @subpackage Plugins  - Elements
 * @author Valérie Isaksen
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2011 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id:$
 */
if (!class_exists('VmConfig'))  require(JPATH_VM_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
if(!class_exists('VmModel'))    require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmmodel.php');

/*
 * This class is used by VirtueMart Payment or Shipment Plugins
 * which uses JParameter
 * So It should be an extension of JElement
 * Those plugins cannot be configured througth the Plugin Manager anyway.
 */
class JElementVmOrderStates extends JElement {

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'OrderStates';

	function fetchElement ($name, $value, &$node, $control_name) {
		$statusModel = VmModel::getModel('OrderStatus');
		$fields = $statusModel->getOrderStatusNames();
		$class = 'class="inputbox" multiple="multiple" size="6" ';

		return JHTML::_ ('select.genericlist', $fields, $control_name . '[' . $name . '][]', $class, 'order_status_code', 'order_status_name', $value, $control_name . $name, true);
	}
}
