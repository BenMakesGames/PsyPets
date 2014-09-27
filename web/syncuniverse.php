<?php
require_once 'commons/dbconnect.php';

$command = '
  SELECT idnum,universeid
  FROM psypets_galactic_objects
';
$galaxies = $database->FetchMultiple($command, 'fetching galaxies');

foreach($galaxies as $galaxy)
{
  $command = '
    UPDATE psypets_stellar_objects
    SET universeid=' . $galaxy['universeid'] . '
    WHERE galaxyid=' . $galaxy['idnum'] . '
  ';
  $database->FetchNone($command, 'updating stellar objects');

  echo 'Updated ' . $database->AffectedRows() . ' solar systems<br />';
}

$command = '
  SELECT idnum,universeid
  FROM psypets_stellar_objects
';
$systems = $database->FetchMultiple($command, 'fetching solar systems');

foreach($systems as $system)
{
  $command = '
    UPDATE psypets_stars
    SET universeid=' . $system['universeid'] . '
    WHERE systemid=' . $system['idnum'] . '
  ';
  $database->FetchNone($command, 'updating stars');

  echo 'Updated ' . $database->AffectedRows() . ' stars<br />';

  $command = '
    UPDATE psypets_planets
    SET universeid=' . $system['universeid'] . '
    WHERE systemid=' . $system['idnum'] . '
  ';
  $database->FetchNone($command, 'updating planets');

  echo 'Updated ' . $database->AffectedRows() . ' planets<br />';
}
?>
