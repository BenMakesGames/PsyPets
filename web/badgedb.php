<?php
$whereat = 'library';
$wiki = 'Library';
$require_petload = 'no';

$url = 'badgedb.php';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/badges.php';

$see_all_badges = ($_GET['cheat'] == 'yes' && $user['user'] == 'telkoth');

$badges = get_badges_byuserid($user['idnum']);

$total = count($BADGE_DESC);
$count = 0;
foreach($badges as $yesno)
  $count += ($yesno == 'yes' ? 1 : 0);

for($x = 10; $x <= 100; $x += 10)
{
  if($badges[$x . 'badges'] == 'no' && $count >= $x)
  {
    $badges[$x. 'badges'] = 'yes';
    $count++;
    $got_quantity_badge = true;
    $badge_quantity = $x;

    set_badge($user['idnum'], $x . 'badges');
    
    if($x == 100 && $badges['ridiculous'] == 'no')
    {
      set_badge($user['idnum'], 'ridiculous');
      $got_ridiculous_badge = true;
    }

    break;
  }
}

if($_POST['submit'] == 'Use Selected')
{
  $use_badges = array();
  
  foreach($_POST as $key=>$value)
  {
    if(array_key_exists($key, $BADGE_DESC) && ($value == 'yes' || $value == 'on'))
      $use_badges[] = $key;
  }
  
  if(count($use_badges) < 4)
  {
    $user['badges'] = implode(',', $use_badges);

    $command = 'UPDATE monster_users SET badges=' . quote_smart($user['badges']) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'saving user badge display');

    $message = '<p class="success">Success!  You\'re now wearing the following badges: ';

    $first = true;
    
    foreach($use_badges as $badge)
    {
      if($first)
        $first = false;
      else
        $message .= ', ';

      $message .= $BADGE_DESC[$badge];
    }

    $message .= '.</p>';
  }
  else
    $message = '<p class="failure">You may not wear more than 3 Badges at any one time.</p>';
}

$use_badges = explode(',', $user['badges']);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Library &gt; Badge Archive</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/library.php">Library</a> &gt; Badge Archive</h4>
     <ul class="tabbed">
      <li><a href="/library.php">Information</a></li>
      <li class="activetab"><a href="/badgedb.php">Badge Archive</a></li>
      <li><a href="/gl_browse.php">Graphics Library</a></li>
     </ul>
<?php
if($error_message)
  echo '     <p class="failure">' . $error_message . '</p>';
?>
<?php
echo '<a href="/npcprofile.php?npc=Marian+Witford"><img src="//saffron.psypets.net/gfx/npcs/marian-the-librarian.png" align="right" width="350" height="350" alt="(Marian the Librarian)" /></a>';

include 'commons/dialog_open.php';

if($got_quantity_badge)
{
?>
<p>Oh, congratulations on collecting <?= $badge_quantity ?> badges!  Here, take this <?= $badge_quantity ?>-Badges Badge in recognition of your progress.</p>
<p><i>(You received the <?= $badge_quantity ?>-Badges Badge!)</i></p>
<?php
  if($got_ridiculous_badge)
    echo '<p><i>(You also received the This is Getting Ridiculous Badge... \'cause this <em>is</em> getting kind of ridiculous...)</i></p>';

  $options[] = '<a href="?dialog=1">Ask about pet badges</a>';
}
else if($_GET['dialog'] == 1)
{
  require_once 'commons/petbadges.php';
  echo '<p>I don\'t know much about pet badges myself, but I\'ve heard there are ' . count($PET_BADGE_DESC) . ' of them in total.</p>';
}
else if($message != '')
  echo $message;
else
{
?>
<p>Below is a list of all the badges you've acquired so far.</p>
<p>There are <?= $total ?> badges available.  You have <?= $count ?> of them, <?= floor($count * 100 / $total) ?>%.</p>
<?php
  $options[] = '<a href="?dialog=1">Ask about pet badges</a>';
}

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';
?>
<h5>Your Collection</h5>
<p>You may select up to three Badges to display along with your avatar on Plaza posts.</p>
<form action="<?= $url ?>" method="post">
<p><input type="submit" name="submit" value="Use Selected" class="bigbutton" /></p>
<table>
 <tr class="titlerow">
  <th></th><th></th><th></th><th>Badge</th>
 </tr>
<?php
$num = 1;
$rowclass = begin_row_class();

foreach($BADGE_DESC as $badge=>$desc)
{
  if($badges[$badge] == 'yes' || $see_all_badges)
  {
    echo '<tr class="' . $rowclass . '" onmouseover="Tip(\'&quot;' . $BADGE_HELP[$badge] . '&quot;\')"><td class="centered">' . $num . '</td>'; 
    echo '<td><input type="checkbox" name="' . $badge . '"' . (in_array($badge, $use_badges) ? ' checked' : '') . ' /></td>';
    echo '<td><img src="//' . $SETTINGS['static_domain'] . '/gfx/badges/' . $badge . '.png" width="20" height="20" alt="" /></td><td>' . $desc . '</td>';
  }
  else
  {
    echo '<tr class="' . $rowclass . '" onmouseover="Tip(\'&quot;You haven\\\'t received this badge yet.&quot;\')"><td class="centered">' . $num . '</td>'; 
    echo '<td><input type="checkbox" name="badge' . $num . '" disabled /></td>';
    echo '<td><img src="/gfx/unknownbadge.png" width="20" height="20" alt="" /></td><td>' . mystery_string($desc) . '</td>';
  }

  echo '</tr>';

  $num++;
  $rowclass = alt_row_class($rowclass);
}
?>
</table>
<p><input type="submit" name="submit" value="Use Selected" class="bigbutton" /></p>
</form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
