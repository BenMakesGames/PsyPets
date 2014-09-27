<?php
if($okay_to_be_here !== true)
  exit();

$makes = $database->FetchMultiple('SELECT * FROM psypets_crafts WHERE makes LIKE \'%candle\' ORDER BY idnum ASC');
?>
<style type="text/css">
#candle-book h5
{
  padding-left:52px;
  border-bottom: 1px solid #999;
  padding-bottom: 4px;
  margin-bottom: 4px;
}

#candle-book .picture
{
  float: left;
  width: 48px;
  text-align: center;
}

#candle-book .description
{
  margin-left: 52px;
}

#candle-book .divider
{
  clear:left;
  padding-bottom:32px;
}
</style>
<p><i>(This book details candlemaking, and includes the ingredients needed for several "flavors" of candle.)</i></p>
<div id="candle-book">
<?php
foreach($makes as $candle)
{
  $details = get_item_byname($candle['makes']);

  echo '
    <h5>' . $details['itemname'] . '</h5>
    <div class="picture">' . item_display($details) . '</div>
    <div class="description">
  ';
  
  if($details['enc_entry'] != '')
    echo '<p>' . $details['enc_entry'] . '</p>';

  echo '
      <ul><li>' . str_replace(',', '</li><li>', $candle['ingredients']) . '</li></ul>
    </div>
    <div class="divider"></div>
  ';
}
?>
</div>
