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
  <p>Oh, this is your Notepad.</p>
  <p>You can use it to keep notes on - well - whatever you like!</p>
  <p>For example, if you're thinking about sketching down notes about your pets on paper, you might want to type it up here, instead.  That way it will be available no matter where you go!</p>
  <p>Well, it's ultimately up to you how you use it.</p>
  <p>One last thing: all notes are private.  Other players cannot view them.</p>
  <ul><li><a href="#" onclick="tut_close(); return false;">(end dialog)</a></li></ul>
 </span>
</div>
