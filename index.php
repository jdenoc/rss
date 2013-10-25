<?php
/**
 * Created by: jdenoc
 * Created on: 2013-6-04
 * Last Modified: 2013-7-09
 */

include_once('php_scripts/Mobile_Detect.php');
$detect = new Mobile_Detect();
if($detect->isMobile()){
    header('Location: mobile/');
}

?>
<!DOCTYPE HTML>
<html>
<head>
    <title>RSS Reader</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link href="img/favicon.ico" rel="SHORTCUT ICON"/>
    <link href='http://fonts.googleapis.com/css?family=Noto+Serif|Shanti|Goudy+Bookletter+1911' rel='stylesheet' type='text/css'/>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/main_screen.js"></script>
    <script type="text/javascript" src="js/menu_col.js"></script>
    <script type="text/javascript" src="js/scrollTo.js"></script>

    <!-- Bootstrap -->
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <!-- END - Bootstrap -->

    <link rel="stylesheet" href="css/base.css" type="text/css" />
    <link rel="stylesheet" href="css/bootstrap_custom.css" type="text/css" />

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>

<!-- Nav Bar (top of page) -->
<div class="navbar navbar-inverse">
    <div class="navbar-inner"><div class="container">
        <span class="brand">RSS Reader</span>
        <span class="pull-right" style="display: none">
            <!-- Sorting -->
            <div class="btn-group dropdown">
                <button class="btn btn-mini" onclick="alert('TODO - Not yet working...')">Sort: </button>
                <button class="btn btn-mini dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">
                    <!--  TODO - finish  -->
                    <li><a tabindex="-1" href="#" onclick="alert('TODO - Not yet working...')">Newest</a></li>
                    <li><a tabindex="-1" href="#" onclick="alert('TODO - Not yet working...')">Oldest</a></li>
                </ul>
            </div>
            <!-- END - Sorting -->
            <!-- Marking as Read -->
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
            <!-- END - Marking as Read -->
            <!-- Click this button, should refresh articles of current feed. -->
            <button class="btn btn-mini" onclick="loadRss('', activeFeed)">Refresh <i class="icon-refresh"></i></button>

        </span>
    </div></div>
</div>
<!-- END - Nav Bar -->

<div id="sidebar">
    <!-- Sidebar content -->
    <ul id="menu_feeds_list">
        <li>
            <button class="btn btn-inverse" onclick="loadMenuCol()">Refresh Menu <i class="icon-refresh icon-white"></i></button>
            <!-- Clicking on this button should Reload the items in the feeds menu. -->
        </li>
        <li>
            <button class="btn btn-inverse" onclick="showSubscribeToFeed()">Subscribe <i class="icon-plus icon-white"></i></button>   <!-- Clicking on this button should show you the new feed dialog. -->
            <div id="subscribe_form">
                <label>URL: <input type="text" id="feed_url"></label>
                <span id="create_subscription_notice">&nbsp;</span>             <!-- Warning message displayed if error. -->
                <button onclick="subscribeToFeed()">Add Feed</button>           <!-- Clicking on this button should run a jQuery call to call the create_subscription.php script. -->
                <button onclick="cancelSubscribeToFeed()">Cancel</button>       <!-- Clicking on this button should hide the new feed dialog. -->
            </div>
        </li>
        <li>&nbsp;</li>
        <li><span title="Marked for Later" class="menu_link" onclick="displayRss(0)">
            <span class="badge badge-warning">&nbsp;</span>Marked for Later <span id="marked_count"></span>
        </span></li>

        <!-- This rest of this list is populated from functions in menu_col.js -->

    </ul>
    <!-- END - Sidebar content-->
</div>

<div id="content">
<!-- Body content -->
    <ul id="feed_display" >
        <li id="initial_display"></li>
    </ul>
<!-- END - Body Content -->
</div>
</body>
</html>