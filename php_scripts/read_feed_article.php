<?php
/**
 * Created by: jdenoc
 * Created on: 2013-06-27
 * Last Modified: 2013-07-07
 */
require_once('connection.php');
$db = new pdo_connection('jdenocco_rss');

$viewed = ($_REQUEST['viewed']==1) ? 0 : 1;    // If already viewed, then un-view and vice-versa
$article = $db->update("feed_articles", array('viewed'=>$viewed), 'id=:id', array('id'=>$_REQUEST['article_id']));
if(!$article){        // No feeds available.
    print -1;
} else {
    print $viewed;
}
exit;