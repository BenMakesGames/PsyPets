<?php
if($okay_to_be_here !== true)
  exit();

$flowers = $database->FetchMultiple('SELECT idnum,itemname,graphic,graphictype,enc_entry FROM monster_items WHERE itemtype LIKE \'plant/flower%\' AND custom=\'no\' AND enc_entry!=\'\' ORDER BY itemname ASC');
?>
<style type="text/css">
#flower-book h5
{
  padding-left:52px;
  border-bottom: 1px solid #999;
  padding-bottom: 4px;
  margin-bottom: 4px;
}

#flower-book .picture
{
  float: left;
  width: 48px;
  text-align: center;
}

#flower-book .description
{
  margin-left: 52px;
}

#flower-book .divider
{
  clear:left;
  padding-bottom:32px;
}
</style>
<div id="flower-book">
<?php
foreach($flowers as $flower)
{
  echo '
    <h5>' . $flower['itemname'] . '</h5>
    <div class="picture">' . item_display($flower) . '</div>
    <div class="description">
  ';
  
  echo '<p>' . $flower['enc_entry'] . '</p>';

  echo '
    </div>
    <div class="divider"></div>
  ';
}
?>
</div>
