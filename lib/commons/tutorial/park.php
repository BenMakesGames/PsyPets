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
  <p>Hello!  Welcome to The Park.</p>
  <p>Other players host competitive events here, which you may sign your pets up for.  Your pets can win prizes, gain experience, and feel loved and esteemed by participating in park events.</p>
  <p>Due to various issues, you will not be able to sign up one of your pets for your first 24 hours of play.  And anyway, you should probably be saving your money to feed your pet during that time!</p>
  <p>You can host your own Park Events once you get a License to Commerce.  Ask at the Bank about the License to Commerce if you haven't already done so.  It's something you'll want to get ASAP.</p>
  <ul><li><a href="#" onclick="tut_close(); return false;">(end dialog)</a></li></ul>
 </span>
</div>
