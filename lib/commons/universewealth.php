<ul class="wealth">
<?php
  if($universe['galaxies'] > 0)
    echo '<li>' . $universe['clouds'] . ' Galax' . ($universe['clouds'] != 1 ? 'ies' : 'y') . '</li>';

  if($universe['clouds'] > 0)
    echo '<li>' . $universe['clouds'] . ' Cloud' . ($universe['clouds'] != 1 ? 's' : '') . '</li>';

  echo '
    <li>' . $universe['hydrogen'] . ' Hydrogen</li>
    <li>' . $universe['stars'] . ' Star' . ($universe['stars'] != 1 ? 's' : '') . '</li>
  ';

  if($universe['supernova'] > 0)
    echo '<li>' . $universe['supernova'] . ' Supernova</li>';

  echo '
    <li>' . $universe['rocks'] . ' Rock' . ($universe['rocks'] != 1 ? 's' : '') . '</li>
  ';

  if($universe['gasgiants'] > 0)
    echo '<li>' . $universe['gasgiants'] . ' Gas Giant' . ($universe['gasgiants'] != 1 ? 's' : '') . '</li>';

  if($universe['stage'] == 'gameplay')
    echo '<li>(<a href="universe_raidstorage.php">get more!</a>)</li>';
?>
</ul>
<div id="objectmenu">
<ul><li><span class="dim">Nothing to place!</span></li></ul>
</div>
