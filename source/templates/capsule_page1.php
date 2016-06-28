<script type="text/javascript">Timesules.prompt.init();</script>
<?php
$this->load("main_header", $data);

// Setup default values
$id = -1;
$title = "";

$lockDate = explode(" ", date('n j Y g i A'));
$releaseDate = explode(" ", date('n j Y g i A', time()+3600*24*30));

$message = "";
$vis = 1;

$contacts = $data["toList"];
$error = $data["error"];
$result = $data["result"];

$attachments = "";

$p = $data["p"];
$c = $data["c"];


if(!is_null($p) && $p === FALSE) {
  $error = '<strong>Error:</strong> The time capsule requested is not able to be edited or could not be found.';
} else if(!is_null($p)) {
  $id = $p["post_id"];
  $title = $p["cap_title"];

  $lockDate = explode(" ", date('n j Y g i A', $p["cap_lock"]));
  $releaseDate = explode(" ", date('n j Y g i A', $p["cap_release"]));

  $message = $p["cap_msg"];
  $vis = $p["cap_vis"];

  //$attachments = $data["p"]["post_attachments"];

} else if(!is_null($c)) {

  if (isset($c["update"])){
    $id = $c["update"];
  }

  $title = $c["cap_title"];

  $lock = $c["cap_lock"];
  $lock = strtotime("{$lock["month"]}/{$lock["date"]}/{$lock["year"]} {$lock["time"]} {$lock["meridiem"]}");
  $lockDate = explode(" ", date('n j Y g i A', $lock));

  $release = $c["cap_release"];
  $release = strtotime("{$release["month"]}/{$release["date"]}/{$release["year"]} {$release["time"]} {$release["meridiem"]}");
  $releaseDate = explode(" ", date('n j Y g i A', $release));

  $message = $c["cap_msg"];
  $vis = $c["vis"];
  $attachments = "";
}
?>

<div id="centerContent">
  <?php if($error != "") echo "<div class=\"ui-error\">{$error}</div>"; ?>
  <?php //if($result != "") echo "<div class=\"ui-success fade\">{$result}</div>"; ?>
  <?php if($result != "") { ?>
  <div id="timePromptSuccessPopup" style="text-align: center;">
   <img src="/source/templates/images/capsule.png" /><br />
   <div style="border: 10px solid #cce4ed;padding:0px 10px;margin:15px 125px;font-size: 25px;font-weight: bold;color:#464646">
    <?php echo strtoupper($result); ?>
  </div>
</div>
<script type="text/javascript">
  $("#timePromptSuccessPopup").dialog({
   autoOpen: true,
   height: "350",
   modal: true,
   resizable: false
 });
</script>
<?php } ?>
<div id="timePrompt">
  <div class="timePromptHeader">CREATE A TIME CAPSULE</div>
  <div id="timePromptForm">
    <form action="/capsule.php" method="post" enctype="multipart/form-data">
     <div>Capsule Title</div>
     <input type="text" name="p[title]" placeholder="200 max characters..." value="<?php echo $title; ?>" required/><br />
     <div style="margin-bottom: 20px;">
      Lock Day/Time &nbsp;
      <select name="p[lock][month]" required>
        <?php
        $months = Array("JAN","FEB","MAR","APR","MAY","JUN","JUL","AUG","SEP","OCT","NOV","DEC");
        foreach($months as $key=>$month) echo "<option value=\"".($key+1)."\"".(($key==$lockDate[0]-1)?" selected=\"selected\"":"").">{$month}</option>\n";
        ?>
      </select> &nbsp;
      <select name="p[lock][date]" required>
        <?php for($i=1;$i<32;$i++) echo "<option value=\"{$i}\"".(($i==$lockDate[1])?" selected=\"selected\"":"").">".str_pad($i, 2, "0", STR_PAD_LEFT)."</option>\n"; ?>
        </select> &nbsp;
        <select name="p[lock][year]" required>
          <?php $thisYear = date("Y"); for($i=$thisYear;$i<$thisYear+100;$i++) echo "<option value=\"{$i}\"".(($i==$lockDate[2])?" selected=\"selected\"":"").">{$i}</option>\n"; ?>
        </select> &nbsp;
        <div class="separator"></div> &nbsp;

        <select name="p[lock][time]" required>
          <?php
          for($i=1;$i<=12;$i++) {
           echo "<option value=\"".$i."\"".(($i==$lockDate[3] && $lockDate[4] < 30 && $lockDate[4] >= 15)?" selected=\"selected\"":"").">{$i}</option>\n";

         }
         ?>
       </select> &nbsp;

       :

       <select name="p[lock][time]" required>

        <?php
        for($j=0; $j<=59; $j++){

         if($j<10){
          echo "<option value=\"".$j."\"".(($i==$lockDate[3] && $lockDate[4] < 30 && $lockDate[4] >= 15)?" selected=\"selected\"":"").">0{$j}</option>\n";
        }
        else{
          echo "<option value=\"".$j."\"".((($i==$lockDate[3] && $lockDate[4] < 15))?" selected=\"selected\"":"").">{$j}</option>\n";
        }
      }
      ?>
    </select> &nbsp;

    <select name="p[lock][meridiem]"> required
     <option value="AM"<?php if($lockDate[5] == "AM") echo " selected=\"selected\""; ?>>AM</option>
     <option value="PM"<?php if($lockDate[5] == "PM") echo " selected=\"selected\""; ?>>PM</option>
   </select>
 </div>
 <div style="margin-bottom: 20px;">
  Release Day/Time &nbsp;
  <select name="p[release][month]" required>
    <?php foreach($months as $key=>$month) echo "<option value=\"".($key+1)."\"".(($key==$releaseDate[0]-1)?" selected=\"selected\"":"").">{$month}</option>\n"; ?>
    </select> &nbsp;
    <select name="p[release][date]">
      <?php for($i=1;$i<32;$i++) echo "<option value=\"{$i}\"".(($i==$releaseDate[1])?" selected=\"selected\"":"").">".str_pad($i, 2, "0", STR_PAD_LEFT)."</option>\n"; ?>
      </select> &nbsp;
      <select name="p[release][year]" required>
        <?php for($i=$thisYear;$i<$thisYear+100;$i++) echo "<option value=\"{$i}\"".(($i==$releaseDate[2])?" selected=\"selected\"":"").">{$i}</option>\n"; ?>
        </select> &nbsp;
        <div class="separator"></div> &nbsp;
        <select name="p[release][time]" required>
          <?php
          for($i=1;$i<=12;$i++) {
           echo "<option value=\"".$i."\"".(($i==$lockDate[3] && $lockDate[4] < 30 && $lockDate[4] >= 15)?" selected=\"selected\"":"").">{$i}</option>\n";

         }
         ?>
       </select> &nbsp;

       :

       <select name="p[lock][time]" required>

        <?php
        for($j=0; $j<=59; $j++){

         if($j<10){
          echo "<option value=\"".$j."\"".(($i==$lockDate[3] && $lockDate[4] < 30 && $lockDate[4] >= 15)?" selected=\"selected\"":"").">0{$j}</option>\n";
        }
        else{
          echo "<option value=\"".$j."\"".((($i==$lockDate[3] && $lockDate[4] < 15))?" selected=\"selected\"":"").">{$j}</option>\n";
        }
      }
      ?>
    </select> &nbsp;
    <select name="p[release][meridiem]">
     <option value="AM"<?php if($releaseDate[5] == "AM") echo " selected=\"selected\""; ?>>AM</option>
     <option value="PM"<?php if($releaseDate[5] == "PM") echo " selected=\"selected\""; ?>>PM</option>
   </select>
 </div>
 <div>Message</div>
 <?php
 $params = Array(
   "name"=>"p[message]",
   "placeholder"=>"Enter message here...",
   "text"=>$message
   );
 $theme->load("formatBar",$params,false);
 ?>
 <div style="margin-bottom:15px;">
  <input type="radio" name="p[vis]" value="1" id="Public"<?php if($vis == 1) echo " checked=\"checked\""; ?> /> <label for="Public">Public</label>
  <input type="radio" name="p[vis]" value="0" id="Private"<?php if($vis == 0) echo " checked=\"checked\""; ?> /> <label for="Private">Private</label>
