<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_login = 'no';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Item Availability</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; Item Availability</h4>
     <p>An item's "availability" refers to how the item may be acquired.  Most items are "Common" - they can be acquired through normal gameplay - but a few can be acquired only as prizes for special events, purchased with <a href="/wherethemoneygoes.php">Favor</a>, or other means.</p>
     <table>
      <thead>
       <tr><th>Availability</th><th>Definition</th></tr>
      </thead>
      <tbody>
       <tr class="row"><th>Common</th><td>May be acquired through normal play.  It's worth noting that "Common" items may in fact be rather hard to get, and therefore quite rare.</td></tr>
       <tr class="altrow"><th>Limited</th><td>Some Limited items used to be Common, but have since been discontinued; these items <em>might</em> become Common again later.  Maybe.  Other Limited items are prizes for special in-game events, and will probably never be made Common.</td></tr>
       <tr class="row"><th>Custom</th><td>Custom items are created by other players, for example by using the <a href="/af_combinationstation3.php">Combination Station</a>.  Some players sell their custom items at the <a href="/favorstores.php">Custom Item Market</a>.</td></tr>
       <tr class="altrow"><th>Favor</th><td>These items are available for <a href="/wherethemoneygoes.php">Favor</a>.  Many Favor items are available only during certain times of the year, from certain NPCs (notably, <a href="/af_getrare2.php">The Smithery's Unique Item Shop</a>).</td></tr>
       <tr class="row"><th>Erstwhile</th><td>These items used to be available for <a href="/wherethemoneygoes.php">Favor</a>, but were disconinuted.  However, they are now available at random via the <?= item_text_link('Erstwhile Wand'); ?>.</td></tr>
       <tr class="altrow"><th>Cross-game</th><td>You can receive these items by performing certain actions in other games I've made, such as <a href="http://www.fifthage.net/">Fifth Age</a>.</td></tr>
      </tbody>
     </table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
