<?php
$noteData = '{"i":'.$data["post_id"].',"g":'.$data["group_id"].',"n":"'.$data["name"].'"}';

$subject = "Group Capsule Released";

$plain_text = "Hello {USER_FIRST} {USER_LAST}!

\"{$data["prompt"]}\"
has been released for the group
{$data["name"]}. View it at
http://timesules.com
";

$html = "<html>
 <body>
  <p>Hello {USER_FIRST} {USER_LAST}!</p>
  <p>
\"{$data["prompt"]}\"<br />
has been released for the group<br />
{$data["name"]}. View it at<br />
<a href='http://timesules.com/'>Timesules.com</a>
  </p>
 </body>
</html>"; 
?>