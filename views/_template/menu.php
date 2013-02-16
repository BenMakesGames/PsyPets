<ul class="megamenu">
 <li>
  <a href="#" onclick="return false;" accesskey="r">Residence</a>
  <div id="residence_menu">
   <div class="column">
    <ul class="plainlist nomargin">
     <li><a href="/houseaction.php?room=Common"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/myhouse.png" width="24" height="24" alt="" /> My House</a></li>
     <li><a href="/storage.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/storage.png" width="24" height="24" alt="" /> Storage</a></li>
<?php
if($user['license'] == 'yes')
{
  echo '
    <li><a href="/storage_locked.php"><img src="//' . $SETTINGS['static_domain'] . '/gfx/menu/storage_locked.png" width="24" height="24" alt="" /> Locked Storage</a></li>
    <li><a href="/mystore.php"><img src="//' . $SETTINGS['static_domain'] . '/gfx/menu/fleamarket.png" width="24" height="24" alt="" /> My Store</a></li>
  ';
}
?>
     <li class="separator"><a href="/bank.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/bank.png" width="24" height="24" alt="" /> Bank</a></li>
     <li><a href="/post.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/mailbox.png" width="24" height="24" alt="" /> Mailbox</a></li>
     <li class="separator"><a href="/mynotepad.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/notepad.png" width="24" height="24" alt="" /> My Notepad</a></li>
     <li><a href="/mysketchbook.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/sketchpad.png" width="24" height="24" alt="" /> My Sketchbook</a></li>
<?php
if($user['childlockout'] == 'no')
{
?>
     <li class="separator"><a href="/myfriends.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/friends.png" width="24" height="24" alt="" /> My Friends</a></li>
     <li><a href="/mygroups.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/groups.png" width="24" height="24" alt="" /> My Groups</a></li>
<?php
}
?>
     <li class="separator"><a href="/questbook.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/questlog.png" width="24" height="24" alt="" /> Quest Log</a></li>
<?php
if($user['show_roleplay'] == 'yes')
    echo '<li class="separator"><a href="/stories.php"><img src="//' . $SETTINGS['static_domain'] . '/gfx/menu/book.png" width="24" height="24" alt="" /> Hollow Earth Stories</a></li>';
?>
    </ul>
   </div>
<?php
echo '
  <div class="column" id="myrooms">
  <h5>Rooms <a href="/managerooms.php"><img src="/gfx/pencil.png" alt="(edit)" style="vertical-align:bottom;" /></a></h5>
';

if(strlen($house['rooms']) > 0)
{
  echo '<ul class="plainlist nomargin">';

  $menu_rooms = explode(',', $house['rooms']);

  $room_class = '';
  foreach($menu_rooms as $i=>$menu_room)
  {
    if($i == $house['max_rooms_shown'])
      $room_class = ' class="dim"';

    echo '<li><a href="/houseaction.php?room=' . $menu_room . '"' . $room_class . '>' . ($menu_room{0} == '$' ? substr($menu_room, 1) : $menu_room) . '</a></li>';
  }

  echo '</ul></div>';
}

if(strlen($house['addons']) > 0)
{
  echo '
    <div class="column" id="myaddons">
    <h5>Add-ons <a href="/arrangeaddons.php"><img src="/gfx/pencil.png" alt="(edit)" style="vertical-align:bottom;" /></a></h5>
    <ul class="plainlist nomargin">
  ';

  $menu_addons = explode(',', $house['addons']);

  $room_class = '';
  foreach($menu_addons as $i=>$menu_addon)
  {
    if($i == $house['max_addons_shown'])
      $room_class = ' class="dim"';

    $addon_url = str_replace(' ', '_', strtolower($menu_addon));
    echo '<li><a href="/myhouse/addon/' . $addon_url . '.php"' . $room_class . '><img src="//' . $SETTINGS['static_domain'] . '/gfx/addons/' . $addon_url . '.png" width="13" height="13" alt="" /> ' . $menu_addon . '</a></li>';
  }

  echo '</ul></div>';
}
?>
   <div style="clear:both;"></div>
  </div>
 </li>
<?php
if($user['childlockout'] == 'no')
{
?>
 <li>
  <a href="#" onclick="return false;" accesskey="m">Community</a>
  <div id="community_menu">
   <div class="column">
    <ul class="plainlist nomargin">
     <li><a href="/plaza.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/forums.png" width="24" height="24" alt="" /> The Plaza</a></li>
     <li><a href="/plaza/search.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/search.png" width="24" height="24" alt="" /> Search Plaza</a></li>
     <li><a href="/threadsubscriptions.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/favorites.png" width="24" height="24" alt="" /> Favorite Threads</a></li>
     <li class="separator"><a href="/groupindex.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/groups.png" width="24" height="24" alt="" /> Groups</a></li>
     <li><a href="/directory.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/directory.png" width="24" height="24" alt="" /> Resident Directory</a></li>
    </ul>
   </div>
   <div class="column" id="mythreads" style="width:400px; border-left: 1px dashed #999;"><img src="/gfx/throbber.gif" width="16" height="16" /></div>
   <div style="clear:both;"></div>
  </div>
 </li>
<?php
}
?>
 <li>
  <a href="#" onclick="return false;" accesskey="e">Recreation</a>
  <div>
   <div class="column">
    <ul class="plainlist nomargin">
