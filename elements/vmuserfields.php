<?php
defined('_JEXEC') or die();
/**
 *
 * @package	VirtueMart
 * @subpackage Plugins  - Userfields Form Field
 * @author Reinhold Kainhofer, Open Tools
 * @link http://www.open-tools.net
 * @copyright Copyright (c) 2015 Reinhold Kainhofer. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
 
if (!class_exists('JElementList')) require_once(JPATH_LIBRARIES.DS.'joomla'.DS.'html'.DS.'parameter'.DS.'element'.DS.'list.php');

class JELementVmUserFields extends JElementList {

    var $_name = 'vmUserFields';

    protected function _getOptions(&$node) {
        $model = VmModel::getModel('userfields');
        $userfields = $model->getUserfieldsList();
        $options = array();
        foreach ($userfields as $field) {
            if ($field->published) {
                $options[] = JHtml::_ ('select.option', $field->name, JText::_($field->title) . " (" . $field->name . ")");
            }
        }
        return $options;
    }

}