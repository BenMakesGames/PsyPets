<?php
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/equiplib.php';
require_once 'commons/sellermarketlib.php';
require_once 'commons/blimplib.php';
require_once 'commons/encyclopedialib.php';

$itemname = unlink_safe($_GET['item']);
$itemid = (int)$_GET['i'];

if($itemid > 0)
{
  $item = get_item_byid($itemid);
  $itemname = $item['itemname'];
}
else
{
  $item = get_item_byname($itemname);
  $itemid = $item['idnum'];
}

$style_layout = (array_key_exists($user['style_layout'], $SITE_LAYOUTS) ? $user['style_layout'] : 'default');
$style_color = (array_key_exists($user['style_color'], $SITE_COLORS) ? $user['style_color'] : 'telkoth');

if($item === false || $item['custom'] == 'secret')
{
  echo '<p>No such item exists.</p>';
}
else
{
  RenderEncyclopediaItem($item, $user, $userpets);
}
?>
<hr />
<h5>Search</h5>
<form action="/encyclopedia.php" method="get">
<p><input name="itemname" value="<?= $_POST['itemname'] ?>" maxlength="64" style="width:192px;" /> <input type="hidden" name="submit" value="Search" /><input type="submit" value="Search" /></p>
</form>
