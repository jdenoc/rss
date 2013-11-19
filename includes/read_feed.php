<?php
/**
 * Created by: jdenoc
 * Created on: 2013-06-30
 * Last Modified" 2013-07-09
 */

require_once('connection.php');
$db = new pdo_connection('jdenocco_rss');

$period = $_REQUEST['period'];
$feed_id = $_REQUEST['feed_id'];

$where_array = array();
$where_string = '';
if($feed_id == 0){
    $where_string .= 'marked=:marked';
    $where_array['marked'] = 1;
} else {
    $where_string .= 'feed_id=:feed_id';
    $where_array['feed_id'] = $feed_id;
}
if($period != 0){
    $where_string .= ' AND stamp<=:stamp';
    $where_array['stamp'] = date('Y-m-d H:i:s', strtotime('-'.$period.' days'));
}

$read = $db->update(
    'feed_articles',
    array('viewed'=>1),
    $where_string,
    $where_array
);
if($read){
    print 1;
} else {
    print 0;
}
$db->closeConnection();
exit;