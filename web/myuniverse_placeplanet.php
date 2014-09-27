<?php
$whereat = 'home';
$wiki = 'Multiverse';
$THIS_ROOM = 'Multiverse';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/universelib.php';

if($user['show_universe'] != 'yes')
{
  header('Location: ./myhouse.php');
  exit();
}

$CONTENT_STYLE = 'background-color: #000;';
$CONTENT_CLASS = 'universe';

$systemid = (int)$_GET['system'];

$universe = get_universe($user['idnum']);

if($universe === false || $universe['stage'] != 'gameplay' || $universe['rocks'] < 1)
{
  header('Location: ./myuniverse.php');
  exit();
}

$solar_system = get_solar_system($systemid);

if($solar_system === false)
{
  header('Location: ./myuniverse.php?systemid=' . $systemid);
  exit();
}

$galaxy = get_galaxy($solar_system['galaxyid']);

if($galaxy === false || $galaxy['universeid'] != $universe['idnum'])
{
  header('Location: ./universe_viewsystem.php');
  exit();
}

$x = (int)$_GET['x'];
$y = (int)$_GET['y'];

if($x < 16 || $x >= 600 - 16 || $y < 16 || $y >= 300 - 16)
{
  header('Location: ./universe_viewsystem.php?id=' . $systemid . '&msg=143');
  exit();
}

if(!solar_system_space_is_empty($systemid, $x, $y, 16))
{
  header('Location: ./universe_viewsystem.php?id=' . $systemid . '&msg=143');
  exit();
}

if($_POST['action'] == 'Place')
{
  $rocks = (int)$_POST['size'];

  if($rocks < 2 || $rocks > 12)
    $messages_list[] = '<span class="failure">How many Rocks would you like to use?</span>';
  else if($rocks > $universe['rocks'])
    $messages_list[] = '<span class="failure">You do not have enough Rocks!</span>';
  else
  {
    universe_spend($universe, 'rocks', $rocks);
    create_rocky_planet($universe['idnum'], $solar_system, $x, $y, $rocks);

    header('Location: ./universe_viewsystem.php?id=' . $systemid);
    exit();
  }
}

$galaxy_fullname = galaxy_full_name($galaxy);
$system_fullname = solar_system_full_name($solar_system);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; My Universe &gt; <?= $galaxy_fullname ?> &gt; <?= $system_fullname ?></title>
<?php include 'commons/head.php'; ?>
  <link rel="stylesheet" href="styles/multiverse_1.css" />
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="multiverse.php">The Multiverse</a> &gt; <a href="myuniverse.php">My Universe</a> &gt; <a href="universe_viewgalaxy.php?id=<?= $galaxy['idnum'] ?>"><?= $galaxy_fullname ?></a> &gt; <a href="universe_viewsystem.php?id=<?= $systemid ?>"><?= $system_fullname ?></a> &gt; Place Rocky Object...</h4>
<p>How big of a planet do you want?  The bigger the planet, the more Rocks required.</p>
<p>You have <?= $universe['rocks'] ?> Rocks.</p>
<form action="myuniverse_placeplanet.php?system=<?= $systemid ?>&amp;x=<?= $x ?>&amp;y=<?= $y ?>" method="post">
<table>
 <thead>
  <th></th><th>Size</th><th>Rocks</th>
 </thead>
 <tbody>
  <tr><td><input type="radio" name="size" value="2" /></td><td>Mars-ish</td><td class="centered">2</td></tr>
  <tr><td><input type="radio" name="size" value="3" /></td><td>Earth-ish</td><td class="centered">3</td></tr>
  <tr><td><input type="radio" name="size" value="4" /></td><td>2&times; Earth</td><td class="centered">4</td></tr>
  <tr><td><input type="radio" name="size" value="5" /></td><td></td><td class="centered">5</td></tr>
  <tr><td><input type="radio" name="size" value="6" /></td><td></td><td class="centered">6</td></tr>
  <tr><td><input type="radio" name="size" value="7" /></td><td>5&times; Earth</td><td class="centered">7</td></tr>
  <tr><td><input type="radio" name="size" value="8" /></td><td></td><td class="centered">8</td></tr>
  <tr><td><input type="radio" name="size" value="9" /></td><td></td><td class="centered">9</td></tr>
  <tr><td><input type="radio" name="size" value="10" /></td><td></td><td class="centered">10</td></tr>
  <tr><td><input type="radio" name="size" value="11" /></td><td></td><td class="centered">11</td></tr>
  <tr><td><input type="radio" name="size" value="12" /></td><td>10&times; Earth</td><td class="centered">12</td></tr>
 </tbody>
</table>
<p><input type="submit" name="action" value="Place" class="universeinput" /></p>
</form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
