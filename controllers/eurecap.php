<?php
if( !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

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

defined ('VMPATH_ADMIN') or define ('VMPATH_ADMIN', JPATH_VM_ADMINISTRATOR);
if(!class_exists('VmController')) require(VMPATH_ADMIN.DS.'helpers'.DS.'vmcontroller.php');


/**
 * Report Controller
 *
 * @package	VirtueMart
 * @subpackage Report
 * @author Open Tools, Reinhold Kainhofer
 */
class VirtuemartControllerEuRecap extends VmController {

	/**
	 * Report Controller Constructor
	 */
	function __construct(){
		parent::__construct();
		// Add the proper view pathes...
		$this->addViewPath(JPATH_PLUGINS.DS . 'vmextended' . DS . 'eurecap' . DS . 'views');
	}

	public function cancel(){
		$msg = vmText::_('VMEXT_EU_RECAP_CANCELLED');
		$this->setRedirect($this->redirectPath, $msg, 'message');
	}

	function settings($layout='settings'){

		vRequest::setVar('controller', $this->_cname);
		vRequest::setVar('view', $this->_cname);
		vRequest::setVar('layout', $layout);

		$document = JFactory::getDocument();
		$viewType = $document->getType();
		$view = $this->getView($this->_cname, $viewType);

		$view->setLayout($layout);

		$this->display();
	}

	function display($cachable = false, $urlparams = false) {
		if (vRequest::getVar('format')=='raw') {
			// Raw forman means "Export"!!!
			// Ideally, we would use an export task, but unfortunately
			// we cannot both set format=raw and task=export in the export
			// button. format=raw MUST be set, because otherwise a HTML
			// document is initialized by default!
			$layout = 'export';

			$viewName = $this->_cname;
			vRequest::setVar('controller', $viewName);
			vRequest::setVar('view', $viewName);
			vRequest::setVar('layout', $layout); // Set the layout to export!

			$document = JFactory::getDocument();
			$viewType = $document->getType();
			$view = $this->getView($viewName, $viewType);
			$view->writeJs = false;
			$view->setLayout($layout);
		}

		return parent::display($cachable, $urlparams);
	}

	/**
	 * Handle the save task
	 */
	function save($data = 0){
		vRequest::vmCheckToken();
		$data = vRequest::getPost();
		$model = VmModel::getModel('eurecap_config');
		$model->saveConfig($data);

		$msg = vmText::_('COM_VIRTUEMART_CONFIG_SAVED');
		$redir = $this->redirectPath;
		if(vRequest::getCmd('task') == 'apply'){
			$redir = $redir . '&task=settings';
		}
		$this->setRedirect($redir, $msg);

	}
}
// pure php no closing tag