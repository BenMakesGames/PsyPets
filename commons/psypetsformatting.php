<?php
function utf8_stripslashes($string)
{
  return preg_replace(array('/\x5C(?!\x5C)/u', '/\x5C\x5C/u'), array('','\\'), $string);
}
?>
