<script type="text/javascript">Tc.contacts.initResponse(<? echo $data["userData"]["user_id"];?>);</script>
<?php
$this->load("main_header", $data);
$this->load("notifications_bar", Array(), FALSE);

$newUsr = $data["userData"];

$gender = ($newUsr["user_gender"]==1)?"Male":(($newUsr["user_gender"]==2)?"Female":"Unknown");

$year = substr($newUsr["user_age"],0,4);
$mon = substr($newUsr["user_age"],5,2);
$day = substr($newUsr["user_age"],8,2);

$age = date("Y")-$year-1;
$age += (date("m")>$mon || (date("m")==$mon && date("d")>=$day))?1:0;
?>
<div id="centerContent" class="contactsPage">
 <div id="userProfileContent">
  <div class="userProfileHeader"><?php echo $newUsr["user_first"].' '.$newUsr["user_middle"].' '.$newUsr["user_last"]; ?></div>
  <div class="capsuleBlock" id="mainDiv">
   <div id="leftFloater">
    <img src="<?php echo $manager->getAvatar($newUsr["user_avatar"], false); ?>" />
   </div>
   <div id="profileInfo"> 
    <div>Email: <?php echo $newUsr["user_email"]; ?></div>
    <div>Gender: <?php echo $gender; ?></div>
    <div>Age: <?php echo $age; ?></div>
   </div>
  </div>
  <div>
   <span id="acceptButton" class="fake-link">Accept</span> | <span id="ignoreButton" class="fake-link">Ignore</span>
  </div>
 </div>
</div>
<?php
$this->load("right_sidebar", @array_merge(Array("closeAll"), is_array($data)?$data:Array()), FALSE);
$this->load("main_footer", $data, FALSE);
?>