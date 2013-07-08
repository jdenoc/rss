<?php
/**
 * Created by: denis
 * Created on: 2013-06-16
 *
 * This cron script will run once every hour.
 * It will update all the feeds.
 * If an error occurs while updating any of the feeds, the script will terminate, notify admin and run again the following hour
 */

require_once('../php_scripts/connection.php');
$db = new pdo_connection('jdenocco_rss');

$feeds = $db->getAllValues("SELECT id FROM subscriptions");
foreach($feeds as $feed_id){
    $response = file_get_contents($_SERVER['HTTP_HOST'].'php_scripts/update_feed.php?feed_id='.$feed_id);
    if($response != 1){
        mail('rss@jdenoc.com', 'CRON ERROR '.date('Y-m-d H:i:s e').' | '.$_SERVER['SCRIPT_FILENAME'], $response);
        exit;
    }
}