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
  <p>Oh, nice!  It looks like your pet is ready to self-actualize, or, if you prefer, "level up"!</p>
  <p>When a pet gains enough experience points, it will improve its skills.  You can direct this improvement by answering the questions presented here.</p>
  <p>Pets gain experience for a variety of things: catching fish (even failing to catch fish), getting through a <a href="challenge.php">Daily Adventure challenge</a>, performing well in a <a href="park.php">Park event</a>, and a few other things.</p>
  <p>Just remember: a pet that's starving, scared, or otherwise "in the red" never gains experience points!</p>
  <ul><li><a href="#" onclick="tut_close(); return false;">(end dialog)</a></li></ul>
 </span>
</div>
