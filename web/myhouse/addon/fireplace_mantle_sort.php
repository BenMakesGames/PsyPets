<?php
require_once 'commons/init.php';

$whereat = "home";
$wiki = "Fireplace";
$THIS_ROOM = 'Fireplace';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/fireplacelib.php';
require_once 'commons/utility.php';
require_once 'commons/itemlib.php';

if(!addon_exists($house, 'Fireplace'))
{
  header('Location: /myhouse.php');
  exit();
}

$first_visit = false;

$fireplace = get_fireplace_byuser($user['idnum'], $user['locid']);
if($fireplace === false)
{
  create_fireplace($user['idnum'], $user['locid']);
  $fireplace = get_fireplace_byuser($user['idnum'], $user['locid']);
  if($fireplace === false)
  {
    echo "Failed to load your fireplace.  Try reloading this page; if the problem persists, contact <a href=\"admincontact.php\">an administrator</a>.<br />\n";
    exit();
  }

  $first_visit = true;
}

if($_POST['submit'] == 'Save')
{
  foreach($_POST as $key=>$value)
  {
    if($value == 'item')
      $itemids[] = (int)substr($key, 1); 
  }

  $fireplace['mantle'] = implode(',', $itemids);

  $command = 'UPDATE psypets_fireplaces SET mantle=' . quote_smart($fireplace['mantle']) . ' WHERE idnum=' . $fireplace['idnum'] . ' LIMIT 1';
  fetch_none($command, 'updating mantle sort');
  
  header('Location: /myhouse/addon/fireplace_mantle.php');
  exit();
}

$command = 'SELECT * FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=\'fireplace\'';
$mantle = fetch_multiple_by($command, 'idnum', 'fetching mantle items');

if(count($mantle) == 0)
{
  header('Location: /myhouse/addon/fireplace.php');
  exit();
}

if(strlen($fireplace['mantle']) > 0)
  sort_items_by_mantle($mantle, explode(',', $fireplace['mantle']));

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Fireplace</title>
<?php include 'commons/head.php'; ?>
  <style type="text/css">
	#itemarranger {
		list-style-type: none;
		padding: 0;
		margin: 0;
	}
	#itemarranger li {
		cursor: move;
		position: relative;
		float: left;
		margin: 3px 3px 1px 1px;
		border: 1px solid #000;
		text-align: center;
		padding-top: 5px;
		width: 96px;
		height: 72px;
    background: #fff;
	}
  #itemarranger li:hover {
    border-width: 2px;
		margin: 2px 2px 0px 0px;
  }
  </style>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/toolman2.js"></script>
  <script type="text/javascript">
  var dragsort = ToolMan.dragsort()
	var junkdrawer = ToolMan.junkdrawer()

  window.onload = function() {
    dragsort.makeListSortable(document.getElementById("itemarranger"), saveOrder);

    document.getElementById('spinner').style.display = 'none';
    document.getElementById('sortme').style.display = 'block';
  }
  
  function saveOrder()
  {
  }
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; <a href="/myhouse/addon/fireplace.php">Fireplace</a> &gt; Arrange Mantle</h4>
<?php
echo $message;
room_display($house);
?>
<div id="spinner">
<center>Hold on a sec while the page loads...<br /><br />
<img src="/gfx/throbber.gif" height="16" width="16" alt="" /></center>
</div>
<div id="sortme" style="display:none;">
<p>Drag and drop your mantle items into the desired order.</p>
<form method="post">
<ul id="itemarranger">
<?php
  foreach($mantle as $item)
  {
    $details = get_item_byname($item['itemname']);
?>
  <li class="item" id="i<?= $item['idnum'] ?>">
   <?= item_display_extra($details, '', false) ?><br />
   <?= $details['itemname'] ?><br />
   <input type="hidden" name="i<?= $item['idnum'] ?>" value="item" />
  </li>
<?php
  }
?>
</ul>
<p style="clear:both; padding-top: 1em;"><input type="button" value="Cancel" onclick="window.location='/myhouse/addon/fireplace_mantle.php';" /> <input type="submit" name="submit" value="Save" /></p>
</form>
</div>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
