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
  <p>Welcome to The Plaza!</p>
  <p>"The Plaza" is what the forums are called around here, though I couldn't really tell you why...</p>
  <p>Anyway, people come here to talk about all kinds of things, <?= $SETTINGS['site_name'] ?>-related or not.</p>
  <p>Feel free to take a look around, and get familiar with the sections.  And remember: interacting with people on the Internet is not that different from doing so in real-life; all of the same rules apply.</p>
  <p>Be awesome, and have fun <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/emote/hee.gif" width="16" height="16" alt="[happy]" /></p>
  <ul><li><a href="#" onclick="tut_close(); return false;">(end dialog)</a></li></ul>
 </span>
</div>
