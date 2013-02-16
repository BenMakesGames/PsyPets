<?php
require_once 'commons/formatting.php';
require_once 'commons/bannedurls.php';

$text = trim($_GET['text']);

if(strlen($text) == 0)
  echo '<p class="failure">You should post <em>something</em>...</p>';
else
{
  foreach($BANNED_URLS as $url)
  {
    if(strpos($text, $url) !== false)
      echo '<p class="failure">Linking to ' . $url . ' is not allowed.  (<a href="/help/bannedurls.php">Why?</a>)</p>';
  }
}

echo format_text($text);
?>
