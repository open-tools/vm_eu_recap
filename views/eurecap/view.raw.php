<?php
if( !defined( '_JEXEC' ) ) die('Restricted access');

/**
*
* @version $Id$
* @package VirtueMart
* @subpackage EU Recapitulative Statement
* @copyright Copyright (C) Open Tools, Reinhold Kainhofer
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

if(!defined('VM_VERSION') or VM_VERSION < 3){
	// VM2 has class VmView instead of VmViewAdmin:
	if(!class_exists('VmView'))      require(VMPATH_ADMIN.DS.'helpers'.DS.'vmview.php');
	class VmViewAdmin extends VmView {}
} else {
	if(!class_exists('VmViewAdmin')) require(VMPATH_ADMIN.DS.'helpers'.DS.'vmviewadmin.php');
}

/**
 * EU Recapitulative Statement View class
 *
 * @package	VirtueMart
 * @subpackage Report
 * @author Open Tools
 */
class VirtuemartViewEuRecap extends VmViewAdmin {
	function __construct(){
		parent::__construct();
		// Add the proper view pathes...
		$this->_addPath('template', JPATH_PLUGINS.DS . 'vmextended' . DS . 'eurecap' . DS . 'views' . DS . $this->getName() . DS  . 'tmpl');
	}

	/**
	 * Render the view
	 */
	function display($tpl = null){
		$model		= VmModel::getModel();

// 		$layoutName = vRequest::getCmd('layout', 'export');

		$month = vRequest::getVar('month', '1');
		$year = vRequest::getVar('year', date("Y"));

		$settingsModel = VmModel::getModel("eurecap_config");
		$settings = $settingsModel->getConfig();
		
		$this->frequency = $settings['frequency'];

		$this->assignRef('from_period', $model->from_date);
		$this->assignRef('until_period', $model->until_date);

		$this->assignRef('from', $model->from);
		$this->assignRef('until', $model->until);

		$euIntracommunityRevenue = $model->getEuRecap();
		$this->assignRef('report', $euIntracommunityRevenue);
		
		$this->assignRef('settings', $settings);

		$user = JFactory::getUser();
		if($user->authorise('core.admin', 'com_virtuemart') or $user->authorise('core.manager', 'com_virtuemart')){
			$vendorId = vRequest::getInt('virtuemart_vendor_id');
		} else {
			$vendorId = VmConfig::isSuperVendor();
		}
		$vendorModel = VmModel::getModel('vendor');
		$vendor = $vendorModel->getVendor($vendorId);
		$vendor->vendorFields = $vendorModel->getVendorAddressFields($vendorId);
		$this->assignRef('vendor', $vendor);

		$oldformat = isset($settings['export_format'])?$settings['export_format']:'full';
		$settings['export_format'] = vRequest::getVar('export_format', $oldformat);
		if ($oldformat != $settings['export_format'])
			$settingsModel->saveConfig(array('settings'=>$settings));
		
		$this->assignRef('export_format', $settings['export_format']);
		parent::display($tpl);
	}




}
