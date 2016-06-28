<?php
/**
 * Timesules signup handler. Response codes are as follows:
 * 200 = OK
 * 500 = Unknown Error
 * 900 = Validation Error
 *
 *@author Tyler Hadidon Yuzhen Liu
 *@copyright 2015
 */

session_start();

$cap = $_SESSION["LAST_CAPTCHA"];

define('IN_TIMESULES', true);
require_once 'source/startup.php';

$minAge = 13;
$signup = array();

// E. Register!
if(!is_null($_POST["doSignup"])) {
	$signup = $_POST["signup"];
	unset($_POST);

	$signup["email"] = trim($signup["email"]);

	// Check for First and Last name
	$error = "";
	if(!validateEmpty($signup["first"])) $error .= "<span>First name</span> must be filled out.<br />";
	if(!validateEmpty($signup["last"])) $error .= "<span>Last name</span> must be filled out.<br />";

	// Check age requirements
	$birthday = Array(
		"month"=>$signup["bdayMon"],
		"day"=>$signup["bdayDay"],
		"year"=>$signup["bdayYear"]
   );

	$age = date("Y")-$birthday["year"];
	if($age == $minAge && ($birthday["month"]>date("m") || ($birthday["month"]==date("m") && $birthday["day"]>date("d"))))
		$age = 0;

	if(!@checkdate($birthday["month"],$birthday["day"],$birthday["year"])) $error .= "Please enter your birthday.<br />";
	else if($age < $minAge)
		$error .= "<span>Too young</span> in compliance with the \"Children's Online Privacy Protection Act\".<br />";

	$signup["bday"] = $birthday["year"]."-".$birthday["month"]."-".$birthday["day"];

	// Make sure gender is valid (PHP security rule #4, never trust anything until proven valid)
	if($signup["gender"]=="") $signup["gender"] = 0;
	if(filter_var($signup["gender"], FILTER_VALIDATE_INT, Array('options'=>Array('min_range'=>0,'max_range'=>2))) === FALSE)
		$error .= "That <span>gender is not valid</span>. Please select a valid gender.<br />";

	// Check that the email is valid (for the most part, real validation comes later)
	if(!validateEmpty($signup["email"])) $error .= "Please enter your <span>email address</span>.<br />";
	else if(!filter_var($signup["email"], FILTER_VALIDATE_EMAIL)) $error .= "That is an <span>invalid email address</span>.<br />";
	else {
		// Make sure this email does not already exist
		$email = $sql->escape($signup["email"]);
		$num = $sql->select("users", "user_email", "WHERE `user_email`='{$email}'");
		$check = $sql->fetch();

		if($check["user_email"]==$email || $num != 0)
			$error .= '<span>Email address already in use.</span> The same email address can not be used to register multiple accounts. If you have forgotten your password, you can <a href="/index.php?fgtpw" id="'.$signup["email"].'" onclick="signup_fgtpass_link(this);">Reset Your Password</a><br />';
	}

	// Check passwords
	if(!validateEmpty($signup["pass"])) $error .= "Please enter a <span>password</span>.<br />";
	else if($signup["passConf"]!==$signup["pass"]) $error .= "Your <span>password confirmation did not match</span> the first. Please try again.<br />";
	unset($signup["passConf"]);

	// Check Captcha
	if(!validateEmpty($signup["captcha"])) $error .= "You must <span>enter the security code</span>.<br />";
	else if(strtoupper($signup["captcha"]) != $cap) $error .= "The <span>security code was incorrect</span>.<br />";

	// Make sure they checked "I AGREE" and not anything else (rule #4!!)
	if($signup["terms"] !== "I AGREE") $error .= "You must <span>agree to the <a href='/terms.php' target='_blank'>Terms of Use</a> and <a href='/privacy.php' target='_blank'>Privacy Policy</a></span>.<br />";

	// YOU ARE VALID!?
  if($error === "") {
    define('GEN_PWD', TRUE);

    $hash = md5( rand(0,1000) ); // Generate random 32 character hash and assign it to a local variable. Example output: f4552671f8909587cf485ea990207f3b

		//$confChecksum = md5('rand'.rand(3,10).time().$signup["first"].'rand');
    $insert = Array(
     "user_email"=>$signup["email"],
     "user_password"=>$user->genPasswordHash($signup["pass"]),
     "user_first"=>$signup["first"],
     "user_middle"=>$signup["middle"],
     "user_last"=>$signup["last"],
     "user_age"=>$signup["bday"],
     "user_gender"=>$signup["gender"],
     "user_ip"=>$_SERVER['REMOTE_ADDR'],
     "hash"=>$hash,
     "active"=>0
			//"user_conf"=>$confChecksum
     );
    $insert = $sql->escape($insert);

    if($sql->insert("users", $insert)) {
     try {
      $_POST["user"] = $signup["email"];
      $_POST["pass"] = $signup["pass"];
      unset($signup["pass"]);
      // $user->login();
      //redirect to home and display message to verify account
      // $_SESSION["message"] = 'Your account has been made, please verify it by clicking the activation link that has been send to your email: '.$signup['email'];

      send_email($signup["email"], $hash);

      // $manager->mail($signup["email"], "welcome", $data);
      redirect_to("/index.php?alert=verify&email=".$signup['email']);

			} catch(LoginFailureException $e) { } // Ignore not being able to log in.

			// $data = Array(
			// 	"first"=>$signup["first"],
			// 	"last"=>$signup["last"],
			// 	"reset"=>"http://{$_SERVER["SERVER_NAME"]}/index.php?fgtpw"
			// 	//"confirm"=>'http://'.$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"]."?conf={$confChecksum}&email={$signup["email"]}"
   //     );
			// $manager->mail($signup["email"], "welcome", $data);
			// $manager->redirect("/index.php");
			// exit;
		} else {
			$error = "An error occured while adding your account to the database. Please try again.";
		}
	}
}

