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
  <p>Incoming is where you'll find new items you've received!</p>
  <p>Whether you buy an item, trade with another player for one, or are sent one by another player, incoming is where you'll find it!</p>
  <p>There's not a lot you can do with an item in Incoming, though, so it's usually best to get stuff out of it, and into your house, or storage.</p>
  <p>Keep in mind: Incoming is part of Storage, and if you have a lot of stuff in Storage, you may have to pay fees every 24 hours!  So if you don't have room in your house, or just don't need an item, consider Gameselling it for a little money.</p>
  <ul><li><a href="#" onclick="tut_close(); return false;">(end dialog)</a></li></ul>
 </span>
</div>
