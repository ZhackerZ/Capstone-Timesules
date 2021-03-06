<script type="text/javascript">Timesules.prompt.init();</script>
<?php

$this->load("main_header", $data);
date_default_timezone_set ( "America/New_York" );

// use DebugBar\StandardDebugBar;
// $debugbar = new StandardDebugBar();
// $debugbarRenderer = $debugbar->getJavascriptRenderer();


if(isset($_SESSION["draft"]) && $_SESSION["draft"]){
$draft = true;
}

// Setup default values
$id = -1;
$title = "";

$lockDate = "";
$releaseDate = "";

$message = "";
$vis = 1;

$contacts = $data["toList"];
$error = $data["error"];
$result = $data["result"];

$attachments = "";

//POST
$capsule = $data["capsule"];

//
$c = $data["cap_result"];

// $debugbar["messages"]->addMessage($capsule);
// $debugbar["messages"]->addMessage($c);
// $debugbar["messages"]->addMessage(date("Y-m-d H:i:s", time()));



if(!is_null($capsule) && $capsule === FALSE) {

$error = '<strong>Error:</strong> The time capsule requested is not able to be edited or could not be found.';

} else if(!is_null($capsule)) {

	$id = $capsule["cap_id"];
	$title = $capsule["cap_title"];

	// $lockDate = explode(" ", date('n j Y g i A', $capsule["cap_lock"]));
	// $releaseDate = explode(" ", date('n j Y g i A', $capsule["cap_release"]));

$lockDate = $capsule["cap_lock"];
$releaseDate = $capsule["cap_release"];

$message = $capsule["cap_msg"];
$vis = $capsule["cap_vis"];

//$attachments = $data["p"]["post_attachments"];

} else if(!is_null($c)) {

	if (isset($c["update"])){
	$id = $c["update"];
}

$title = $c["cap_title"];

$lockDate = $c["cap_lock"];
// $lock = strtotime("{$lock["month"]}/{$lock["date"]}/{$lock["year"]} {$lock["time"]} {$lock["meridiem"]}");
//$lockDate = explode(" ", date('n j Y g i A', $lock));

	// Convert the releaseDate back to Eastern time from Europe/London time
$tempDate = $c["cap_release"];
$date = date_create($tempDate);
date_sub($date, date_interval_create_from_date_string('5 hours'));
$releaseDate = $date->format('Y-m-d H:i:s');
//$release = strtotime("{$release["month"]}/{$release["date	"]}/{$release["year"]} {$release["time"]} {$release["meridiem"]}");
//$releaseDate = explode(" ", date('n j Y g i A', $release));

$message = $c["cap_msg"];
$vis = $c["cap_vis"];
$attachments = "";
}
?>
<?php //echo $debugbarRenderer->renderHead() ?>

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
	<input type="text" name="capsule[title]" placeholder="200 max characters..." value="<?php echo $title; ?>" required/><br />

	<div style="margin-bottom: 20px;">
	<!--       Lock Day/Time &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
<!--     <input type="datetime-local" name="capsule[cap_lock]" value="<?php //echo date('Y-m-d H:i:s', time());?>" hidden>
--></div>

<div style="margin-bottom: 20px;">
Release Day/Time &nbsp; <?php echo $draft;  /*draft variable test*/ ?>
<input type="datetime-local" name="capsule[cap_release]" value="<?php if($draft){echo date('Y-m-d\TH:i:s', /*strtotime("now")*/strtotime($releaseDate));} ?>" required>
</div>

<div>Message</div>
<?php
$capsulearams = Array(
"name"=>"capsule[cap_msg]",
"placeholder"=>"Enter message here...",
"text"=>$message
);

	//tiny_mce
$theme->load("formatBar",$capsulearams,false);
?>

<div style="margin-bottom:15px;">
<input type="radio" name="capsule[cap_vis]" value="1" id="Public"<?php if($vis == 1) echo " checked=\"checked\""; ?> /> <label for="Public">Public</label>
<input type="radio" name="capsule[cap_vis]" value="0" id="Private"<?php if($vis == 0) echo " checked=\"checked\""; ?> /> <label for="Private">Private</label>
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

	<div id="attachmentBase" class="attachmentForm" style="position: relative;margin-bottom:5px;">
	<!-- <input style="position:relative;z-index:2;width:100%;height:25px;opacity:0;filter:alpha(opacity=0)" size="67" type="file" name="attachment0"  /> -->
	<input type="file" name="attachment[]" style="position:relative;top:0;left:0px;width:400px;height:25px;text-indent:5px" id="attachment0"/>
	<!-- <input type="button" style="position:absolute;top:0px;right:0px;width:80px" class="submit-button" value="Browse..." /> -->
	</div>

	<div class="fake-link" style="margin-top: 20px;" id="addAttachment">(+) Add Another Attachment</div>
</div>
</div>

<div>Recipients</div>

<div id="contactsDrop"<?php if(!is_null($contacts)) { echo ' style="color:#000;"' ?>>
<?php
	// echo 'Drag';
foreach($contacts as $cont) {
	echo '<span id="promptCT-'.$cont["user_id"].'"><img src="'.$manager->getAvatar($cont["user_avatar"],false).'" class="avatar32" /> '.$cont["user_first"].' '.$cont["user_last"].'</span>';
}
} else {
echo '>Drag and drop contacts here...';
}
?>
</div>
<input type="hidden" id="contactsDropField" name="capsule[cap_to]" value="" />
<?php if($id != -1) echo '<input type="hidden" name="capsule[update]" value="'.$id.'" />'; ?>
<!-- if error style="-moz-box-shadow: inset 0 0px 8px rgba(255,0,0,1);-webkit-box-shadow: inset 0 0px 8px rgba(255,0,0,1);box-shadow: inset 0 0px 8px rgba(255,0,0,1); -->
<div>
<input class="submit-button" name="send" type="submit" value="SEND THIS CAPSULE"><?php if($data["allowDraft"]) echo '&nbsp;<input class="submit-button" name="draft" type="submit" value="SAVE DRAFT">'; ?>
</div>
</form>
</div>
</div>
</div>

<?php
$this->load("right_sidebar", @array_merge(Array("closeGroups"), is_array($data) ? $data : Array()), FALSE);

$this->load("main_footer", $data, FALSE);

?>
<?php //echo $debugbarRenderer->render() ?>