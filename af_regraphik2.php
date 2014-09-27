<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/petlib.php';
require_once 'commons/graphiclibrary.php';
require_once 'commons/globals.php';
require_once 'commons/favorlib.php';

// only sign-up graphics allowed, so as not to spoil the ark (or the goat badge)

$petgfx = get_global('petgfx');

$numgfx = count($petgfx);

$favor_cost = 50;

if($_POST['action'] == 'customizepet' && $user['favor'] >= $favor_cost)
{
  $graphic = $_POST['graphic'];
  $get_pet = get_pet_byid((int)$_POST['petid']);

  if($get_pet['user'] != $user['user'] || $get_pet === false || $get_pet['location'] != 'home')
  {
    $errored = true;
    $error_message = 'Choose one of the pets listed below...';
  }
  else if($get_pet['dead'] != 'no')
  {
    $errored = true;
    $error_message = 'You cannot customize a dead pet\'s graphic.';
  }
  else if($get_pet['zombie'] == 'yes')
  {
    $errored = true;
    $error_message = 'You cannot customize a zombie pet!';
  }
  else if(array_search($graphic, $petgfx) === false)
  {
    $errored = true;
    $error_message = 'Choose one of the graphics listed below...';
  }
  else if($graphic == $get_pet['graphic'])
  {
    $errored = true;
    $error_message = $get_pet['petname'] . ' already looks like that!';
  }

  if($errored == false)
  {
    $command = 'UPDATE monster_pets SET graphic=' . quote_smart($graphic) . ' WHERE user=' . quote_smart($user['user']) . ' AND idnum=' . $get_pet['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'regraphiking pet');

    $favor = 'pet graphic change - ' . $get_pet['petname'] . ' (#' . $get_pet['idnum'] . ')';

    spend_favor($user, $favor_cost, $favor);

    $message = $get_pet['petname'] . '\'s appearance has been changed!';

    $_POST = array();
  }
}

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; The Alchemist's &gt; Pet Transmutations &gt; Common Appearance</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="alchemist.php">The Alchemist's</a> &gt; <a href="alchemist_transmute.php">Pet Transmuations</a> &gt; Common Appearance</h4>
     <ul class="tabbed">
      <li><a href="alchemist.php">General Shop</a></li>
      <li><a href="alchemist_potions.php">Potion Shop</a></li>
      <li><a href="alchemist_pool.php">Cursed Pool</a></li>
      <li class="activetab"><a href="alchemist_transmute.php">Pet Transmutations</a></li>
     </ul>
<?php
echo '<img src="gfx/npcs/thaddeus.png" align="right" width="350" height="250" alt="(Thaddeus the Alchemist)" />';

include 'commons/dialog_open.php';

if($error_message)
  echo '<p style="color:red;">' . $error_message . '</p>';

if($message)
  echo '<p style="color:green;">' . $message . '</p>';
else
  echo '
    <p>I can transform the appearance of one of your pets into that of any other common PsyPet for <strong>' . $favor_cost . ' Favor.</strong></p>
    <p>If you\'d like to use an uploaded graphic, <a href="af_custompetgraphic2.php">I can also do that!</a></p>
  ';

$options[] = '<a href="/buyfavors.php">Support PsyPets; get Favor</a>';

include 'commons/dialog_close.php';
?>
     <p>You have <?= $user['favor'] ?> Favor.  Changing a pet's graphic to another, common graphic costs <?= $favor_cost ?> Favor.</p>
<?php
if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

$command = 'SELECT idnum,petname,graphic FROM monster_pets WHERE user=' . quote_smart($user['user']) . ' AND location=\'home\' AND dead=\'no\' AND zombie=\'no\' AND protected=\'no\' ORDER BY orderid ASC';
$pets = $database->FetchMultiple($command, 'fetching pets');

if($user['favor'] >= $favor_cost)
{
  if(count($pets) > 0)
  {
?>
     <h5>Pet to Change</h5>
     <p>(Dead, zombie, and custom pets may not be changed.)</p>
     <form action="af_regraphik2.php" method="post">
     <table>
      <tr class="titlerow">
       <th></th><th></th><th>Pet</th>
      </tr>
<?php
    $rowclass = begin_row_class();

    foreach($pets as $pet)
    {
?>
      <tr valign="middle" class="<?= $rowclass ?>">
       <td><input type="radio" name="petid" value="<?= $pet['idnum'] ?>"<?= ($_POST['petid'] == $pet['idnum'] ? ' checked' : '') ?> /></td>
       <td><img src="gfx/pets/<?= $pet['graphic'] ?>" /></td>
       <td><?= $pet['petname'] ?></td>
      </tr>
<?php
        $rowclass = alt_row_class($rowclass);
    }
?>
     </table>
     <h5>Choose New Graphic</h5>
     <table>
      <tr>
<?php
    $i = 0;
    foreach($petgfx as $graphic)
    {
      if($i % 8 == 0 && $i > 0)
        echo '</tr><tr>';
?>
      <td align="center">
       <img src="//saffron.psypets.net/gfx/pets/<?= $graphic ?>" /><br />
       <input type="radio" name="graphic" value="<?= $graphic ?>" />
      </td>
<?php
      ++$i;
    }
?>
      </tr>
     </table>
     <p><input type="hidden" name="action" value="customizepet" /><input type="submit" value="Make It So" /></p>
     </form>
<?php
  }
  else
  {
?>
     <p>You do not have any pets which can be customized at this time.  (Like dead pets.  Dead pets can't be customized.)</p>
     <p>Pets with custom graphics are also not listed here.</p>
<?php
    if($dead_pets > 0)
      echo "     <p>If you would like to revive a dead pet, visit <a href=\"af_revive2.php\">Pet Revival</a> Favor Dispenser.</p>\n";
  }
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
