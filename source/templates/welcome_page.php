<?php
/**
 * Timesules welcome page template
 *
 * @author Tyler Hadidon
 * @copyright 2012
 */

// $_SESSION['message'] = "this is a test welcome";

// See if we are trying to reset our password
global $fgtpwError,$fgtpwSuccessful,$confirming_reset_pass;

if(isset($_GET["email"]) && isset($_POST["fgtpw"]["email"]))
  $confirming_reset = ((!is_null($_GET["email"]) && !is_null($_GET["conf"])) || (!is_null($_POST["fgtpw"]["email"]) && !is_null($_POST["fgtpw"]["conf"])));
else
  $confirming_reset = false;
if($confirming_reset) {
	$email = $sql->escape(!is_null($_POST["fgtpw"]["email"])?$_POST["fgtpw"]["email"]:$_GET["email"]);
	$conf = $sql->escape(!is_null($_POST["fgtpw"]["conf"])?$_POST["fgtpw"]["conf"]:$_GET["conf"]);
	$num = $sql->select("users","`user_email`,`user_id`,`user_conf`,`user_ban`", "WHERE `user_email`='{$email}' AND `user_conf`='{$conf}'");
	$row = $sql->fetch();

	if($num===1 && $row["user_email"]==$email && $row["user_conf"]===$conf && $row["user_ban"]=="0")
		$confirming_reset_pass = TRUE;
	else
		$fgtpwError = "An error occured while validating your email! Either the code given was incorrect, your account has been banned, or your email could not be found.";
}

?>
<!DOCTYPE HTML>
<html>
<head>
 <title><?php if(isset($data["title"])){ echo($data["title"]!="")?$data["title"]:"Welcome"; }?> | Timesules</title>
 <meta name="description" content="Timesules lets individuals and organizations relive memorable experiences and events." />
 <meta name="description" content="Timesules, Time Capsule, Social Networking, Social Network, Sharing, Friends, Organizations" />
 <meta charset="UTF-8" />
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
 <script type="text/javascript" src="/source/templates/js/jquery-styleForm.js"></script>
 <script type="text/javascript">
  $(document).ready(function() {
   function toggleLogin() {
    var me = $(this);
    var it = $("#login-box-wrapper");
    var signup = $("#signup-box");
    var fgtpw = $("#fgtpw-box");

    me.html("LOGIN");
    if(signup.css("display")!="none")
     toggleSignup();
   if(fgtpw.css("display")!="none")
     toggleFgtpw();

   if(it.css("display") == "none" && !me.hasClass('active')) {
     me.addClass("active");
     it.slideDown(200);
   } else {
     me.removeClass("active");
     it.slideUp(200);
   }
 }
 $("#login-open").click(toggleLogin);

 function toggleSignup() {
  var button = $("#login-open");
  var signup = $("#signup-box");
  var login = $("#login-box");

  if(signup.css("display")=="none") {
   button.html("SIGNUP");
   login.slideUp(200);
   signup.delay(200).slideDown(200);
 } else {
   signup.slideUp(200);
   login.delay(200).slideDown(200);
   button.html("LOGIN");
 }
}
$("#login-signup").click(toggleSignup);
$("#signup-back").click(toggleSignup);

function toggleFgtpw() {
  var button = $("#login-open");
  var signup = $("#signup-box");
  var login = $("#login-box");
  var fgtpw = $("#fgtpw-box");

  if(signup.css("display")!="none")
   toggleSignup();

 if(fgtpw.css("display")=="none") {
   button.html("FORGOT PASSWORD");
   login.slideUp(200);
   fgtpw.delay(200).slideDown(200);
 } else {
   fgtpw.slideUp(200);
   login.delay(200).slideDown(200);
   button.html("LOGIN");
 }
}
$("#login-fgtpw").click(toggleFgtpw);
$("#fgtpw-back").click(toggleFgtpw);

function refreshCaptcha() {
  $("#captcha").attr("src", "/source/captcha.php?"+Math.random());
}
$("#refreshCaptcha").click(refreshCaptcha);

var search = window.location.search.substr(1);
if(search=="signup") { $("#login-open").click(); toggleSignup(); }
if(search=="fgtpw") { $("#login-open").click(); toggleFgtpw(); }
<?php if(!is_null($fgtpwError) || $confirming_reset_pass) { ?>$("#login-open").click(); toggleFgtpw();<?php } ?>
});
</script>
</head>

<body>
<div style="padding:10px;">
  <?php //echo '<div class="ui-notice">'.$_SESSION['test'].'</div>';
  echo message(); ?>
