<?php
function redirect_to($new_location){
  header("Location: ". $new_location);
  exit;
}

function send_email($email, $hash)
{
$to      = $email; // Send email to our user
$subject = 'Timesules Signup | Verification'; // Give the email a subject
$message = '

Thanks for signing up for Timesules!
Your account has been created and you can login with your credentials after you have activated your account with the url below.

Please click this link to activate your account:
http://www.timesules.com/verify.php?email='.$email.'&hash='.$hash.'
';
// Our message above including the link
//http://www.timesules.dev/verify.php?email='.$email.'&hash='.$hash.'


$headers = 'From:noreply@timesules.com' . "\r\n"; // Set from headers
mail($to, $subject, $message, $headers); // Send our email
}


function form_errors($errors=array())
{
  $output = "";
  if (!empty($errors)) {
    $output .= "<div class=\"error\">";
    $output .= "Please fix the following errors:";
    $output .= "<ul>";
    foreach ($errors as $key => $error) {
      $output .= "<li>";
      $output .= htmlentities($error);
      $output .= "</li>";
    }
    $output .= "</ul>";
    $output .= "</div>";
  }
  return $output;
}
?>