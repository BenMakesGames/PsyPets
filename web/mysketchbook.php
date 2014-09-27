<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/sketchbooklib.php';
require_once 'commons/questlib.php';

$sketchbook_tutorial_quest = get_quest_value($user['idnum'], 'tutorial: my sketchbook');
if($sketchbook_tutorial_quest === false)
  $no_tip = true;

$command = 'SELECT idnum,userid,use_for_store,timestamp FROM psypets_store_portraits WHERE userid=' . $user['idnum'] . ' ORDER BY idnum DESC';
$pictures = $database->FetchMultiple($command, 'fetching pictures');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; My Sketchbook</title>
<?php include 'commons/head.php'; ?>
  <style type="text/css">
  ul#sketchbook
  {
    list-style: none;
  }

  #sketchbook li
  {
    width: 105px;
    height: 175px;
    margin-left: 0;
    list-style: none;
    display: block;
    float: left;
    padding: 4px;
    margin: 4px;
    border: 1px solid #000;
  }

  .transparent_image:hover
  {
    filter:alpha(opacity=100);
    -moz-opacity: 1.0;
    -khtml-opacity: 1.0;
    opacity: 1.0;
  }
  </style>
 </head>
 <body>
<?php
include 'commons/header_2.php';

if($sketchbook_tutorial_quest === false)
{
  include 'commons/tutorial/mysketchbook.php';
  add_quest_value($user['idnum'], 'tutorial: my sketchbook', 1);
}
?>
     <h4>My Sketchbook</h4>
     <ul>
      <li><a href="/mysketchbook_edit.php">Draw new picture</a></li>
      <li><a href="/shopkeepgallery.php">Browse other players' pictures</a></li>
     </ul>
<?php
if(count($pictures) == 0)
  echo '<p>You have not drawn any pictures.</p>';
else
{
  echo '
    <ul id="sketchbook">
  ';

  $rowclass = begin_row_class();

  foreach($pictures as $picture)
  {
    $postdate = duration($now - $picture['timestamp'], 2);

    echo '<li class="centered"><img src="sketch.php?id=' . $picture['idnum'] . '" width="105" height="150" alt="thumbnail" /><br />',
         '<a style="position:relative; top:-2px;" href="mysketchbook_delete.php?id=' . $picture['idnum'] . '" onclick="return confirm(\'Really delete this sketch?\');"><b class="failure">X</b></a> <a href="mysketchbook_edit.php?id=' . $picture['idnum'] . '"><img src="//' . $SETTINGS['site_domain'] . '/gfx/pencil.png" width="16" height="16" alt="(edit)" /></a>';

    if($user['license'] == 'yes')
    {
      if($picture['use_for_store'] == 'yes')
        echo '<img src="//' . $SETTINGS['site_domain'] . '/gfx/forsale.png" width="16" height="16" alt="current shop keeper" title="current shop keeper" />';
      else
        echo '<a href="mysketchbook_set_shop_keep.php?id=' . $picture['idnum'] . '"><img src="//' . $SETTINGS['site_domain'] . '/gfx/forsale.png" class="transparent_image" width="16" alt="use as shop keeper" title="use as shop keeper" /></a>';
    }

    echo '</li>';

    $rowclass = alt_row_class($rowclass);
  }

  echo '
    </ul>
  ';
}
?>
<div style="clear:both;"></div>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
