<?php
  $petgender = 'male';
  $petname = random_name($petgender);
?>
<img src="//saffron.psypets.net/gfx/npcs/petsheltergirl-2.png" align="right" width="350" height="450" alt="(Kim Littrell)" />
<?php include 'commons/dialog_open.php'; ?>
<div id="dialog_text">
<p>Hi, I'm Kim, and I run the Pet Shelter here!</p>
<p>Since they started studying PsyPets in 2004, HERG - you know, the Hollow Earth Research Group - has been overwhelmed with the things, and asked me to help out.</p>
<p>Oh!  What are PsyPets?  They're the creatures native to Hollow Earth!</p>
<p>They can make jewelry, and musical instruments, and swords, and lamps, and... I dunno, all kinds of things!</p>
<p>This little blue guy's called <?= $petname ?>.</p>
<p>If you'd like to get a PsyPet of your own, HERG is always looking for more people to help take care of them.</p>
<p>Interested?</p>
</div>
<?php include 'commons/dialog_close.php'; ?>
<ul>
 <li><a href="/signup.php"><strong>Ask how to get one!</strong></a></li>
 <li><a href="/petencyclopedia.php">Ask to see some of these "PsyPets".</a></li>
 <li><a href="/help/">Learn more about PsyPets.</a></li>
 <li><a href="/contactme.php">Ask how to contact That Guy Ben.</a></li>
</ul>
