<?php

include("includes/sessions.php");
include("includes/functions.php");

mysql_connect("localhost", "mcouncil3", "TimesulesSp15") or die(mysql_error()); // Connect to database server(localhost) with username and password.
mysql_select_db("timesules") or die(mysql_error()); // Select registration database.


if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['hash']) && !empty($_GET['hash'])){
    // Verify data
  $email = mysql_escape_string($_GET['email']); // Set email variable
  $hash = mysql_escape_string($_GET['hash']); // Set hash variable

  $search = mysql_query("SELECT user_email, hash, active FROM users WHERE user_email='".$email."' AND hash='".$hash."' AND active='0'") or die(mysql_error());
  $match  = mysql_num_rows($search);

  if($match > 0){
    // We have a match, activate the account
    mysql_query("UPDATE users SET active='1' WHERE user_email='".$email."' AND hash='".$hash."' AND active='0'") or die(mysql_error());
    $_SESSION['message'] ='Your account has been activated, you can now login';
    redirect_to('/index.php');

  }else{
    // No match -> invalid url or account has already been activated.
    $_SESSION['message'] = 'The url is either invalid or you already have activated your account.';
    redirect_to('/index.php');
  }

}else{
    // Invalid approach
  $_SESSION['message'] = 'Invalid approach, please use the link that has been send to your email.';
  redirect_to("/index.php");
}

?>














