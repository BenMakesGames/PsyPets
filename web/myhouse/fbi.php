<?php
require_once 'commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/questlib.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/statlib.php';

$fbi_quest = get_quest_value($user['idnum'], 'close encounter');

$dialog = false;

// let's begin!
if($now_month >= 11 && $fbi_quest['value'] == 1)
{
  if($_GET['dialog'] == 'sure')
  {
    $fbi_quest['value'] = 3;
    update_quest_value($fbi_quest['idnum'], 3);
  }
  else if($_GET['dialog'] == 'goaway')
  {
    $fbi_quest['value'] = 2;
    update_quest_value($fbi_quest['idnum'], 2);
  }
  else
  {
    $dialog = '<p>' . $user['display'] . '?  I\'m agent Mully and this is agent Sculder.  We\'re with the FBI.  Do you mind if we ask you a few questions?</p>';
    $options[] = '<a href="?dialog=sure">Invite them in</a>';
    $options[] = '<a href="?dialog=goaway">Turn them away</a>';
  }
}

// turn them away
if($fbi_quest['value'] == 2)
{
  if($_GET['dialog'] == 'insist')
  {
    $fbi_quest['value'] = 7;
    update_quest_value($fbi_quest['idnum'], 7);
    
    header('Location: /myhouse.php');
    exit();
  }
  else if($_GET['dialog'] == 'givein')
  {
    $fbi_quest['value'] = 3;
    update_quest_value($fbi_quest['idnum'], 3);
  }
  else
  {
    $dialog = '<p><i>(One of them plants their foot in the door before you can fully close it.)</i></p><p>We just want to talk to you about Halloween.  Did you see anything unusual?</p>';
    $options[] = '<a href="?dialog=insist">Insist that they leave</a>';
    $options[] = '<a href="?dialog=givein">Reluctantly allow them in after all</a>';
  }
}

// allow them in
if($fbi_quest['value'] == 3)
{
  if($_GET['dialog'] == 'outoftown')
  {
    $fbi_quest['value'] = 4;
    update_quest_value($fbi_quest['idnum'], 4);
    $pre_dialog = '<p><i>(Sculder and Mully exchange an acknowledging glance.)</i></p>';
  }
  else if($_GET['dialog'] == 'here')
  {
    $fbi_quest['value'] = 5;
    update_quest_value($fbi_quest['idnum'], 5);
    $pre_dialog = '<p><i>(Sculder and Mully exchange an acknowledging glance.)</i></p>';
  }
  else
  {
    $dialog = '<p><i>(You begin to clear a place where you can sit down to talk, but they don\'t wait for you to finish...)</i></p><p>Can I ask where you were during the week leading up to Halloween, ' . $user['display'] . '?</p>';
    $options[] = '<a href="?dialog=outoftown">Tell them you were out of town</a>';
    $options[] = '<a href="?dialog=here">Tell them you were here, with your pets</a>';
  }
}

// say you were out of town
if($fbi_quest['value'] == 4)
{
  if($_GET['dialog'] == 'pwnd')
  {
    $fbi_quest['value'] = 5;
    update_quest_value($fbi_quest['idnum'], 5);
  }
  else
  {
    if(date('Y', $user['tot_time']) == 2011)
      $dialog = '<p>Really.  Well that\'s interesting, because one of your neigbors saw you giving candy out to trick-or-treaters October ' . date('jS', $user['tot_time'] - 20 * 60) . '.</p>';
    else
      $dialog = '<p>Really.  Well that\'s interesting, because one of your neigbors said she saw you in the fields around that time - in what she described as being "crop circles", actually.</p>';

    $dialog .= '<p>Does that sound familiar?</p>';
      
    $options[] = '<a href="?dialog=pwnd">Admit, embarrassed, that that sound rather familiar...</a>';
  }
}

if($fbi_quest['value'] == 5)
{
  if($_GET['dialog'] == 'yes')
  {
    $pre_dialog = '<p><i>(The agents exchange glances again, but this time you get the impression that it\'s because they don\'t agree on the validity of this line of questioning.)</i></p>';

    $fbi_quest['value'] = 6;
    update_quest_value($fbi_quest['idnum'], 6);
  }
  else if($_GET['dialog'] == 'no')
  {
    $pre_dialog = '<p><i>(The agents exchange glances again, but this time you get the impression that it\'s because they don\'t agree on the validity of this line of questioning.)</i></p>';
    $dialog = '<p>I see.  Well, thank you for your time, ' . $user['display'] . '.  If anything else comes to mind, you can reach me at this number.  <i>(They both hand you their cards, and leave.)</i></p>';
    $dialog .= '<p><i>(You have learned Agent Sculder and Agent Mully\'s phone numbers!)</i></p>';

    $fbi_quest['value'] = 7;
    update_quest_value($fbi_quest['idnum'], 7);
  }
  else
  {
    $dialog = '<p>Does <em>this</em> look familiar to you?</p><p><i>(You\'re shown a photograph...)</i></p><p style="text-align: center;"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/alien-photo.png" width="310" height="300" /></p>';
    $options[] = '<a href="?dialog=no">Point out that the photo is too blurry to make out any details</a>';
    $options[] = '<a href="?dialog=yes">Point out the alien!</a>';
  }
}

if($fbi_quest['value'] == 6)
{
  if($_GET['dialog'] == 'halloween')
  {
    $dialog = '<p>I see.  Well, thank you for your time, ' . $user['display'] . '.  If anything else comes to mind, you can reach me at this number.  <i>(They both hand you their cards, and leave.)</i></p>';
    $dialog .= '<p><i>(You have learned Agent Sculder and Agent Mully\'s phone numbers!)</i></p>';

    $fbi_quest['value'] = 7;
    update_quest_value($fbi_quest['idnum'], 7);
  }
  else
  {
    $dialog = '<p>Have you seen anything else like this before?</p>';
    
    $options[] = '<a href="?dialog=halloween">Explain that the aliens only make an appearance during Halloween</a>';
  }
}

if($dialog === false)
{
  header('Location: /myhouse.php');
  exit();
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Front Door</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
<?php
if($its_your_birthday)
  echo '<div style="background: url(\'gfx/streamers_yellow.png\'); height: 48px; font-size: 48px;"><center><img src="gfx/happy_birthday.png" width="450" height="48" alt="Happy Birthday!" /></center></div>';
?>
<h4><?= $user['display'] ?>'s House &gt; Front Door <i>(<?= $house['curbulk'] ?>/<?= $house['maxbulk'] ?>; <?= ceil($house['curbulk'] * 100 / $house['maxbulk']) ?>% full)</i></h4>
<?php
echo '<a href="/npcprofile.php?npc=Agents Mully and Sculder"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/fbi.png" align="right" width="350" height="487" alt="(Agents Mully and Sculder)" /></a>';

include 'commons/dialog_open.php';

echo $pre_dialog, $dialog, $post_dialog;

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>', implode('</li><li>', $options), '</li></ul>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
