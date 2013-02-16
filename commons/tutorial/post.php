<?php
require_once 'commons/tutorial/css.php';
?>
<script type="text/javascript">
function tut_close()
{
  document.getElementById('tutorial').style.display = 'none';
}
</script>
<div id="tutorial">
 <img src="gfx/shim.png" height="200" width="1" align="left" alt="" />
 <span id="tutorial_text">
  <p>Hello!  Welcome to your Mailbox!</p>
  <p>This is where you send and receive messages to talk with other players privately.  You can even have copies of messages sent to your e-mail address! (Weird!)</p>
  <p>Messages sent from the administrator are identified by this graphic: <img src="gfx/admintag.png" alt="" />.  Also remember that the administrator is super-powerful, and never needs your login name, password, e-mail address, or any other details besides your "Resident name" - <?= $user['display'] ?>.</p>
  <p>If you ever think someone's trying to scam you, or posing as an administrator, please report them to the <em>real</em> administator, using the "Administrators" link under the "Help" menu.</p>
  <ul><li><a href="#" onclick="tut_close(); return false;">(end dialog)</a></li></ul>
 </span>
</div>
