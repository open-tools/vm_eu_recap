<?php
if( !defined( '_JEXEC' ) ) die('Restricted access');

/**
*
* @version $Id$
* @package VirtueMart
* @subpackage EU Recapitulative Statement
* Based in parts on VirtueMart's "Revenue Report"
* @copyright Copyright (C) VirtueMart Team - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.org
*/

if(!defined('VM_VERSION') or VM_VERSION < 3){
	// VM2 has class VmView instead of VmViewAdmin:
	if(!class_exists('VmView'))      require(VMPATH_ADMIN.DS.'helpers'.DS.'vmview.php');
	class VmViewAdmin extends VmView {}
	defined ('VMPATH_PLUGINLIBS') or define ('VMPATH_PLUGINLIBS', JPATH_VM_PLUGINS);
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
// 		$this->_addPath('models', JPATH_PLUGINS.DS . 'vmextended' . DS . 'eurecap' . DS . 'models' );
	}

	/**
	 * Render the view
	 */
	function display($tpl = null){

		if (!class_exists('VmHTML'))
			require(VMPATH_ADMIN . DS . 'helpers' . DS . 'html.php');

		$model		= VmModel::getModel();
		$this->addStandardDefaultViewLists($model);

		vRequest::setvar('task','');
		$this->SetViewTitle('EU_RECAP');

		$layoutName = vRequest::getCmd('layout', 'default');
		if ($layoutName == 'settings') {
			JToolBarHelper::divider();
			JToolBarHelper::save();	
			JToolBarHelper::apply();
			JToolBarHelper::cancel();
			if(!defined('VM_VERSION') or VM_VERSION < 3){
				// VM 2.x uses vmParameters
				$form = $this->renderPluginSettingsVM2();
				$this->assignRef('config_params', $form);
			} else {
				// VM 3.x uses JForm
				$form = $this->renderPluginSettingsVM3();
				$this->assignRef('config_form', $form);
			}
			// TODO
		} else {
			$this->setupListView($model);
		}

		parent::display($tpl);
	}

	/**
	 * Copied from VmView(Admin). Only change is the inserted $name in JText::_('COM_VIRTUEMART_'. $name . '_' . $task)
	 */
	function SetViewTitle($name ='', $msg ='',$icon ='') {

		$view = JRequest::getWord('view', JRequest::getWord('controller'));
		if ($name == '')
			$name = strtoupper($view);
		if ($icon == '')
			$icon = strtolower($view);
		if (!$task = JRequest::getWord('task'))
			$task = 'list';

		if (!empty($msg)) {
			$msg = ' <span style="color: #666666; font-size: large;">' . $msg . '</span>';
		}

		$viewText = JText::_('COM_VIRTUEMART_' . strtoupper($name));

		$taskName = ' <small><small>[ ' . JText::_('COM_VIRTUEMART_'. $name . '_' . $task) . ' ]</small></small>';

		JToolBarHelper::title($viewText . ' ' . $taskName . $msg, 'head vm_' . $icon . '_48');
		$this->assignRef('viewName',$viewText); //was $viewName?
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();
		$doc->setTitle($app->getCfg('sitename'). ' - ' .JText::_('JADMINISTRATION').' - '.strip_tags($msg));
	}

	function setupListView ($model) {
		$month = vRequest::getVar('month', '1');
		$year = vRequest::getVar('year', date("Y"));
		
		$settingsModel = VmModel::getModel("eurecap_config");
		$settings = $settingsModel->getConfig();

		$bar = JToolbar::getInstance('toolbar');
		JToolBarHelper::custom('settings', 'options', 'options','VMEXT_EU_RECAP_SETTINGS', false);
// 		JToolBarHelper::custom('check_eu_vatid', 'recheck', 'recheck', 'VMEXT_EU_RECAP_RECHECK_EUVATID', true);
// 		$bar->appendButton('Link', 'export', 'VMEXT_EU_RECAP_FULLEXPORT', 'index.php?option=com_virtuemart&view=eurecap&task=export&format=raw&layout=export_full&month='.$month.'&year='.$year);
		$bar->appendButton('Link', 'export', 'VMEXT_EU_RECAP_EXPORT_TB_' . $settings['export_format'], 'index.php?option=com_virtuemart&view=eurecap&task=export&format=raw&layout=export&month='.$month.'&year='.$year);

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

		$this->frequency = $settings['frequency'];
		$period_list = array();
		$period_list['month_list'] = $model->renderMonthSelectList($this->frequency, $month);
		$period_list['year_list'] = $model->renderYearSelectList($year);
		$this->assignRef('period_lists', $period_list);

		$this->assignRef('from_period', $model->from_date);
		$this->assignRef('until_period', $model->until_date);

		$this->assignRef('from', $model->from);
		$this->assignRef('until', $model->until);
		
		$this->include_taxed_orders = vRequest::getVar('include_taxed_orders', 0);
		
		$this->addStandardDefaultViewLists($model);
		$euIntracommunityRevenue = $model->getEuRecap();
		$this->assignRef('report', $euIntracommunityRevenue);

		$this->export_format_list = $model->renderExportFormatList($this->_path['template'], $settings['export_format']);
// 		$this->assignRef('export_format', $settings['export_format']);

		$pagination = $model->getPagination();
		$this->assignRef('pagination', $pagination);

	}

	function renderPluginSettingsVM3(){

		if (!class_exists('vmExtendedPlugin')) require(VMPATH_PLUGINLIBS . DS . 'vmextendedplugin.php');

		JForm::addFieldPath(VMPATH_ADMIN . DS . 'fields');

		$path = VMPATH_ROOT .DS. 'plugins' .DS. 'vmextended' . DS . $this->getName() . DS . $this->getName() . '.xml';
		if (file_exists($path)){

// 			$form = vmPlugin::loadConfigForm($path, $this->getName());
			$form = JForm::getInstance($this->getName().'-vmconfig', $path, array(), false, '//vmconfig | //config[not(//vmconfig)]');

			// load config
			$eurecapSettingsModel = VmModel::getModel("eurecap_config");
			$settings = $eurecapSettingsModel->getConfig();
			$form->bind(array('settings'=>$settings));
		} else {
			$form = false;
			vmdebug('renderPluginSettingsVM3: Unable to find xml for ' . $this->getName() . ' at ' . $path);
		}
		return $form;
	}

	function renderPluginSettingsVM2(){
		if (!class_exists('vmParameters'))
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'parameterparser.php');

		// load config
		$eurecapSettingsModel = VmModel::getModel("eurecap_config");
		$settings = $eurecapSettingsModel->getConfig();

		$parameters = new vmParameters($settings, 'eurecap', 'plugin', 'vmextended');
		return $parameters;
	}


}
