<?php
/**
 * Created by: jdenoc
 * Created on: 2013-7-07
 * Last Modified: 2013-7-09
 */

include_once('../php_scripts/Mobile_Detect.php');
$detect = new Mobile_Detect();
if(!$detect->isMobile()){
    header('Location: ../');
}

if(isset($_REQUEST['id'])){
    $feed_id = $_REQUEST['id'];
    require_once('../php_scripts/connection.php');
    $db = new pdo_connection('jdenocco_rss');
    $feed_name = $db->getValue("SELECT feed_title FROM subscriptions WHERE id=:feed_id", array('feed_id'=>$feed_id));
    $db->closeConnection();
} else {
    $feed_id = '';
    $feed_name = 'RSS Reader';
}

if(isset($_REQUEST['a'])){
    $article = $_REQUEST['a'];
} else {
    $article = '';
}


?>
<!DOCTYPE HTML>
<html>
<head>
    <title>RSS Reader</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../img/menu_icons/rss.png" rel="SHORTCUT ICON">
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script type="text/javascript" src="../js/mobile.js"></script>
    <script type="text/javascript" src="../js/scrollTo.js"></script>

    <!-- Bootstrap -->
    <link href="../css/bootstrap.css" rel="stylesheet"/>
    <script type="text/javascript" src="../js/bootstrap.js"></script>
    <script type="text/javascript" src="../js/bootstrap.min.js"></script>
    <!-- END - Bootstrap -->

    <link rel="stylesheet" href="../css/mobile.css" type="text/css" />
    <link rel="stylesheet" href="../css/bootstrap_custom.css" type="text/css" />

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>

<div class="navbar navbar-inverse">
    <div class="navbar-inner"><div class="container">
        <a href="<?php echo ($article != '' ? '?id='.$feed_id : '?'); ?>" class="brand"><?php echo $feed_name; ?></a>
        <span class="pull-right">
             <!--
                TODO - Add a change order (oldest first; newest first)
                -->
            <div class="btn-group dropdown">
                <button class="btn btn-mini" onclick="markFeedRead(activeFeed, 0)">Mark All Read</button>
                <button class="btn btn-mini dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">
                    <li><a tabindex="-1" href="#" onclick="markFeedRead(activeFeed, 1)">Older than a Day</a></li>
                    <li><a tabindex="-1" href="#" onclick="markFeedRead(activeFeed, 7)">Older than a Week</a></li>
                    <li><a tabindex="-1" href="#" onclick="markFeedRead(activeFeed, 14)">Older than 2 Weeks</a></li>
                    <li><a tabindex="-1" href="#" onclick="markFeedRead(activeFeed, 30)">Older than a Month</a></li>
                </ul>
            </div>
            <!-- Click this button, should refresh articles of current feed. -->
            <button class="btn btn-mini" onclick="loadRss('', activeFeed)"><i class="icon-refresh"></i></button>

        </span>
        </div></div>
</div>

<div id="content">
<input type="hidden" id="feed_id" value="<?php echo $feed_id; ?>"/>
<input type="hidden" id="article" value="<?php echo $article; ?>"/>
<!--Body content-->
    <ul id="feed_list">
        <!-- This is populated by functions from js/mobile.js -->
    </ul>
<!-- END - Body Content -->
</div>

</body>
</html>