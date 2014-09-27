<?php
if($okay_to_be_here !== true)
  exit();
?>
Which song will you play?
<ul>
 <li><a onclick="window.open('audioplayer.php?id=<?= md5('songn*1') ?>', 'pp_audio', 'location=0,status=0,scrollbars=0,resizable=0,width=300,height=100');" href="#">Brahms' Hungarian Dance No. 1</a></li>
</ul>
