<?php
$messages = array(
  'Alain hates food.',
  'Deleted 20 <a href="here.php">awesome messages</a>.',
);
?>
<html>
 <body>
<?php
foreach($messages as $message)
{
  echo 'uncompressed: ' . strlen($message) . '; compressed: ' . strlen(gzcompress($message, 9)) . '<br />';
}
?>
 </body>
</html>