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
  <p>Ah, your sketchpad!  Feel free to draw whatever you like!</p>
  <p>If you get bored, or need inspiration, you can <a href="sketchvote.php">rate other people's pictures</a>.  Of course, you can also see how <em>other people</em> have rated <em>your</em> pictures!</p>
  <p>Finally, if you have a License to Commerce, you can create your own shop keeper graphic!  Click the "$" below one of your sketches to set that sketch as your shop keeper.</p>
  <ul><li><a href="#" onclick="tut_close(); return false;">(end dialog)</a></li></ul>
 </span>
</div>
