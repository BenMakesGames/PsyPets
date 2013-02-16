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
  <p>Ah, the City Hall!</p>
  <p>This is where announcements about latest game developments are posted.  When a new announcement is made, you will see an icon next to your name on the top of the page, like this: <img src="gfx/newpost.gif" height="16" width="16" alt="" />  Clicking on it takes you to the City Hall, so you can read the latest announcement.</p>
  <p>There's a lot of other stuff to look at here, too: near the top are a couple polls you can participate in, and on the right is a list of new members, updates to the <?= $SETTINGS['site_name'] ?> wiki, and the latest entries in the changelog.</p>
  <ul><li><a href="#" onclick="tut_close(); return false;">(end dialog)</a></li></ul>
 </span>
</div>
