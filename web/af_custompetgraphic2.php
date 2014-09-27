<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/petlib.php';
require_once 'commons/graphiclibrary.php';
require_once 'commons/favorlib.php';

if($now_year > 2010 || ($now_year == 2010 && $now_month >= 4))
  $favor_cost = 500;
else
  $favor_cost = 500;

if($_POST['action'] == 'customizepet' && $user['favor'] >= $favor_cost)
{
  $graphicid = (int)$_POST['graphic'];
  $graphic = get_graphic_byid($graphicid);
  $get_pet = get_pet_byid((int)$_POST['petid']);

  if($get_pet['user'] != $user['user'] || $get_pet === false || $get_pet['location'] != 'home')
  {
    $errored = true;
    $error_message = 'The selected pet does not belong to you.';
  }
  else if($get_pet['dead'] != 'no')
  {
    $errored = true;
    $error_message = 'You cannot customize a dead pet\'s graphic.';
  }
  else if($get_pet['zombie'] == 'yes')
  {
    $errored = true;
    $error_message = 'You cannot customize a zombie!';
  }
  else if($get_pet['protected'] == 'yes')
  {
    $errored = true;
    $error_message = 'You cannot customize an already-customized pet!';
  }
  else if($graphic === false || $graphic['h'] != 48)
  {
    $errored = true;
    $error_message = 'Please select a graphic to apply to your pet.';
  }
  else if($graphic['recipient'] > 0 && $graphic['recipient'] != $user['idnum'])
  {
    $errored = true;
    $error_message = 'Please select a graphic to apply to your pet.';
  }

  if($errored == false)
  {
    $favor = 'custom pet graphic - ' . $graphic['author'] . '\'s "' . $graphic['title'] . '"';

    spend_favor($user, $favor_cost, $favor);

    $command = 'UPDATE monster_pets SET graphic=' . quote_smart('../../' . $graphic['url']) . ", protected='yes' WHERE user=" . quote_smart($user["user"]) . " AND idnum=" . $get_pet["idnum"] . " LIMIT 1";
    $database->FetchNone($command, 'updating pet graphic');

    $uploader = get_user_byid($graphic['uploader']);

    record_graphic_use($graphicid, $graphic, $uploader);

    if($uploader !== false)
    {
      $badges = get_badges_byuserid($uploader['idnum']);
      if($badges['artist'] == 'no')
      {
        set_badge($uploader['idnum'], 'artist');
        $extra = '<br /><br />{i}(You won the Artist Badge!){i}';
      }

      psymail_user($uploader['user'], 'psypets', 'Your graphic from the Graphic Library was used!', '{r ' . $user['display'] . '} has used your {i}' . $graphic['title'] . '{/} graphic to customize their pet, ' . $get_pet['petname'] . '.' . $extra);
    }

    require_once 'commons/dailyreportlib.php';
    record_daily_report_stat('Someone Bought a Custom Pet Graphic', 1);

    $message = $get_pet['petname'] . "'s appearance has been changed!  Magic!";
    $_POST = array();
  }
}

$graphics = get_graphics_byuserid($user['idnum'], 48);

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; The Alchemist's &gt; Pet Transmuations &gt; Custom Appearance</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="alchemist.php">The Alchemist's</a> &gt; <a href="alchemist_transmute.php">Pet Transmuations</a> &gt; Custom Appearance</h4>
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
  echo "<p style=\"color:red;\">" . $error_message . "</p>\n";

if($message)
  echo "<p style=\"color:green;\">" . $message . "</p>\n";

echo '
  <p>I can transform the appearance of one of your pets into anything you can imagine, for <strong>' . $favor_cost . ' Favor</strong>.</p>
  <p>If you would like to use a common pet graphic (Desikh, The Bones, Rooster, etc), <a href="af_regraphik2.php">I can also do that, and for much cheaper!</a></p>
  <p>And please take note: female pets with a custom appearance give birth to entirely random pets.</p>
';

$options[] = '<a href="/buyfavors.php">Support PsyPets; get Favor</a>';

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';
?>
     <p>Your account has <?= $user['favor'] ?> Favor.  Customizing a pet's graphic costs <?= $favor_cost ?> Favor.</p>
<?php
if(count($graphics) < 1)
  echo '
    <p>There are no graphics available to you at this time.  But it\'s not a problem: <a href="gl_upload.php">you can upload one</a>.</p>
  ';
else
{
  if($user['favor'] >= $favor_cost)
  {
    $command = 'SELECT idnum,petname,graphic FROM monster_pets WHERE user=' . quote_smart($user['user']) . ' AND location=\'home\' AND dead=\'no\' AND zombie=\'no\' AND protected=\'no\' ORDER BY orderid ASC';
    $pets = $database->FetchMultiple($command, 'fetching pets');

    if(count($pets) > 0)
    {
?>
     <h5>Pet to Change</h5>
     <p>(Dead, zombie, and custom pets may not be changed.)</p>
     <form action="af_custompetgraphic2.php" method="post">
     <table>
      <tr class="titlerow">
       <th></th><th></th><th>Pet</th>
      </tr>
<?php
      $rowclass = begin_row_class();

      foreach($pets as $pet)
      {
?>
      <tr class="<?= $rowclass ?>">
       <td><input type="radio" name="petid" value="<?= $pet['idnum'] ?>"<?= ($_POST['petid'] == $pet["idnum"] ? " checked" : "") ?> /></td>
       <td><img src="gfx/pets/<?= $pet['graphic'] ?>" /></td>
       <td><?= $pet['petname'] ?></td>
      </tr>
<?php
          $rowclass = alt_row_class($rowclass);
      }
?>
     </table>
     <h5>Choose New Graphic</h5>
<?php include 'commons/gl_warning.php'; ?>
     <table>
      <tr>
<?php
      $i = 0;
      foreach($graphics as $graphic)
      {
        if($i % 4 == 0 && $i > 0)
          echo "</tr><tr>\n";
?>
      <td align="center">
       <table><tr><td><img src="<?= $graphic['url'] ?>" /></td><td bgcolor="#f0f0f0"><img src="<?= $graphic['url'] ?>" /></td></tr></table>
       <input type="radio" name="graphic" value="<?= $graphic['idnum'] ?>" />
      </td>
<?php
        ++$i;
      }
?>
      </tr>
     </table>
     <p><input type="hidden" name="action" value="customizepet" /><input type="submit" value="Customize!" /></p>
     </form>
<?php
    }
    else
    {
?>
     <p>You do not have any pets which can be customized at this time.  (Dead pets and zombies can't be customized.)</p>
<?php
    }
  }
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
