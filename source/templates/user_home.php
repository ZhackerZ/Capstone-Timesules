 <script type="text/javascript">Timesules.user_home.init(<?php echo $data["capsuleCount"]; ?>);</script>
<?php
$this->load("main_header", $data);
$this->load("notifications_bar", $data, FALSE);
?>
<div id="centerContent">
 <div id="newestReleasedTimesules">
  <div class="newestReleasedHeader">NEWEST RELEASED TIMESULES <a href="/index.php" id="newPosts"></a></div>
  <noscript>
   <div><?php
echo 'Capsules '.($start+1).'-'.($start+$capsuleCount+1);
if($prevPosts)
	echo ' <a href="/index.php?loadCapsules='.($start-$length+1).'">Previous Page</a>';
if($morePosts)
	echo ' <a href="/index.php?loadCapsules='.($start+$length-1).'">Next Page</a>';
?></div>
  </noscript>

<?php echo $ob_get_contents; ?>

  <noscript>
   <div><?php
echo 'Capsules '.($start+1).'-'.($start+$capsuleCount+1);
if($prevPosts)
	echo ' <a href="/index.php?loadCapsules='.($start-$length+1).'">Previous Page</a>';
if($morePosts)
	echo ' <a href="/index.php?loadCapsules='.($start+$length-1).'">Next Page</a>';
?></div>
  </noscript>
 </div>
 <div id="loadingCapsules"><div id="loadingCapsulesImg"></div><span id="loadingCapsulesText">Loading More Capsules...</span></div>
</div>
<?php
$this->load("right_sidebar", @array_merge(Array("closeAll"), is_array($data)?$data:Array()), FALSE);
$this->load("main_footer", $data, FALSE);
?>