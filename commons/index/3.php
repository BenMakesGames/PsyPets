<?php
  $petgender = 'male';
  $petname = random_name($petgender);
?>
<!--<img src="" align="right" width="350" height="" alt="" />-->
<?php include 'commons/dialog_open.php'; ?>
<div id="dialog_text">
<p>Intro dialog 3</p>
</div>
<?php include 'commons/dialog_close.php'; ?>
<ul>
 <li><a href="/signup.php"><strong>Ask how to get one!</strong></a></li>
 <li><a href="/petencyclopedia.php">Ask to see some of these pets.</a></li>
 <li><a href="/help/">Learn more about <?= $SETTINGS['site_name'] ?>.</a></li>
 <li><a href="/contactme.php">Ask how to contact <?= $SETTINGS['author_real_name'] ?>.</a></li>
</ul>
