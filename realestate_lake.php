<?php
$wiki = 'Real_Estate';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/houselib.php';
require_once 'commons/userlib.php';
require_once 'commons/economylib.php';

$dialog = '';

// check to see if we already have a lake
$locid = $user['locid'];
$house = get_house_byuser($user['idnum'], $locid);

$addons = take_apart(',', $house['addons']);
$have_lake = (array_search('Lake', $addons) !== false);

// check to see if we have the Castle badge
$badges = get_badges_byuserid($user['idnum']);

$have_castle = ($badges['castle'] == 'yes');

// check to see if we're already working on the lake
load_user_projects($user, $userprojects);

$working_on_lake = false;

if(count($userprojects) > 0)
{
  foreach($userprojects as $project)
  {
    if($project['itemid'] == 22)
    {
      $working_on_lake = true;
      break;
    }
  }
}

$lake_fee = value_with_inflation(5000);

if($_GET['action'] == 'buildlake' && !$working_on_lake && !$have_lake && $have_castle && $user['money'] >= $lake_fee)
{
  $herg_fee = floor($lake_fee * .175);
  $re_fee = $lake_fee - $herg_fee;

  take_money($user, $herg_fee, 'HERG Lake paperwork fee');
  take_money($user, $re_fee, 'Real Estate surveying fee');

  $user['money'] -= $lake_fee;

  $command = 'INSERT INTO monster_projects (`type`, `userid`, `itemid`, `progress`, `notes`) ' .
             "VALUES ('construct', " . $user['idnum'] . ", 22, '0', 'Real Estate started this construction.')";
  $database->FetchNone($command, 'starting project for house add-on');

  $dialog = '<p>Excellent!  We\'ll set up the foundations for the project at your house.</p>';
  $working_on_lake = true;
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Real Estate &gt; Build Lake</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h5>Real Estate</h5>
     <ul class="tabbed">
      <li><a href="realestate.php">Buy Land</a></li>
      <li class="activetab"><a href="realestate_lake.php">Build Lake</a></li>
      <li><a href="realestate_deeds.php">Acquire Deeds</a></li>
     </ul>
<?php
// NPC AMANDA BRANAMAN
echo '<a href="npcprofile.php?npc=Amanda+Branaman"><img src="gfx/npcs/real-estate-agent.png" align="right" width="350" height="490" alt="(Amanda, the Real Estate agent)" /></a>';

include 'commons/dialog_open.php';

if($error_message)
  echo '     <p class="failure">' . $error_message . "</p>\n";
else if($dialog != '')
  echo $dialog;
else if($have_lake)
  echo '<p>It looks like you already have a Lake, and unfortunately we can only allow one per property.</p>';
else if($working_on_lake)
  echo '<p>It looks like you\'re already working on a Lake, and unfortunately we can only allow one per property.</p>';
else
{
?>
  <p>You'd like to build a Lake?  Wonderful!</p>
  <p>Now, because HERG is concerned about the ecological impact of building an artificial Lake, there are a few rules we have to follow.</p>
  <p>The biggest thing you'll have to worry about is the size of your estate.  HERG believes that having <em>too many</em> Lakes would be bad for the ecosystem, so they require that you have a Castle-sized estate - 2000 space - before building a Lake.</p>
  <p>Most of the rest of the rules are details regarding where exactly on your estate the Lake is built, to ensure water runoff is correctly handled, to avoid risk of landslides, etc.</p>
  <p>The last detail is in the fees associated with building a Lake.  HERG imposes a paperwork-related fee, and we have to send some engineers to survey your estate.</p>
  <p>The total cost is <?= $lake_fee ?><span class="money">m</span>.</p>
<?php
}

include 'commons/dialog_close.php';

if(!$have_lake && !$working_on_lake)
{
  echo '<h4>Lake Prerequisite Checklist</h4>';
  echo '<ol>';

  if($have_castle)
    echo '<li class="success">Have earned the Castle badge</li>';
  else
    echo '<li class="failure">Have earned the Castle badge</li>';

  if($user['money'] >= $lake_fee)
    echo '<li class="success">Have ' . $lake_fee . '<span class="money">m</span></li>';
  else
    echo '<li class="failure">Have ' . $lake_fee . '<span class="money">m</span></li>';

  echo '</ol>';

  if($have_castle && $user['money'] >= $lake_fee)
    echo '<ul><li><a href="realestate_lake.php?action=buildlake">Start building the Lake add-on</a> (' . $lake_fee . '<span class="money">m</span>)</li></ul>';
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
