<?php
ob_start();
?>
<!DOCTYPE HTML>
<html>
<head>
<base href="http://<?php echo $_SERVER["SERVER_NAME"];?>">
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
<?php echo $html; ?>
  </div>
 </div>
 </div>
</body>
</html>
<?php
$html = ob_get_contents();
ob_end_clean();
?>