</div>

 <div id="page-wrapper">
   <div id="page-container">

    <div id="welcome-header">
     <a href="/index.php"><div id="logo"></div></a>

     <?php if(!isset($data["noLogin"])){ $data = array("noLogin" => FALSE);}
     if($data["noLogin"] !== TRUE) {
      ?>
      <noscript><style type="text/css">#login-open { display: none; } </style></noscript>
      <div id="login-open">LOGIN</div>
      <div id="login-box-wrapper">

        <div id="login-box">
          <form action="/login.php" method="post">
           <div>E-mail Address</div>
           <input type="text" name="user" /><br />
           <div>Password</div>
           <input type="password" name="pass" /><br />
           <div style="text-align: right;">
            <input type="submit" class="submit-button" style="margin-right:4px;" name="login" value="LOGIN" />
            <!--       link to signup page -->
            <input type="button" class="submit-button" id="login-signup" value="SIGN UP"/>
            <div><span class="fake-link" id="login-fgtpw">forgot password?</span></div>
          </div>
        </form>
      </div>

      <div id="signup-box">
        <form action="/signup.php" method="post">
         <h2><span>&bull;&bull;</span> SIGN UP! <span>&bull;&bull;</span></h2>
         <div id="signup-form">
          <div><div class="form-left"><span class="req">*</span>First Name:</div><input type="text" name="signup[first]" /></div>
          <div><div class="form-left">Middle Name:</div><input type="text" name="signup[middle]" /></div>
          <div><div class="form-left"><span class="req">*</span>Last Name:</div><input type="text" name="signup[last]" /></div>
          <div style="margin-bottom:10px;">
           <div class="form-left"><span class="req">*</span>Birthday:</div>
           <select name="signup[bdayMon]">
            <option value="">Month</option>
            <?php $months = Array("JAN","FEB","MAR","APR","MAY","JUN","JUL","AUG","SEP","OCT","NOV","DEC"); foreach($months as $key=>$month) echo "<option value=\"".($key+1)."\">{$month}</option>\n"; ?>
          </select>
          <select name="signup[bdayDay]">
            <option value="">Day</option>
            <?php for($i=1;$i<32;$i++) echo "<option value=\"{$i}\">".str_pad($i, 2, "0", STR_PAD_LEFT)."</option>\n"; ?>
          </select>
          <select name="signup[bdayYear]">
            <option value="">Year</option>
            <?php $thisYear = date("Y"); for($i=$thisYear;$i>$thisYear-100;$i--) echo "<option value=\"{$i}\">{$i}</option>\n"; ?>
          </select>
        </div>
        <div style="margin-bottom:10px;">
         <div class="form-left">Gender:</div>
         <label for="genderMale">Male</label><input type="radio" id="genderMale" name="signup[gender]" value="1" />
         <label for="genderFemale">Female</label><input type="radio" id="genderFemale" name="signup[gender]" value="2" />
       </div>
       <div><div class="form-left"><span class="req">*</span>Email Address:</div><input type="text" name="signup[email]" /></div>
       <div><div class="form-left"><span class="req">*</span>Password:</div><input type="password" name="signup[pass]" /></div>
       <div><div class="form-left"><span class="req">*</span>Confirm Pass:</div><input type="password" name="signup[passConf]" /></div>
       <div style="text-align:center;margin-bottom:5px;"><img src="/source/captcha.php" id="captcha" /><div id="refreshCaptcha" class="fake-link" title="refresh"></div></div>
       <div><div class="form-left"><span class="req">*</span>Security Code:</div><input type="text" name="signup[captcha]" /></div>
       <div class="form-terms">
        I have read the Timesules <a href="/terms.php" target="_blank">Terms of Use</a> and
        I understand the Timesules <a href="/privacy.php" target="_blank">Privacy Policy</a>.</div>
        <div><div class="form-left"><span class="req">*</span>I Agree:</div><input type="checkbox" name="signup[terms]" value="I AGREE" /></div>
        <div style="height:10px"></div>
        <div><div class="form-left"></div><input type="submit" class="submit-button" name="doSignup" value="Sign Up!" /></div>
        <div><div class="form-left"></div>Already have an account? <span class="fake-link" id="signup-back">Log In!</span></div>
      </div>
    </form>
  </div>

  <div id="fgtpw-box"<?php if($fgtpwSuccessful === TRUE) echo ' class="ui-success" style="text-align: center;"';?>>
    <form action="/fgtpw.php" method="post">
     <?php if($confirming_reset_pass === TRUE) { ?>
     <h2><span>&bull;&bull;</span> RESET PASSWORD <span>&bull;&bull;</span></h2>
     <?php  if($fgtpwError != "") echo '    <div class="ui-error">'.$fgtpwError.'</div>'; ?>
     <div style="text-align: center;">Please enter a new password and confirm it by typing it again in the form below. When you are complete, press "Reset Password" below.</div>
     <div style="height:5px"></div>
     <div>New Password: <input type="password" name="fgtpw[pass]" /></div>
     <div>Password Again: <input type="password" name="fgtpw[passConf]" /></div>
     <input type="hidden" name="fgtpw[email]" value="<?php echo $row["user_email"];?>" />
     <input type="hidden" name="fgtpw[conf]" value="<?php echo $row["user_conf"];?>" />
     <input type="hidden" name="fgtpw[uid]" value="<?php echo $row["user_id"];?>" />
     <input type="submit" class="submit-button" name="fgtpwReset" value="Reset Password" />
     <?php } else if($fgtpwSuccessful === TRUE) { ?>
     <h2><span>&bull;&bull;</span> REQUEST SENT <span>&bull;&bull;</span></h2>
     <div><?php echo $fgtpwError; ?></div>
     <?php } else if($fgtpwError != "") { ?>
     <h2><span>&bull;&bull;</span> CONFIRMATION ERROR <span>&bull;&bull;</span></h2>
     <div class="ui-error"><?php echo $fgtpwError; ?></div>
     <div style="text-align: center;">Forgotten your password? We can email you a link to change it!</div>
     <div style="height:5px"></div>
     <div>E-mail Address: <input type="text" name="fgtpw[email]" /> <input type="submit" class="submit-button" name="fgtpwSend" value="Send" /></div>
     <div><span class="fake-link" id="fgtpw-back">Go Back!</span></div>
     <?php } else { ?>
     <h2><span>&bull;&bull;</span> FORGOTTEN PASSWORD <span>&bull;&bull;</span></h2>
     <div style="text-align: center;">Forgotten your password? We can email you a link to change it!</div>
     <div style="height:5px"></div>
     <div>E-mail Address: <input type="text" name="fgtpw[email]" /> <input type="submit" class="submit-button" name="fgtpwSend" value="Send" /></div>
     <div><span class="fake-link" id="fgtpw-back">Go Back!</span></div>
     <?php } ?>
   </form>
 </div>

</div>
<?php } ?>
</div>
<div id="welcome-header-bar"></div>

