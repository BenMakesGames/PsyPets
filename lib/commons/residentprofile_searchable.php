<?php
$profile_user_age = birthdate_to_age($profile_user['birthday']);

if($searchable_profile['gender'] != 'none')
{
  if($searchable_profile['show_age'] == 'yes')
    $statement = 'Is ' . article($profile_user_age) . ' ' . $profile_user_age . ' year-old <a href="/directorysearch.php?action=search&gender=' . $searchable_profile['gender'] . '">' . $searchable_profile['gender'] . '</a>';
  else
    $statement = 'Is a <a href="/directorysearch.php?action=search&gender=' . $searchable_profile['gender'] . '">' . $searchable_profile['gender'] . '</a>';
}
else if($searchable_profile['show_age'] == 'yes')
  $statement .= 'Is ' . article($profile_user_age) . ' ' . $profile_user_age . ' year-old';

if(strlen($searchable_profile['location']) > 0)
{
  if(strlen($statement) == 0)
    $statement .= 'Is';
  $statement .= ' in ' . $searchable_profile['location'];
}

if($searchable_profile['locationsearch'] == 'yes')
{
  require_once 'commons/zip.php';
  
  $my_location = get_user_profile($user['idnum']);

  if(getDistance($searchable_profile['latitude'], $searchable_profile['longitude'], $my_location['latitude'], $my_location['longitude']) < 120)
  {
    if(strlen($statement) == 0)
      $statement = '<a href="directorysearch.php?action=search&nearby=yes">Lives nearby</a>';
    else
      $statement .= ' who <a href="directorysearch.php?action=search&nearby=yes">lives nearby</a>';
  }
}

if(strlen($statement) > 0)
{
?>
      <tr>
       <td valign="top"><img src="gfx/search.gif" width="16" height="16" alt="" /></td>
       <td><?= $statement ?>.</td>
      </tr>
<?php
}

if(strlen($searchable_profile['facebook']) > 0)
{
  if(is_numeric($searchable_profile['facebook']))
    $url = 'http://www.facebook.com/profile.php?id=' . $searchable_profile['facebook'];
  else
    $url = 'http://www.facebook.com/' . $searchable_profile['facebook'];
?>
      <tr>
       <td><img src="gfx/facebook_icon.png" width="16" height="16" alt="Facebook" /></td>
       <td><a href="<?= $url ?>">Facebook Profile</a></td>
      </tr>

<?php
}

if(strlen($searchable_profile['myspace']) > 0)
{
  $url = 'http://www.myspace.com/' . $searchable_profile['myspace'];
?>
      <tr>
       <td><img src="gfx/myspace_icon.png" width="16" height="16" alt="MySpace" /></td>
       <td><a href="<?= $url ?>">MySpace Profile</a></td>
      </tr>

<?php
}

if(strlen($searchable_profile['url']) > 0)
{
  $url = 'http://' . $searchable_profile['url'];
?>
      <tr>
       <td><img src="gfx/worldicon.png" width="16" height="16" alt="web site" /></td>
       <td><a href="<?= $url ?>"><?= $searchable_profile['url'] ?></a></td>
      </tr>

<?php
}

if(strlen($searchable_profile['aim']) > 0)
{
  $snlist = explode(',', $searchable_profile['aim']);
  $sndisplay = array();

  foreach($snlist as $snitem)
    $sndisplay[] = '<a href="aim:goim?screenname=' . $snitem . '">' . $snitem . '</a>';
?>
      <tr>
       <td valign="top"><img src="gfx/aimicon.gif" width="16" height="16" alt="AIM names" /></td>
       <td><?= implode(', ', $sndisplay) ?></td>
      </tr>
<?php
}

if(strlen($searchable_profile['yahoo']) > 0)
{
  $snlist = explode(',', $searchable_profile['yahoo']);
  $sndisplay = array();

  foreach($snlist as $snitem)
    $sndisplay[] = '<a href="ymsgr:sendIM?' . $snitem . '">' . $snitem . '</a>';
?>
      <tr>
       <td valign="top"><img src="gfx/yicon.gif" width="16" height="16" alt="Yahoo names" /></td>
       <td><?= implode(', ', $sndisplay) ?></td>
      </tr>
<?php
}

if(strlen($searchable_profile['msn']) > 0)
{
  $snlist = explode(',', $searchable_profile['msn']);
  $sndisplay = array();
?>
      <tr>
       <td valign="top"><img src="gfx/msnicon.png" width="16" height="16" alt="MSN names" /></td>
       <td><?= implode(', ', $snlist) ?></td>
      </tr>
<?php
}

if(strlen($searchable_profile['skype']) > 0)
{
  $snlist = explode(',', $searchable_profile['skype']);
  $sndisplay = array();

  foreach($snlist as $snitem)
    $sndisplay[] = '<a href="skype:' . $snitem . '?chat">' . $snitem . '</a>';
?>
      <tr>
       <td valign="top"><img src="gfx/skypeicon.png" width="16" height="16" alt="Skype names" /></td>
       <td><?= implode(', ', $sndisplay) ?></td>
      </tr>
<?php
}
?>
