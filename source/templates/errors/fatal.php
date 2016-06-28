<!DOCTYPE HTML>
<html>
<head>
 <title>Fatal Error | Timesules</title>
 <meta name="description" content="Timesules lets individuuals and organizations relive memorable experiences and events." />
 <meta name="description" content="Timesules, Time Capsule, Social Networking, Social Network, Sharing, Friends, Organizations" />
 <meta charset="UTF-8" />
 <meta http-equiv="X-UA-Compatible" content="IE=edge" />
 <style type="text/css">
html { margin: 0px; height: 100%; }
body { background: #d3d9df url("/source/templates/images/dark-noise.png"); margin: 0px; padding: 0px; font-family: Helvetica,sans-serif,Ariel; font-size: 12px; height: 100%; color: #494b4d; }
#page-wrapper {	height: 100%; min-height: 100%; min-width: 900px; padding: 0px 69px; }
#page-container { min-height: 100%; position: relative; background: #f5f5f5 url("/source/templates/images/white-noise.png"); box-shadow: 0px 0px 5px #838588; }
#header { margin-left: 30px; padding-top: 10px; margin-bottom: 5px; }
#logo { color: #494b4d; font-weight: bold; }
#header-bar { height: 30px; background: #0077a7; box-shadow: 0 5px 4px -4px #d9d9d9; }
#page-content { padding: 10px; }
a:link,a:visited { color: #0077a7; text-decoration: underline; cursor: pointer; font-weight: bold; }
a:hover,a:active { color: #009cdb; }
h2 { color: #03648b; margin-top: 0; }
h2 span { color: #748891; letter-spacing: 2px; }
img { border: 0px }
.ui-error {
	background-color: #e3b4b4;
	border: 5px solid #d89595;
	padding: 5px;
	margin-bottom: 10px;
	text-align: center;
	font-weight: bold;
	
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
}
.ui-notice {
	background-color: #add1df;
	border: 5px solid #8cbdd2;
	padding: 5px;
	margin: 10px 0px;
	color: #494b4d;
	text-align: center;
	font-weight: bold;
	
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
}
 </style>
</head>

<body>
 <div id="page-wrapper">
 <div id="page-container">
  <div id="header">
   <a href="/index.php"><img id="logo" src="/source/templates/images/logo.png" alt="Timesules" /></a>
  </div>
  <div id="header-bar"></div>
  <div id="page-content">
   <div class="ui-error">
    <h2>Fatal Error!</h2>
    <p>An error occured while trying to display this page and could not recover.</p>
   </div>
   <div class="ui-notice">
    <p>
    Support has been notified of this error and will fix it as soon as possible.<br />
    Sorry for the inconvenience.<br /><br />
    Timesules Staff
    </p>
   </div>
<?php if($_SERVER["HTTP_REFERER"]) { ?>
    <div class="ui-notice">
     <p>You can try <a href="<?php echo $_SERVER["HTTP_REFERER"]; ?>">Going Back</a> and trying again.</p>
    </div>
<?php } else if(strpos($_SERVER["REQUEST_URI"], "index.php")===FALSE) { ?>
   <div class="ui-notice">
    <p><a href="/index.php">Click Here</a> to go to the <a href="/index.php">Home Page</a>.</p>
   </div>
<?php } ?>
<!--
===================================
=== Developer Debug information ===
===================================
<?php echo print_r($error, true); ?>
===================================
-->
  </div>
 </div>
 </div>
</body>
</html>