<div id="page-content" class="welcome-page">

  <!-- START NO SCRIPT -->

  <noscript>
   <div style="height:20px;"></div>
   <div class="ui-warn">
    <h2>JavaScript Disabled</h2>
    <p>
     It appears your browser does not have JavaScript enabled. Timesules.com requires JavaScript to function correctly.
     Please enable JavaScript for a full Timesules experience.
   </p>
 </div>

 <?php if(!is_null($_GET["signup"])) { ?>
 <div class="ui-notice" style="text-align:left">
  <form action="/signup.php" method="post">
   <h2><span>&bull;&bull;</span> SIGN UP! <span>&bull;&bull;</span></h2>
   <div id="signup-form">
    <div><div class="form-left"><span class="req">*</span>First Name:</div><input type="text" name="signup[first]" /></div>
    <div><div class="form-left">Middle Name:</div><input type="text" name="signup[middle]" /></div>
    <div><div class="form-left"><span class="req">*</span>Last Name:</div><input type="text" name="signup[last]" /></div>
    <div style="margin-bottom:10px;">
     <div class="form-left"><span class="req">*</span>Birthday:</div>
     <select name="signup[bdayMon]">
      <option value="">Month</option>
      <?php $months = Array("JAN","FEB","MAR","APR","MAY","JUN","JUL","AUG","SEP","OCT","NOV","DEC"); foreach($months as $key=>$month) echo "<option value=\"".($key+1)."\">{$month}</option>\n"; ?>
    </select>
    <select name="signup[bdayDay]">
      <option value="">Day</option>
      <?php for($i=1;$i<32;$i++) echo "<option value=\"{$i}\">".str_pad($i, 2, "0", STR_PAD_LEFT)."</option>\n"; ?>
    </select>
    <select name="signup[bdayYear]">
      <option value="">Year</option>
      <?php $thisYear = date("Y"); for($i=$thisYear;$i>$thisYear-100;$i--) echo "<option value=\"{$i}\">{$i}</option>\n"; ?>
    </select>
  </div>
  <div style="margin-bottom:10px;">
   <div class="form-left">Gender:</div>
   <label for="genderMale">Male</label><input type="radio" id="genderMale" name="signup[gender]" value="1" />
   <label for="genderFemale">Female</label><input type="radio" id="genderFemale" name="signup[gender]" value="2" />
 </div>
 <div><div class="form-left"><span class="req">*</span>Email Address:</div><input type="text" name="signup[email]" /></div>
 <div><div class="form-left"><span class="req">*</span>Password:</div><input type="password" name="signup[pass]" /></div>
 <div><div class="form-left"><span class="req">*</span>Confirm Pass:</div><input type="password" name="signup[passConf]" /></div>
 <div style="margin-bottom:5px;"><img src="/source/captcha.php" id="captcha" /><a href="/index.php?signup"><div id="refreshCaptcha" title="refresh"></div></a></div>
 <div><div class="form-left"><span class="req">*</span>Security Code:</div><input type="text" name="signup[captcha]" /></div>
 <div class="form-terms">
  I have read the Timesules <a href="/terms.php" target="_blank">Terms of Use</a> and
  I understand the Timesules <a href="/privacy.php" target="_blank">Privacy Policy</a>.</div>
  <div><div class="form-left"><span class="req">*</span>I Agree:</div><input type="checkbox" name="signup[terms]" value="I AGREE" /></div>
  <div style="height:10px"></div>
  <input type="hidden" name="signup[noscript]" value="TRUE" />
  <div><div class="form-left"></div><input type="submit" class="submit-button" name="doSignup" value="Sign Up!" /></div>
  <div><div class="form-left"></div>Already have an account? <a href="/index.php">Log In!</a></div>
</div>
</form>
</div>
<?php } else if(!is_null($_GET["fgtpw"]) || $confirming_reset_pass === TRUE || $fgtpwError != "" || $fgtpwSuccessful === TRUE) { ?>
<div class="<?php echo ($fgtpwSuccessful === TRUE)?'ui-success':'ui-notice';?>">
  <form action="/fgtpw.php" method="post">
   <?php if($confirming_reset_pass === TRUE) { ?>
   <h2><span>&bull;&bull;</span> RESET PASSWORD <span>&bull;&bull;</span></h2>
   <?php  if($fgtpwError != "") echo '    <div class="ui-error">'.$fgtpwError.'</div>'; ?>
   <div style="text-align: center;">Please enter a new password and confirm it by typing it again in the form below. When you are complete, press "Reset Password" below.</div>
   <div style="height:5px"></div>
   <div>New Password: <input type="password" name="fgtpw[pass]" /></div>
   <div>Password Again: <input type="password" name="fgtpw[passConf]" /></div>
   <input type="hidden" name="fgtpw[email]" value="<?php echo $row["user_email"];?>" />
   <input type="hidden" name="fgtpw[conf]" value="<?php echo $row["user_conf"];?>" />
   <input type="hidden" name="fgtpw[uid]" value="<?php echo $row["user_id"];?>" />
   <input type="submit" class="submit-button" name="fgtpwReset" value="Reset Password" />
   <?php } else if($fgtpwSuccessful === TRUE) { ?>
   <h2><span>&bull;&bull;</span> REQUEST SENT <span>&bull;&bull;</span></h2>
   <div><?php echo $fgtpwError; ?></div>
   <?php } else if($fgtpwError != "") { ?>
   <h2><span>&bull;&bull;</span> CONFIRMATION ERROR <span>&bull;&bull;</span></h2>
   <div class="ui-error"><?php echo $fgtpwError; ?></div>
   <div style="text-align: center;">Forgotten your password? We can email you a link to change it!</div>
   <div style="height:5px"></div>
   <div>E-mail Address: <input type="text" name="fgtpw[email]" /> <input type="submit" class="submit-button" name="fgtpwSend" value="Send" /></div>
   <div><a href="/index.php">Login!</a></div>
   <?php } else { ?>
   <h2><span>&bull;&bull;</span> FORGOTTEN PASSWORD <span>&bull;&bull;</span></h2>
   <div style="text-align: center;">Forgotten your password? We can email you a link to change it!</div>
   <div style="height:5px"></div>
   <div>E-mail Address: <input type="text" name="fgtpw[email]" /> <input type="submit" class="submit-button" name="fgtpwSend" value="Send" /></div>
   <div><a href="/index.php">Login!</a></div>
   <?php } ?>
 </form>
</div>
<?php } else if($data["noLogin"] !== TRUE) { ?>
<div class="ui-notice">
  <h2><span>&bull;&bull;</span> Login <span>&bull;&bull;</span></h2>
  <form action="/login.php" method="post">
   <div style="width:150px;text-align:left;margin: 0 auto;">
     <div>E-mail Address</div>
     <input type="text" name="user" style="width:100%" />
     <div>Password</div>
     <input type="password" name="pass" style="width:100%" />
   </div>
   <input type="hidden" name="noscript" value="TRUE" />
   <input type="submit" class="submit-button" style="margin-right:4px;" name="login" value="LOGIN" />
   <div>Do not have an account? <a href="/index.php?signup">Signup!</a></div>
   <div><a href="/index.php?fgtpw">forgot password?</a></div>
 </form>
</div>
<?php }?>
</noscript>

<!-- END NO SCRIPT -->

<?php echo $ob_get_contents; ?>

</div>

</div>
</div>
</body>
</html>
