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
    $feed_name = '<i class="icon-chevron-left icon-white"></i>'.$feed_name;
} else {
    $article = '';
}


?>
<!DOCTYPE HTML>
<html>
<head>
    <title>RSS Reader</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../img/favicon.ico" rel="SHORTCUT ICON"/>
    <link href="http://fonts.googleapis.com/css?family=Noto+Serif|Shanti|Goudy+Bookletter+1911" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script type="text/javascript" src="../js/taphold.js" ></script>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="../css/bootstrap.css" type="text/css" />
    <script type="text/javascript" src="../js/bootstrap.js"></script>
    <script type="text/javascript" src="../js/bootstrap.min.js"></script>
    <!-- END - Bootstrap -->

    <!-- Hook (Pull to Refresh) -->
    <link rel="stylesheet" href="../css/hook.css" type="text/css" />
    <script type="text/javascript" src="../js/hook.min.js"></script>
    <!-- END - Hook (Pull to Refresh) -->

    <link rel="stylesheet" href="../css/mobile.css" type="text/css" />
    <link rel="stylesheet" href="../css/bootstrap_custom.css" type="text/css" />
    <script type="text/javascript" src="../js/mobile.js"></script>
    <script type="text/javascript" src="../js/scrollTo.js"></script>

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>

<div id="hook" class="hook"></div>

<div class="navbar navbar-inverse">
    <div class="navbar-inner"><div class="container">
        <a href="<?php echo ($article != '' ? '?id='.$feed_id : '?'); ?>" class="brand"><?php echo $feed_name; ?></a>
    </div></div>
</div>

<div id="content">
<!--Body content-->
    <input type="hidden" id="feed_id" value="<?php echo $feed_id; ?>"/>
    <input type="hidden" id="article" value="<?php echo $article; ?>"/>
    <ul id="feed_list">
        <!-- This is populated by functions from js/mobile.js -->
    </ul>
<!-- END - Body Content -->
</div>

</body>
</html>