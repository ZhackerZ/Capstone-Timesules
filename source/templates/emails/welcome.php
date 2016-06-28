<?php
$subject = "Welcome to Timesules!";
$plain_text = "Welcome to Timesules {$data["first"]} {$data["last"]}!

Your account has been successfully created with this email address.
You are now able to login by going to the login page.
Also, if you ever forget your password, you can reset it here by
copying the link below and pasting it into your favorite
web browser.

{$data["reset"]}

Thanks,
Timesules Staff
";

$html = "<html>
 <body>
  <p>Welcome to Timesules {$data["first"]} {$data["last"]}!</p>
  <p>
Your account has been successfully created with this email address.<br/>
You are now able to login by going to the login page.<br/>
Also, if you ever forget your password, you can reset it by clicking or
copying the link below and pasting it into your favorite
web browser.<br />
<br />
<a href=\"{$data["reset"]}\">{$data["reset"]}</a>.<br/>
  </p>
  <p>Thanks,<br/>Timesules Staff</p>
 </body>
</html>";
?>