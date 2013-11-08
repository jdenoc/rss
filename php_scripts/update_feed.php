<?php
/**
 * User: jdenoc
 * Date: 2013-06-04
 * Last modified: 2013-07-09
 */
date_default_timezone_set("UTC");
require_once('connection.php');
$db = new pdo_connection('jdenocco_rss', true);

$feed_id = $_REQUEST['feed_id'];
$feed_info = $db->getRow("SELECT feed_url, feed_type FROM subscriptions WHERE id=:feed_id", array('feed_id'=>$feed_id));
$doc = new DOMDocument();
if(!$doc->load($feed_info['feed_url'])){
    echo $url;
    echo 0;     // Failed to load feed url
}

$feed_attr = array();
if($feed_info['feed_type']=='rss'){
    $feed_attr['node'] = 'item';
    $feed_attr['content'] = array('description', 'content');
    $feed_attr['uid'] = 'guid';
}elseif($feed_info['feed_type']=='atom'){
    $feed_attr['node'] = 'entry';
    $feed_attr['content'] = array('summary', 'content');
    $feed_attr['uid'] = 'id';
}

// Processing RSS/Atom Feed
$feed_updated = false;
foreach($doc->getElementsByTagName($feed_attr['node']) as $node){
    $node_value_array = array();
    $node_value_array['title'] = htmlentities(trim($node->getElementsByTagName('title')->item(0)->nodeValue), ENT_QUOTES);
    $node_value_array['link'] = trim($node->getElementsByTagName('link')->item(0)->nodeValue);
    $node_value_array['stamp'] = date('Y-m-d H:i:s');   // Set Download stamp
    $node_value_array['feed_id'] = $feed_id;
    $content_index = 0;
    if(is_null($node->getElementsByTagName( $feed_attr['content'][$content_index] )->item(0))){
        $content_index = 1;
    }
    $content = $node->getElementsByTagName( $feed_attr['content'][$content_index] )->item(0)->nodeValue;
    $content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);
    $node_value_array['content'] = htmlentities(trim($content), ENT_QUOTES);
    $unique_id_node = $node->getElementsByTagName($feed_attr['uid'])->item(0);

    if(!is_null($unique_id_node)){
        if($db->getValue("SELECT id FROM feed_articles WHERE guid = :guid", array('guid'=>$unique_id_node->nodeValue))){
            $db->update('feed_articles', $node_value_array, 'guid=:guid', array('guid'=>$unique_id_node->nodeValue));
        } else {
            $node_value_array['guid'] = $unique_id_node->nodeValue;
            $db->insert('feed_articles', $node_value_array);
        }
        $feed_updated = true;

    }elseif(!($db->getValue("SELECT id FROM feed_articles WHERE title LIKE :title", array('title'=>$node_value_array['title'])))){
        $node_value_array['guid'] = '';
        $db->insert('feed_articles', $node_value_array);
        $feed_updated = true;
    }
}

if($feed_updated)
    $db->update('subscriptions', array('last_updated'=>date('Y-m-d H:i:s')), 'id=:id', array('id'=>$feed_id));

echo 1;