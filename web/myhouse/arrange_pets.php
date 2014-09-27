<?php
require_once 'commons/init.php';

$whereat = 'home';
$wiki = 'My_House';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/formatting.php';
require_once 'commons/sessions.php';
require_once 'commons/petlib.php';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Rearrange Pets</title>
<?php include 'commons/head.php'; ?>
  <style type="text/css">
	#petarranger {
		list-style-type: none;
		padding: 0;
		margin: 0;
/*		width: <?= $user['petcolumns'] * 134 ?>px;*/
	}
	#petarranger li {
		cursor: move;
		position: relative;
		float: left;
		margin: 3px 3px 1px 1px;
		border: 1px solid #000;
		text-align: center;
		padding-top: 5px;
		width: 128px;
		height: 96px;
    background: #fff;
	}
  #petarranger li:hover {
    border-width: 2px;
		margin: 2px 2px 0px 0px;
  }
  </style>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/toolman2.js"></script>
  <script type="text/javascript">
  var dragsort = ToolMan.dragsort()
	var junkdrawer = ToolMan.junkdrawer()

  window.onload = function() {
    dragsort.makeListSortable(document.getElementById("petarranger"), saveOrder);

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
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Rearrange Pets</h4>
     <p>Arrange your pets by dragging them in to place.  The order of your pets also determines the order they act during each hour<a href="/help/hours.php" class="help">?</a>.</p>
<div id="spinner">
<center>Hold on a sec while the page loads...<br /><br />
<i>If you have lots and lots of pets, it may take more than "a sec"... >_>)</i><br /><br />
<img src="/gfx/throbber.gif" height="16" width="16" alt="" /></center>
</div>
<div id="sortme" style="display:none;">
<?php
if(count($userpets) > 0)
{
?>
<form action="/myhouse/update_pet_order.php" method="post">
<ul id="petarranger">
<?php
  $pet_count = 0;
  $maxpets = count($userpets);

  foreach($userpets as $petnum=>$pet)
  {
?>
  <li class="pet" id="p<?= $pet['idnum'] ?>">
   <div style="padding-left: 40px; width:48px; height:48px;"><?= pet_graphic($pet, false) ?></div>
   <?= $pet['petname'] ?><br />
   Level <?= pet_level($pet) ?><br />
   <input type="hidden" name="p<?= $pet['idnum'] ?>" value="pet" />
  </li>
<?php
  }
?>
</ul>
<p style="clear:both; padding-top: 1em;"><input type="button" value="Cancel" onclick="window.location='/myhouse.php';" /> <input type="submit" value="Save" /></p>
</form>
<?php
}
else
  echo '<p>You don\'t have any pets <em>to</em> arrange.</p>';
?>
</div>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
