<?php
/**
 * Created by: jdenoc
 * Created on: 2013-06-18
 * Modified on: 2013-07-13
 */

require_once('connection.php');
$db = new pdo_connection('jdenocco_rss');

if($_REQUEST['marked']==1){    // If already marked, then un-mark and vice versa
    $marked = 0;
    $marked_date = '0000-00-00 00:00:00';
} else {
    $marked = 1;
    $marked_date = date('Y-m-d H:i:s');
}
$article = $db->update(
    "feed_articles",
    array('marked'=>$marked, 'marked_date'=>$marked_date),
    'id=:id',
    array('id'=>$_REQUEST['article_id'])
);
if(!$article){        // No feeds available.
    print -1;
    exit;
} else {
    print $marked;
    exit;
}