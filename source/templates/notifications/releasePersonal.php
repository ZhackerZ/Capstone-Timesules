<?php
$noteData = '{"i":'.$data["id"].',"p":"'.$data["prompt"].'"}';

$subject = "New Capsule Released";

$plain_text = "Hello {USER_FIRST} {USER_LAST}!
{$data["name"]} shared the capsule named
\"{$data["prompt"]}\"
with you. This capsule has now been released
and you are able to view it at
http://timesules.com
";

$html = "<html>
 <body>
  <p>Hello {USER_FIRST} {USER_LAST}!</p>
  <p>{$data["name"]} shared the capsule named</p>
  <p>\"{$data["prompt"]}\"</p>
  <p>
with you. This capsule has now been released
and you are able to view it at<br />
<a href='http://timesules.com/'>Timesules.com</a>
  </p>
 </body>
</html>"; 
?>