<?php
require_once 'commons/grammar.php';

function get_room_inventory($username, $room, $num_items, $num_pages, $page, $sort)
{
  if($page < 1 || $page > $num_pages || $num_pages === false || $num_items === false )
    $page = 1;

  if($page == $num_pages)
  {
    $limit = $num_items % 2000;

    if($limit == 0)
      $limit = 2000;
  }
  else
    $limit = 2000;

  if($sort == 'itemname')
    $order = 'a.itemname ASC';
  else if($sort == 'itemtype')
    $order = 'b.itemtype ASC,b.itemname ASC';
  else if($sort == 'ediblefood')
    $order = 'b.ediblefood DESC,b.itemname ASC';
  else if($sort == 'bulk')
    $order = 'b.bulk DESC,b.itemname ASC';
  else if($sort == 'message')
    $order = 'CONCAT(a.message,a.message2) ASC,b.itemname ASC';
  else // $sort == 'idnum'
    $order = 'a.idnum DESC';

  return fetch_multiple('
    SELECT
      a.idnum,a.creator,a.itemname,a.message,a.message2,a.health,a.changed,
      b.itemtype,b.bigname,b.bulk,b.weight,b.graphictype,b.graphic,b.durability,b.value,b.is_edible,b.ediblefood,b.book_text,b.action,b.nosellback
    FROM
      monster_inventory AS a,
      monster_items AS b
    WHERE 
      a.itemname=b.itemname
      AND a.user=' . quote_smart($username) . '
      AND a.location=' . quote_smart($room) . '
    ORDER BY ' . $order . '
    LIMIT ' . (($page - 1) * 2000) . ',' . $limit . '
  ');
}

function encyclopedia_sort($i1, $i2)
{
  $name1 = $i1['itemname'];
  $name2 = $i2['itemname'];

  if($name1 > $name2)
    return 1;
  else if($name1 < $name2)
    return -1;
  else
    return 0;
}
/*
function HouseSort($i1, $i2)
{
  $v1 = $i1[1];
  $v2 = $i2[1];

  $sortby = $i1[2];

  if($sortby == 'itemname')
  {
    // sort by what we want first, then by the itemtype
    $v1_val = $v1[$sortby] . $v1['itemtype'];
    $v2_val = $v2[$sortby] . $v2['itemtype'];
  }
  else if($sortby == 'itemtype')
  {
    // sort by what we want first, then by the itemname
    $v1_val = $v1[$sortby] . $v1['itemtype'] . $v1['itemname'];
    $v2_val = $v2[$sortby] . $v2['itemtype'] . $v2['itemname'];
  }
  else if($sortby == 'message')
  {
    $v1_val = (strlen($i1[0][$sortby]) == 0 ? chr(127) : $i1[0][$sortby]);
    $v2_val = (strlen($i2[0][$sortby]) == 0 ? chr(127) : $i2[0][$sortby]);
  }
  else if($sortby == 'idnum') // (in reverse order)
  {
    $v1_val = $i2[0][$sortby];
    $v2_val = $i1[0][$sortby];
  }
  // sort descending
  else
  {
    // turn the values into strings of equal length
    $v1_val = $v2[$sortby];
    $v2_val = $v1[$sortby];

    while(strlen($v1_val) < strlen($v2_val))
      $v1_val = '0' . $v1_val;

    while(strlen($v2_val) < strlen($v1_val))
      $v2_val = '0' . $v2_val;

    $v1_val .= $v1['itemname'];
    $v2_val .= $v2['itemname'];
  }

  $v1_val = strtoupper($v1_val);
  $v2_val = strtoupper($v2_val);

  if($v1_val > $v2_val)
    return 1;
  else if($v1_val < $v2_val)
    return -1;
  else
    return 0;
}
*/
function get_inventory($whereat, $aisle, $user)
{
  $command = '';

  if($whereat == 'newmail')
  {
    $command = 'SELECT * ' .
               'FROM monster_inventory ' .
               'WHERE `user`=' . quote_smart($user['user']) . " AND `location`='storage' " .
               'ORDER BY itemname ASC';
  }
  else if($whereat == 'mystore')
  {
    $command = 'SELECT * ' .
               'FROM monster_inventory ' .
               "WHERE `user`=" . quote_smart($user["user"]) . " AND `location`='storage' " .
               "ORDER BY itemname ASC";
  }
  else if($whereat == "userstore")
  {
    $command = 'SELECT * ' .
               'FROM monster_inventory ' .
               'WHERE `user`=' . quote_smart($aisle) . ' ' .
               'AND forsale>0 ' .
               'ORDER BY itemname,forsale ASC';
  }
  else if($whereat == "newtrade")
  {
    $command = 'SELECT * ' .
               'FROM monster_inventory ' .
               "WHERE `user`=" . quote_smart($user["user"]) . " AND `location`='storage' " .
               'ORDER BY itemname ASC';
  }
  else
  {
    $command = 'SELECT * ' .
               'FROM monster_inventory ' .
               "WHERE `user`=" . quote_smart($user["user"]) . " AND `location`=" . quote_smart($whereat) . " " .
               'ORDER BY itemname ASC';
  }

  return $GLOBALS['database']->FetchMultiple($command);
}

function display_inventory($whereat, $my_inventory, $user, $pets)
{
  global $SPECIAL_CHECKALL;

  $bgcolor = begin_row_class();
  $now = time();

  if(count($my_inventory) > 0)
  {
    echo '<table><tr class="titlerow">';

    if($SPECIAL_CHECKALL === true)
      echo '  <th><input type="checkbox" name="checkall" id="checkall" onclick="javascript:check_all();" /></th>';
    else if($whereat == 'home' || $whereat == 'storage' || $whereat == 'newtrade' || $whereat == 'newmail' || $whereat == 'userstore' || $whereat == 'mystore')
      echo '<th></th>';

    // column for graphics
    echo '<th></th>' .
         '<th>Size/Weight</th>' .
         '<th>Name</th>';

    if($whereat != 'newmail' && $whereat != 'userstore')
      echo '<th>Type</th>';

//    if($whereat)
      echo '<th>Condition</th>';

    if($whereat == 'home' || $whereat == 'storage' || $whereat == 'userstore')
      echo '<th>Meal&nbsp;Size</th>';

    // the store does not report the maker of an item (maker is always psypets)
    if($whereat == 'home' || $whereat == 'storage'|| $whereat == 'newtrade'  || $whereat == 'mystore' || $whereat == 'userstore')
      echo '<th>Maker</th>';

    // when sending an item, we want to know if we're sending an item from home, or storage
    if($whereat == 'newmail')
      echo '<th>Location</th>';

    // in the store, we want the buy value
    if($whereat == 'mystore' || $whereat == 'userstore')
      echo '<th>Buy&nbsp;Price</th>';

    // in storage, we want the sell value
    if($whereat == 'storage'|| $whereat == 'newtrade' || $whereat == 'mystore')
      echo '<th>Gamesell</th>';

    // when writing a new mail, we want to know the shipping cost
    if($whereat == 'newmail')
      echo '<th>Shipping</th>';

    // comments at home and in storage
    if($whereat == 'newtrade' || $whereat == 'home' || $whereat == 'newmail' || $whereat == 'userstore' || $whereat == 'mystore')
      echo '<th>Comment</th>';

    echo '</tr>';

    foreach($my_inventory as $my_item)
    {
      if($whereat == 'userstore')
      {
        if($last_itemname != $my_item['itemname'])
        {
          $last_itemname = $my_item['itemname'];
          $cheapest_sale = $my_item['forsale'];
          $red_row = false;
        }
      
        if($my_item['forsale'] > $cheapest_sale)
          $red_row = true;
      }

      // at home and storage, items are kept as references to the real item - so let's de-reference them, here...
      if($whereat == 'home' || $whereat == 'storage' || $whereat == 'newtrade' || $whereat == 'newmail' || $whereat == 'mystore' || $whereat == 'userstore')
        $item = get_item_byname($my_item['itemname']);
      // at the store, we get the items directly, so...
      else
        $item = $my_item;

      if($item['cursed'] == 'yes' && ($whereat == 'mystore' || $whereat == 'userstore' || $whereat == 'newmail' || $whereat == 'newtrade'))
        continue;

      if($item['noexchange'] == 'yes' && ($whereat == 'mystore' || $whereat == 'userstore' || $whereat == 'newmail' || $whereat == 'newtrade'))
        continue;

      $bgcolor = alt_row_class($bgcolor);

      if($red_row)
        $realbgcolor = backlight_alert($bgcolor);
      else
        $realbgcolor = $bgcolor;

      echo '<tr class="' . $realbgcolor . '">';

      if($whereat == 'storage' || $whereat == 'newtrade' || $whereat == 'home' || $whereat == 'newmail' || $whereat == 'userstore' || $whereat == 'mystore')
        $namevalue = $my_item['idnum'];
      else
        $namevalue = preg_replace("/ /", '_', $my_item['itemname']);

      if($whereat == 'mystore')
        echo '<td></td>';
      else if($whereat == 'userstore')
      {
        if($user['money'] < $my_item['forsale'])
          echo '<td><input type="checkbox" name="' . $namevalue . '" disabled /></td>';
        else
          echo '<td><input type="checkbox" name="' . $namevalue . '"' . ($_POST[$namevalue] == 'on' || $_POST[$namevalue] == 'yes' ? ' checked' : '') . ' /></td>';
      }
      else
        echo '<td><input type="checkbox" name="' . $namevalue . '"' . ($_POST[$namevalue] == 'on' || $_POST[$namevalue] == 'yes' ? ' checked' : '') . ' /></td>';

      if($whereat == 'mystore')
        echo '<td align="center">' . item_display_extra($item, '', ($user['inventorylink'] == 'yes')) . '</td>';
      else
        echo '<td align="center">' . item_display_extra($item, '', true) . '</td>';

      echo '<td align="center">' . ($item['bulk'] / 10) . ' / ' . ($item['weight'] / 10) . '</td>';

      echo '<td>' . $item['itemname'] . '</td>';

      if($whereat != 'newmail' && $whereat != 'userstore')
        echo '<td>' . $item['itemtype'] . '</td>';

//      if($whereat)
        echo '<td>' . durability($my_item['health'], $item['durability']) . '</td>';

      // home and storage tells us how filling an item is
      if($whereat == 'home' || $whereat == 'storage' || $whereat == 'userstore')
      {
        echo '<td>';

        if($item['is_edible'] == 'no')
          echo '<i class="dim">Inedible</i>';

        if($item['is_edible'] == 'yes' && count($pets) > 0)
        {
          $pet = reset($pets);

          if($item['ediblefood'] < 0)
            echo '<span class="failure">Bad to eat</span>';
          else if($item['ediblefood'] > 0)
          {
            $ratio = max_food($pet) / $item['ediblefood'];

            if($ratio < .80)
              $food_size = 'Too much';
            else if($ratio < 1.5)
              $food_size = 'A full meal';
            else if($ratio < 3)
              $food_size = 'A light meal';
            else
              $food_size = 'A snack';

            echo $food_size;

            if(count($pets) > 1)
              echo ' for ' . $pet['petname'];
          }
          else
            echo 'Unfilling';
        }

        echo '</td>';
      }

      // the store does not report the maker of an item (maker is always psypets)
      if($whereat == 'home' || $whereat == 'storage' || $whereat == 'newtrade' || $whereat == 'mystore' || $whereat == 'userstore')
      {
        $maker = item_maker_display($my_item['creator'], true);
        
        echo '<td>' . $maker . '</td>';
      }

      // when sending an item, we want to know if we're sending an item from home, or storage
      if($whereat == 'newmail')
        echo '<td>' . $my_item['location'] . '</td>';

      // in the store, we want the buy value
      if($whereat == 'userstore')
        echo '<td align="center">' . $my_item['forsale'] . '<span class="money">m</span></td>';

      // when managing your store, you need to set the value
      if($whereat == 'mystore')
      {
        if($my_item['forsale'] > 0)
          echo '<td><input name="' . $namevalue . '_sellfor" value="' . $my_item['forsale'] . '" style="width:48px;" maxlength="7" /></td>';
        else
          echo '<td><input name="' . $namevalue . '_sellfor" style="width:48px;" maxlength="7" /></td>';
      }

      // in storage, we want the sell value
      if($whereat == 'storage' || $whereat == 'newtrade' || $whereat == 'mystore')
        echo '<td align="center">' . ceil($item['value'] * sellback_rate()) . '<span class="money">m</span></td>';

      // when writing mail, we want to know the shipping cost
      if($whereat == 'newmail')
        echo '<td align="center">' . ceil($item['weight'] / 5.0) . '<span class="money">m</span></td>';

      if($whereat == 'newtrade' || $whereat == 'home' || $whereat == 'newmail' || $whereat == 'userstore' || $whereat == 'mystore')
      {
        echo '<td>' . format_text($my_item['message']) . '<br />' . format_text($my_item['message2']);

        if(strlen($item['action']) > 0)
        {
          $item_action = explode(';', $item['action']);
          echo ' [ <a href="/itemaction.php?idnum=' . $my_item['idnum'] . '">' . $item_action[0] . '</a> ]';
        }

        echo '</td>';
      }

      echo '</tr>';
    }

    echo '</table>';
  }
}

function render_inventory_xhtml_2_list($inventory_list)
{
  global $user, $userpets, $now;

  echo '<table class="inventory">';

  $rowstyle = alt_row_class(begin_row_class());

  foreach($inventory_list as $my_item)
  {
    $item = get_item_byname($my_item['itemname']);

    $namevalue = $my_item['idnum'];

    $meal_size = meal_size_description($item, $userpets);

    $itemmaker = item_maker_display($my_item['creator']);

    if($user['backlightnew'] == 'yes' && $my_item['changed'] > $now - $user['backlighttime'])
      $realrowstyle = backlight($rowstyle);
    else
      $realrowstyle = $rowstyle;
?>
<tr class="<?= $realrowstyle ?>" id="item_<?= $my_item['idnum'] ?>">
 <td class="centered"><div id="checkbox_<?= $my_item['idnum'] ?>"><input type="checkbox" name="<?= $namevalue ?>"<?= ($_POST[$namevalue] == 'on' || $_POST[$namevalue] == 'yes' ? ' checked' : '') ?> /></div></td>
 <td class="centered"><?= item_display_extra($item, $extra, ($user['inventorylink'] == 'yes')) ?></td>
 <td class="centered"><div><?= ($item['bulk'] / 10) . '/' . ($item['weight'] / 10) ?></div></td>
 <td><?php
    if(strlen($item['action']) > 0)
    {
      $item_action = explode(';', $item['action']);
      echo '<a href="/itemaction.php?idnum=' . $my_item['idnum'] . '" title="' . $item_action[0] . '" class="item-action">' . $item['itemname'] . '</a>';
    }
    else
      echo $item['itemname'];

    if($item['is_edible'] == 'yes')
      $food_display = '<a onmouseover="Tip(\'<h5>Meal&nbsp;size</h5>' . htmlentities($meal_size, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '\');">Meal&nbsp;size&nbsp;info</a>';
    else
      $food_display = '<i class="dim">Inedible</i>';
      
    echo '
      </td>
      <td>' . $item['itemtype'] . '</td>
      <td class="centered"><div>' . ($item['nosellback'] == 'yes' ? '&mdash;' : ceil(sellback_rate() * $item['value']) . '<span class="money">m</span>') . '</div></td>
      <td>' . $food_display . '</td>
      <td>' . (strlen($my_item['message']) > 0 ? $my_item['message'] . '<br />' : '') . $my_item['message2'] . '</td>
      </tr>
    ';
  }

  echo '</tr></table><div class="endinventory"></div>';
}

function render_inventory_xhtml_2_mystore($inventory_list)
{
  global $user, $userpets, $now;

  echo '
    <table class="inventory">
     <tr class="titlerow">
      <th></th><th></th><th></th>
      <th>Item</th>
      <th class="righted"><acronym title="Gamesell Value">GV</acronym></th>
      <th class="righted"><acronym title="Seller\'s Market">SM</acronym></th>
      <th>Maker</th>
      <th>List Price</th>
      <th></th>
     </tr>
  ';

  $rowstyle = alt_row_class(begin_row_class());

  foreach($inventory_list as $my_item)
  {
    $item = get_item_byname($my_item['itemname']);

    $namevalue = $my_item['idnum'];

    $itemmaker = item_maker_display($my_item['creator']);

    if($user['backlightnew'] == 'yes' && $my_item['changed'] > $now - $user['backlighttime'])
      $realrowstyle = backlight($rowstyle);
    else
      $realrowstyle = $rowstyle;

    if(!array_key_exists($my_item['itemname'], $market_prices))
    {
      if($item['nosellback'] == 'yes')
        $gamesell_note = '&ndash;';
      else
        $gamesell_note = ceil($item['value'] * sellback_rate()) . '<span class="money">m</span>';
    
      $bid = get_highbid_byitem($my_item['itemname'], 0);

      if($bid === false)
        $seller_note = '<span class="dim">none</span>';
      else
        $seller_note = '<a href="reversemarket.php">' . $bid['bid'] . '<span class="money">m</span></a>';

      $market_prices[$my_item['itemname']]['gamesell_note'] = $gamesell_note;
      $market_prices[$my_item['itemname']]['seller_note'] = $seller_note;
    }
?>
<tr class="<?= $realrowstyle ?>" id="item_<?= $my_item['idnum'] ?>">
  <td class="centered"><div id="checkbox_<?= $my_item['idnum'] ?>"><input type="checkbox" name="<?= $namevalue ?>"<?= ($_POST[$namevalue] == 'on' || $_POST[$namevalue] == 'yes' ? ' checked' : '') ?> /></div></td>
  <td class="centered"><?= item_display_extra($item, $extra, ($user['inventorylink'] == 'yes')) ?></td>
  <td class="centered"><div><?= ($item['bulk'] / 10) . '/' . ($item['weight'] / 10) ?></div></td>
  <td><div><?php
    if(strlen($item['action']) > 0)
    {
      $item_action = explode(';', $item['action']);
      echo '<a href="/itemaction.php?idnum=' . $my_item['idnum'] . '" title="' . $item_action[0] . '" class="item-action">' . $item['itemname'] . '</a>';
    }
    else
      echo $item['itemname'];

    echo '
      </div></td>
      <td align="right"><span class="gamesell_value">' . $market_prices[$my_item['itemname']]['gamesell_note'] . '</span></td>
      <td align="right">' . $market_prices[$my_item['itemname']]['seller_note'] . '</td>
      <td>' . item_maker_display($my_item['creator']) . '</td>
    ';

    if($item['cursed'] == 'yes' || $item['noexchange'] == 'yes')
      echo '<td class="centered"><i class="dim">may not list</i></td>';
    else if($my_item['forsale'] > 0)
      echo '<td class="centered"><nobr><input class="list_price" id="i' . $item['idnum'] . 'n' . $my_item['idnum'] . '" name="' . $my_item['idnum'] . '_sellfor" value="' . $my_item['forsale'] . '" style="width:48px;" maxlength="7" /><span class="money">m</span></nobr></td>';
    else
      echo '<td class="centered"><nobr><input class="list_price" id="i' . $item['idnum'] . 'n' . $my_item['idnum'] . '" name="' . $my_item['idnum'] . '_sellfor" style="width:48px;" maxlength="7" /><span class="money">m</span></nobr></td>';

    echo '<td><a href="#" onclick="copyprice(' . $item['idnum'] . ', ' . $my_item['idnum'] . '); return false;"><img src="gfx/mimic.png" width="16" height="16" alt="... for all such items" border="0" /></a></td>';

    echo '</td>';
  }

  echo '</tr></table><div class="endinventory"></div>';
}

function meal_size_description($item, $pets)
{
  if($item['is_edible'] == 'no')
    return '<i class="dim">Inedible</i>';

  if($item['is_edible'] == 'yes' && count($pets) > 0)
  {
    if($item['ediblefood'] < 0)
      $meal_size = '<span class="failure">Bad to eat</span>';
    else if($item['ediblefood'] == 0)
      $meal_size = 'Unfilling';
    else
    {
      $meal_size = '';

      $i = 0;
      foreach($pets as $pet)
      {
        if($i++ >= 10)
        {
          $meal_size .= '<i class="dim">(see encyclopedia entry for more)</i>';
          break;
        }

        $ratio = max_food($pet) / $item['ediblefood'];

        if($ratio < .80)
          $food_size = 'Too much';
        else if($ratio < 1.5)
          $food_size = 'A full meal';
        else if($ratio < 3)
          $food_size = 'A light meal';
        else
          $food_size = 'A snack';

        $meal_size .= $food_size;
        if(count($pets) > 1)
          $meal_size .= ' for ' . java_safe($pet['petname']) . '<br />';
      }
    }
  }
  else
    $meal_size = '';

  return $meal_size;
}

function render_inventory_xhtml_2($inventory_list, $checkboxes = true)
{
  global $user, $userpets, $now;

  echo '<ul class="inventory items">';

  $rowstyle = alt_row_class(begin_row_class());

  foreach($inventory_list as $my_item)
  {
    $item = get_item_byname($my_item['itemname']);

    $namevalue = $my_item['idnum'];

    $meal_size = meal_size_description($item, $userpets);

    $itemmaker = item_maker_display($my_item['creator']);

    if($user['backlightnew'] == 'yes' && $my_item['changed'] > $now - $user['backlighttime'])
      $realrowstyle = backlight($rowstyle);
    else
      $realrowstyle = $rowstyle;

    $food_display = '<tr><th valign=\\\'top\\\'>Meal&nbsp;size</th><td>' . htmlentities($meal_size, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</td></tr>';

    if($user['iconhoverbox'] == 'yes')
      $extra = "onmouseover=\"Tip('<table class=\\'tip\\'><tr><th>Size/Weight</th><td>" . ($item['bulk'] / 10) . '/' . ($item['weight'] / 10) . '</td></tr><tr><th>Type</th><td>' . $item['itemtype'] . '</td></tr><tr><th>Condition</th><td>' . durability($my_item['health'], $item['durability']) . '</td></tr><tr><th>Maker</th><td>' . java_safe($itemmaker) . '</td></tr><tr><th>Gamesell</th><td>' . ($item['nosellback'] == 'yes' ? 'may not be sold to the game' : ceil($item['value'] * sellback_rate()) . "<span class=\\'money\\'>m</span>") . '</td></tr><tr><th>Comment</th><td>' . java_safe(format_text($my_item['message']) . '<br />' . format_text($my_item['message2'])) . "</td></tr>$food_display</table>')\"";
    else
      $extra = '';

    echo '<li class="centered ' . $realrowstyle . '" id="item_' . $my_item['idnum'] . '">' .
         item_display_extra($item, $extra, ($user['inventorylink'] == 'yes')) . '<br />';

    if($checkboxes)
      echo '<div id="checkbox_' . $my_item['idnum'] . '"><input type="checkbox" name="' . $namevalue . '"' . ($_POST[$namevalue] == 'on' || $_POST[$namevalue] == 'yes' ? ' checked' : '') . ' /></div>';

    if($item['bigname'] == 'yes')
      echo '<span class="size9">';

    if(strlen($item['action']) > 0)
    {
      $item_action = explode(';', $item['action']);
      echo '<a href="/itemaction.php?idnum=' . $my_item['idnum'] . '" title="' . $item_action[0] . '" class="item-action">' . $item['itemname'] . '</a>';
    }
    else
      echo $item['itemname'];

    if($item['bigname'] == 'yes')
      echo '</span>';

    if($my_item['forsale'] > 0)
      echo '<img src="gfx/forsale.png" title="selling in your store for ' . $my_item['forsale'] . 'm" alt="selling in your store for ' . $my_item['forsale'] . 'm" class="inlineimage" />';

    echo '</li>';
  }

  echo '</ul><div class="endinventory"></div>';
}

// NO NEED TO GET ITEM FROM DATABASE WITH THESE FUNCTIONS

function render_inventory_xhtml_3($inventory_list)
{
  global $user, $userpets, $now;

  echo '<ul class="inventory items">';

  $rowstyle = alt_row_class(begin_row_class());

  foreach($inventory_list as $item)
  {
    $namevalue = $item['idnum'];

    $meal_size = meal_size_description($item, $userpets);

    $itemmaker = item_maker_display($item['creator']);

    if($user['backlightnew'] == 'yes' && $item['changed'] > $now - $user['backlighttime'])
      $realrowstyle = backlight($rowstyle);
    else
      $realrowstyle = $rowstyle;

    $food_display = '<tr><th valign=\\\'top\\\'>Meal&nbsp;size</th><td>' . htmlentities($meal_size, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</td></tr>';

    if($user['iconhoverbox'] == 'yes')
      $extra = "onmouseover=\"Tip('<table class=\\'tip\\'><tr><th>Size/Weight</th><td>" . ($item['bulk'] / 10) . '/' . ($item['weight'] / 10) . '</td></tr><tr><th>Type</th><td>' . $item['itemtype'] . '</td></tr><tr><th>Condition</th><td>' . durability($item['health'], $item['durability']) . '</td></tr><tr><th>Maker</th><td>' . java_safe($itemmaker) . '</td></tr><tr><th>Gamesell</th><td>' . ($item['nosellback'] == 'yes' ? 'may not be sold to the game' : ceil($item['value'] * sellback_rate()) . "<span class=\\'money\\'>m</span>") . '</td></tr><tr><th>Comment</th><td>' . java_safe(format_text($item['message']) . '<br />' . format_text($item['message2'])) . "</td></tr>$food_display</table>')\"";
    else
      $extra = '';
?>
<li class="centered <?= $realrowstyle ?>" id="item_<?= $item['idnum'] ?>">
 <?= item_display_extra($item, $extra, ($user['inventorylink'] == 'yes')) ?><br />
 <div id="checkbox_<?= $item['idnum'] ?>"><input type="checkbox" name="<?= $namevalue ?>"<?= ($_POST[$namevalue] == 'on' || $_POST[$namevalue] == 'yes' ? ' checked' : '') ?> /></div>
<?php
    if($item['bigname'] == 'yes')
      echo '<span class="size9">';

    if(strlen($item['action']) > 0)
    {
      $item_action = explode(';', $item['action']);
      echo '<a href="/itemaction.php?idnum=' . $item['idnum'] . '" title="' . $item_action[0] . '" class="item-action">' . $item['itemname'] . '</a>';
    }
    else
      echo $item['itemname'];

    if($item['bigname'] == 'yes')
      echo '</span>';

    echo '</li>';
  }

  echo '</ul><div class="endinventory"></div>';
}

function render_inventory_xhtml_3_list($inventory_list)
{
  global $user, $userpets, $now;

  echo '<table class="inventory">';

  $rowstyle = alt_row_class(begin_row_class());

  foreach($inventory_list as $item)
  {
    $namevalue = $item['idnum'];

    $meal_size = meal_size_description($item, $userpets);

    $itemmaker = item_maker_display($item['creator']);

    if($user['backlightnew'] == 'yes' && $item['changed'] > $now - $user['backlighttime'])
      $realrowstyle = backlight($rowstyle);
    else
      $realrowstyle = $rowstyle;
?>
<tr class="<?= $realrowstyle ?>" id="item_<?= $item['idnum'] ?>">
 <td class="centered"><div id="checkbox_<?= $item['idnum'] ?>"><input type="checkbox" name="<?= $namevalue ?>"<?= ($_POST[$namevalue] == 'on' || $_POST[$namevalue] == 'yes' ? ' checked' : '') ?> /></div></td>
 <td class="centered"><?= item_display_extra($item, $extra, ($user['inventorylink'] == 'yes')) ?></td>
 <td class="centered"><?= ($item['bulk'] / 10) . '/' . ($item['weight'] / 10) ?></td>
 <td><?php
    if(strlen($item['action']) > 0)
    {
      $item_action = explode(';', $item['action']);
      echo '<a href="/itemaction.php?idnum=' . $item['idnum'] . '" title="' . $item_action[0] . '" class="item-action">' . $item['itemname'] . '</a>';
    }
    else
      echo $item['itemname'];

    if($item['is_edible'] == 'yes')
      $food_display = '<a onmouseover="Tip(\'<h5>Meal&nbsp;size</h5>' . htmlentities($meal_size, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '\');">Meal&nbsp;size&nbsp;info</a>';
    else
      $food_display = '<i class="dim">Inedible</i>';

    echo '
      </td>
      <td>' . $item['itemtype'] . '</td>
      <td class="centered">' . ($item['nosellback'] == 'yes' ? '&mdash;' : ceil(sellback_rate() * $item['value']) . '<span class="money">m</span>') . '</td>
      <td>' . $food_display . '</td>
      <td>' . (strlen($item['message']) > 0 ? $item['message'] . '<br />' : '') . $item['message2'] . '</td>
      </tr>
    ';
  }

  echo '</tr></table><div class="endinventory"></div>';
}

?>
