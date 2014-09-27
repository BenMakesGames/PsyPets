<?php
$require_login = 'no';
$whereat = 'encyclopedia';
$wiki = 'Encyclopedia';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/encyclopedialib.php';
require_once 'commons/messages.php';

$itemname_message = '<p>&nbsp;</p>';
$entry_message = '<p>&nbsp;</p>';

$results = false;

$_GET['itemname'] = trim($_GET['itemname']);

$ITEM_FIELDS = 'idnum,itemname,itemtype,custom,graphictype,graphic';

$command = 'SELECT ' . $ITEM_FIELDS . ' FROM monster_items';
$errored = false;
$wheres = array('idnum!=0');

$items_per_page = 30;

$basicoptions_style = '';
$advancedoptions_style = ' style="display: none;"';

if($_GET['submit'] == 'Search')
{
  $searched = true;
  if(strlen($_GET['itemname']) > 2)
    $wheres[] = 'itemname LIKE ' . quote_smart('%' . $_GET['itemname'] . '%');
  else if(strlen($_GET['itemname']) > 0)
  {
    $wheres[] = 'itemname=' . quote_smart($_GET['itemname']);
    $warning = ' Since you provided less than three letters, only exact matches were searched for.';
  }

  if(strlen($_GET['itemtype']) > 0)
  {
    $_GET['itemtype'] = trim($_GET['itemtype']);
    $wheres[] = 'itemtype LIKE ' . quote_smart('%' . $_GET['itemtype'] . '%');
    $basicoptions_style = ' style="display: none;"';
    $advancedoptions_style = '';
  }
}

$in = array();

if($_GET['standard'] == 'on')
{
  $in[] = 'no';
  $standard_selected = ' checked="checked"';
  $types[] = 'standard=on';
}

if($_GET['custom'] == 'on')
{
  $in[] = 'yes';
  $custom_selected = ' checked="checked"';
  $types[] = 'custom=on';
}

if($_GET['limited'] == 'on')
{
  $in[] = 'limited';
  $limited_selected = ' checked="checked"';
  $types[] = 'limited=on';
}

if($_GET['recurring'] == 'on')
{
  $in[] = 'recurring';
  $recurring_selected = ' checked="checked"';
  $types[] = 'recurring=on';
}

if($_GET['monthly'] == 'on')
{
  $in[] = 'monthly';
  $monthly_selected = ' checked="checked"';
  $types[] = 'monthly=on';
}

if($_GET['crossgame'] == 'on')
{
  $in[] = 'x-game';
  $crossgame_selected = ' checked="checked"';
  $types[] = 'crossgame=on';
}

if($_GET['is_equipment'] == 'on')
{
  $wheres[] = 'is_equipment=\'yes\'';
  $equipment_selected = ' checked="checked"';
  $basicoptions_style = ' style="display: none;"';
  $advancedoptions_style = '';
  $types[] = 'is_equipment=on';
}

if($_GET['is_key'] == 'on')
{
  $wheres[] = 'key_id>0';
  $key_selected = ' checked="checked"';
  $basicoptions_style = ' style="display: none;"';
  $advancedoptions_style = '';
  $types[] = 'is_key=on';
}

if($_GET['is_food'] == 'on')
{
  $wheres[] = 'is_edible=\'yes\'';
  $food_selected = ' checked="checked"';
  $basicoptions_style = ' style="display: none;"';
  $advancedoptions_style = '';
  $types[] = 'is_food=on';
}

if($_GET['can_pawn_with'] == 'on')
{
  $wheres[] = 'can_pawn_with=\'yes\'';
  $pawn_with_selected = ' checked="checked"';
  $basicoptions_style = ' style="display: none;"';
  $advancedoptions_style = '';
  $types[] = 'can_pawn_with=on';
}

if($_GET['can_pawn_for'] == 'on')
{
  $wheres[] = 'can_pawn_for=\'yes\'';
  $pawn_for_selected = ' checked="checked"';
  $basicoptions_style = ' style="display: none;"';
  $advancedoptions_style = '';
  $types[] = 'can_pawn_for=on';
}

if($_GET['is_recyclable'] == 'on')
{
  $wheres[] = 'can_recycle=\'yes\'';
  $recyclable_selected = ' checked="checked"';
  $basicoptions_style = ' style="display: none;"';
  $advancedoptions_style = '';
  $types[] = 'is_recyclable=on';
}

if($_GET['can_gamesell'] == 'on')
{
  $wheres[] = 'nosellback=\'no\'';
  $gamesell_selected = ' checked="checked"';
  $basicoptions_style = ' style="display: none;"';
  $advancedoptions_style = '';
  $types[] = 'can_gamesell=on';
}

