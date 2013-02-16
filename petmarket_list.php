<?php
$whereat = 'petmarket';
$wiki = 'Pet_Market';

require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/petmarketlib.php';
require_once 'commons/messages.php';
require_once 'commons/userlib.php';
require_once 'commons/petlib.php';

if($user['breeder'] != 'yes')
{
  header('Location: ./breederslicense.php?dialog=2');
  exit();
}

$command = 'SELECT * FROM monster_pets WHERE user=' . quote_smart($user['user']) . ' ORDER BY location ASC,orderid ASC';
$my_pets = $database->FetchMultiple($command, 'fetching pets to sell');

$command = 'SELECT * FROM psypets_pet_market WHERE expiration>' . $now;
$sale_pets = $database->FetchMultipleBy($command, 'petid', 'fetching pet market pets');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Pet Market &gt; List Pet</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : '') ?>
     <h4><a href="petmarket.php">Pet Market</a> &gt; List Pet</h4>
     <?= ($message ? "<p><span class=\"success\">$message</span></p>" : "") ?>
     <?= ($error_message ? "<p><span class=\"failure\">$error_message</span></p>" : '') ?>
<?php
if(count($my_pets) == 0)
  echo '<p>You do not have any pets to sell.</p>';
else
{
?>
<p>Enter the prices for the pets you want to list for sale.  You may even list pets in the Daycare, if you have any (and moving pets from your house to the Daycare or vice versa will not remove them from the Pet Market).</p>
<p>There is a <?= pet_sellers_fee() * 100 ?>% listing fee.  Pet listings expire after 1 week.</p>
<p><strong>Do not list a pet for sale if you care about who buys it.</strong>  This is an open market; anyone with a Breeder's License may purchase the pets you list here.</p>
<form action="/petmarket_confirm.php" method="post">
<table>
 <tr class="titlerow">
  <th></th>
  <th>Name</th>
  <th>Details</th>
  <th class="centered">Price</th>
 </tr>
<?php
  $rowclass = begin_row_class();

  foreach($my_pets as $pet)
  {
    $details = pet_market_details($pet);
    
    if($sale_pets[$pet['idnum']]['price'] > 0)
      $sell = '<i class="dim">already listed</i>';
    else
      $sell = '<input name="p_' . $pet['idnum'] . '" maxlength="7" size="7" /><span class="money">m</span>';
?>
 <tr class="<?= $rowclass ?>">
  <td><?= pet_graphic($pet) ?></td>
  <td><?= $pet['petname'] ?></td>
  <td><ul class="plainlist"><li><?= implode('</li><li>', $details) ?></li></ul></td>
  <td class="centered"><?= $sell ?></td>
 </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
</table>
<p><input value="Clear" type="reset"> <input type="submit" value="Confirm &gt;" /></p>
</form>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
