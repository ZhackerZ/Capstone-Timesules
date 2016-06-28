<?php
/**
 * Timesules login handler.
 *
 *@author Tyler Hadidon Yuzhen Liu
 *@copyright 2015
 */
define('IN_TIMESULES', true);
require_once 'source/startup.php';

// First, check and see if the client is loging out
if(isset($_GET["logout"]) || isset($_POST["logout"])) {
	$user->logout();
	usleep(500);
	$manager->redirect("/index.php");
	exit;
}

// If we are already logged in, redirect to home page
if($user->isLoggedIn())
	$manager->redirect("/index.php");

// Now see if we are requesting to login
if(!is_null($_POST["login"])) {
	try {
		if($user->login() === TRUE) {
			$manager->redirect("/index.php",0);
		} else {
			throw new LoginFailureException();
		}
	} catch(LoginFailureException $e) {
		$err = "<p>An unknown error has occured while trying to log you in. Please try again.</p>";
		$head = "Login Error";
		$noLogin = FALSE;
		if($e->getCode() == LoginFailureException::BAD_USER) {
			$err = "<p>The Email address you supplied was not found in our database.</p>
			<p>Please check that you have entered the correct Email address and try again.</p>";
			$head = "Incorrect Login";
		} else if($e->getCode() == LoginFailureException::BAD_PASS) {
			$err = "<p>The password you supplied was incorrect.</p>
			<p>Remember, passwords are case sensitive. Make sure Caps Lock is off and try again.</p>";
			$head = "Incorrect Password";
		} else if($e->getCode() == LoginFailureException::NOT_VALID) {
			$err = "<p>It appears your account is not valid. Make sure you've verified your account with the link sent to your email then try again.</p>";
			$head = "Invalid Account";
		} else if($e->getCode() == LoginFailureException::BANNED) {
			$err = "<p>It appears your account has been disabled by an administrator.</p>
			<p>To gain access back to your account, please contact <a href=\"/support.php\">Support</a>.</p>";
			$head = "Account Disabled";
			$noLogin = TRUE;
		}
?>
   <div id="send-a-capsule-wrapper">
    <div id="send-a-capsule">
     <h2><span>&bull;&bull;</span> <?php echo $head; ?> <span>&bull;&bull;</span></h2>
     <div class="ui-error">
     <?php echo $err; ?>
     </div>
<?php if(!$noLogin) { ?>
    <form action="/login.php" method="post">
     <div>E-mail Address</div>
     <input type="text" name="user" style="width:100%" value="<?php echo $_POST["user"]; ?>" /><br />
     <div>Password</div>
     <input type="password" name="pass" style="width:100%" /><br />
     <div style="text-align: right;">
      <input type="submit" class="submit-button" style="margin-right:4px;" name="login" value="LOGIN" />
<?php if(isset($_POST["noscript"]) && $_POST["noscript"] == "TRUE") { ?>
      <input type="hidden" name="noscript" value="TRUE" />
      <div>Do not have an account? <a href="index.php?signup">Sign Up!</a></div>
<?php } else { ?>
      <input type="button" class="submit-button" id="login-signup" value="SIGN UP" onclick="window.location='index.php?signup'" />
<?php } ?>
      <div><a href="index.php?fgtpw">forgot password?</a></div>
     </div>
    </form>
    </div>
   </div>
<?php
} // End No Login check

		$theme->load("welcome_page", Array("title"=>"Login Error","noLogin"=>TRUE));
	}
	exit;
}

header("Location: /index.php");
?>