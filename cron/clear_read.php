<?php
/**
 * Created by: denis
 * Created on: 2013-06-12
 */

require_once('../php_scripts/connection.php');
$db = new pdo_connection('jdenocco_rss');

$db->delete("feed_articles", 'viewed=1 AND stamp < :today', array('today'=>date('Y-m-d', strtotime('-1 month'))));