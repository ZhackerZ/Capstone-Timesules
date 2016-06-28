<?php
$subject = "Timesules Password Reset Request";
$plain_text = "Hello {$data["first"]} {$data["last"]}!

It has been requested that your password be reset. If you have not requested your password to be reset, disregard this email.
To reset your password, copy the link below and paste it into your favorite web browser.

{$data["confirm"]}

Thanks,
Timesules Staff
";

$html = "<h2>Hello {$data["first"]} {$data["last"]}!</h2>
<p>
It has been requested that your password be reset. If you have not requested your password to be reset, disregard this email.
To reset your password, click the link below or copy the link below and paste it into your favorite web browser.
</p>
<p><a href='{$data["confirm"]}'>{$data["confirm"]}</a></p>
<p>Thanks,<br />Timesules Staff</p>";
?>