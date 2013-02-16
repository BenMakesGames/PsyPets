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
    '<p>Oh, of course!</p>' +
    '<p>Good luck!</p>' +
    '<ul><li><a href="#" onclick="tut_close(); return false;">(end dialog)</a></li></ul>'
  );

  $.post('/ajax_tutorial.php', { 'tutorial': 2 });
}

function tut_3()
{
  document.getElementById('tutorial_text').innerHTML =
    '<p>Right!</p>' +
    '<p>So, that icon appears next to the price field for every item in your store.  It\'s best explained with an example:</p>' +
    '<p>Suppose you have 10 - I dunno - Potatoes.  Rather than pricing them all, price one, then click the <img src="gfx/mimic.png" height="16" width="16" alt="... for all such items" /> icon next to that price.  Doing so will price the rest of your Potatoes for the same price!</p>' +
    '<p class="size8">(price, price, price, price, price...)</p>' +
    '<p>*ahem*  Anyway, if you have multiple pets, the half-hourly actions also have that icon, and they behave in very similar ways.  That should help you remember <img src="gfx/emote/hee.gif" alt="" /></p>' +
    '<ul><li><a href="#" onclick="tut_last(); return false;">Thank him/her for his/her time.</a></li></ul>';
}

function tut_2()
{
  document.getElementById('tutorial_text').innerHTML =
    '<p>Oh, it\'s easy!  Once you\'ve moved items into your store, you\'ll be able to set the prices for your items.  Other people, using the Flea Market, will be able to find the items you\'re selling.</p>' +
    '<p>Remember that the Flea Market uses your Storage space, and if you use a lot of Storage space, you will have to pay a daily fee.  The fee is quite low, but with a lot of items over a lot of time can really add up.</p>' +
    '<p>So it\'s definitely better to sell higher-priced items when possible, but if you can consistently sell cheap items, that can work out great, too!</p>' +
    '<ul><li><a href="#" onclick="tut_3(); return false;">Ask about the <img src="gfx/mimic.png" height="16" width="16" alt="... for all such items" /> icon.</a></li></ul>';
}
</script>
<div id="tutorial">
 <img src="gfx/shim.png" height="200" width="1" align="left" alt="" />
 <span id="tutorial_text">
  <p>Hello!  Welcome to My Store.  From here you can sell items to other players, right out of your Storage.</p>
  <ul>
   <li><a href="#" onclick="tut_2(); return false;">Ask how it's used.</a></li>
   <li><a href="#" onclick="tut_last(); return false;">Explain that you already know all this.</a></li>
  </ul>
 </span>
</div>
