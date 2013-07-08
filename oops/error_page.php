<?php
/**
 * Created by: denis
 * Created on: 2013-06-15
 *
 * Error page that will be displayed for 400 and 500 errors
 */

$error = (isset($_GET['type'])) ? str_replace('+', ' ', $_GET['type']) : 'OOPS!';
$msg = 'Error type: '.$error."\r\n";
$msg .= "URI Accessed: ".$_SERVER['REQUEST_URI']."\r\n";
$msg .= "Error originated at IP: ".$_SERVER['REMOTE_ADDR']."\r\n";
mail('rss@jdenoc.com', 'ERROR', $msg);

?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $error; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="5; URL=<?php echo 'http://'.$_SERVER["HTTP_HOST"].'/index.html'?>"/>
    <link rel="icon" href="<?php echo 'http://'.$_SERVER["HTTP_HOST"].'/img/rss.png'; ?>" type="image/x-icon"/>
    <link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Merienda+One' type='text/css'/>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>

    <style>
        #main{
            font-family: 'Merienda One', cursive;
            font-size: 14px;
            width: 510px;
            text-align: center;
            margin: auto;
            padding-top: 50px;
        }
        body{
            background: #222;
            color: #e9e9e9;
        }
        #main a:link,
        #main a:visited,
        #main a:hover{
            color: #3299bb;
        }
    </style>
</head>
<body>
<div id="container">
    <div id="main">
        <h1><?php echo $error; ?></h1><br/>
        <p>It seems that you've encountered a problem.</p>
        <p>Please wait a moment while you are redirected...</p>
        <p>&nbsp;</p>
        <p>If you are not redirected automatically, click <a href="<?php echo 'http://'.$_SERVER["HTTP_HOST"].'/index.html'?>">Here</a></p>
    </div>
</div>
</body>
</html>