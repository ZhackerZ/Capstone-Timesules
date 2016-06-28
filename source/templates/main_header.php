<?php
/**
 * Timesules Main Header template
 *
 * @author Tyler Hadidon
 * @copyright 2012
 */

// require 'vendor/autoload.php';

use DebugBar\StandardDebugBar;

// global $debugbar;
$debugbar = new StandardDebugBar();
$debugbarRenderer = $debugbar->getJavascriptRenderer();

// $debugbar["messages"]->addMessage("hello world1!");

?>
<!DOCTYPE HTML>
<html>
<head>
 <title><?php if (isset($data["title"])){ echo($data["title"]!="")?$data["title"]:"Welcome"; }?> | Timesules</title>
 <meta name="description" content="Timesules lets individuuals and organizations relive memorable experiences and events." />
 <meta name="description" content="Timesules, Time Capsule, Social Networking, Social Network, Sharing, Friends, Organizations" />
 <meta charset="UTF-8" />
 <link type="text/css" rel="stylesheet" href="/source/templates/css/jquery-ui-1.8.23.custom.css" />
 <link type="text/css" rel="stylesheet" href="/source/templates/css/style.css" />
 <!--[if IE 9]>
 <link type="text/css" rel="stylesheet" href="/source/templates/css/ie9.css" />
 <![endif]-->
 <!--[if IE 8]>
 <link type="text/css" rel="stylesheet" href="/source/templates/css/ie8.css" />
 <![endif]-->
 <!--[if lte IE 7]>
 <link type="text/css" rel="stylesheet" href="/source/templates/css/ie-lte7.css" />
 <![endif]-->
 <script type="text/javascript" src="/source/templates/js/jquery-1.7.2.min.js"></script>
 <script type="text/javascript" src="/source/templates/js/jquery-ui-1.8.23.custom.min.js"></script>
 <script type="text/javascript" src="/source/templates/js/jquery.ui.touch-punch.min.js"></script>
 <script type="text/javascript" src="/source/templates/js/jquery-styleForm.js"></script>
 <script type="text/javascript" src="/source/templates/js/jquery-jscrollpane.min.js"></script>
 <script type="text/javascript" src="/source/templates/js/timesules.js"></script>
<?php echo $ob_get_contents; ?>
<!--         <?php //echo $debugbarRenderer->renderHead() ?>
 -->
</head>

<body>
<?php if(class_exists("Tracker")) { ?>
 <div style="width:100%;background-color:#E3B4B4;position:absolute;z-index:1000;text-align: center;color: #494b4d;-webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;">
  <div style="border:5px solid #d89595;padding: 5px;-webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;">Tracking user data and SQL Queries! <a href="/bugs.php" style="font-weight: normal;">Report A Bug</a></div>
 </div>
<?php } ?>
 <div id="main-header-bar-backdrop"></div>

 <div id="page-wrapper">
 <div id="page-container">

  <div id="main-header">
   <a href="/index.php"><div id="logo"></div></a>

   <div id="account-open">Welcome, <?php echo $user->get("first").' '.$user->get("last"); ?> <img src="/source/templates/images/account-arrow.png" /></div>
   <div id="account-dropdown">
    <div><a href="/settings.php">Account Settings</a></div>
    <div><a href="/privacy.php">Privacy Policy</a></div>
    <div><a href="/terms.php">Terms of Use</a></div>
    <div><a href="/login.php?logout">Logout</a></div>
   </div>
  </div>
  <div id="main-header-bar">
<?php
$activePage = -1;
switch(substr($_SERVER["PHP_SELF"],1,-4)) {
	case "index": $activePage = 0; break;
	case "prompt": $activePage = 1; break;
	case "timecap": $activePage = 2; break;
	case "groups": $activePage = 3; break;
	case "contacts": $activePage = 4; break;
}
?>
   <a <?php if($activePage==0) echo 'class="active-page" '; ?>href="/index.php">HOME</a><div class="separator">:</div>
   <a <?php if($activePage==1) echo 'class="active-page" '; ?>href="/capsule.php">CREATE CAPSULE</a><div class="separator">:</div>
   <a <?php if($activePage==2) echo 'class="active-page" '; ?>href="/timecap.php?timesule=2">TIMESULES</a><div class="separator">:</div>
   <a <?php if($activePage==3) echo 'class="active-page" '; ?>href="/groups.php">GROUPS</a><div class="separator">:</div>
   <a <?php if($activePage==4) echo 'class="active-page" '; ?>href="/contacts.php">CONTACTS</a>
  </div>

<!--         <?php //echo $debugbarRenderer->render() ?>
 -->
  <div id="page-content">




