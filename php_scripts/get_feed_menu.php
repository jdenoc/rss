<?php
/**
 * Created by: denis
 * Created on: 2013-06-13
 * Modified on: 2013-06-26
 */

require_once('connection.php');
$db = new pdo_connection('jdenocco_rss');

$feeds = $db->getAllRows("SELECT
      s.*,
      CONCAT( '(', (SELECT COUNT(id) FROM feed_articles WHERE feed_id=s.id AND viewed=0), ')' ) AS unviewed
    FROM subscriptions AS s
    ORDER BY s.feed_title"
);
if(!$feeds){        // No feeds available.
    print 0;
    exit;
}

$text = '';
foreach($feeds as $f){
    $text .= '<li class="menu_feed_list_item ">'."\r\n";
    $text .= '  <span title="'.$f['feed_url'].'" class="menu_link" onclick="loadRss(\''.$f['feed_title'].'\', '.$f['id'].');">'."\r\n";
    $text .= '      <img src="'.check_feed_icon($f['feed_title']).'" alt="icon" class="menu_icon" />'.$f['feed_title'].' '.$f['unviewed']."\r\n";
    $text .= "  </span>\r\n";
    $text .= '  <span class="del_feed icon-trash icon-white" onclick="removeSubscription('.$f['id'].')" ></span>'."\r\n";
    $text .= "</li>\r\n";
}

print $text;
$db->closeConnection();
exit;

function check_feed_icon($feed_title){
    if(file_exists($_SERVER['DOCUMENT_ROOT'].'img/menu_icons/'.$feed_title.'.ico')){
        return 'img/menu_icons'.$feed_title.'.png';
    }else{
        return 'img/menu_icons/rss.png';
    }
}