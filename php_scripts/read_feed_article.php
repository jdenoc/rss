<?php
/**
 * Created by: denis
 * Created on: 2013-06-27
 */
require_once('connection.php');
$db = new pdo_connection('jdenocco_rss');

$viewed = ($_REQUEST['viewed']=='true') ? 0 : 1;    // If already viewed, then un-view and vice versa
$article = $db->update("feed_articles", array('viewed'=>$viewed), 'id=:id', array('id'=>$_REQUEST['article_id']));
if(!$article){        // No feeds available.
    print -1;
} else {
    print $viewed;
}
exit;