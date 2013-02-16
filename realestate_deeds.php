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

$locid = $user['locid'];
$house = get_house_byuser($user['idnum'], $locid);

$addons = take_apart(',', $house['addons']);
$have_basement = (array_search('Basement', $addons) !== false);

if($_POST['action'] == 'getdeed')
{
  $size = (int)$_POST['size'];
  
  if($house['maxbulk'] >= 5000 + $size && $house['curbulk'] <= $house['maxbulk'] - $size)
  {
    if($size == 500 || $size == 1000 || $size == 2000 || $size == 5000 || $size == 10000)
    {
      upgrade_house($house['idnum'], $house['maxbulk'] - $size);
      add_inventory($user['user'], 'u:' . $user['idnum'], 'Deed to ' . ($size / 10) . ' Units', '', 'storage/incoming');
      flag_new_incoming_items($user['user']);
      $message = '<span class="success">Transaction complete.  The deed is in Incoming.</span>';
      
      $house['maxbulk'] -= $size;

      require_once 'commons/statlib.php';
      record_stat($user['idnum'], 'Acquired a Deed from Real Estate', 1);
    }
    else
      $message = "<span class=\"failure\">There is no such deed available.</span>";
  }
  else
    $message = "<span class=\"failure\">You do not have enough space to get a deed of that size.</span>";
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Real Estate &gt; Acquire Deeds</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h5>Real Estate &gt; Acquire Deeds</h5>
     <ul class="tabbed">
      <li><a href="realestate.php">Buy Land</a></li>
      <li><a href="realestate_lake.php">Build Lake</a></li>
      <li class="activetab"><a href="realestate_deeds.php">Acquire Deeds</a></li>
     </ul>
<?php
// NPC AMANDA BRANAMAN
echo '<a href="npcprofile.php?npc=Amanda+Branaman"><img src="gfx/npcs/real-estate-agent.png" align="right" width="350" height="490" alt="(Amanda, the Real Estate agent)" /></a>';

include 'commons/dialog_open.php';
if($message)
  echo "     <p>$message</p>\n";

if($have_basement)
  echo '     <p>You currently have a size ' . ($house['maxbulk'] / 10) . ' estate, and a ' . ($house['maxbasement'] / 100) . '-level basement.</p>';
else
  echo '     <p>You currently have a size ' . ($house['maxbulk'] / 10) . ' estate.</p>';
?>
     <p>You may get a paper deed for a plot of land you own no smaller than 50 units.  You can use these to buy, sell and trade parts of your estate.</p>
     <p>City ordinances prevent us from reducing an estate size below 500 units.  Also, you must have enough space left over to hold your items.</p>
<?php
if($have_basement)
  echo '     <p>Since you have a basement, you should know that a Deed to 1000 Units can be used to add a floor to your basement.</p>';

include 'commons/dialog_close.php';

if($house['maxbulk'] >= 5500)
{
  $rowclass = begin_row_class();
  $disabled = '';

  if($house['curbulk'] > $house['maxbulk'] - 500)
    $disabled = ' disabled';
?>
     <table>
      <tr class="titlerow">
       <th>Plot Size</th>
       <th></th>
      </tr>
      <form action="realestate_deeds.php" method="post">
      <tr class="<?= $rowclass ?>">
       <td class="centered">50</td>
       <td><input type="hidden" name="action" value="getdeed" /><input type="hidden" name="size" value="500" /><input type="submit" value="Get Deed"<?= $disabled ?> /></td>
      </tr>
      </form>
<?php
  if($house['maxbulk'] >= 6000)
  {
    if($house['curbulk'] > $house['maxbulk'] - 1000)
      $disabled = ' disabled';

    $rowclass = alt_row_class($rowclass);
?>
      <form action="realestate_deeds.php" method="post">
      <tr class="<?= $rowclass ?>">
       <td class="centered">100</td>
       <td><input type="hidden" name="action" value="getdeed" /><input type="hidden" name="size" value="1000" /><input type="submit" value="Get Deed"<?= $disabled ?> /></td>
      </tr>
      </form>
<?php
  }

  if($house['maxbulk'] >= 7000)
  {
    if($house['curbulk'] > $house['maxbulk'] - 2000)
      $disabled = ' disabled';

    $rowclass = alt_row_class($rowclass);
?>
      <form action="realestate_deeds.php" method="post">
      <tr class="<?= $rowclass ?>">
       <td class="centered">200</td>
       <td><input type="hidden" name="action" value="getdeed" /><input type="hidden" name="size" value="2000" /><input type="submit" value="Get Deed"<?= $disabled ?> /></td>
      </tr>
      </form>
<?php
  }

  if($house['maxbulk'] >= 10000)
  {
    if($house['curbulk'] > $house['maxbulk'] - 5000)
      $disabled = ' disabled';

    $rowclass = alt_row_class($rowclass);
?>
      <form action="realestate_deeds.php" method="post">
      <tr class="<?= $rowclass ?>">
       <td class="centered">500</td>
       <td><input type="hidden" name="action" value="getdeed" /><input type="hidden" name="size" value="5000" /><input type="submit" value="Get Deed"<?= $disabled ?> /></td>
      </tr>
      </form>
<?php
  }

  if($house["maxbulk"] >= 15000)
  {
    if($house["curbulk"] > $house["maxbulk"] - 10000)
      $disabled = " disabled";

    $rowclass = alt_row_class($rowclass);
?>
      <form action="realestate_deeds.php" method="post">
      <tr class="<?= $rowclass ?>">
       <td class="centered">1000</td>
       <td><input type="hidden" name="action" value="getdeed" /><input type="hidden" name="size" value="10000" /><input type="submit" value="Get Deed"<?= $disabled ?> /></td>
      </tr>
      </form>
<?php
  }
?>
     </table>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
