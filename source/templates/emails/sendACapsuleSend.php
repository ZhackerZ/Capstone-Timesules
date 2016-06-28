<?php
$subject = "Time Capsule Locked";

$plain_text = "Hello there!

The time capsule named:
\"{$data["subject"]}\"
has been locked and buried.
You will be sent an email on {$data["release"]} with your message when
this time capsule is unlocked.
 
Thanks for trying us out,
Timesules.com
";

$html = "<h2>Hello there!</h2>
<p>The time capsule named:<br />
\"{$data["subject"]}\"<br />
has been locked and buried.</p>
<p>You will be sent an email on {$data["release"]} with your message when
this time capsule is unlocked.</p>
<p>Thanks for trying us out,<br />Timesules.com</p>";
?>