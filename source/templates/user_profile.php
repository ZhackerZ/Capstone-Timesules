<script type="text/javascript">Timesules.user_profile.init(<?php echo $data["capsuleCount"].', '.$data["user_id"]; ?>);</script>
<?php
$this->load("main_header", Array("title"=>$data["user_first"].' '.$data["user_last"]));
$this->load("notifications_bar", Array(), FALSE);
$gender = ($data["user_gender"]==1)?"Male":(($data["user_gender"]==2)?"Female":"Unknown");

$year = substr($data["user_age"],0,4);
$mon = substr($data["user_age"],5,2);
$day = substr($data["user_age"],8,2);

$age = date("Y")-$year-1;
$age += (date("m")>$mon || (date("m")==$mon && date("d")>=$day))?1:0;
?>
<div id="centerContent">
  <?php if($data["nowContacts"] === TRUE) echo '<div class="ui-success" id="nowContacts">You are now contacts with '.$data["user_first"].' '.$data["user_last"].'!</div>'; ?>
  <div id="userProfileContent">
    <div class="userProfileHeader"><?php echo $data["user_first"].' '.$data["user_middle"].' '.$data["user_last"]; ?></div>
    <div class="capsuleBlock" id="mainDiv">
     <div id="leftFloater">
      <img src="<?php echo $manager->getAvatar($data["user_id"], false); ?>" />
    </div>
    <div id="profileInfo">
      <div>Email: <?php echo $data["user_email"]; ?></div>
      <div>Gender: <?php echo $gender; ?></div>
      <div>Age: <?php echo $age; ?></div>
    </div>
  </div>
  <?php
  if($data["id"]!=$user->get("id")){ ?>
  <div class="userProfileHeader">CURRENT CAPSULES</div>
  <?php
  if($data["isContact"] ) {
    echo $ob_get_contents; ?>
    <div id="loadingCapsules"><div id="loadingCapsulesImg"></div><span id="loadingCapsulesText">Loading More Capsules...</span></div>
    <?php } else { ?>
    <div class="capsuleBlock">You are not able to view prompts unless you are contacts.</div>
    <?php }
  } ?>
</div>
</div>
<?php
$this->load("right_sidebar", Array("closeAll"), FALSE);
$this->load("main_footer", Array(), FALSE);
?>
