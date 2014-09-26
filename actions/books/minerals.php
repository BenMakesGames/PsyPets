<?php
if($okay_to_be_here !== true)
  exit();

$minerals = $database->FetchMultiple('
  SELECT *
  FROM monster_items
  WHERE
    custom=\'no\' AND
    itemtype LIKE \'earth/%\' AND
    enc_entry!=\'\'
  ORDER BY itemname ASC
');
?>
<style type="text/css">
#minerals-book h5
{
  padding-left:52px;
  border-bottom: 1px solid #999;
  padding-bottom: 4px;
  margin-bottom: 4px;
}

#minerals-book .picture
{
  float: left;
  width: 48px;
  text-align: center;
}

#minerals-book .description
{
  margin-left: 52px;
}

#minerals-book .divider
{
  clear:left;
  padding-bottom:32px;
}
</style>
<div id="minerals-book">
<?php
foreach($minerals as $mineral)
{
  echo '
    <h5>' . $mineral['itemname'] . '</h5>
    <div class="picture">' . item_display($mineral) . '</div>
    <div class="description">' . $mineral['enc_entry'] . '</div>
    <div class="divider"></div>
  ';
}
?>
</div>