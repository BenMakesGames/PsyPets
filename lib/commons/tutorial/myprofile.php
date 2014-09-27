<?php
require_once 'commons/tutorial/css.php';
?>
<script type="text/javascript">
function tut_close()
{
  $('#tutorial').hide();
}
</script>
<div id="tutorial">
 <img src="gfx/shim.png" height="200" width="1" align="left" alt="" />
 <span id="tutorial_text">
  <p>This is your profile page!</p>
  <p>This is the page other players will see when they click on your name.  There are a few ways you can customize it, some of which you'll have to discover later, but you can make some basic changes from the <a href="/myaccount/profile.php">My Account &gt; Resident Profile</a> page.</p>
  <p>You can view other player's profiles by clicking their name.  When viewing another player's profile, you'll have some different options than you do when viewing your own... well, you'll understand when you try it.</p>
  <p>Players can comment on each other's profiles as well!  When you receive such a comment, an icon will appear next to your name on the top of the page, like this: <img src="/gfx/newcomment.png" width="16" height="16" />  Clicking on that icon will take you here, so you can look over them.</p>
  <p>If you want to change who's allowed to post comments on your profile, you can also do that from the <a href="/myaccount/profile.php">My Account &gt; Resident Profile</a> page.</p>
  <ul><li><a href="#" onclick="tut_close(); return false;">(end dialog)</a></li></ul>
 </span>
</div>
