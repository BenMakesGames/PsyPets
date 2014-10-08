<?php
require_once 'commons/init.php';

$require_petload = 'no';
$wiki = 'Daily_Adventure';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/challengelib.php';

$challenge = get_challenge($user['idnum']);
if($challenge === false)
{
  create_challenge($user['idnum']);
  $challenge = get_challenge($user['idnum']);
  if($challenge === false)
    die('error loading daily challenge information.  this is bad.');
}

if(array_key_exists('start', $_GET) && $step == 0 && date('Ymd') != $challenge['lastchallenge'])
{
  if((int)$_GET['start'] >= 0 && (int)$_GET['start'] <= 4)
  {
    start_challenge($user['idnum'], (int)$_GET['start']);
    $challenge = get_challenge($user['idnum']);

    require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Started a Daily Adventure Challenge', 1);
  }
}

$message = '';

if($challenge['step'] > 0)
{
  if($_GET['action'] == 'cancel')
  {
    if($challenge['step'] > 1 && $challenge['difficulty'] > 0)
    {
      failed_challenge($challenge);

      if($challenge['failed'] == 'yes')
        $message .= '<p class="progress">You were unable to complete this Adventure, however the progress you did make has not gone unnoticed.</p><p><i>(You received 1 Copper Token.  This is not an item, but can be exchanged with certain NPCs for items.)</i></p>';
      else
        $message .= '<p class="progress">You were unable to complete this Adventure, however the progress you did make has not gone unnoticed.</p>';
    }
    else
      $message .= '<p class="failure">You were unable to make any progress in this Adventure.</p>';

    $challenge['step'] = 0;
    cancel_challenge($user['idnum']);

    require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Canceled a Daily Adventure Challenge', 1);
  }
  else if($_GET['action'] == 'go')
  {
    if(puzzle_post($challenge))
    {
      $challenge['step']++;
    
      if($challenge['step'] == 3 && $challenge['difficulty'] == 0)
      {
        $challenge['step'] = 0;
        $challenge['plastic']++;
        $message .= '<p><i>(You have completed this adventure, and received 1 Plastic Token.  This is not an item, but can be exchanged with certain NPCs for other tokens.)</i></p>';

        require_once 'commons/statlib.php';
        record_stat($user['idnum'], 'Completed a Daily Adventure Challenge', 1);
      }
      else if($challenge['step'] == 3 && $challenge['difficulty'] == 1)
      {
        $challenge['step'] = 0;
        $challenge['copper']++;
        $message .= '<p><i>(You have completed this adventure, and received 1 Copper Token.  This is not an item, but can be exchanged with certain NPCs for items or other tokens.)</i></p>';

        require_once 'commons/statlib.php';
        record_stat($user['idnum'], 'Completed a Daily Adventure Challenge', 1);
      }
      else if($challenge['step'] == 4 && $challenge['difficulty'] == 2)
      {
        $challenge['step'] = 0;
        $challenge['silver']++;
        $message .= '<p><i>(You have completed this adventure, and received 1 Silver Token.  This is not an item, but can be exchanged with certain NPCs for items or other tokens.)</i></p>';

        require_once 'commons/statlib.php';
        record_stat($user['idnum'], 'Completed a Daily Adventure Challenge', 1);
      }
      else if($challenge['step'] == 5 && $challenge['difficulty'] == 3)
      {
        $challenge['step'] = 0;
        $challenge['gold']++;
        $message .= '<p><i>(You have completed this adventure, and received 1 Gold Token.  This is not an item, but can be exchanged with certain NPCs for items or other tokens.)</i></p>';

        require_once 'commons/statlib.php';
        record_stat($user['idnum'], 'Completed a Daily Adventure Challenge', 1);
      }
      else if($challenge['step'] == 5 && $challenge['difficulty'] == 4)
      {
        $challenge['step'] = 0;
        $challenge['platinum']++;
        $message .= '<p><i>(You have completed this adventure, and received 1 Platinum Token.  This is not an item, but can be exchanged with certain NPCs for items.)</i></p>';

        require_once 'commons/statlib.php';
        record_stat($user['idnum'], 'Completed a Daily Adventure Challenge', 1);
      }
    
      update_challenge($challenge);
    }
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Daily Adventure</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Daily Adventure</h4>
<?php
if($challenge['step'] == 0)
{
?>
     <ul class="tabbed">
      <li class="activetab"><a href="/daily_adventure/">Go On an Adventure</a></li>
      <li><a href="/daily_adventure/rankings.php">Most Adventurous Residents</a></li>
      <li><a href="/daily_adventure/shop.php">Adventurer's Shop</a></li>
     </ul>
<?php
}

if($message)
  echo $message . '<hr />';

if($challenge['step'] == 0)
{
  if(date('Ymd') != $challenge['lastchallenge'])
  {
    echo '<a href="/npcprofile.php?npc=Jerrad+Shiflett"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/adventurer.png" align="right" width="350" height="410" alt="(Jerrad the Adventurer)" /></a>';

    include 'commons/dialog_open.php';
?>
     <p>Looking for adventure?  This is the place!</p>
     <p>You may embark on up to one adventure per day.  Each adventure will consist of a number of obstacles which you or your pets will have to overcome.</p>
     <p>Complete an adventure to earn Tokens; exchanged Tokens at my shop for various items.</p>
     <p>Remember: choose carefully! You're stuck with whichever adventure you choose until tomorrow, even if you can't complete it.</p>
<?php
    include 'commons/dialog_close.php';
?>
     <ul>
      <li><a href="?start=0">Plastic</a> (two obstacles; recommended for new players and low-level pets)</li>
      <li><a href="?start=1">Copper</a> (two obstacles)</li>
      <li><a href="?start=2">Silver</a> (three obstacles)</li>
      <li><a href="?start=3">Gold</a> (four obstacles)</li>
      <li><a href="?start=4">Platinum</a> (four obstacles)</li>
     </ul>
     <p><i>Hint: you will receive a Copper token as a consolation prize every two challenges you fail (with the exception of Plastic challenges).</i></p>
<?php
  }
  else
  {
    echo '<a href="/npcprofile.php?npc=Jerrad+Shiflett"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/adventurer.png" align="right" width="350" height="410" alt="(Jerrad the Adventurer)" /></a>';
    include 'commons/dialog_open.php';
    echo '<p>You have already completed today\'s adventure.  You\'ll have to wait until tomorrow to start another.</p>';
    include 'commons/dialog_close.php';
  }
}
else
{
  render_puzzle($challenge);
  echo '<ul><li><a href="?action=cancel" onclick="return confirm(\'Really quit the adventure?  You will still have to wait until tomorrow to start another.\');">Quit this adventure</a></li></ul>';
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