function validateEmpty($thing) {
	return !(is_null($thing) || $thing == "");
}
?>

<div id="send-a-capsule-wrapper" style="width: 500px;">
  <div id="send-a-capsule" style="padding: 10px;">
   <h2><span>&bull;&bull;</span> SIGN UP! <span>&bull;&bull;</span></h2>

   <?php if($error != "") { echo '    <div class="ui-error">'.$error.'</div>'; } ?>
   <?php echo message(); ?>

   <form action="/signup.php" method="post">
     <div id="signup-form">
      <div><div class="form-left"><span class="req">*</span>First Name:</div><input type="text" name="signup[first]" value="<?php echo $signup["first"]; ?>" /></div>
      <div><div class="form-left">Middle Name:</div><input type="text" name="signup[middle]" value="<?php echo $signup["middle"]; ?>" /></div>
      <div><div class="form-left"><span class="req">*</span>Last Name:</div><input type="text" name="signup[last]" value="<?php echo $signup["last"]; ?>" /></div>
      <div style="margin-bottom:10px;">
       <div class="form-left"><span class="req">*</span>Birthday:</div>
       <select name="signup[bdayMon]">
        <option value="">Month</option>
        <?php $months = Array("JAN","FEB","MAR","APR","MAY","JUN","JUL","AUG","SEP","OCT","NOV","DEC");
        foreach($months as $key=>$month)
         echo "<option value=\"".($key+1)."\"".(($key+1==$signup["bdayMon"])?' selected="selected"':"").">{$month}</option>\n";
       ?>
     </select>
     <select name="signup[bdayDay]">
      <option value="">Day</option>
      <?php for($i=1;$i<32;$i++) echo "<option value=\"{$i}\"".(($i==$signup["bdayDay"])?' selected="selected"':"").">".str_pad($i, 2, "0", STR_PAD_LEFT)."</option>\n"; ?>
      </select>
      <select name="signup[bdayYear]">
        <option value="">Year</option>
        <?php $thisYear = date("Y"); for($i=$thisYear;$i>$thisYear-100;$i--) echo "<option value=\"{$i}\"".(($i==$signup["bdayYear"])?' selected="selected"':"").">{$i}</option>\n"; ?>
      </select>
    </div>
    <div style="margin-bottom:10px;">
     <div class="form-left">Gender:</div>
     <label for="genderMale">Male</label><input type="radio" id="genderMale" name="signup[gender]" value="1"<?php if($signup["gender"]==1) echo ' checked="checked"'; ?> />
     <label for="genderFemale">Female</label><input type="radio" id="genderFemale" name="signup[gender]" value="2"<?php if($signup["gender"]==2) echo ' checked="checked"'; ?> />
   </div>
   <div><div class="form-left"><span class="req">*</span>Email Address:</div><input type="text" name="signup[email]" value="<?php echo $signup["email"]; ?>" /></div>
   <div><div class="form-left"><span class="req">*</span>Password:</div><input type="password" name="signup[pass]" /></div>
   <div><div class="form-left"><span class="req">*</span>Confirm Pass:</div><input type="password" name="signup[passConf]" /></div>
   <div style="text-align:center;margin-bottom:5px;"><img src="/source/captcha.php" id="captcha" />
    <?php if($signup["noscript"] == "TRUE") { ?>
    <a href="/signup.php"><div id="refreshCaptcha" title="refresh"></div></a>
    <?php } else { ?>
    <div id="refreshCaptcha" class="fake-link" title="refresh"></div>
    <?php } ?>
  </div>
  <div><div class="form-left"><span class="req">*</span>Security Code:</div><input type="text" name="signup[captcha]" /></div>
  <div class="form-terms">
    I have read the Timesules <a href="/terms.php" target="_blank">Terms of Use</a> and
    I understand the Timesules <a href="/privacy.php" target="_blank">Privacy Policy</a>.</div>
    <div><div class="form-left"><span class="req">*</span>I Agree:</div><input type="checkbox" name="signup[terms]" value="I AGREE" /></div>
    <div style="height:10px"></div>
    <div><div class="form-left"></div><input type="submit" class="submit-button" name="doSignup" value="Sign Up!" /></div>
    <div><div class="form-left"></div>Already have an account? <a href="/index.php">Log In!</a></div>
  </div>
</form>


</div>
</div>
<?php
$theme->load("welcome_page",Array("title"=>($error!="")?"Error Signing Up":"Sign Up!","noLogin"=>TRUE));
?>