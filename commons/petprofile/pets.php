<?php
require_once 'commons/petbadges.php';

$petbadges = get_pet_badges($this_pet['idnum']);

$pet_level = pet_level($this_pet);

if($petbadges['level20'] == 'no' && $pet_level >= 20)
{
  $petbadges['level20'] = 'yes';
  set_pet_badge($this_pet, 'level20');
}

if($petbadges['level50'] == 'no' && $pet_level >= 50)
{
  $petbadges['level50'] = 'yes';
  set_pet_badge($this_pet, 'level50');
}

if($petbadges['level100'] == 'no' && $pet_level >= 100)
{
  $petbadges['level100'] = 'yes';
  set_pet_badge($this_pet, 'level100');
}

if($petbadges['oneyearold'] == 'no' && $pet_years >= 1)
{
  $petbadges['oneyearold'] = 'yes';
  set_pet_badge($this_pet, 'oneyearold');
}

$pet_badge_count = 0;
foreach($PET_BADGE_DESC as $badge=>$desc)
{
  if($petbadges[$badge] == 'yes')
    $pet_badge_count++;
}

if($petbadges['10badges'] == 'no' && $pet_badge_count >= 10)
{
  $petbadges['10badges'] = 'yes';
  set_pet_badge($this_pet, '10badges');
  $pet_badge_count++;
}

if($petbadges['20badges'] == 'no' && $pet_badge_count >= 20)
{
  $petbadges['20badges'] = 'yes';
  set_pet_badge($this_pet, '20badges');
  $pet_badge_count++;
}

if($petbadges['30badges'] == 'no' && $pet_badge_count >= 30)
{
  $petbadges['30badges'] = 'yes';
  set_pet_badge($this_pet, '30badges');
  $pet_badge_count++;
}

if($this_pet['user'] == $user['user'])
{
  $other_pets = fetch_multiple('SELECT idnum,petname,location,love_level,love_exp,zombie,changed FROM monster_pets WHERE user=' . quote_smart($user['user']) . ' ORDER BY location ASC,orderid ASC');

  if(count($other_pets) > 1)
  {
?>
<div style="float:right; min-width: 200px; margin-top: 10px; border-left: 1px dashed #999; margin-left: 10px; padding-left: 10px; background-color: #fff; min-height: 200px;">
<h5>Your Other Pets</h5>
<ul class="plainlist">
<?php
  $previous_pet = false;

  foreach($other_pets as $other_pet)
  {
    if($previous_pet !== false && $previous_pet['location'] != 'shelter' && $other_pet['location'] == 'shelter')
    {
      $shelter_pets++;
      echo '</ul><h5>Pets in Daycare</h5><ul class="plainlist">';
    }

    if($other_pet['love_exp'] >= level_exp($other_pet['love_level']) && $other_pet['zombie'] == 'no')
      $style = 'background: url(//' . $SETTINGS['static_domain'] . '/gfx/ui/love.png) left center no-repeat';
    else
      $style = '';
    
    if($other_pet['idnum'] == $this_pet['idnum'])
      echo '<li><b style="padding-left:15px;display:block;' . $style . '">' . $other_pet['petname'] . $extra . '</b></li>';
    else
      echo '<li><a style="padding-left:15px;display:block;' . $style . '" href="?petid=' . $other_pet['idnum'] . '">' . $other_pet['petname'] . $extra . '</a></li>';
      
    $previous_pet = $other_pet;
  }
?>
</ul>
</div>
<?php
  }
}
?>
<table border="0" style="padding-top:8px;">
 <tr>
  <td><?= pet_graphic($this_pet) ?></td>
  <td>
   <h4><a href="<?= $profile_url ?>?resident=<?= link_safe($owner['display']) ?>"><?= $owner['display'] ?></a> &gt; <?= $this_pet['petname'] ?></h4>
<?php
foreach($petbadges as $badge=>$value)
{
  if($value == 'yes')
    echo '<img src="//' . $SETTINGS['static_domain'] . '/gfx/badges/pet/' . $badge . '.png" height="20" width="20" title="' . $PET_BADGE_DESC[$badge] . '" /> ';
}
?>
  </td>
 </tr>
</table>
<?php
if($owner['idnum'] == $user['idnum'])
{
?>
<form action="/pet/savenote.php" method="post" id="save_mininote">
<input type="hidden" name="petid" value="<?= $this_pet['idnum'] ?>" />
<p title="Note (appears when you hover over the pet at home)"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/menu/notepad.png" class="inlineimage" width="24" height="24" alt="" /><input type="text" name="mininote" id="mininote_note" value="<?= htmlentities($this_pet['mininote']) ?>" maxlength="100" style="width:300px;" /> <input type="submit" value="Save" /> <img src="/gfx/throbber.gif" class="throbber inlineimage" style="display:none;" width="16" height="16" /></p>
</form>
<?php
}
?>