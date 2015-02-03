<?php
if( !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

/**
 * Abstract class for extended plugins
 * This class provides some standard methods that can implemented to add features into the VM core
 * Be sure to include this line in the plugin file:
 * require(VMPATH_ADMIN.DS.'helpers'.DS.'vmextendedplugin.php');
 *
 * @package	VirtueMart
 * @subpackage Plugins
 * @author Christopher Roussel
 */
if (!class_exists('vmExtendedPlugin')) require(VMPATH_PLUGINLIBS . DS . 'vmextendedplugin.php');

class plgVmExtendedEuRecap extends vmExtendedPlugin {

	public function __construct (&$subject, $config=array()) {
		parent::__construct($subject, $config);
		$this->_path = JPATH_PLUGINS.DS.'vmextended'.DS.$this->getName();
		JPlugin::loadLanguage('plg_vmextended_'.$this->getName());
	}

//     public function getVmPluginCreateTableSQL () {
//     }

    /* In versions before VM 2.6.8, the onStoreInstallPluginTable function was protected, so the installer couldn't call it to create the plugin table...
       This function simply is a public wrapper to make this function available to the installer on all VM versions: */
    public function plgVmOnStoreInstallPluginTable($psType, $name='') {
        return $this->onStoreInstallPluginTable($psType, $name);
    }

	/**
	 * Plugs into the backend controller logic to insert a custom controller into the VM component space
	 * This means that links can be constructed as index.php?option=com_virtuemart&view=myaddon and work
	 *
	 * @param string $controller Name of controller requested
	 * @return True if this loads a file (null otherwise)
	 */
	public function onVmAdminController ($controller) {
		if ($controller = 'eurecap') {
			VmModel::addIncludePath($this->_path . DS . 'models');

			// TODO: Make sure the model exists. We probably should find a better way to load this automatically! 
			//       Currently, some path config seems missing, so the model is not found by default.
			require_once($this->_path.DS.'models'.DS.'eurecap.php');
			require_once($this->_path.DS.'models'.DS.'eurecap_config.php');
			require_once($this->_path.DS.'controllers'.DS.'eurecap.php');
			return true;
		}
	}
	
	/**
	 * The onVmAdminMenuItems($moduleId) trigger is supposed to return an array of admin menu entries, each of which has the following structure:
	 * array('module_id'=>1, 'module_name'=>'product', 'module_perms'=>'storeadmin,admin', 
	 *       'id'=>..., 'name'=>'COM_VIRTUEMART_PRODUCT_S', 'link'=>'', 'depends'=>'', 'icon_class'=>'vmicon vmicon-16-camera',
	 *       'view'=>'YOURVIEW', 'task'=>'', 'module_ordering'=>1, 'item_ordering'=>3)
	*/
	public function onVmAdminMenuItems($moduleId=0) {
		return array(
			array('module_id'=>14, 
				  'module_name'=>'report', 
				  'module_perms'=>'storeadmin,admin', 
				  'id'=>100877, 
				  'name'=>'COM_VIRTUEMART_EU_RECAP', 
				  'link'=>'', 
				  'depends'=>'', 
				  'icon_class'=>'vmicon vmicon-16-report',
				  'view'=>'eurecap', 
				  'task'=>'', 
				  'module_ordering'=>4, 
				  'item_ordering'=>25,
			),
		);
	}

}