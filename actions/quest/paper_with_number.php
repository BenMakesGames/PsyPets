<?php
if($okay_to_be_here !== true)
  exit();
?>
<p><i>You hurridly scraweled the following number on this paper:</i></p>
<p style="font-size:20px;"><?= book_code_number($user) ?></p>