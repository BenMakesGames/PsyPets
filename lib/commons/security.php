<?php

$DIGITS = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";

function quote_smart($value)
{
	return $GLOBALS['database']->Quote($value);
}

function RandomKey($length)
{
  global $DIGITS;

  $keyset = substr($DIGITS, 0, 16);

  $key = "";

  for($i = 0; $i < $length; ++$i)
    $key .= $keyset{rand(0, strlen($keyset) - 1)};

  return $key;
}

?>