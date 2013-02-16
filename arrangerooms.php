<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/houselib.php';
require_once 'commons/messages.php';

$locid = $user['locid'];

$house = get_house_byuser($user['idnum'], $locid);

$rooms = take_apart(',', $house['rooms']);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Arrange Rooms</title>
<?php include 'commons/head.php'; ?>
  <style type="text/css">
	#roomarranger {
		list-style-type: none;
		padding: 0;
		margin: 0;
	}
	#roomarranger li {
		cursor: move;
		position: relative;
		margin: 3px 3px 1px 1px;
		border: 1px solid #000;
		padding: 3px;
		width: 128px;
    background: #fff;
	}
  #roomarranger li:hover {
    border-width: 2px;
    padding: 2px;
  }
  </style>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/toolman2.js"></script>
  <script type="text/javascript">
  var dragsort = ToolMan.dragsort()
	var junkdrawer = ToolMan.junkdrawer()

  window.onload = function() {
    dragsort.makeListSortable(document.getElementById('roomarranger'), saveOrder);

    document.getElementById('spinner').style.display = 'none';
    document.getElementById('sortme').style.display = 'block';
  }

  function saveOrder()
  {
  }

  function savelist()
  {
		window.location = 'saveroomorder.php?list=' + junkdrawer.serializeList(document.getElementById('roomarranger'));
  }
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <h4><a href="myhouse.php"><?= $user["display"] ?>'s House</a> &gt; Arrange Rooms</h4>
<ul class="tabbed">
 <li><a href="managerooms.php">Add/Remove Rooms</a></li>
 <li class="activetab"><a href="arrangerooms.php">Arrange Rooms</a></li>
 <li><a href="arrangeaddons.php">Arrange Add-ons</a></li>
</ul>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";
?>
<p>Arrange your rooms by dragging them into place.</p>
<p>Rooms below the "hidden rooms" separator will not be listed at home, but will still be listed in the Residence menu and Inventory Summary.  Hiding a room does not prevent pets from accessing it (<a href="managerooms.php">only locking it can do that</a>).</p>
<div id="spinner">
<center>Hold on a sec while the page loads...<br /><br />
<img src="gfx/throbber.gif" height="16" width="16" alt="" /></center>
</div>
<div id="sortme" style="display:none;">
<?php
if(count($rooms) > 0)
{
?>
<ul id="roomarranger">
<?php
  foreach($rooms as $id=>$room)
  {
    if($id == $house['max_rooms_shown'])
      echo '<li id="separator" style="font-style:italic; text-align: center;">&#9660; hidden rooms &#9660;</li>';

    echo '<li id="r' . $id . '">';

    if($room{0} == '$')
      echo substr($room, 1);
    else
      echo $room;

    echo '</li>';
  }
  
  if($house['max_rooms_shown'] >= count($rooms))
    echo '<li id="separator" style="font-style:italic; text-align: center;">&#9660; hidden rooms &#9660;</li>';
?>
</ul>
<p style="clear:both; padding-top: 1em;"><input type="button" value="Cancel" onclick="window.location='myhouse.php';"/> <input type="button" value="Save" onclick="savelist()"/></p>
<?php
}
else
  echo '<p>You have not added any rooms to your house yet...</p>';
?>
</div>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
