<?php
$noteData = '{"i":'.$user->get("id").',"n":"'.$user->get("first").' '.$user->get("last").'"}';

$subject = "New Timesules Contact";

$plain_text = "Hello {USER_FIRST} {USER_LAST}!

{$user->get("first")} {$user->get("last")} has requested you as a contact.
To accept {$user->get("first")} as a contact, copy the link below and paste it into your favorite web browser.

{$data["url"]}
";

$html = "<html>
 <body>
  <p>Hello {USER_FIRST} {USER_LAST}!</p>
  <p>
{$user->get("first")} {$user->get("last")} has requested you as a contact.
To accept {$user->get("first")} as a contact, click or copy the link below and paste it into your favorite web browser.
  </p>
  <p><a href='{$data["url"]}'>{$data["url"]}</a></p>
 </body>
</html>"; 
?>