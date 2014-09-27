<?php
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/totemlib.php';

$command = 'SELECT * FROM psypets_totempoles';
$totempoles = $database->FetchMultiple($command, 'fetching totem poles');

foreach($totempoles as $totempole)
{
  $totems = take_apart(',', $totempole['totem']);
  $score = totem_score($totems);
  echo $score . ' &lt;-' . $totempole['totem'] . '<br />';
  $command = 'UPDATE psypets_totempoles SET rating=' . $score . ' WHERE idnum=' . $totempole['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating score');
}
?>
