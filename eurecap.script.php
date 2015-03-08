<?php
defined('_JEXEC') or die('Restricted access');
/**
 * Installation script for the plugin
 *
 * @copyright Copyright (C) 2015 Reinhold Kainhofer, office@open-tools.net
 * @license GPL v3+,  http://www.gnu.org/copyleft/gpl.html
 */
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
if (!class_exists( 'VmConfig' )) 
    require(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');

class plgVmExtendedEuRecapInstallerScript
{
    /**
     * Constructor
     *
     * @param   JAdapterInstance  $adapter  The object responsible for running this script
     */
//     public function __constructor(JAdapterInstance $adapter);
 
    /**
     * Called before any type of action
     *
     * @param   string  $route  Which action is happening (install|uninstall|discover_install)
     * @param   JAdapterInstance  $adapter  The object responsible for running this script
     *
     * @return  boolean  True on success
     */
//     public function preflight($route, JAdapterInstance $adapter);
 
    /**
     * Called after any type of action
     *
     * @param   string  $route  Which action is happening (install|update|uninstall|discover_install)
     * @param   JAdapterInstance  $adapter  The object responsible for running this script
     *
     * @return  boolean  True on success
     */
    public function postflight ($type, $parent = null) {
        if(!class_exists( 'plgVmExtendedEuRecap' )) 
            require JPATH_ROOT.DS.'plugins'.DS.'vmextended'.DS.'eurecap'.DS.'eurecap.php';
        $dispatcher = new JDispatcher();
        $config = array('name' => 'eurecap', 'type' => 'vmextended');
        $plugin = new plgVmExtendedEuRecap($dispatcher, $config);
        $plugin->onInstallCheckAdminMenuEntries();
//         $plugin->plgVmOnStoreInstallPluginTable('extended');
// //         $dispatcher->trigger("plgVmOnStoreInstallPluginTable", array('vmshopper'));
    }
 
    /**
     * Called on installation
     *
     * @param   JAdapterInstance  $adapter  The object responsible for running this script
     *
     * @return  boolean  True on success
     */
    public function install(JAdapterInstance $adapter)
    {
        // enabling plugin
        $db = JFactory::getDBO();
        $db->setQuery('update #__extensions set enabled = 1 where type = "plugin" and element = "eurecap" and folder = "vmextended"');
        $db->query();
        
        return True;
    }
 
    /**
     * Called on update
     *
     * @param   JAdapterInstance  $adapter  The object responsible for running this script
     *
     * @return  boolean  True on success
     */
    public function update(JAdapterInstance $adapter)
    {
//         jimport( 'joomla.filesystem.file' ); 
//         $file = JPATH_ROOT . DS . "administrator" . DS . "language" . DS . "en-GB" . DS . "en-GB.plg_vmshopper_ordernumber.sys.ini";
//         if (JFile::exists($file)) JFile::delete($file); 
//         $file = JPATH_ROOT . DS . "administrator" . DS . "language" . DS . "de-DE" . DS . "de-DE.plg_vmshopper_ordernumber.sys.ini"; 
//         if (JFile::exists($file)) JFile::delete($file); 
        return true;
    }
 
    /**
     * Called on uninstallation
     *
     * @param   JAdapterInstance  $adapter  The object responsible for running this script
     */
    public function uninstall(JAdapterInstance $adapter)
    {
		$db = JFactory::getDBO();
		$q = "DELETE FROM `#__virtuemart_adminmenuentries` WHERE `view` = 'eurecap' AND `task` = '' AND `module_id` = 2";
		$db->setQuery($q);
		$db->query();
//         // Remove plugin table
//         $db =& JFactory::getDBO();
//         $db->setQuery('DROP TABLE IF EXISTS `#__virtuemart_shopper_plg_ordernumber`;');
//         $db->query();
    }
}