if($_GET['cursed'] == 'on')
{
  $wheres[] = 'cursed=\'yes\'';
  $cursed_selected = ' checked="checked"';
  $basicoptions_style = ' style="display: none;"';
  $advancedoptions_style = '';
  $types[] = 'cursed=on';
}

if($_GET['no_is_equipment'] == 'on')
{
  $wheres[] = 'is_equipment=\'no\'';
  $no_equipment_selected = ' checked="checked"';
  $basicoptions_style = ' style="display: none;"';
  $advancedoptions_style = '';
  $types[] = 'no_is_equipment=on';
}

if($_GET['no_is_key'] == 'on')
{
  $wheres[] = 'key_id=0';
  $no_key_selected = ' checked="checked"';
  $basicoptions_style = ' style="display: none;"';
  $advancedoptions_style = '';
  $types[] = 'no_is_key=on';
}

if($_GET['no_is_food'] == 'on')
{
  $wheres[] = 'is_edible=\'no\'';
  $no_food_selected = ' checked="checked"';
  $basicoptions_style = ' style="display: none;"';
  $advancedoptions_style = '';
  $types[] = 'no_is_food=on';
}

if($_GET['no_can_pawn_with'] == 'on')
{
  $wheres[] = 'can_pawn_with=\'no\'';
  $no_pawn_with_selected = ' checked="checked"';
  $basicoptions_style = ' style="display: none;"';
  $advancedoptions_style = '';
  $types[] = 'no_can_pawn_with=on';
}

if($_GET['no_can_pawn_for'] == 'on')
{
  $wheres[] = 'can_pawn_for=\'no\'';
  $no_pawn_for_selected = ' checked="checked"';
  $basicoptions_style = ' style="display: none;"';
  $advancedoptions_style = '';
  $types[] = 'no_can_pawn_for=on';
}

if($_GET['no_is_recyclable'] == 'on')
{
  $wheres[] = 'can_recycle=\'no\'';
  $no_recyclable_selected = ' checked="checked"';
  $basicoptions_style = ' style="display: none;"';
  $advancedoptions_style = '';
  $types[] = 'no_is_recyclable=on';
}

if($_GET['no_can_gamesell'] == 'on')
{
  $wheres[] = 'nosellback=\'yes\'';
  $no_gamesell_selected = ' checked="checked"';
  $basicoptions_style = ' style="display: none;"';
  $advancedoptions_style = '';
  $types[] = 'no_can_gamesell=on';
}

if($_GET['no_cursed'] == 'on')
{
  $wheres[] = 'cursed=\'no\'';
  $no_cursed_selected = ' checked="checked"';
  $basicoptions_style = ' style="display: none;"';
  $advancedoptions_style = '';
  $types[] = 'no_cursed=on';
}

if(count($types) > 0)
  $type = '&' . implode('&', $types);

if(count($in) > 0 && count($in) < 6)
{
  $wheres[] = 'custom IN (\'' . implode('\', \'', $in) . '\')';
  $basicoptions_style = ' style="display: none;"';
  $advancedoptions_style = '';
}
else
{
  $standard_selected = ' checked="checked"';
  $custom_selected = ' checked="checked"';
  $limited_selected = ' checked="checked"';
  $recurring_selected = ' checked="checked"';
  $monthly_selected = ' checked="checked"';
  $crossgame_selected = ' checked="checked"';

  $wheres[] = 'custom!=\'secret\'';
}

if(count($wheres) > 0)
  $command .= ' WHERE ' . implode(' AND ', $wheres);

if($errored === false)
{
  $count_command = str_replace($ITEM_FIELDS, 'COUNT(idnum)', $command);
  $data = $database->FetchSingle($count_command, 'getting encyclopedia search result count');
  $count = $data['COUNT(idnum)'];
  $max_pages = ceil($count / $items_per_page);
    
  $page = (int)$_GET['page'];
  if($page < 1)
    $page = 1;
  else if($page > $max_pages)
    $page = $max_pages;

  $command .= ' ORDER BY itemname ASC LIMIT ' . (($page - 1) * $items_per_page) . ',' . $items_per_page;
  
  $items = $database->FetchMultiple($command, 'searching encyclopidea');
}

$search_command = $command;

$avail_heading = 'Availability<a href="/help/item_availability.php" class="help">?</a>';

// disable encyclopedia popup on this page
$user['encyclopedia_popup'] = 'no';

if(array_key_exists('msg', $_GET))
  $error_messages = form_message(explode(',', $_GET['msg']));