<?php if($user['show_park'] == 'yes') { ?><li><a href="/park.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/thepark.png" width="24" height="24" alt="" /> The Park</a></li><?php } ?>
     <li><a href="/adventure/"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/adventure.png" width="24" height="24" alt="" /> Adventure</a></li>
<?php if($now_month == 10) { ?><li class="separator"><a href="/cropcircles.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/cropcircles.png" width="24" height="24" alt="" /> Crop Circles <i style="color:red;">wtf?</i></a></li><?php } ?>
    </ul>
   </div>
   <div class="column">
    <ul class="plainlist nomargin">
     <li><a href="/museum/"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/museum.png" width="24" height="24" alt="" /> The Museum</a></li>
<?php if($user['show_ark'] == 'yes') { ?><li><a href="/ark.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/ark.png" width="24" height="24" alt="" /> The Ark</a></li><?php } ?>
<?php if($user['show_pattern'] == 'yes') echo '<li><a href="/pattern/"><img src="//' . $SETTINGS['static_domain'] . '/gfx/menu/pattern.png" width="24" height="24" alt="" /> The Pattern</a></li>'; ?>
<?php if($user['show_totemgardern'] == 'yes') echo '<li><a href="/totemgarden.php"><img src="//' . $SETTINGS['static_domain'] . '/gfx/menu/totem.png" width="24" height="24" alt="" /> Totem Pole Garden</a></li>'; ?>
<?php if($user['show_universe'] == 'yes') echo '<li><a href="/myuniverse.php"><img src="//' . $SETTINGS['static_domain'] . '/gfx/menu/multiverse.png" width="24" height="24" alt="" /> The Multiverse</a></li>'; ?>
    </ul>
   </div>
   <div style="clear:both;"></div>
  </div>
 </li>
 <li>
  <a href="#" onclick="return false;" accesskey="c">Commerce</a>
  <div>
   <div class="column">
    <ul class="plainlist nomargin">
     <li><a href="/reversemarket.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/sellersmarket.png" width="24" height="24" alt="" /> Seller's Market</a></li>
<?php if($user['license'] == 'yes') { ?>
     <li><a href="/fleamarket/"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/fleamarket.png" width="24" height="24" alt="" /> Flea Market</a></li>
     <li><a href="/auctionhouse.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/auctionhouse.png" width="24" height="24" alt="" /> Auction House</a></li>
     <li><a href="/trading_public2.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/tradinghouse.png" width="24" height="24" alt="" /> Trading House</a></li>
<?php } ?>
<?php if($user['breeder'] == 'yes') { ?>   <li class="separator"><a href="/petmarket.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/petmarket.png" width="24" height="24" alt="" /> Pet Market</a></li><?php } ?>
<?php if($user['license'] == 'yes' && $user['childlockout'] == 'no') { ?>   <li class="separator"><a href="/broadcast.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/advertising2.png" width="24" height="24" alt="" /> Advertising</a></li><?php } ?>
    </ul>
   </div>
   <div class="column">
    <ul class="plainlist nomargin">
     <li><a href="/givegift.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/givingtree.png" width="24" height="24" alt="" /> Giving Tree</a></li>
    </ul>
   </div>
   <div style="clear:both;"></div>
  </div>
 </li>
 <li>
  <a href="#" onclick="return false;" accesskey="s">Services</a>
  <div>
   <div class="column">
    <ul class="plainlist nomargin">
     <li><a href="/recycling.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/recycling.png" width="24" height="24" alt="" /> Recycling</a></li>
<?php if($user['show_florist'] == 'yes') { ?><li><a href="/greenhouse.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/greenhouse.png" width="24" height="24" alt="" /> Greenhouse</a></li><?php } ?>
<?php if($user['license'] == 'yes') { ?>   <li><a href="/pawnshop.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/pawnshop.png" width="24" height="24" alt="" /> Pawn Shop</a></li><?php } ?>
     <li class="separator"><a href="/smith.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/smithery.png" width="24" height="24" alt="" /> The Smithery</a></li>
     <li><a href="/alchemist.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/alchemist.png" width="24" height="24" alt="" /> The Alchemist's</a></li>
<?php if($now_month == 10) { ?>   <li><a href="/tailor.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/tailory.png" width="24" height="24" alt="" /> The Tailory <i style="color:red;">ooh!</i></a></li><?php } ?>
     <li class="separator"><a href="/library.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/library.png" width="24" height="24" alt="" /> The Library</a></li>
     <li><a href="/temple.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/temple.png" width="24" height="24" alt="" /> The Temple</a></li>
    </ul>
   </div>
   <div class="column">
    <ul class="plainlist nomargin">
     <li><a href="/grocerystore.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/groceries.png" width="24" height="24" alt="" /> Grocery Store</a></li>
