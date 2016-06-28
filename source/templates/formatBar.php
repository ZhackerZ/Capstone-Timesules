<?php
$params = "";
foreach($data as $key=>$val) {
	if($key != "text")
		$params .= " {$key}=\"{$val}\"";
}

$fonts = "Andale Mono|Arial|Arial Black|Book Antiqua|Comic Sans MS|Courier New|Georgia|Helvetica|Impact|Symbol|Tahoma|Terminal|Times New Roman|Trebuchet MS|Verdana|Webdings|Wingdings";
$fonts = explode("|",$fonts);
$sizes = "8pt|10pt|12pt|14pt|18pt|24pt|36pt";
$sizes = explode("|",$sizes);
?>
<script type="text/javascript" src="/source/tiny_mce/jquery.tinymce.js"></script>
<script type="text/javascript" src="/source/templates/js/jquery-colorpicker.js"></script>
<div id="formatBar">
 <div class="formatButton" id="exec|Bold" title="Bold text" style="font-weight: bold;">B</div>
 <div class="formatButton" id="exec|Italic" title="Italicize text" style="font-style: italic;">I</div>
 <div class="formatButton" id="exec|Underline"  title="Underline text" style="text-decoration: underline;">U</div>
 <div class="formatSeparator">&nbsp;</div>
 <div id="colorpicker" style="background-color:#000;width:20px;height:20px;" title="Change font color">&nbsp;</div>
 <select id="exec|FontName" class="no-style formatSelect" title="Change font family">
<?php
foreach($fonts as $font) {
	echo "  <option value=\"{$font}\"".(($font=="Verdana")?" selected=\"selected\"":"").">{$font}</option>\n";
}
?>
 </select> 
 <select id="exec|FontSize" class="no-style formatSelect" title="Change Font Size">
<?php
foreach($sizes as $size) {
	echo "  <option value=\"{$size}\"".(($size=="8pt")?" selected=\"selected\"":"").">{$size}</option>\n";
}
?>
 </select>
 <div class="formatSeparator">&nbsp;</div>
 <div class="formatButton" id="attachment|file"><div class="paperclip">&nbsp;</div></div>
 <div class="formatButton" style="display:none;" id="media|photo"><div class="camera">&nbsp;</div></div>
 <!-- <div class="formatButton" style="display:none;" id="media|video"><div class="camcorder">&nbsp;</div></div> -->
<!-- <span id="viewMore" style="font-size: 10px;"><img src="/source/templates/images/account-arrow.png" /> View more...</span> -->
</div>
<textarea id="formatInput"<?php echo $params.'>'.$data["text"];?></textarea>
<div id="formattingLoading">Loading...</div>
<div id="mediaInput">
 <p id="acceptMessage">
  Timesules needs your permission to use your camera. Click Accept above to start your camera.<br /><br />
  <img src="/source/templates/images/record-prompt.png" />
 </p>
 <canvas style="display:none;" id="mediaCanvas"></canvas>
 <video autoplay id="mediaVideo" style="width: 300px;height:225px;border: 1px solid #000;"></video>
 <button id="shot-button" class="submit-button">Take Photo</button>
 <img src="" id="mediaSample" style="width: 300px;height:225px;border: 1px solid #000;" />
 <button id="clear-button" class="submit-button">Clear Photo</button>
 <input type="hidden" name="screenShot" id="mediaData" value= "" />
</div>
<script type="text/javascript">
var attachmentCount = 0;
navigator.getUserMedia = navigator.getUserMedia ||
	navigator.webkitGetUserMedia ||
	navigator.webkitGetUserMedia ||
	navigator.mozGetUserMedia ||
	navigator.msGetUserMedia;
if(navigator.getUserMedia) $("[id^='media']").show();

$('#formatInput').tinymce({
	script_url : '/source/tiny_mce/tiny_mce.js',
	theme : "advanced",
	theme_advanced_toolbar_location:"none",
	theme_advanced_statusbar_location:"none"
});
$(".formatButton").click(function() {
	var id = this.id.split("|");
	switch(id[0]) {
	case "exec":
		$('#formatInput').tinymce().execCommand(id[1]);
		break;
	case "attachment":
		$("#attachmentsBlock").show();
		window.scrollTo(0,$("#attachmentsBlock").position().top-200);
		break;
	case "media":
		$("#mediaInput").dialog("open");
		break;
	default:
		console.error("Format button not implimented: ",id);
		break;
	}
});
$("#colorpicker").ColorPicker({
	color:'#000000',
	onChange:function (hsb, hex, rgb) {
		$('#formatInput').tinymce().execCommand('forecolor',false,'#'+hex);
		$("#colorpicker").css({backgroundColor:"#"+hex});
	}
});
$(".formatSelect").change(function() {
	var cmd = this.id.split('|');
	$('#formatInput').tinymce().execCommand(cmd[1],false,this.value);
});
$(document).ready(function() {
	$("#addAttachment").click(function() {
		attachmentCount++;
		var myCount = attachmentCount;
		var newGuy = $("#attachmentBase").clone();
		newGuy.attr("id","");
		$("[type='file']",newGuy).attr("name","attachment[]")
			.change(function() {
				$("#attachment[]").val($(this).val());
			});
		$("[type='text']",newGuy).attr("id","attachment[]");
		$("#attachmentAppend").prepend(newGuy);
	});
	$("[type='file']",$("#attachmentBase")).change(function() {
		$("#attachment[]").val($(this).val());
	});
});
$("#mediaInput").dialog({
	autoOpen: false,
	draggable: false,
	modal: true,
	resizable: false,
	width:1024,
	open: function(event,ui) {
		navigator.getUserMedia({audio:true,video:true},function(stream) {
			$("#acceptMessage").remove();
			$("#mediaVideo")
				.attr("src", window.URL.createObjectURL(stream))
				.data("stream",stream);
		},function(e) { console.log(e); });
	},
	close: function(event,ui) {
		$(this).parent().prependTo($("#formatDialogAppend"));
		$("#mediaVideo").data("stream").stop();
	}
});
$("#mediaVideo").bind("loadedmetadata",function() {
	var me = $(this);
	$("#mediaCanvas").css({width:me.width(),height:me.height()});
});
$("#mediaCanvas").css("display","none");
$("#shot-button").click(function() {
	$("#mediaCanvas").get(0).getContext("2d").drawImage($("#mediaVideo").get(0),0,0,300,150);
	$("#mediaSample").attr("src", $("#mediaCanvas").get(0).toDataURL('image/png'));
	$("#mediaData").val($("#mediaSample").attr("src"));
});
$("#clear-button").click(function() {
	console.log("Going");
	$("#mediaCanvas").get(0).getContext("2d").clearRect(0,0,300,150);
	$("#mediaSample").attr("src", 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==');
	$("#mediaData").val('');
});
</script>