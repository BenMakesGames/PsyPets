     <ul style="list-style: none;">
      <li><a href="/writemail.php?sendto=<?= urlencode($profile_user['display']) ?>"><img src="gfx/sendmail.gif" alt="" border="0" /> Send mail</a></li>
<?php
  if($profile_user['license'] == 'yes')
  {
?>
      <li><a href="/newtrade.php?user=<?= link_safe($profile_user['display']) ?>"><img src="gfx/dotrade.gif" width="16" height="16" border="0" /> Initiate Trade</a></li>
<?php
  }
?>
      <li>
       <form action="residentprofile.php?resident=<?= link_safe($profile_user['display']) ?>" method="post" name="buddyform" id="buddyform">
<?php
  if(is_friend($user, $profile_user))
  {
?>
       <input type="hidden" name="action" value="rembuddy" height="16" width="16" />
       <input type="image" src="gfx/remove_buddy.gif" alt="Remove from Friend List" /> <a href="javascript:document.buddyform.submit();">Remove from Friend List</a>
<?php
  }
  else
  {
?>
       <input type="hidden" name="action" value="addbuddy" height="16" width="16" />
       <input type="image" src="gfx/add_buddy.gif" alt="Add to Friend List"> <a href="javascript:document.buddyform.submit();">Add to Friend List</a>
<?php
  }
?>
       </form>
      </li>
      <li>
       <form action="residentprofile.php?resident=<?= $profile_user['display'] ?>" method="post" name="ignoreform" id="ignoreform">
<?php
  if(is_enemy($user, $profile_user))
  {
?>
       <input type="hidden" name="action" value="remignore" />
       <input type="image" src="gfx/rem_ignore.gif" height="16" width="16" alt="Remove from Ignore List" /> <a href="javascript:document.ignoreform.submit();">Remove from Ignore List</a>
<?php
  }
  else
  {
?>
       <input type="hidden" name="action" value="addignore" />
       <input type="image" src="gfx/add_ignore.gif" height="16" width="16" alt="Add to Ignore List" /> <a href="javascript:document.ignoreform.submit();">Add to Ignore List</a>
<?php
  }
?>
       </form>
      </li>
<?php
if($give_worst_idea_ever_badge == 'yes')
  echo '<li><a href="/givesolubadge.php?resident=' . link_safe($profile_user['display']) . '"><img src="gfx/givesolubadge.png" width="16" height="16" alt="" border="0" /> Love on this Resident</a></li>';
else if($give_worst_idea_ever_badge == 'no')
  echo '<li class="dim"><img src="gfx/nogivesolubadge.png" width="16" height="16" alt="" border="0" /> Your account is not old enough to love on this Resident</a></li>';
?>
     </ul>