<?php if($user['show_florist'] == 'yes') { ?><li><a href="/florist.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/florist.png" width="24" height="24" alt="" /> The Florist</a></li><?php } ?>
     <li><a href="/recycling_gamesell.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/refusestore.png" width="24" height="24" alt="" /> Refuse Store</a></li>
<?php if($user['show_mysteriousshop'] == 'yes') { ?><li><a href="/mysteriousshop.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/mysteriousshop.png" width="24" height="24" alt="" /> Mysterious Shop</a></li><?php } ?>
<?php if($user['show_aerosoc'] == 'yes') { ?><li class="separator"><a href="/aerosoc.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/aerosoc.png" width="24" height="24" alt="" /> Aeronautical Society</a></li><?php } ?>
     <li class="separator"><a href="/petshelter.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/petshelter.png" width="24" height="24" alt="" /> Pet Shelter</a></li>
     <li><a href="/graveyard.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/graveyard.png" width="24" height="24" alt="" /> Graveyard</a></li>
<?php if($user['show_volcano'] == 'yes') { ?><li class="separator"><a href="/volcano.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/volcano.png" width="24" height="24" alt="" /> The Volcano</a></li><?php } ?>
     <li class="separator"><a href="/realestate.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/realestate.png" width="24" height="24" alt="" /> Real Estate</a></li>
    </ul>
   </div>
   <div style="clear:both;"></div>
  </div>
 </li>
 <li>
  <a href="#" onclick="return false;" accesskey="y">The City</a>
  <div>
   <div class="column">
    <ul class="plainlist nomargin">
     <li><a href="/cityhall.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/cityhall.png" width="24" height="24" alt="" /> City Hall</a></li>
     <li><a href="/livebroadcast.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/broadcasting.png" width="24" height="24" alt="" /> Live Broadcasting</a></li>
     <li class="separator"><a href="/arrangewishes.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/todolist.png" width="24" height="24" alt="" /> To-do List</a></li>
     <li><a href="/changelog.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/changelog.png" width="24" height="24" alt="" /> Changelog</a></li>
     <li class="separator"><a href="/featuredrive.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/houseaddondrive.png" width="24" height="24" alt="" /> House Add-on Drive</a></li>
    </ul>
   </div>
   <div style="clear:both;"></div>
  </div>
 </li>
 <li>
  <a href="#" onclick="return false;" accesskey="h">Help</a>
  <div>
   <div class="column">
    <ul class="plainlist nomargin">
     <li><a href="/help/"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/help.png" width="24" height="24" alt="" /> Help Desk</a></li>
     <li class="separator"><a href="/encyclopedia.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/itemencyclopedia.png" width="24" height="24" alt="" /> Item Encyclopedia</a></li>
     <li><a href="/petencyclopedia.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/petencyclopedia.png" width="24" height="24" alt="" /> Pet Encyclopedia</a></li>
    </ul>
   </div>
   <div class="column">
    <ul class="plainlist nomargin">
     <li><a href="/admincontact.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/admins.png" width="24" height="24" alt="" /> Administrators</a></li>
     <li class="separator"><a href="/statistics.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/statistics.png" width="24" height="24" alt="" /> Statistics</a></li>
<?php if($user['admin']['clairvoyant'] == 'yes') { ?>
     <li><a href="/admin/dailystats.php"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/statistics.png" width="24" height="24" alt="" /> Resident Activity Statistics</a></li>
<?php } ?>
    </ul>
   </div>
   <div style="clear:both;"></div>
  </div>
 </li>
<?php
if(strlen($wiki) > 0)
{
?>
 <li>
  <a href="http://<?= $SETTINGS['wiki_domain'] ?>/<?= $wiki ?>" accesskey="w">About This Page</a>
 </li>
<?php
}

require_once 'commons/moonphase.php';
?>
 <li id="moonphase">
  <a href="/myaccount/display.php"><?= moon_graphic() . ' ' . local_time_short(time(), $user['timezone'], $user['daylightsavings']) ?></a>
 </li>
</ul>
<script type="text/javascript">
<?php
if($user['menu_popup_setting'] == 'mouseover')
  echo 'MM_OBJECT = jQuery(".megamenu").megamenu({\'activate_action\': \'hover\'});';
else if($user['menu_popup_setting'] == 'click')
  echo 'MM_OBJECT = jQuery(".megamenu").megamenu({\'activate_action\': \'click\'});';
else
  die('unhandled menu popup configuration.  please notify an administrator!');

if($user['menu_floating'] == 'no')
  echo 'MM_FIRMLY_ATTACHED = true;';
?>

var loaded_favorite_threads = false;

$('#community_menu').watch(
  'display,visibility',
  function()
  {
    if(($('#community_menu').is(':visible')) && (!loaded_favorite_threads))
    {
      loaded_favorite_threads = true;

      $.get(
        '/top5favoritethreads.php',
        function(html)
        {
          if(html == 'none')
            $('#mythreads').remove();
          else
            $('#mythreads').html(html);
        }
      );
    }
  }
);
</script>
