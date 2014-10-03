<?php
require_once 'commons/init.php';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/houselib.php';
require_once 'commons/messages.php';

$house = get_house_byuser($user['idnum']);

$rooms = take_apart(',', $house['addons']);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Arrange Add-ons</title>
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
    dragsort.makeListSortable(document.getElementById("roomarranger"), saveOrder);

    document.getElementById('spinner').style.display = 'none';
    document.getElementById('sortme').style.display = 'block';
  }

  function saveOrder()
  {
  }

  function savelist()
  {
		window.location = '/myhouse/saveaddonorder.php?list=' + junkdrawer.serializeList(document.getElementById('roomarranger'));
  }
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <h4><a href="/myhouse.php"><?= $user["display"] ?>'s House</a> &gt; Arrange Add-ons</h4>
<ul class="tabbed">
 <li><a href="/myhouse/managerooms.php">Add/Remove Rooms</a></li>
 <li><a href="/myhouse/arrangerooms.php">Arrange Rooms</a></li>
 <li class="activetab"><a href="/myhouse/arrangeaddons.php">Arrange Add-ons</a></li>
</ul>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";
?>
<p>Arrange your add-ons by dragging them into place.</p>
<p>Add-ons below the "hidden add-ons" separator will not be listed at home, but will still be listed in the Residence menu.  Hiding an add-on does not disable it in any way.  For example, pets can still interact with a hidden Lake or Farm.</p>
<div id="spinner">
<center>Hold on a sec while the page loads...<br /><br />
<img src="/gfx/throbber.gif" height="16" width="16" alt="" /></center>
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
    if($id == $house['max_addons_shown'])
      echo '<li id="separator" style="font-style:italic; text-align: center;">&#9660; hidden add-ons &#9660;</li>';

    echo '<li id="r' . $id . '">' . $room . '</li>';
  }

  if($house['max_addons_shown'] >= count($rooms))
    echo '<li id="separator" style="font-style:italic; text-align: center;">&#9660; hidden add-ons &#9660;</li>';
?>
</ul>
<p style="clear:both; padding-top: 1em;"><input type="button" value="Cancel" onclick="window.location='/myhouse.php';"/> <input type="button" value="Save" onclick="savelist()"/></p>
<?php
}
else
  echo '<p>Your house does not have any add-ons yet...</p>';
?>
</div>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
