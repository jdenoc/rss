<?php
/**
 * Created by: denis
 * Created on: 2013-06-12
 *
 * Creates a new table to store the feed data in.
 * Inserts this new table name & associated URL into subscriptions table.
 */

require_once('connection.php');
$db = new pdo_connection('jdenocco_rss');

if(!isset($_REQUEST['url']) || $_REQUEST['url']==''){
    echo -1;    // URL not passed
    exit;
}
$url = trim($_REQUEST['url']);

if(isset($_REQUEST['json'])){
    $subscription = $db->getRow("SELECT id, feed_title FROM subscriptions WHERE feed_url LIKE :url", array('url'=>$url));
    echo json_encode($subscription);
    exit;
}

// Check if feeds is valid
$feed_response = validate_feed($url);
if(!$feed_response['valid']){
    echo 0;     // Not a valid feed.
    exit;
}

// Check if feed already exists
if($db->getValue("SELECT id FROM subscriptions WHERE feed_url LIKE :url", array('url'=>$url))){
    echo 2;     // Already exists
    exit;
}
//
$doc = new DOMDocument();
$doc->load($url);
$title = $doc->getElementsByTagName('title')->item(0)->nodeValue;
$db->insert('subscriptions', array('feed_url'=>$url, 'feed_title'=>$title, 'feed_type'=>$feed_response['type']));
get_feed_icon($url, $title);

$db->closeConnection();
echo 1;         // Success
exit;

// ***** FUNCTIONS ***** //
function validate_feed($feed_url){
    // TODO - consider making this work with atom feeds as well as rss feeds
    $validator = 'http://feedvalidator.org/check.cgi?url=';
    $response = array();
    if( $validation_response = @file_get_contents($validator.urlencode($feed_url)) ) {
        if( stristr( $validation_response , 'This is a valid RSS feed' ) !== false ) {
            $response['valid'] = true;
            $response['type'] = 'rss';
        } elseif(stristr( $validation_response , 'This is a valid Atom' ) !== false) {
            $response['valid'] = true;
            $response['type'] = 'atom';
        } else {
            $response['valid'] = false;
            $response['type'] = '';
        }
    } else {
        $response['valid'] = false;
        $response['type'] = '';
    }
    return $response;
}


function get_feed_icon($feed_url, $feed_name){
    // TODO - get this to work
    $favicon_url = 'http://www.google.com/s2/favicons?domain='.$feed_url;
    $img_path = $_SERVER['DOCUMENT_ROOT'].'/img/main_icons/';
    $file = @fopen ($favicon_url, "rb");
    if ($file) {
        $newf = @fopen ($img_path, "wb");

        if ($newf){
            while(!feof($file)) {
                @fwrite($newf, fread($file, 1024 * 8 ), 1024 * 8 );
            }
            @fclose($newf);
        }
        @fclose($file);
    }
}