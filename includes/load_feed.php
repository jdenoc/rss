<?php
/**
 * Created by: denis
 * Created on: 2013-06-26
 */

require_once('connection.php');
$db = new pdo_connection('jdenocco_rss');

$limit = $_REQUEST['limit'];
if($_REQUEST['feed_id'] == 0){
    $articles = $db->getAllRows("SELECT * FROM feed_articles WHERE marked=1 ORDER BY marked_date DESC LIMIT $limit, 100", array('feed_id'=>$_REQUEST['feed_id']));
}else{
    $articles = $db->getAllRows("SELECT * FROM feed_articles WHERE feed_id=:feed_id AND viewed=0 ORDER BY stamp DESC LIMIT $limit, 100", array('feed_id'=>$_REQUEST['feed_id']));
}
if(!$articles){        // No feeds available.
    print 0;
    exit;
}

$text = '';
if(isset($_REQUEST['m'])){
    $a_ids = array();
    foreach($articles as $a){
        $marked = ($a['marked'] == 1) ? 'badge-warning' : '';
        $text .= '<li id="'.$a['id'].'" class="list_item label '.($a['viewed']==1 ? 'read' : '').'" >'."\r\n";
        $text .= '  <span class="badge '.$marked.'" onclick="markArticle('.$a['id'].')">&nbsp;</span>'."\r\n";
        $text .= '  <span onclick="window.location=\'?a='.$a['id'].'&id='.$_REQUEST['feed_id'].'\'">'.html_entity_decode($a['title'])."</span>\r\n";
        $text .= "</li>\r\n";
        $a_ids[] = $a['id'];
    }
    $text .= '<script type="text/javascript">var contextMenuIDs = ["'.implode('","', $a_ids).'"]</script>';

} else {
    // TODO - display feed name for marked articles
    foreach($articles as $a){
        $date = (date('Y-m-d') == date('Y-m-d', strtotime($a['stamp']))) ? date('H:i', strtotime($a['stamp'])) : date('Y-m-d', strtotime($a['stamp']));
        $marked = ($a['marked'] == 1) ? 'badge-warning' : '';
        $text .= '<li id="'.$a['id'].'" class="label '.($a['viewed']==1 ? 'read' : '').'">'."\r\n";
        $text .= '  <span class="badge '.$marked.'" onclick="markArticle('.$a['id'].')">&nbsp;</span>'."\r\n";
        $text .= '  <span  onclick="displayArticle('.$a['id'].')">'.html_entity_decode($a['title'])."</span>\r\n";
        $text .= '  <div class="article_stamp">'.$date.'</div>';
        $text .= "</li>\r\n";
    }
}

if(count($articles)>=100){
    $text .= "<li id=\"more_btn\" class=\"list_item label\">\r\n";
    $text .= '  <span onclick="displayMoreRss('.($limit+count($articles)+1).')">MORE</span>'."\r\n";
    $text .= "</li>\r\n";
}

print $text;
$db->closeConnection();
exit;