<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/favorlib.php';
require_once 'commons/houselib.php';

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; Favor Dispenser</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Favor Dispenser</h4>
     <p>The Favor Dispenser can automatically fulfill many of your requests!  If you have ideas for other things to add here, <a href="admincontact.php">let me know</a>.</p>
     <p>Fair warning: no refunds.</p>
<?php
if($error_message)
  echo '<p class="failure">' . $error_message . '</p>';
?>
     <h5>You Have <?= $user['favor'] ?> Favor</h5>
     <ul>
      <li><a href="/buyfavors.php">Get more Favor</a></li>
      <li><a href="/myaccount/favorhistory.php">View Favor History</a></li>
     </ul>
     <h5>Pet-related</h5>
     <ul class="spacedlist">
      <li>
       <a href="af_revive2.php">Resurrections</a><br />
       Brings pets back from the dead.  The pet must not have been "moved on"!<br />
      </li>
      <li>
       <a href="af_respec.php">Pet Respec</a><br />
       Allows you to completely re-level a pet.<br />
      </li>
      <li>
       <a href="af_regraphik2.php">Pet Regraphiker</a><br />
       Change any of your pets' appearance into one of the standard graphics (for custom graphics, use the <a href="af_custompetgraphic.php">Pet Graphic Customizer</a>).<br />
      </li>
      <li>
       <a href="af_custompetgraphic2.php">Pet Graphic Customizer</a><br />
       Check out the <a href="graphicslibrary.php">Custom Graphics Library</a> for more information on how to get a custom graphic for your pet.<br />
      </li>
      <li>
       <a href="af_movepet2.php">Pet Exchange</a><br />
       If you'd like to give one of your pets to another Resident, you can do so here.<br />
      </li>
     </ul>
     <h5>Item-related</h5>
     <ul class="spacedlist">
      <li>
       <a href="af_printerprinter2.php">The Printer-Printer</a><br />
       Make a book with your own custom text, and you don't get the book, you get something better: a printer which can print you as many copies of the book as you like, for 1 Paper and 1 Black Dye a copy.<br />
      </li>
      <li>
       <a href="af_customavataritem2.php">Custom Avatar Item Builder Plus!</a><br />
       Check out the <a href="graphicslibrary.php">Custom Graphics Library</a> for more information on how to get the custom graphics for this (you'll need two!)<br />
      </li>
      <li>
       <a href="af_combinationstation3.php">Combination Station</a><br />
       Take the stats of two items you already have (some limitations may apply :P) and cram them together into a new item with the graphic of your choice.  Check out the <a href="graphicslibrary.php">Custom Graphics Library</a> for more information on how to get a custom graphic for your item.<br />
      </li>
      <li>
       <a href="af_getrare2.php">Unique Item Shop</a><br />
       Allows you to receive a copy of any custom item you've made using the <a href="af_combinationstation3.php">Combination Station</a> or <a href="af_customavataritem2.php">Custom Avatar Item Builder Plus!</a>  The current monthly item is also available here.<br />
      </li>
      <li>
       <a href="af_trinkets.php">Rare Trinkets</a><br />
       A handful of items available for 50 Favor each, including items to speed up time.<br />
      </li>
      <li>
       <a href="af_favortickets.php">Get Favor Tickets</a><br />
       Turn Favor into Favor Tickets for exchange with other players.<br />
      </li>
     </ul>
     <h5>Account-related</h5>
     <ul class="spacedlist">
      <li>
       <a href="/af_resrename2.php">Name Change Application</a><br />
       If you find you're not liking your name as much as you used to, this tool can change it for you.<br />
      </li>
      <li>
<?php
if(addon_exists($house, 'Fiberoptic Link'))
  echo '<a href="/myhouse/addon/fiberoptic_link_titles.php">Custom Title</a> <i>(requires the Fiberoptic Link house add-on)</i><br />';
else
  echo '<span class="dim">Custom Title <i class="failure">(requires the Fiberoptic Link house add-on)</i></span>';
?>
       Allows you to create custom titles (you are currently "<?= $user['title'] ?>").<br />
      </li>
      <li>
       <a href="af_favortransfer2.php">Transfer Favor</a><br />
       Transfer Favor to another Resident.<br />
      </li>
     </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