</div>
<div id="attachmentsBlock"<?php if($attachments=="") echo ' style="display: none;"'; ?>>
  <div>Attachments</div>
  <div id="attachmentAppend" style="margin-bottom:10px;background-color:#fff;padding: 5px;">
    <?php
    $count = 0;
    if($attachments != "") {
     $attachments = explode(";",$attachments);
     foreach($attachments as $a) {
      $m = explode("|",$a);
      $type = substr($m[0], strrpos($m[0],".")+1);
      $icon = "file";
      switch($type) {
       case "png":
       case "jpg":
       case "jpeg":
       case "pjpg":
       case "pjpeg":
       $icon = "image";
       break;

       case "doc":
       case "docx":
       $icon = "doc";
       break;

       case "pdf":
       $icon = "pdf";
       break;

       default:
       $icon = "file";
       break;
     }
     echo '<span><img src="/source/templates/images/'.$icon.'.png" style="width:16px;height:16px;" /><a href="/uploads/'.$m[0].'" target="_blank">'.$m[1].'</a></span><br />';
     $count++;
   }
 }
 ?>
 <div id="attachmentBase" class="attachmentForm" style="position: relative;margin-bottom:10px;">
  <input style="position:relative;z-index:2;width:100%;height:25px;opacity:0;filter:alpha(opacity=0)" size="67" type="file" name="attachment0"  />
  <input type="file" name="attmnt" style="position:absolute;top:0;left:0px;width:400px;height:25px;text-indent:5px" id="attachment0" value="Upload Attachment" />
  <input type="button" style="position:absolute;top:0px;right:0px;width:80px" class="submit-button" value="Browse..." />
</div>
<div class="fake-link" style="margin-top: 10px;" id="addAttachment">(+) Add Another Attachment</div>
</div>
</div>
<div>Contacts</div>
<div id="contactsDrop"<?php
if(!is_null($contacts)) {
  echo ' style="color:#000;">';
  echo 'Drag';
  foreach($contacts as $cont) {
    echo '<span id="promptCT-'.$cont["user_id"].'"><img src="'.$manager->getAvatar($cont["user_avatar"],false).'" class="avatar32" /> '.$cont["user_first"].' '.$cont["user_last"].'</span>';
  }
} else {
  echo '>Drag and drop contacts here...';
}
?></div><input type="hidden" id="contactsDropField" name="p[to]" value="" />
<?php if($id != -1) echo '<input type="hidden" name="p[update]" value="'.$id.'" />'; ?>
<!-- if error style="-moz-box-shadow: inset 0 0px 8px rgba(255,0,0,1);-webkit-box-shadow: inset 0 0px 8px rgba(255,0,0,1);box-shadow: inset 0 0px 8px rgba(255,0,0,1); -->
<div><input class="submit-button" name="send" type="submit" value="SEND THIS CAPSULE"><?php if($data["allowDraft"]) echo '&nbsp;<input class="submit-button" name="draft" type="submit" value="SAVE DRAFT">'; ?></div>
</form>
</div>
</div>
</div>
<?php
$this->load("right_sidebar", @array_merge(Array("closeGroups"), is_array($data)?$data:Array()), FALSE);
$this->load("main_footer", $data, FALSE);
?>
