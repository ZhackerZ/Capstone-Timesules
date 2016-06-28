<?php
if(!is_array($data))
	$data = Array();
$startClosed = in_array("closeAll",$data,true);
$closeContacts = in_array("closeContacts",$data,true);
$closeGroups = in_array("closeGroups",$data,true);
$hideContacts = in_array("hideContacts",$data,true);
$hideGroups = in_array("hideGroups",$data,true);

//contact sidebar

$contactsHTML = '<div class="sidebarGroup">
<div class="sidebarHeader'.(($closeContacts||$startClosed)?' hidden':'').'" id="contactsSideBar">Contacts</div>
<div class="sideContentWrapper'.(($closeContacts||$startClosed)?' hidden':'').'">
  <div class="sideContent">
    <div>';

     $contacts = $sql->select("user_has_contacts", "*", "WHERE `user_id`='{$user->get("id")}' OR `contact_id`='{$user->get("id")}'");
     $contacts = $sql->fetchAll();

     $contact_ids = array();

     if(count($contacts) > 0) {
      foreach($contacts as $cont) {
        $id = null; $contact = null;
        if($cont["user_id"]==$user->get("id")){
          $id = $cont["contact_id"];
          if(!in_array($id, $contact_ids)){
            array_push($contact_ids, $id);
          }
        }else{
          $id = $cont["user_id"];
          if(!in_array($id, $contact_ids)){
            array_push($contact_ids, $id);
          }
        }
      }
    }

    $contactsHTML .= '<div id="rsContactID-'.$user->get('id').'"><img src="'.$manager->getAvatar($user->get("id"), false).'" class="rsContact" /> <a href="/profile.php?user='.$user->get("id").'">'.$user->get("first").' '.$user->get("last").'</a></div>';

    foreach($contact_ids as $id){
      $contact = $sql->select("users", "user_first, user_last", "WHERE `user_id`='{$id}'");
      $contact = $sql->fetch();

      $contactsHTML .= '   <div id="rsContactID-'.$id.'"><img src="'.$manager->getAvatar($id, false).'" class="rsContact" /> <a href="/profile.php?user='.$id.'">'.$contact["user_first"].' '.$contact["user_last"].'</a></div>'."\n";
    }
    $contactsHTML .= '  </div>
  </div>
</div>
</div>';

//group sidebar

$groupsHTML = '<div class="sidebarGroup">
<div class="sidebarHeader'.(($closeGroups||$startClosed)?' hidden':'').'" id="groupsSideBar">Groups</div>
<div class="sideContentWrapper'.(($closeGroups||$startClosed)?' hidden':'').'">
  <div class="sideContent">
   <div>
    ';
    $userGroups = $sql->select("user_owns_groups", "*", "WHERE `user_id`='{$user->get("id")}'");
    $userGroups = $sql->fetchAll();

    $allGroupIds = array();
    foreach ($userGroups as $userGroup) {
      array_push($allGroupIds, $userGroup[group_id]);
    }

    $groups = array();
    foreach ($allGroupIds as $allGroupId) {
      $group = $sql->select("groups", "*", "WHERE `group_id`='$allGroupId'");
      $group = $sql->fetch();
      array_push($groups, $group);
    }

    if(sizeof($groups) > 0) {
      foreach($groups as $group) {
       $groupsHTML .= '   <div><img src="'.$manager->getGroupImage($group["group_id"], false).'" /> <a href="/groups.php?g='.$group["group_id"].'">'.$group["group_name"].'</a></div>'."\n";
     }
   }
   $groupsHTML .= '   </div>
 </div>
</div>
</div>';

// Print out the order
echo '<div class="rightBar">
';
if(in_array("reverse", $data, true)) {
	if(!$hideGroups) echo $groupsHTML;
	if(!$hideContacts) echo $contactsHTML;
} else {
	if(!$hideContacts) echo $contactsHTML;
	if(!$hideGroups) echo $groupsHTML;
}
echo '</div>
';
?>
