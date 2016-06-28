<?php
$noteData = '{"i":'.$data["i"].',"n":"'.$data["n"].'"}';

$subject = "Added To Group";

$plain_text = "Hello {USER_FIRST} {USER_LAST}!

You have been added to the group \"{$data["n"]}\" group on Timesules.com.
To view this group, copy the link below and paste it into your favorite web browser.

{$data["url"]}
";

$html = "<html>
 <body>
  <p>Hello {USER_FIRST} {USER_LAST}!</p>
  <p>
You have been added to the group \"{$data["n"]}\" group on Timesules.com.
To view this group, click or copy the link below and paste it into your favorite web browser.
  </p>
  <p><a href='{$data["url"]}'>{$data["url"]}</a></p>
 </body>
</html>"; 
?>