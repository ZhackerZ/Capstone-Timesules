<?php
global $status;
$this->load("main_header", $data);

//Comment added to get GitHub to pull changes correctly

// Get some variables
$usr = $user->get("*");
// Generate list of genders and select current gender
$genders = "";
foreach(Array("------","Male","Female") as $name=>$text)
	$genders .= "     <option value='{$name}'".(($usr["user_gender"]==$name)?" selected='selected'":"").">{$text}</option>\n";
$genders = substr($genders, 0, -1);

// Generate the $birthday
$birthDate = explode("-", $usr["user_age"]);
$birthday = '<select name="update[bdayMonth]">';
$months = Array("JAN","FEB","MAR","APR","MAY","JUN","JUL","AUG","SEP","OCT","NOV","DEC"); foreach($months as $key=>$month) $birthday .= "<option value=\"".str_pad($key+1, 2, "0", STR_PAD_LEFT)."\"".(($key+1==$birthDate[1])?' selected="selected"':'').">{$month}</option>\n";
$birthday .= '</select> <select name="update[bdayDate]">';
for($i=1;$i<32;$i++) $birthday .= "<option value=\"".str_pad($i, 2, "0", STR_PAD_LEFT)."\"".(($i==$birthDate[2])?' selected="selected"':'').">".str_pad($i, 2, "0", STR_PAD_LEFT)."</option>\n";
$birthday .= '</select> <select name="update[bdayYear]">';
$thisYear = date("Y"); for($i=$thisYear;$i>$thisYear-100;$i--) $birthday .= "<option value=\"{$i}\"".(($i==$birthDate[0])?' selected="selected"':'').">{$i}</option>\n";
$birthday .= '</select>';
?>
<div id="centerContent">
<?php echo $status; ?>
 <div id="settingsPage">
  <form action="/settings.php" method="post" enctype="multipart/form-data">
  <div class="settingsHeader">Account Settings</div>
  <div class="settingsForm">
   <div><span class="req">*</span>Email: <input type="text" name="update[email]" value="<?php echo $usr["user_email"]; ?>" /></div>
   <div style="position: relative;margin-bottom: 10px;">
    <img src="<?php echo $manager->getAvatar($user->get("id"), false); ?>" width="32" height="32" />
    <input style="position:relative;top:-10px;z-index:2;width:255px;height:35x;opacity:0;filter:alpha(opacity=0)" size="27" type="file" name="avatarUL" onchange="$('#avatarUpload').val(this.value.split('\\').pop())" />
    <input type="text" style="width:180px;position:absolute;top:0x;right:75px;" id="avatarUpload" value="Current Avatar" />
    <input type="button" class="submit-button" value="Browse..." style="position:absolute;top:0;right:0px" /><br />
    <div style="position:absolute;right:0px;width:255px;text-align:left;">(jpg,jpeg,png,gif 30kb) scaled to 32x32
    <div style="height: 5px;"></div>
    <input type="checkbox" name="update[removeAvatar]" value="removeAvatar" /> Remove Avatar</div>
    <div style="height: 40px;"></div>
   </div>
   <div>New Password: <input type="password" name="update[newPW]" size="50" /></div>
   <div>Confirm Password: <input type="password" name="update[confPW]" size="50" /></div>
   <div>Current Password: <input type="password" name="update[currentPW]" size="50" /></div>
   <div style="font-size: 10px;margin-top:-10px;">Only required if chaning your password</div>
   <div style="text-align: right"><input type="submit" class="submit-button" value="Update" /></div>
  </div>
  <div class="settingsHeaderSep"></div>
  <div class="settingsHeader">Profile Settings</div>
  <div class="settingsForm">
   <div><span class="req">*</span>First Name: <input type="text" name="update[first]" value="<?php echo $usr["user_first"]; ?>" /></div>
   <div>Middle Name: <input type="text" name="update[middle]" value="<?php echo $usr["user_middle"]; ?>" /></div>
   <div><span class="req">*</span>Last Name: <input type="text" name="update[last]" value="<?php echo $usr["user_last"]; ?>" /></div>
   <div style="margin-left: 60px;margin-bottom: 10px;text-align:left;"><span class="req">*</span>Birthday: <?php echo $birthday; ?></div>
   <div style="margin-left: 71px;margin-bottom: 10px;text-align:left;">Gender: <select name="update[gender]"><?php echo $genders; ?></select></div>
   <div style="text-align: right"><input type="submit" class="submit-button" value="Update" /></div>
  </div>
  <div class="settingsHeaderSep"></div>
  <div class="settingsHeader">Email Preferences</div>
  <div class="settingsForm email-prefs" style="text-align: left">
<?php
$prefs = Array(Manager::NEW_CONTACT,Manager::RELEASED_CAPSULE,Manager::ADDED_TO_GROUP,Manager::NEW_GROUP_CAPSULE,Manager::RELEASED_GROUP);
$names = Array("New Contact","Capsule Released","Added to Group","New Group Capsule","Group Capsule Released");
$prefList = "";
$count=0;
foreach($prefs as $pref) {
	$prefList .= '   <div style="margin-bottom: 10px;"><input type="checkbox" name="pref['.($pref-1).']"'.(($usr["prefs"][$pref-1]==1)?' checked="checked"':"").' /> '.$names[$count++].'</div>'."\n";
}
echo $prefList;
?>
   <div style="text-align: right"><input type="submit" class="submit-button" value="Update" /></div>
  </div>
  </form>
 </div>
</div>
<?php
$this->load("right_sidebar", @array_merge(Array("closeGroups"), is_array($data)?$data:Array()), FALSE);
$this->load("main_footer", $data, FALSE);
?>
