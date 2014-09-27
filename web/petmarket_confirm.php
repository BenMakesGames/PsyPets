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

if($NO_PVP)
{
  header('Location: ./lostdata.php');
  exit();
}

if($user['breeder'] != 'yes')
{
  header('Location: ./breederslicense.php?dialog=2');
  exit();
}

$command = 'SELECT * FROM monster_pets WHERE user=' . quote_smart($user['user']) . ' ORDER BY location ASC,orderid ASC';
$my_pets = $database->FetchMultipleBy($command, 'idnum', 'fetching pets to sell');

$command = 'SELECT * FROM psypets_pet_market WHERE expiration>' . $now;
$sale_pets = $database->FetchMultipleBy($command, 'petid', 'fetching pet market pets');

$listed = array();

$total = 0;

foreach($my_pets as $idnum=>$pet)
{
  if((int)$_POST['p_' . $idnum] > 0 && !array_key_exists($idnum, $sale_pets))
  {
    $listed[$idnum] = (int)$_POST['p_' . $idnum];
    $total += $listed[$idnum];
    $details[] = $pet['petname'] . ' for ' . $listed[$idnum];
  }
}

if(count($listed) == 0)
{
  header('Location: /petmarket_list.php');
  exit();
}

$fee = ceil(pet_sellers_fee() * $total);

if($fee > $user['money'])
{
  $style = ' class="failure"';
  $list_message = '<p>You do not have enough money to make this listing.</p>';
}

if($_GET['step'] == 2 && $fee <= $user['money'])
{
  $expiration = $now + (7 * 24 * 60 * 60);

  take_money($user, $fee, 'Pet Market listing fee', implode('<br />', $details));

  foreach($listed as $petid=>$price)
  {
		$database->FetchNone('
			DELETE FROM psypets_pet_market
			WHERE petid=' . (int)$petid . '
		');
	
    $database->FetchNone('
			INSERT INTO psypets_pet_market
			(expiration, petid, ownerid, price)
			VALUES
			(
				' . $expiration . ',
				' . $petid . ',
				' . $user['idnum'] . ',
				' . $price . '
			)
		');
  }
  
  if(count($listed) == 1)
    header('Location: /petmarket.php?msg=103');
  else
    header('Location: /petmarket.php?msg=102');

  exit();
}

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
<form action="petmarket_confirm.php?step=2" method="post">
<table>
 <tr class="titlerow">
  <th></th>
  <th>Name</th>
  <th>Details</th>
  <th class="centered">Price</th>
 </tr>
<?php
$rowclass = begin_row_class();

foreach($listed as $idnum=>$price)
{
  $pet = $my_pets[$idnum];
  $details = pet_market_details($pet);
?>
 <tr class="<?= $rowclass ?>">
  <td><img src="gfx/pets/<?= $pet['graphic'] ?>" width="48" height="48" /></td>
  <td><?= $pet['petname'] ?></td>
  <td><ul class="plainlist"><li><?= implode('</li><li>', $details) ?></li></ul></td>
  <td class="centered"><input type="hidden" name="p_<?= $idnum ?>" value="<?= $price ?>" /><?= $price ?><span class="money">m</span></td>
 </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
</table>
<?php
?>
<table>
 <tr><td>Total value:</td><td><?= $total ?><span class="money">m</span></td></tr>
 <tr><td<?= $style ?>>Seller's fee:</td><td<?= $style ?>><?= $fee ?><span class="money">m</span></td></tr>
</table>
<?= $list_message ?>
<p><input type="button" value="Cancel" onclick="location.href='petmarket_list.php'" /><?php if($fee <= $user['money']) { ?> <input type="submit" value="Sell" /><?php } ?></p>
</form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
