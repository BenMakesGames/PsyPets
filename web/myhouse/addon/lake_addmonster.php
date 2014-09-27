<?php
require_once 'commons/init.php';

$whereat = 'home';
$wiki = 'Lake';
$THIS_ROOM = 'Lake';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/checkpet.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/lakelib.php';
require_once 'commons/utility.php';
require_once 'commons/moonphase.php';

if(!addon_exists($house, 'Lake'))
{
  header('Location: /myhouse.php');
  exit();
}

$lake = get_lake_byuser($user['idnum']);
if($lake === false || $lake['monster'] != 'no')
{
  header('Location: /myhouse/addon/lake.php');
  exit();
}

if($_POST['petid'] > 0)
{
  $petid = (int)$_POST['petid'];
  
  $pet = get_pet_byid($petid, 'user');
  
  if($pet['user'] == $user['user'] && strstr($pet['graphic'], '/') === false)
  {
    $command = 'UPDATE monster_pets SET user=\'lmonsterpp\' WHERE idnum=' . $petid . ' LIMIT 1';
    fetch_none($command, 'moving pet to lake (1)');

    $command = 'UPDATE psypets_lakes SET monster=\'' . $petid . '\' WHERE idnum=' . $lake['idnum'] . ' LIMIT 1';
    fetch_none($command, 'moving pet to lake (2)');
    
    header('Location: /myhouse/addon/lake.php');
    exit();
  }
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user["display"] ?>'s House &gt; Lake &gt; Add Monster</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Lake &gt; Add Monster</h4>
<?php
echo $message;
room_display($house);
?>
     <p>You can have one of your (non-custom) pets take up residence in the lake.  It will do so <strong>permanently</strong>, so choose with caution!  (I may in the future make available some <em>extreme measure</em> which can be taken to call a pet back from the lake.  But don't hold your breath waiting <img src="/gfx/emote/suspicious.gif" />)</p>
     <form method="post">
     <table>
      <tr class="titlerow">
       <th></th><th></th><th>Pet</th><th>Presence</th>
      </tr>
<?php
$rowclass = begin_row_class();

foreach($userpets as $pet)
{
  if(strstr($pet['graphic'], '/') !== false)
    continue;
?>
      <tr class="<?= $rowclass ?>">
       <td><input type="radio" name="petid" value="<?= $pet['idnum'] ?>" /></td>
       <td><a href="/petprofile.php?petid=<?= $pet['idnum'] ?>"><img src="/gfx/pets/<?= $pet['graphic'] ?>" width="48" height="48" alt="" border="0" /></a></td>
       <td><?= $pet['petname'] ?></td>
       <td><?= ucfirst(monster_description($pet)) ?></td>
      </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
     </table>
     <p><input type="submit" name="submit" value="I Choose You!" class="bigbutton" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
