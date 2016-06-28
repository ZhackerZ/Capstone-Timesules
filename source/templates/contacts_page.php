<?php

// $userContacts = $data["userContacts"];
$searchResults = $data["searchResults"];

// $userContacts = $sql->select("user_has_contacts", "*", "WHERE `user_id`='{$user->get("id")}' OR `contact_id`='{$user->get("id")}'");
// $userContacts = $sql->fetchAll();


// ----------------------------------------
// PRINT JSON
// ----------------------------------------
if(isset($_GET["ajaxCall"]) && $_GET["ajaxCall"]=="true") {
	header("Content-Type: text/json");
	echo '{"code":200,"contacts":[';
	$usrContsEnd = "";
	if(count($userContacts) > 0) {
		foreach($userContacts as $cont)
			$usrContsEnd .= '{"id":"'.$cont["user_id"].'","avatar":"'.$manager->getAvatar($cont["user_avatar"], false).'",'.
   '"email":"'.$manager->getSummary($cont["user_email"],20).'"'.
   ',"first":"'.$cont["user_first"].'","last":"'.$cont["user_last"].'"},';
 }
    // echo substr($usrContsEnd,0,-1).']}';

// var_dump($searchResults);

  // echo '{"code":200,"newContacts":[';
 echo substr($usrContsEnd,0,-1).'],"newContacts":[';
 $usrContsEnd = "";
 if(count($searchResults) > 0) {
  foreach($searchResults as $cont)
   $usrContsEnd .= '{"id":"'.$cont["id"].'","avatar":"'.$manager->getAvatar($cont["avatar"], false).'","email":"'.$manager->getSummary($cont["email"],20).'"'.',"first":"'.$cont["first"].'","last":"'.$cont["last"].'"},';
}
echo substr($usrContsEnd,0,-1).']}';

// var_dump($usrContsEnd,0,-1);

// ----------------------------------------
// PRINT HTML
// ----------------------------------------
} else {
  ?>
  <script type="text/javascript">Tc.contacts.init();</script>
  <?php
  $this->load("main_header", $data);
  ?>
  <div id="centerContent" class="contactsPage">
   <div id="contactsButtons">
    <!-- <button class="toggle-button disabled" id="addToCapsule" title="Click on capsule blocks below to select them, then click here to add them to a new prompt.">ADD TO CPASULE</button> -->
    <span><button class="trashcan" id="delete_send" title="Click on capsule blocks below to select them, then click the trashcan to delete."></button></span>
    <input style="margin-left: 10px;width: 200px;padding: 0px 5px;" type="text" id="searchBox" onkeyup="Tc.contacts.search();" placeholder="Search for contact..." />
    <button type="button" class="search-button" id="searchButton" onclick="Tc.contacts.search();"></button>
  </div>
  <div style="display:none;" class="ui-error" id="errorBlock">An error has occured. Please try searching again.</div>
  <?php if(isset($data["notFound"]) && $data["notFound"] === TRUE) echo '<div class="ui-error">You do not have a request for this user or the user does not exist. If this problem presists, please contact <a href="/support.php">Support</a>.</div>'; ?>
  <div id="contactsList">


   <?php

   //OBSOLETE

//    if(count($userContacts) > 0) {
//      foreach($userContacts as $c) {
//       echo <<<END
//       <div class="contactBlock" id="contactID-{$c["id"]}">
//        <div class="contactAvatar"><img src="{$manager->getAvatar($c["avatar"], false)}" /></div>
//        <div class="contactName">{$c["first"]} {$c["last"]}</div>
//        <div class="contactInfo">E-mail: <span title="{$c["email"]}">{$manager->getSummary($c["email"],20)}</span></div>
//      </div>
// END;
//    }
//  }
   ?>


 <!--   <div class="contactBlock">
 <div class="contactAvatar"><img src="/avatars/tmp-avatar1.png" /></div>
 <div class="contactName">Lauren Romano</div>
 <div class="contactInfo">Birthday: 07/10/12<br />Gender: Female<br />E-mail: romanola@muohio.edu</div>
</div>
<div class="contactBlock">
 <div class="contactAvatar"><img src="/avatars/tmp-avatar2.png" /></div>
 <div class="contactName">Tyler Hadidon</div>
 <div class="contactInfo">Birthday: 07/10/12<br />Gender: Male<br />E-mail: hadidotj@muohio.edu</div>
</div>
<div class="contactBlock">
 <div class="contactName">John Doe</div>
 <div class="contactInfo">Birthday: 07/10/12<br />Gender: Unknown<br />E-mail: someone@muohio.edu</div>
</div>
-->


</div>

<?php
// if(count($userContacts) <= 0)
// 	echo '<div class="ui-notice">No contacts found! Please try a different search term.</div>';
$showResults = (!is_null($searchResults) && count($searchResults)>0);
// <div id="searchContainer"'.((!$showResults)?' style="display:none;"':'').'>
echo '
<div id="searchContainer">
 <div id="contactsSeparator"></div>
 <div id="contactsSeparatorHeading">New Contacts</div>
 <button class="toggle-button disabled" id="requestButton" title="Click on capsule blocks below to select them, then click here to add them as a new contact.">REQUEST</button>
 <div style="display:none;" class="ui-error" id="requestError">An error has occured. Please try sending another request.</div>
 <div style="display:none;" class="ui-success" id="requestSent">Your request has been sent.</div>
 <div id="newContacts">';

// var_dump($searchResults);

//     if($showResults) {
//      foreach($searchResults as $c) {
//       echo
//       '<div class="contactBlock" id="contactID-{$c["id"]}">
//       <div class="contactAvatar"><img src="{$manager->getAvatar($c["avatar"], false)}" /></div>
//       <div class="contactName">{$c["first"]} {$c["last"]}</div>
//       <div class="contactInfo">E-mail: <span title="{$c["email"]}">{$manager->getSummary($c["email"],20)}</span></div>
//     </div>';
//   }
// }
echo "</div>";
/*  <div class="contactBlock">
<div class="contactAvatar"><img src="/avatars/tmp-avatar2.png" /></div>
<div class="contactName">Tyler Hadidon</div>
<div class="contactInfo">Birthday: 07/10/12<br />Gender: Male<br />E-mail: hadidotj@muohio.edu</div>
</div>
<div class="contactBlock">
 <div class="contactName">John Doe</div>
 <div class="contactInfo">Birthday: 07/10/12<br />Gender: Unknown<br />E-mail: someone@muohio.edu</div>
</div>*/
?>
</div>
</div>
<?php
$this->load("right_sidebar", @array_merge(Array("closeAll"), is_array($data)?$data:Array()), FALSE);
$this->load("main_footer", $data, FALSE);
} //<!-- End else for if($_GET["ajaxCall"]=="true") -->
?>
