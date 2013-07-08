<?php
/**
 * User: denis
 * Date: 2013-06-04
 */
date_default_timezone_set("UTC");
require_once('connection.php');
$db = new pdo_connection('jdenocco_rss');

$feed_id = $_REQUEST['feed_id'];
$feed_info = $db->getRow("SELECT feed_url, feed_type FROM subscriptions WHERE id=:feed_id", array('feed_id'=>$feed_id));
$doc = new DOMDocument();
if(!$doc->load($feed_info['feed_url'])){
    echo $url;
    echo 0;     // Failed to load feed url
}

$node_name = '';
if($feed_info['feed_type']=='rss'){
    $node_name = 'item';
    $content_name = 'description';
}elseif($feed_info['feed_type']=='atom'){
    $node_name = 'entry';
    $content_name = 'summary';
}

// Processing RSS/Atom Feed
$insert_array = array();
foreach($doc->getElementsByTagName($node_name) as $node){
    $insert_array['title'] = trim($node->getElementsByTagName('title')->item(0)->nodeValue);
    if(!($db->getValue("SELECT id FROM feed_articles WHERE title LIKE :title", array('title'=>$insert_array['title'])))){
        $insert_array['link'] = trim($node->getElementsByTagName('link')->item(0)->nodeValue);
        $insert_array['content'] = trim($node->getElementsByTagName($content_name)->item(0)->nodeValue);
        $insert_array['stamp'] = date('Y-m-d H:i:s');   // Set Download stamp
        $insert_array['feed_id'] = $feed_id;

        $db->insert('feed_articles', $insert_array);
        $db->update('subscriptions', array('last_updated'=>date('Y-m-d H:i:s')), 'id=:id', array('id'=>$feed_id));
    }
}
echo 1;