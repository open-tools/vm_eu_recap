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
		if (!class_exists('CurrencyDisplay'))
			require(VMPATH_ADMIN . DS . 'helpers' . DS . 'currencydisplay.php');

		$model		= VmModel::getModel();
		$this->addStandardDefaultViewLists($model);

		vRequest::setvar('task','');
		$this->SetViewTitle('EU_RECAP');

		$myCurrencyDisplay = CurrencyDisplay::getInstance();

		$layoutName = vRequest::getCmd('layout', 'default');
		if ($layoutName == 'settings') {
			JToolBarHelper::divider();
			JToolBarHelper::save();	
			JToolBarHelper::apply();
			JToolBarHelper::cancel();
			$form = $this->renderPluginSettings();
			$this->assignRef('config_form', $form);
			// TODO
		} else {
			$this->setupListView($model);
		}

		parent::display($tpl);
	}

	function setupListView ($model) {
		JToolBarHelper::custom('settings', 'options', 'options','VMEXT_EU_RECAP_SETTINGS', false);
		JToolBarHelper::custom('check_eu_vatid', 'recheck', 'recheck', 'VMEXT_EU_RECAP_RECHECK_EUVATID', true);
		JToolBarHelper::custom('export', 'export', 'export', 'VMEXT_EU_RECAP_EXPORT', false);

		$month = vRequest::getVar('month', '1');
		$year = vRequest::getVar('year', date("Y"));

		$settingsModel = VmModel::getModel("eurecap_config");
		$settings = $settingsModel->getConfig();
		$this->frequency = $settings['frequency'];
		$this->lists['month_list'] = $model->renderMonthSelectList($this->frequency, $month);
		$this->lists['year_list'] = $model->renderYearSelectList($year);

		$this->assignRef('from_period', $model->from_date);
		$this->assignRef('until_period', $model->until_date);

		$myCurrencyDisplay = CurrencyDisplay::getInstance();
		
		$this->addStandardDefaultViewLists($model);
		$euIntracommunityRevenue = $model->getEuRecap();
		foreach ($euIntracommunityRevenue as &$r) {
			$r['sum_order_total'] = $myCurrencyDisplay->priceDisplay($r['sum_order_total']);
			$r['sum_order_tax'] = $myCurrencyDisplay->priceDisplay($r['sum_order_tax']);
		}
		$this->assignRef('report', $euIntracommunityRevenue);

		$pagination = $model->getPagination();
		$this->assignRef('pagination', $pagination);
	
	}

	function renderPluginSettings(){

		if (!class_exists('vmExtendedPlugin')) require(VMPATH_PLUGINLIBS . DS . 'vmextendedplugin.php');

		JForm::addFieldPath(VMPATH_ADMIN . DS . 'fields');

		$path = VMPATH_ROOT .DS. 'plugins' .DS. 'vmextended' . DS . $this->getName() . DS . $this->getName() . '.xml';
		// Get the payment XML.
		$formFile	= vRequest::filterPath( $path );
		if (file_exists($formFile)){

			$form = vmPlugin::loadConfigForm($formFile, $this->getName());

			// load config
			$eurecapSettingsModel = VmModel::getModel("eurecap_config");
			$settings = $eurecapSettingsModel->getConfig();
			$form->bind(array('settings'=>$settings));
		} else {
			$form = false;
			vmdebug('renderUserfieldPlugin could not find xml for ' . $this->getName() . ' at ' . $path);
		}
		//vmdebug('renderUserfieldPlugin ',$this->userField->form);
		return $form;
	}





}
