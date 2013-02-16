<script type="text/javascript">
function autofill(itemid, value)
{
  $('#i_' + itemid).val(value);
}
</script>
<?php
if(count($items) > 0)
{
?>
<form action="<?= $url ?>" method="post" name="itemlist" id="itemlist">
<table>
 <tr class="titlerow">
  <th class="centered">Quantity</th>
  <th></th>
  <th>Item</th>
  <th>Durability</th>
 </tr>
<?php
  $rowclass = begin_row_class();

  foreach($items as $item)
  {
?>
 <tr class="<?= $rowclass ?>">
  <td><nobr><input type="number" min="0" max="<?= $item['quantity'] ?>" name="i_<?= $item['idnum'] ?>" id="i_<?= $item['idnum'] ?>" maxlength="<?= strlen($item['quantity']) ?>" size="2" /> / <a href="#" onclick="autofill(<?= $item['idnum'] ?>, <?= $item['quantity'] ?>); return false;"><?= $item['quantity'] ?></a></nobr></td>
  <td align="center"><?= item_display($item, '') ?></td>
  <td><?= $item['itemname'] ?></td>
  <td><?= durability($item['health'], $item['durability']) ?></td>
 </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
</table>
<p><input type="hidden" name="action" value="recycle" /><input type="submit" value="Recycle" /></p>
<?php
}
else
{
?>
     <p>You don't have any recyclable items in Storage.</p>
<?php
}
?>
