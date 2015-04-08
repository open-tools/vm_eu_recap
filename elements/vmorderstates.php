<?php
defined ('_JEXEC') or die();
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

if (!class_exists('VmConfig'))  require(JPATH_VM_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
if(!class_exists('VmModel'))    require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmmodel.php');

class JElementVmOrderStates extends JElement {
	var $_name = 'OrderStates';

	function fetchElement ($name, $value, &$node, $control_name) {
		$statusModel = VmModel::getModel('OrderStatus');
		$fields = $statusModel->getOrderStatusNames();
		$class = 'class="inputbox" multiple="multiple" size="6" ';

		return JHTML::_ ('select.genericlist', $fields, $control_name . '[' . $name . '][]', $class, 'order_status_code', 'order_status_name', $value, $control_name . $name, true);
	}
}
