<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_SAME = true;

$possibilities = array("All signs point to yes", "Definitely", "Without a doubt",
                       "Probably not", "No", "It's looking doubtful",
                       "If you act quickly", "Only time will tell", "Try asking again later",
                       "Maybe");

echo '<i>"' . $possibilities[array_rand($possibilities)] . '"</i>';
?>
