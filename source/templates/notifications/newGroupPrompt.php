<?php
$noteData = '{"i":'.$data["id"].',"n":"'.$data["name"].'","g":"'.$data["g"].'"}';

$subject = "New Capsules for ".$data["name"];

$plain_text = "Hello {USER_FIRST} {USER_LAST}!

The {$data["name"]} group has added a prompt for you to reply to by {$data["lock"]}:

{$data["prompt"]}

To add your reply to this prompt, copy the link below and paste it into your favorite web browser.

{$data["url"]}
";

$html = "<html>
 <body>
  <p>Hello {USER_FIRST} {USER_LAST}!</p>
  <p>The {$data["name"]} group has added a prompt for you to reply to by {$data["lock"]}:</p>
  <p>{$data["prompt"]}</p>
  <p>To add your reply to this prompt, click or copy the link below and paste it into your favorite web browser.</p>
  <p><a href='{$data["url"]}'>{$data["url"]}</a></p>
 </body>
</html>";
?>
