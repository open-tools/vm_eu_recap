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

$from_month=date("Y-m", $this->from);
$until_month=date("Y-m", $this->until);

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=\"EU_Recap_Statement_${from_month}_${until_month}.csv\"");

$keys = array_keys($this->report[0]);
if ($this->report) {
    echo '"' . join('","', $keys) . '"
';
}
foreach ($this->report as $r) {
    echo '"' . join('","', array_values($r)) . '"
';
}
