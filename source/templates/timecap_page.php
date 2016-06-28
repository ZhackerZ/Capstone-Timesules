 <script type="text/javascript">Timesules.timecap.init('<?php echo $_GET["timesule"]; ?>');</script>
 <?php
 $this->load("main_header", $data);

 // use DebugBar\StandardDebugBar;
 // $debugbar = new StandardDebugBar();
 // $debugbarRenderer = $debugbar->getJavascriptRenderer();


 // $debugbar["messages"]->addMessage($com);
 // $debugbar["messages"]->addMessage($data["cap_result"]);

 ?>
 <div id="centerContent">
   <div id="timecapButtons">
    <form action="timecap.php" method="post" id="deleteForm">
     <input type="hidden" name="list" id="deleteField" value="" />
   </form>
<!--    <a href="/timecap.php?timesule=<?php //echo constant('MODE_SENT'); ?>"><button class="toggle-button<?php //if($_GET["timesule"]==MODE_SENT) echo " active"; ?>">SENT</button></a> -->
   <a href="/timecap.php?timesule=<?php echo constant('MODE_DRAFT'); ?>"><button class="toggle-button<?php if($_GET["timesule"]==MODE_DRAFT) echo " active"; ?>">DRAFTS</button></a>
   <?php
   if(!isset($_GET["timesule"]) || $_GET["timesule"] != MODE_DRAFT && $_GET["timesule"] != MODE_SENT) {
    ?>
    <button class="toggle-button<?php if($_GET["timesule"]==MODE_RELEASED) echo " active"; ?>" id="show-releaesd">RELEASED</button>
    <button class="toggle-button<?php if($_GET["timesule"]==MODE_LOCKED) echo " active"; ?>" id="show-locked">LOCKED</button>
<!--     <button class="toggle-button<?php //if($_GET["timesule"]==MODE_PENDING) echo " active"; ?>" id="show-pending">PENDING</button>
 -->    <button class="toggle-button<?php if($_GET["timesule"]==MODE_ALL) echo " active"; ?>" id="show-all">ALL</button>
    <?php } else { ?>
    <a href="/timecap.php?timesule=<?php echo constant('MODE_RELEASED'); ?>"><button class="toggle-button">RELEASED</button></a>
    <a href="/timecap.php?timesule=<?php echo constant('MODE_LOCKED'); ?>"><button class="toggle-button">LOCKED</button></a>
<!--     <a href="/timecap.php?timesule=<?php //echo constant('MODE_PENDING'); ?>"><button class="toggle-button">PENDING</button></a>
 -->    <a href="/timecap.php?timesule=<?php echo constant('MODE_ALL'); ?>"><button class="toggle-button">ALL</button></a>
    <?php } ?>
    <span><button class="trashcan" id="delete_send" title="Click on capsule blocks below to select them, then click the trashcan to delete."></button></span>
  </div>
  <div id="deleteStatus"></div>
  <div class="ui-notice" id="noCapsulesNotice"<?php if($data["count"] > 0) echo ' style="display:none;"'; ?>>No Capsules to view at this time.</div>
  <div id="timecapList">

    <?php //echo $debugbarRenderer->renderHead() ?>

    <?php
    echo $ob_get_contents;?>

<!--   <div class="timecapBlock locked">
   <div class="locked"><img src="/source/templates/images/capsule.png" /></div>
   <div class="timecapPrompt">Timesule 1</div>
   <div class="timecapInfo">From: Tyler Hadidon<br />Date: 07/10/12</div>
   <div class="timecapPreview">Lorem ipsum dolor sit amet, consectetur.</div>
   <div class="timecapLink"><span class="fake-link">See full capsule</span></div>
  </div>
  <div class="timecapBlock">
   <div class="timecapActive"></div>
   <div class="timecapPrompt">Timesule 2</div>
   <div class="timecapInfo">From: Tyler Hadidon<br />Date: 07/10/12</div>
   <div class="timecapPreview">Lorem ipsum dolor sit amet, consectetur.</div>
   <div class="timecapLink"><span class="fake-link">See full capsule</span></div>
  </div>
  <div class="timecapBlock">
   <div class="timecapPrompt">Timesule 3</div>
   <div class="timecapInfo">From: Tyler Hadidon<br />Date: 07/10/12</div>
   <div class="timecapPreview">Lorem ipsum dolor sit amet, consectetur.</div>
   <div class="timecapLink"><span class="fake-link">See full capsule</span></div>
  </div>

  <div class="timecapBlock">
   <div class="timecapPrompt">Timesule 4</div>
   <div class="timecapInfo">From: Tyler Hadidon<br />Date: 07/10/12</div>
   <div class="timecapPreview">Lorem ipsum dolor sit amet, consectetur.</div>
   <div class="timecapLink"><span class="fake-link">See full capsule</span></div>
  </div>
  <div class="timecapBlock">
   <div class="timecapPrompt">Timesule 5</div>
   <div class="timecapInfo">From: Tyler Hadidon<br />Date: 07/10/12</div>
   <div class="timecapPreview">Lorem ipsum dolor sit amet, consectetur.</div>
   <div class="timecapLink"><span class="fake-link">See full capsule</span></div>
  </div>
  <div class="timecapBlock">
   <div class="timecapPrompt">Timesule 6</div>
   <div class="timecapInfo">From: Tyler Hadidon<br />Date: 07/10/12</div>
   <div class="timecapPreview">Lorem ipsum dolor sit amet, consectetur.</div>
   <div class="timecapLink"><span class="fake-link">See full capsule</span></div>
  </div>

  <div class="timecapBlock">
   <div class="timecapPrompt">Timesule 7</div>
   <div class="timecapInfo">From: Tyler Hadidon<br />Date: 07/10/12</div>
   <div class="timecapPreview">Lorem ipsum dolor sit amet, consectetur.</div>
   <div class="timecapLink"><span class="fake-link">See full capsule</span></div>
  </div>
  <div class="timecapBlock">
   <div class="timecapPrompt">Timesule 8</div>
   <div class="timecapInfo">From: Tyler Hadidon<br />Date: 07/10/12</div>
   <div class="timecapPreview">Lorem ipsum dolor sit amet, consectetur.</div>
   <div class="timecapLink"><span class="fake-link">See full capsule</span></div>
 </div> -->

 <?php ?>

</div>
</div>
<?php
$this->load("main_footer", $data, FALSE);
?>
<?php //echo $debugbarRenderer->render() ?>
