<?php
require_once 'commons/tutorial/css.php';
?>
<script type="text/javascript">
function tut_close()
{
  $('#tutorial').hide();
}

function tut_last()
{
  $('#tutorial_text').html(
    '<p>Oh, of course!  But believe me, we\'ll meet again, and probably very soon!</p>' +
    '<ul><li><a href="#" onclick="tut_close(); return false;">(end dialog)</a></li></ul>'
  );

  $.post('/ajax_tutorial.php', { 'tutorial': 1 });
}
</script>
<div id="tutorial">
 <img src="gfx/shim.png" height="200" width="1" align="left" alt="" />
 <span id="tutorial_text">
  <p>Hello!  Welcome to your house!</p>
  <p>Who am I?  Just an Introductory Octopus; don't worry about it <img src="/gfx/emote/wink.gif" alt="[wink]" height="16" width="16" class="inlineimage" /></p>
  <p><strong>I know you don't want to read a book just to play the game, so just listen to these <em>three things</em>, and I'll let you go!</strong></p>
  <ol>
   <li><p>Your pets do stuff on their own every hour of real-life time.  Don't believe me?  Come back in one hour and see!  Your pets can go mining, beat up monsters, and who knows what else!<p></li>
   <li><p>See those items you have below?  Not much now, but your pet will bring back more.  Try checking off a couple items together and clicking "Prepare" to make something new from the selected ingredients, or, to get more moneys, "Gamesell" items you don't need!</p></li>
   <li><p>While you wait for an hour to pass, check out all the menus and exciting icons on the bar across the top of the screen.  There's a lot to discover, and even more will be revealed as time goes on!</p></li>
  </ol>
  <ul>
   <li><a href="#" onclick="tut_last(); return false;">Thank the Introductory Octopus for his time.</a></li>
  </ul>
 </span>
</div>
