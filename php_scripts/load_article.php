<?php
/**
 * Created by: denis
 * Created on: 2013-06-27
 */

require_once('connection.php');
$db = new pdo_connection('jdenocco_rss');

$article_id = $_REQUEST['article_id'];
$article = $db->getRow("SELECT * FROM feed_articles WHERE id=:article_id LIMIT 1", array('article_id'=>$article_id));
if(!$article){        // No article available.
    print 0;
    exit;
}


$marked = ($article['marked'] == 1) ? 'checked' : '';
$read = ($article['viewed'] == 1) ? 'checked' : '';
if(isset($_REQUEST['m'])){
    $text  = "<li class=\"list_item article\">\r\n";
    $text .= '  <h3><a href="'.$article['link'].'" target="_blank">'.$article['title'].'</a></h3>'."\r\n";
    $text .= '  '.html_entity_decode($article['content'])."\r\n";
    $text .= "  <div style=\"padding: 5px; border-top: 1px solid #bbb;\">\r\n";
    $text .= '  <label style="display: inline; margin-right: 20px"><input type="checkbox" style="margin: -3px 0 0 8px;" onclick="markRead('.$article_id.')" '.$read.'/> Mark as Read</label>';
    $text .= '  <label style="display: inline;"><span class="badge badge-warning"  style="margin-right:0"><input type="checkbox" style="margin: 0 -3px 0 -2px;" onclick="markArticle('.$article_id.')" '.$marked.'/></span> Marked</label>'."\r\n";
    // TODO - add sharing features.
    // TODO - add return to top link
    $text .= "</li>\r\n";

} else {
    $text  = "<div class=\"article\">\r\n";
    $text .= '  <h3><a href="'.$article['link'].'" target="_blank">'.$article['title'].'</a></h3>'."\r\n";
    $text .= '  '.html_entity_decode($article['content'])."\r\n";
    $text .= "  <div style=\"padding: 5px; border-top: 1px solid #bbb;\">\r\n";
    $text .= '  <label style="display: inline; margin-right: 20px"><input type="checkbox" style="margin: -3px 0 0 8px;" onclick="markRead('.$article_id.')" '.$read.'/> Mark as Read</label>';
    $text .= '  <label style="display: inline;"><span class="badge badge-warning"  style="margin-right:0"><input type="checkbox" style="margin: 0 -3px 0 -2px;" onclick="markArticle('.$article_id.')" '.$marked.'/></span> Marked</label>'."\r\n";
// TODO - add sharing features.
    $text .= "</div>\r\n";

}

print $text;
$db->closeConnection();
exit;

