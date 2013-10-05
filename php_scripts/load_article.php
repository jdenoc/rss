<?php
/**
 * Created by: jdenoc
 * Created on: 2013-06-27
 * Last Modified: 2013-07-11
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
    $text  = "<script type='text/javascript'>var viewed=".$article['viewed'].", marked=".$article['marked'].";</script>\r\n";
    $text .= "<li class=\"list_item article\">\r\n";
    $text .= '  <h3><a href="'.$article['link'].'" target="_blank">'.html_entity_decode($article['title']).'</a></h3>'."\r\n";
    $text .= '  '.html_entity_decode($article['content'], ENT_QUOTES)."\r\n";
    $text .= "  <div class=\"article_sub\">\r\n";
    $text .= '      <label style="display: inline; margin-right: 20px"><input type="checkbox" style="margin: -3px 0 0 8px;" onclick="markRead('.$article_id.', viewed)" '.$read.'/> Marked Read</label>';
    $text .= '      <label style="display: inline;"><span class="badge badge-warning"  style="margin-right:0">';
    $text .= '          <input type="checkbox" style="margin: 0 -3px 0 -2px;" onclick="markArticle('.$article_id.', marked)" '.$marked.'/>';
    $text .= '      </span> Mark for Later</label>'."\r\n";
    $text .= '      <div style="text-align: center; margin-top: 15px"><a onclick="$(\'body\').scrollTo(0)">Scroll to Top</a></div>';
    $text .= '      <strong style="text-align:center; margin-top: 10px; display:block;font-size:10px">'.$article['stamp'].'</strong>';
    // TODO - add sharing features.
    $text .= "  </div>\r\n";
    $text .= "</li>\r\n";

} else {
    $text  = "<div class=\"article\">\r\n";
    $text .= '  <h3><a href="'.$article['link'].'" target="_blank">'.html_entity_decode($article['title']).'</a></h3>'."\r\n";
    $text .= '  '.html_entity_decode($article['content'], ENT_QUOTES)."\r\n";
    $text .= "  <div class='article_sub'>\r\n";
    $text .= '      <label style="display: inline; margin-right: 20px"><input type="checkbox" style="margin: -3px 0 0 8px;" onclick="markRead('.$article_id.')" '.$read.'/> Marked Read</label>';
    $text .= '      <label style="display: inline;"><span class="badge badge-warning"  style="margin-right:0"><input type="checkbox" style="margin: 0 -3px 0 -2px;" onclick="markArticle('.$article_id.')" '.$marked.'/></span> Marked for Later</label>'."\r\n";
    $text .= '      <div id="share" class="pull-right btn-group dropup">';
    $text .= '      <a class="btn btn-inverse btn-mini dropdown-toggle" data-toggle="dropdown" href="#">';
    $text .= '          <span class="icon-share icon-white"></span>';
    $text .= '          <span class="caret"></span>';
    $text .= '      </a>';
    $text .= '      <ul class="dropdown-menu">';
    $text .= '          <li><button class="btn btn-inverse" onclick="window.open(\'mailto:\', \'_blank\', \'width=500, height=500\')"><span class="email icon-envelope icon-white"></span></button></li>';
    $text .= '          <li><button class="btn btn-primary btn-mini" onclick="alert(\'This doens\\\'t work yet\')"><span class="facebook">f</span></button></li>';              // TODO - setup facebook API
    $text .= '          <li><button class="btn btn-danger btn-mini" onclick="alert(\'This doens\\\'t work yet\')"><span class="google">g+</span></button></li>';                // TODO - setup google+ API
    $text .= '          <li><button class="btn btn-mini" onclick="alert(\'This doens\\\'t work yet\')"><span class="twitter">&nbsp;&nbsp;&nbsp;</span></button></li>';          // TODO - setup twitter+ API
    $text .= '          <li><button class="btn btn-info btn-mini" onclick="alert(\'This doens\\\'t work yet\')"><span class="tumblr">t</span></button></li>';                   // TODO - setup tumblr API
    $text .= '          <li><button class="btn btn-mini" onclick="alert(\'This doens\\\'t work yet\')"><span class="pinterest">&nbsp;&nbsp;&nbsp;</span></button></li>';        // TODO - setup pinterest API
    $text .= '      </ul>';
    $text .= '      </div><script type="text/javascript">$(\'.dropdown-toggle\').dropdown();</script> ';
    $text .= '  </div>';
    $text .= "</div>\r\n";

}

print $text;
$db->closeConnection();
exit;