<?php
/**
 * Created by: denis
 * Created on: 2013-06-13
 */

require_once('connection.php');
$db = new pdo_connection('jdenocco_rss');

if(isset($_REQUEST['marked'])){
    print $db->getValue("SELECT COUNT(id) FROM feed_articles WHERE marked=1");
    exit;
}

$feeds = $db->getAllRows("
    SELECT
      s.*,
      CONCAT( '(', (SELECT COUNT(fa.id) FROM feed_articles AS fa WHERE fa.feed_id=s.id AND fa.viewed=0), ')' ) AS unviewed
    FROM subscriptions AS s
    ORDER BY s.feed_title"
);
if(!$feeds){        // No feeds available.
    print 0;
    exit;
}

$text = '';
if(isset($_REQUEST['m'])){
    foreach($feeds as $f){
        $text .= '<li class="list_item" onclick="window.location=\'?id='.$f['id'].'\'">'."\r\n";
        $text .= '  <span title="'.$f['feed_url'].'" class="menu_link">'."\r\n";
        $text .= '      <img src="../'.check_feed_icon($f['feed_title']).'" alt="icon" class="menu_icon" />'.$f['feed_title'].' '.$f['unviewed']."\r\n";
        $text .= "  </span>\r\n";
        $text .= "</li>\r\n";
    }
} else {
    foreach($feeds as $f){
        $text .= '<li class="menu_feed_list_item ">'."\r\n";
        $text .= '  <span title="'.$f['feed_url'].'" class="menu_link" onclick="loadRss(\''.$f['feed_title'].'\', '.$f['id'].');">'."\r\n";
        $text .= '      <img src="'.check_feed_icon($f['feed_title']).'" alt="icon" class="menu_icon" />'.$f['feed_title'].' '.$f['unviewed']."\r\n";
        $text .= "  </span>\r\n";
        $text .= '  <span class="del_feed icon-trash icon-white" onclick="removeSubscription('.$f['id'].')" ></span>'."\r\n";
        $text .= "</li>\r\n";
    }
}

print $text;
$db->closeConnection();
exit;

function check_feed_icon($feed_title){
    $file = preg_replace('/\s+/', '_', $feed_title).'.png';
    if(file_exists(__DIR__.'/../img/menu_icons/'.$file)){
        return 'img/menu_icons/'.$file;
    } else {
        return 'img/menu_icons/rss.png';
    }
}