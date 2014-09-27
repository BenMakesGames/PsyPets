<?php
$require_login = 'no';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Site Map</title>
<?php include 'commons/head.php'; ?>
  <style type="text/css">
   #sitemap div.column
   {
     float: left;
     width: 190px;
     padding-right: 10px;
   }
  </style>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
  <h4><a href="/help/">Help Desk</a> &gt; Site Map</h4>
<?php
if($user['idnum'] == 0)
  echo '<p>Most of the pages below are accessible only while logged-in!  Log in using the form above, or <a href="signup.php">create an account</a> if you don\'t already have one!</p>';
?>
  <div id="sitemap">
   <div class="column">
    <h5>Residence</h5>
    <ul>
     <li><a href="/myhouse.php">My House</a></li>
     <li><a href="/storage.php">Storage</a></li>
<?php
if($user['license'] == 'yes')
{
?>
     <li><a href="/storage_locked.php">Locked Storage</a></li>
     <li><a href="/mystore.php">My Store</a></li>
<?php
}
?>
     <li><a href="/bank.php">Bank</a></li>
     <li><a href="/mynotepad.php">My Notepad</a></li>
     <li><a href="/mysketchbook.php">My Sketchpad</a></li>
     <li><a href="/post.php">Mailbox</a></li>
<?php
    if($user['childlockout'] == 'no')
    {
?>
     <li><a href="/myfriends.php">My Friends</a></li>
     <li><a href="/mygroups.php">My Groups</a></li>
<?php
    }
?>
    </ul>
<?php
    if($user['childlockout'] == 'no')
    {
?>
    <h5>Community</h5>
    <ul>
     <li><a href="/plaza.php">The Plaza</a></li>
     <li><a href="/plaza/search.php">Search Plaza</a></li>
     <li><a href="/threadsubscriptions.php">Favorite Threads</a></li>
     <li><a href="/groupindex.php">Groups</a></li>
     <li><a href="/directory.php">Resident Directory</a></li>
    </ul>
<?php
    }
?>
   </div>
   <div class="column">
    <h5>Recreation</h5>
    <ul>
<?php if($user['show_park'] == 'yes') { ?><li><a href="/park.php">The Park</a></li><?php } ?>
     <li><a href="/adventure/">Adventure</a></li>
     <li><a href="/museum/">The Museum</a></li>
<?php if($user['show_ark'] == 'yes') { ?><li><a href="/ark.php">The Ark</a></li><?php } ?>
<?php if($user['show_pattern'] == 'yes') { ?><li><a href="/pattern/">The Pattern</a></li><?php } ?>
<?php if($user['show_totemgardern'] == 'yes') { ?><li><a href="/totemgarden.php">Totem Pole Garden</a></li><?php } ?>
<?php if($user['show_universe'] == 'yes') { ?><li><a href="/myuniverse.php">The Multiverse</a></li><?php } ?>
    </ul>
    <h5>Commerce</h5>
    <ul>
     <li><a href="/reversemarket.php">Seller's Market</a></li>
<?php
    if($user['license'] == 'yes')
    {
?>
     <li><a href="/fleamarket/">Flea Market</a></li>
     <li><a href="/auctionhouse.php">Auction House</a></li>
     <li><a href="/trading_public2.php">Trading House</a></li>
<?php
    }
?>
<?php if($user['breeder'] == 'yes') { ?><li><a href="petmarket.php">Pet Market</a></li><?php } ?>
<?php if($user['license'] == 'yes' && $user['childlockout'] == 'no') { ?><li><a href="broadcast.php">Advertising</a></li><?php } ?>
     <li><a href="/givegift.php">Giving Tree</a></li>
    </ul>
   </div>
   <div class="column">
    <h5>Services</h5>
    <ul>
     <li><a href="/recycling.php">Recycling</a></li>
<?php if($user['show_florist'] == 'yes') { ?><li><a href="/greenhouse.php">Greenhouse</a></li><?php } ?>
<?php if($user['license'] == 'yes') { ?><li><a href="pawnshop.php">Pawn Shop</a></li><?php } ?>
     <li><a href="/smith.php">The Smithery</a></li>
     <li><a href="/alchemist.php">The Alchemist's</a></li>
<?php if($now_month == 10) { ?><li><a href="tailor.php">The Tailory <i style="color:red;">ooh!</i></a></li><?php } ?>
     <li><a href="/library.php">The Library</a></li>
     <li><a href="/temple.php">The Temple</a></li>
     <li><a href="/grocerystore.php">Grocery Store</a></li>
<?php if($user['show_florist'] == 'yes') { ?><li><a href="/florist.php">The Florist</a></li><?php } ?>
     <li><a href="/recycling_gamesell.php">Refuse Store</a></li>
<?php if($user['show_mysteriousshop'] == 'yes') { ?><li><a href="mysteriousshop.php">Mysterious Shop</a></li><?php } ?>
<?php if($user['show_aerosoc'] == 'yes') { ?><li><a href="aerosoc.php">Aeronautical Society</a></li><?php } ?>
     <li><a href="/petshelter.php">Pet Shelter</a></li>
     <li><a href="/graveyard.php">Graveyard</a></li>
<?php if($user['show_volcano'] == 'yes') { ?><li><a href="/volcano.php">The Volcano</a></li><?php } ?>
     <li><a href="/realestate.php">Real Estate</a></li>
    </ul>
   </div>
   <div class="column">
    <h5>The City</h5>
    <ul>
     <li><a href="/cityhall.php">City Hall</a></li>
     <li><a href="/livebroadcast.php">Live Broadcasting</a></li>
     <li><a href="/arrangewishes.php">To-do List</a></li>
     <li><a href="/changelog.php">Change Log</a></li>
     <li><a href="/autofavor.php">Favor Dispenser</a></li>
     <li><a href="/gl_browse.php">Graphics Library</a></li>
    </ul>
    <h5>Help</h5>
    <ul>
     <li><a href="/help/">Help Desk</a></li>
     <li><a href="/encyclopedia.php">Item Encyclopedia</a></li>
     <li><a href="/petencyclopedia.php">Pet Encyclopedia</a></li>
     <li><a href="/admincontact.php">Administrators</a></li>
     <li><a href="/statistics.php">Statistics</a></li>
    </ul>
   </div>
   <div style="clear:both;"></div>
  </div>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
