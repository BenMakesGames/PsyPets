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
  <p>Hi again!</p>
  <p>From this page you can add or remove "rooms" of your house.</p>
  <p>Rooms don't actually add space to your house.  Rather, they give you a way to organize your items.</p>
  <p>Many Residents, for example, create a "Kitchen" room to store all their food in.</p>
  <p>You may not find that dividing items into rooms is very useful right now, but believe me: when you get a couple more pets, they'll be invaluable!</p>
  <ul>
   <li><a href="#" onclick="tut_close(); return false;">(end dialog)</a></li>
  </ul>
 </span>
</div>
