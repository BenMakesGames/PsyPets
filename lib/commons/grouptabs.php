     <ul class="tabbed">
      <li<?= $activetab == 'profile' ? ' class="activetab"' : '' ?>><a href="grouppage.php?id=<?= $groupid ?>">Profile</a></li>
      <li<?= $activetab == 'town' ? ' class="activetab"' : '' ?>><a href="grouptown.php?id=<?= $groupid ?>">Town</a></li>
      <li<?= $activetab == 'box' ? ' class="activetab"' : '' ?>><a href="groupbox.php?id=<?= $groupid ?>"><?= $ITEM_BOX ?></a></li>
      <li<?= $activetab == 'forum' ? ' class="activetab"' : '' ?>><a href="viewplaza.php?plaza=<?= $group['forumid'] ?>">Forum</a></li>
<?php
if($a_member)
  echo '<li' . ($activetab == 'rights' ? ' class="activetab"' : '') . '><a href="grouprights.php?id=' . $groupid . '">Member&nbsp;Rights</a></li>' . "\n";

if(rank_has_right($ranks, $rankid, 'memberadd') || $group['leaderid'] == $user['idnum'])
  echo '<li' . ($activetab == 'invite' ? ' class="activetab"' : '') . '><a href="groupinvite.php?id=' . $groupid . '">Send&nbsp;Invitation</a></li>' . "\n";

if(rank_has_right($ranks, $rankid, 'groupmail') || $group['leaderid'] == $user['idnum'])
  echo '<li' . ($activetab == 'groupmail' ? ' class="activetab"' : '') . '><a href="groupmail.php?id=' . $groupid . '">Send&nbsp;Announcement</a></li>' . "\n";

if(rank_has_right($ranks, $rankid, 'treasurer') || $group['leaderid'] == $user['idnum'])
  echo '<li' . ($activetab == 'groupcurrency' ? ' class="activetab"' : '') . '><a href="groupcurrency_residents.php?id=' . $groupid . '">Manage&nbsp;Currencies</a></li>' . "\n";

if(rank_has_right($ranks, $rankid, 'editprofile') || $group['leaderid'] == $user['idnum'])
  echo '<li' . ($activetab == 'edit' ? ' class="activetab"' : '') . '><a href="editgroup.php?id=' . $groupid . '">Edit&nbsp;Profile</a></li>' . "\n";

if($group['leaderid'] == $user['idnum'])
  echo '<li' . ($activetab == 'resign' ? ' class="activetab"' : '') . '><a href="changeleader.php?id=' . $groupid . '">Resign</a></li>' . "\n";
?>
     </ul>
