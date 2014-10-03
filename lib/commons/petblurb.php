<?php
require_once 'commons/grammar.php';
require_once 'commons/petlib.php';
require_once 'commons/userlib.php';

function pregnancy_blurb(&$mypet)
{
  if($mypet['pregnant_asof'] >= 20 * 24)
    return '<span class="success">is near birthing!</span><a href="/help/pregnancy.php" class="help">?</a><br />';
  else if($mypet['pregnant_asof'] >= 10 * 24)
    return '<span class="success">is very pregnant!</span><a href="/help/pregnancy.php" class="help">?</a><br />';
  else if($mypet['pregnant_asof'] > 0)
    return '<span class="success">is pregnant!</span><a href="/help/pregnancy.php" class="help">?</a><br />';
}

function wound_description($time)
{
  if($time >= 72)
    return 'nasty wound!';
  else if($time >= 48)
    return 'bad wound!';
  else if($time >= 24)
    return 'wound.';
  else if($time >= 12)
    return 'small wound.';
  else
    return 'minor wound.';
}

function pet_blurb(&$user, &$house, $petnum, $maxpets, &$mypet, $loveoptions, $level_button = true)
{
  global $now, $SETTINGS;

  $half_hour_OK = false;

  $cause_of_death = array(
    'starved' => 'starved to death',
    'magical' => 'was transported to the elemental plane of negative energy',
    'bonestaff' => 'was striken down by powerful magics',
  );

  if($mypet['eggplant'] == 'yes')
    $equip_class = ' class="transparent_image"';
  else
    $equip_class = '';

  if($mypet['toolid'] > 0)
  {
    $tool = get_inventory_byid($mypet['toolid']);
    $tool_item = get_item_byname($tool['itemname']);

    $equipment_health = durability($tool['health'], $tool_item['durability']);

    $equipment_img = item_display_extra($tool_item, $equip_class . "onmouseover=\"Tip('<table class=\\'tip\\'><tr><th>Item&nbsp;Name</th><td>" . tip_safe($tool['itemname']) . '</td></tr><tr><th>Type</th><td>' . $tool_item['itemtype'] . "</td></tr><tr><th>Condition</th><td>" . $equipment_health . "</td></tr><tr><th valign=\\'top\\'>Comment</th><td>" . tip_safe($tool['message'] . '<br />' . $tool['message2']) . "</td></tr></table>');\"");
  }

  if($mypet['keyid'] > 0)
  {
    $key = get_inventory_byid($mypet['keyid']);
    $key_item = get_item_byname($key['itemname']);

    $key_img = item_display_extra($key_item, $equip_class . "onmouseover=\"Tip('<table class=\\'tip\\'><tr><th>Item&nbsp;Name</th><td>" . tip_safe($key['itemname']) . '</td></tr><tr><th>Type</th><td>' . $key_item['itemtype'] . "</td></tr><tr><th valign=\\'top\\'>Comment</th><td>" . tip_safe($key['message'] . '<br />' . $key['message2']) . "</td></tr></table>');\"");
  }

  echo '
    <div style="float:left; width:50px;">
     <div style="width:50px; height:32px; padding-top:2px;' . (($equipment_health == 'Worn' && $house['worn_indicator'] == 'color') ? ' background-color: #fca;' : '') . '" class="centered">
      ' . $equipment_img . '
  ';

  if($equipment_health == 'Worn' && $house['worn_indicator'] == 'text')
    echo '</div><div class="failure size9 centered">worn';

  echo '
     </div>
     <div style="width:50px; height:32px; padding-top:2px;" class="centered">
      ' . $key_img . '
     </div>
     <div class="centered" style="padding-top:2px;">
  ';

  if($mypet['zombie'] == 'no')
    echo '<a href="/pet_entool.php?id=' . $mypet['idnum'] . '"><img src="/gfx/the_hand.png" width="16" height="16" alt="Equip" /></a>';

  if($mypet['original'] == 'no')
    echo '<img src="/gfx/exchange_pet.png" width="12" height="16" title="You have NOT had this pet since it was level 1." />';

  if($mypet['ascend'] == 'yes')
    echo '<br /><a href="/petascend.php?petid=' . $mypet['idnum'] . '"><img src="/gfx/ascend.png" width="16" height="16" alt="this pet may be reincarnated..." /></a>';

  echo '
     </div>
    </div>
    <div style="float:left; width:60px;">
  ';
  
  echo '<div style="width:60px; height:55px; padding: 6px 0 0 6px;' . ($mypet['sleeping'] == 'yes' ? ' background: transparent url(\'gfx/sleeping.gif\') no-repeat scroll top center;' : '') . '">';

  if($mypet['mininote'] != '')
    $note = '<th>Note</th><td>' . htmlentities(str_replace('\'', '\\\'', $mypet['mininote'])) . '</td>';
  else
    $note = '';
  
  echo pet_graphic($mypet, true, "onmouseover=\"Tip('<table class=\\'tip\\'><tr><th>Age</th><td>" . PetAge($mypet['birthday'], $now) . '</td></tr><tr><th>Size</th><td>' . (pet_size($mypet) / 10) . '</td></tr><tr><th>Gender</th><td>' . ucfirst($mypet['gender']) . "</td></tr><tr><th>Fixed?</th><td>" . ($mypet['prolific'] == 'yes' ? 'No' : 'Yes') . '</td></tr>' . $note . "</table>');\"");

  echo '</div><div class="centered"><a href="/petlevelhistory.php?petid=' . $mypet['idnum'] . '">Level ' . pet_level($mypet) . '</a><br />';

  $love_exp = level_exp($mypet['love_level']);

  if($mypet['dead'] == 'no' && $mypet['zombie'] == 'no')
  {
    echo '<div style="clear:both;">';

    echo '</div><div style="clear:both; padding-top:2px;">';

    if($mypet['love_exp'] >= $love_exp)
    {
      if($mypet['changed'] == 'yes')
        echo
          '<div style="margin-left:3px;border-radius:4px;-moz-border-radius:4px;width:51px; background-color:#aaa;text-align:left;" alt="Affection: 100%" title="Affection: 100%">',
          '<img src="//' . $SETTINGS['static_domain'] . '/gfx/ui/love.png" width="9" height="9" style="display:block;padding:1px;margin:0;" />',
          '</div></a>'
        ;
      else
        echo
          '<a href="/affectionup.php?petid=' . $mypet['idnum'] . '"><div style="margin-left:3px;border-radius:4px;-moz-border-radius:4px;width:51px; background-color:#db0;text-align:left;" alt="Affection: 100%" title="Affection: 100%">',
          '<img src="//' . $SETTINGS['static_domain'] . '/gfx/ui/love.png" width="9" height="9" style="display:block;padding:1px;margin:0;" />',
          '</div></a>'
        ;
    }
    else
    {
      $final_exp = min($love_exp, $mypet['love_exp']);

      $bar_width = floor(($final_exp * 40) / $love_exp) + 11;
      $percent = floor(($final_exp * 100) / $love_exp);

      echo
        '<div style="margin-left:3px;border-radius:4px;-moz-border-radius:4px;width:' . $bar_width . 'px;background-color:' . ($mypet['changed'] == 'yes' ? '#aaa' : '#f99') . ';text-align:left;" alt="Affection: ' . $percent . '%" title="Affection: ' . $percent . '%">',
        '<img src="//' . $SETTINGS['static_domain'] . '/gfx/ui/love.png" width="9" height="9" style="display:block;padding:1px;margin:0;" />',
        '</div>'
      ;

    }
    
    echo '</div>';
  }
  

  if($mypet['dead'] != 'no')
    echo '<a href="/petmoveon.php?petid=' . $mypet['idnum'] . '"><img src="//' . $SETTINGS['static_domain'] . '/gfx/badges/pet/died.png" width="20" height="20" alt="Move on?" title="Move on?" /></a>';
?>
 </div>
</div>
<div style="width:120px; height:110px; overflow: auto; float:left; padding-left:14px;">
<?php
  echo '<span style="position:relative; left:-14px; top:2px; margin-right:-12px;">' . gender_graphic($mypet['gender'], $mypet['prolific']) . '</span>',
       '<b>' . $mypet['petname'] . '</b><br />';

  if($mypet['dead'] == 'no')
  {
    if($mypet['sleeping'] == 'no')
    {
      if($mypet['caffeinated'] > 0)
        echo '<span class="progress">is caffeinated!</span>' . ($maxpets < 3 ? '<a href="/help/energy.php" class="help">?</a>' : '') . '<br />';
      else if($mypet['energy'] <= 0)
        echo '<span class="failure">is exhausted.</span>' . ($maxpets < 3 ? '<a href="/help/energy.php" class="help">?</a>' : '') . '<br />';
      else if($mypet['energy'] <= 3)
        echo 'is very tired.' . ($maxpets < 3 ? '<a href="/help/energy.php" class="help">?</a>' : '') . '<br />';
      else if($mypet['energy'] <= 6)
        echo 'is tired.' . ($maxpets < 3 ? '<a href="/help/energy.php" class="help">?</a>' : '') . '<br />';
      else if($mypet['energy'] >= max_energy($mypet) * .8)
        echo 'is wide awake.<br />';
      else
        echo '...<br />';

      if($mypet['food'] <= 0)
        echo '<span class="failure">is starving.</span>' . ($maxpets < 3 ? '<a href="/help/food.php" class="help">?</a>' : '') . '<br />';
      else if($mypet['food'] <= 3)
        echo 'is very hungry.' . ($maxpets < 3 ? '<a href="/help/food.php" class="help">?</a>' : '') . '<br />';
      else if($mypet['food'] <= 6)
        echo 'is hungry.' . ($maxpets < 3 ? '<a href="/help/food.php" class="help">?</a>' : '') . '<br />';
      else if($mypet['food'] >= max_food($mypet) * .9)
        echo 'is stuffed.<br />';
      else if($mypet['food'] >= max_food($mypet) * .75)
        echo 'is full.<br />';
      else
        echo '...<br />';

      if($mypet['safety'] <= 0)
        echo '<span class="failure">cowers in a corner.</span>' . ($maxpets < 3 ? '<a href="/help/safety.php" class="help">?</a>' : '') . '<br />';
      else if($mypet['safety'] <= 5)
        echo 'is a little jumpy.' . ($maxpets < 3 ? '<a href="/help/safety.php" class="help">?</a>' : '') . '<br />';
      else if($mypet['safety'] >= max_safety($mypet) * .8)
        echo 'is very comfortable.<br />';
      else
        echo '...<br />';

      if($mypet['love'] <= 0)
        echo '<span class="failure">whines at you.</span>' . ($maxpets < 3 ? '<a href="/help/love.php" class="help">?</a>' : '') . '<br />';
      else if($mypet['love'] <= 6)
        echo 'misses your attention.' . ($maxpets < 3 ? '<a href="/help/love.php" class="help">?</a>' : '') . '<br />';
      else if($mypet['love'] >= max_love($mypet) * .8)
        echo 'nuzzles you.<br />';
      else
        echo '...<br />';

      if($mypet['esteem'] <= 0)
        echo '<span class="failure">seems depressed.</span>' . ($maxpets < 3 ? '<a href="/help/esteem.php" class="help">?</a>' : '') . '<br />';
      else if($mypet['esteem'] <= 6)
        echo 'isn\'t feeling down.' . ($maxpets < 3 ? '<a href="/help/esteem.php" class="help">?</a>' : '') . '<br />';
      else if($mypet['esteem'] >= max_Esteem($mypet) * .8)
        echo 'is very happy.<br />';
      else if($mypet['esteem'] >= max_Esteem($mypet) * .66)
        echo 'is happy.<br />';
      else
        echo '...<br />';

      if($mypet['inspired'] > 0)
        echo '<span class="success">is feeling inspired!</span><br />';
    }

    if($mypet['sleeping'] == 'yes')
      echo 'is sleeping.<br />';

    if($mypet['nasty_wound'] > 0)
      echo '<span class="failure">has a ' . wound_description($mypet['nasty_wound']) . '</span><a href="/help/wounds.php" class="help">?</a><br />';

    echo pregnancy_blurb($mypet);
  }
  else
    echo 'is dead. ' . ucfirst(he_she($mypet['gender'])) . ' ' . $cause_of_death[$mypet['dead']] . '!';
?>
</div>
<div style="clear:both;"></div>
<div style="margin-top:6px;">
<?php
  if($mypet['dead'] == 'no' && $mypet['zombie'] == 'no')
  {
    if(
      $loveoptions !== false &&
      ($now - $mypet['last_love'] > 30 * 60 || $mypet['last_love_by'] != $user['idnum'])
    )
    {
      $half_hour_OK = true;
?>
     <select name="love<?= $mypet['idnum'] ?>" id="love<?= $mypet['idnum'] ?>">
<?php
      if($mypet['sleeping'] == 'no')
      {
        foreach($loveoptions as $id=>$option)
          echo '<option value="' . $id . '"' . ($mypet['last_love_action'] == $id ? ' selected' : '') . '>' . $option . '</option>';
?>
      <option value="-2">Put to bed</option>
      <option value="0"<?= ($mypet['last_love_action'] == 0 ? ' selected' : '') ?>>Nothing for now</option>
<?php
      }
      else
      {
?>
      <option value="0">Nothing for now</option>
      <option value="-3">Wake up</option>
<?php
      }
      
      echo '</select>';

      if($maxpets > 1 && $user['showmimic'] == 'yes' && $mypet['sleeping'] == 'no')
        echo '<a onclick="set_all_actions(document.getElementById(\'love' . $mypet['idnum'] . '\').selectedIndex); return false;" href="#"><img src="/gfx/mimic.png" alt="... with all pets" height="16" width="16" border="0" /></a>';

    }
  }
  
  echo '</div>';

  return $half_hour_OK;
}
?>
