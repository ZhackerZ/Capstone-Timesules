<?php
$subject = "Time Capsule Released!";

$plain_text = "Hello there!

The time capsule named:
\"{$data["subject"]}\"
has been dug up! Here is the message
you left behind!

{$data["message"]}

Signup at Timesules.com to share capsules
with friends and family!

Thanks for trying us out,
Timesules.com
";

$html = "<h2>Hello there!</h2>
<p>The time capsule named:<br />
\"{$data["subject"]}\"<br />
has been dug up! Here is the message
you left behind!</p>
<p>{$data["message"]}</p>
<p>Signup at Timesules.com to share capsules
with friends and family!</p>
<p>Thanks for trying us out,<br />Timesules.com</p>";
?>