if($user['idnum'] > 0 && count($_GET) > 0)
{
  $secret_search = array(
    'itemname' => '',
    'itemtype' => '',
    'limited' => 'on',
    'crossgame' => 'on',
    'is_key' => 'on',
    'can_pawn_for' => 'on',
    'no_is_key' => 'on',
    'no_can_gamesell' => 'on',
    'no_cursed' => 'on',
    'submit' => 'Search',
  );

  $got_it = 0;

  foreach($_GET as $key=>$value)
  {
    if(array_key_exists($key, $secret_search))
    {
      if($value == $secret_search[$key])
        $got_it++;
    }
    else
      $got_it = -10000;
  }

  if($got_it == count($secret_search))
  {
    $badges = get_badges_byuserid($user['idnum']);
    if($badges['seeker'] == 'no')
    {
      set_badge($user['idnum'], 'seeker');
      $secret_search_result = true;
      add_inventory($user['user'], '', 'Red Scroll', 'Found in the Encyclopedia', 'storage/incoming');
    }
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Item Encyclopedia</title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript">
  $(function() {
    $('#more_options').click(function() {
      $('#advancedsearch').show();
      $('#basicsearch').hide();
      $('#itemname2').val($('#itemname1').val());
      
      return false;
    });
  });
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Item Encyclopedia</h4>
     <?php if($error_messages) echo '<p class="failure">' . $error_messages . '</p>'; ?>
     <h5>Search</h5>
     <div id="basicsearch"<?= $basicoptions_style ?>>
     <form method="get">
     <table>
      <tr>
       <td>
        <p><input name="itemname" value="<?= $_GET['itemname'] ?>" id="itemname1" maxlength="64" style="width:192px;" /></p>
        <p id="more" class="nomargin"<?= $more_style ?>><a href="#" id="more_options">More options &gt;&gt;</a></p>
       </td>
       <td id="searchbutton" valign="top"><input type="hidden" name="submit" value="Search" /><input type="submit" value="Search" /></td>
      </tr>
     </table>
     </form>
     </div>
     <div id="advancedsearch"<?= $advancedoptions_style ?>>
     <form method="get">
     <table>
      <tr>
       <th>Item Name</th>
       <td><input name="itemname" value="<?= $_GET['itemname'] ?>" id="itemname2" maxlength="64" style="width:192px;" /></td>
      </tr>
      <tr>
       <th>Item Type</th>
       <td><input name="itemtype" value="<?= $_GET['itemtype'] ?>" maxlength="32" style="width:192px;" /></td>
      </tr>
      <tr>
       <th valign="top">Availability<a href="/help/item_availability.php" class="help">?</a></th>
       <td valign="top">
        <ul class="plainlist nomargin">
         <li><input type="checkbox" name="standard"<?= $standard_selected ?> /> Common</li>
         <li><input type="checkbox" name="limited"<?= $limited_selected ?> /> Limited</li>
         <li><input type="checkbox" name="recurring"<?= $recurring_selected ?> /> Favor</li>
         <li><input type="checkbox" name="monthly"<?= $monthly_selected ?> /> Erstwhile</li>
         <li><input type="checkbox" name="custom"<?= $custom_selected ?> /> Custom</li>
         <li><input type="checkbox" name="crossgame"<?= $crossgame_selected ?> /> Cross-Game</li>
        </ul>
       </td>
      </tr>
      <tr>
       <th valign="top">Only find...</th>
       <td valign="top">
        <ul class="plainlist nomargin">
         <li><input type="checkbox" name="is_food"<?= $food_selected ?> /> Food</li>
         <li><input type="checkbox" name="is_equipment"<?= $equipment_selected ?> /> Tool</li>
         <li><input type="checkbox" name="is_key"<?= $key_selected ?> /> Key</li>
         <li><input type="checkbox" name="can_pawn_with"<?= $pawn_with_selected ?> /> Items you can pawn <em>with</em></li>
         <li><input type="checkbox" name="can_pawn_for"<?= $pawn_for_selected ?> /> Items you can pawn <em>for</em></li>
         <li><input type="checkbox" name="is_recyclable"<?= $recyclable_selected ?> /> Items you can recycle</li>
         <li><input type="checkbox" name="can_gamesell"<?= $gamesell_selected ?> /> Items you can gamesell</li>
         <li><input type="checkbox" name="cursed"<?= $cursed_selected ?> /> Cursed items</li>
        </ul>
       </td>
       <th valign="top">Don't find...</th>
       <td valign="top">
        <ul class="plainlist nomargin">
         <li><input type="checkbox" name="no_is_food"<?= $no_food_selected ?> /> Food</li>
         <li><input type="checkbox" name="no_is_equipment"<?= $no_equipment_selected ?> /> Tool</li>
         <li><input type="checkbox" name="no_is_key"<?= $no_key_selected ?> /> Key</li>
         <li><input type="checkbox" name="no_can_pawn_with"<?= $no_pawn_with_selected ?> /> Items you can pawn <em>with</em></li>
         <li><input type="checkbox" name="no_can_pawn_for"<?= $no_pawn_for_selected ?> /> Items you can pawn <em>for</em></li>
         <li><input type="checkbox" name="no_is_recyclable"<?= $no_recyclable_selected ?> /> Items you can recycle</li>
         <li><input type="checkbox" name="no_can_gamesell"<?= $no_gamesell_selected ?> /> Items you can gamesell</li>
         <li><input type="checkbox" name="no_cursed"<?= $no_cursed_selected ?> /> Cursed items</li>
        </ul>
       </td>
      </tr>
      <tr>
       <td></td>
       <td><input type="hidden" name="submit" value="Search" /><input type="submit" value="Search" /></td>
      </tr>
     </table>
     </form>
     </div>
<?php
if($_GET['submit'] == 'Search')
{
  if($searched === false)
    echo '
      <h5>Results</h5>
      <p>Please provide more information to search by.</p>
    ';
  else if($secret_search_result)
    echo '
      <h5>Results</h5>
      <p>You find a Red Scroll containing some mysterious writing!  <i>(It\'s been placed in your <a href="/incoming.php">Incoming</a>!)</i></p>
      <p><i>(Also: you received the Seeker Badge!)</i></p>
    ';
  else if(count($items) == 0)
    echo '
      <h5>Results</h5>
      <p>No matching items were found.' . $warning . '</p>
    ';
  else
  {
    $pages = paginate($max_pages, $page, $_SERVER['SCRIPT_NAME'] . '?submit=Search&itemname=' . $_GET['itemname'] . '&itemtype=' . $_GET['itemtype'] . '&page=%s' . $type);
?>
     <h5>Results (<?= $count . ' item' . ($count != 1 ? 's' : '') ?>)</h5>
<?= strlen($warning) > 0 ? ('<p>' . $warning . '</p>') : '' ?>
     <ul>
      <li><a href="/encyclopedia.php">Browse all items</a></li>
     </ul>
     <?php if($max_pages > 1) echo $pages; ?>
     <table>
      <tr class="titlerow"><th></th><th>Item</th><th class="centered"><?= $avail_heading ?></th></tr>
<?php
    $rowclass = begin_row_class();
    foreach($items as $item)
    {
?>
      <tr class="<?= $rowclass ?>">
       <td class="centered"><?= item_display($item, '') ?></td>
       <td>
        <a href="/encyclopedia2.php?i=<?= $item['idnum'] ?>" style="font-weight:bold;"><?= $item['itemname'] ?></a><br />
        <i class="dim"><?= $item['itemtype'] ?></i>
       </td>
       <td class="centered"><?= $CUSTOM_DESC[$item['custom']] ?></td>
      </tr>
<?php
      $rowclass = alt_row_class($rowclass);
    }
?>
     </table>
     <?php if($max_pages > 1) echo $pages; ?>
     <?php if(count($items) >= 7) { ?>
     <ul>
      <li><a href="/encyclopedia.php">Browse all items</a></li>
     </ul>
     <?php } ?>
<?php
  }
}
else
{
  $pages = paginate($max_pages, $page, '/encyclopedia.php?page=%s' . $type);
?>
     <h5>Browse</h5>
     <?php if($max_pages > 1) echo $pages; ?>
     <table>
      <tr class="titlerow"><th></th><th>Item</th><th class="centered"><?= $avail_heading ?></th></tr>
<?php
    $rowclass = begin_row_class();
    foreach($items as $item)
    {
?>
      <tr class="<?= $rowclass ?>">
       <td class="centered"><?= item_display($item, '') ?></td>
       <td>
        <a href="/encyclopedia2.php?i=<?= $item['idnum'] ?>" style="font-weight:bold;"><?= $item['itemname'] ?></a><br />
        <i class="dim"><?= $item['itemtype'] ?></i>
       </td>
       <td class="centered"><?= $CUSTOM_DESC[$item['custom']] ?></td>
      </tr>
<?php
      $rowclass = alt_row_class($rowclass);
    }
?>
     </table>
     <?php if($max_pages > 1) echo $pages; ?>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
