<?php
/**
 * Created by: denis
 * Created on: 2013-06-13
 */

require_once('connection.php');
$db = new pdo_connection('jdenocco_rss');
$where_array = array('id'=>$_REQUEST['feed_id']);

$active_feed = $db->getValue("SELECT feed_url FROM subscriptions WHERE id=:id", $where_array);
if(!$active_feed){        // Feed not available
    print 0;
    exit;
}

$db->delete('subscriptions', 'id=:id', $where_array);
$db->delete('feed_articles', 'marked=0 AND feed_id=:id', $where_array);

print 1;
$db->closeConnection();
exit;