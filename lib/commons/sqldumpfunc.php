<?php
function dump_sql_results($data)
{
  echo '<table>' .
       '<tr class="titlerow"><th>Property</th><th>Value</th></tr>';

  $row = begin_row_class();
  foreach($data as $key=>$value)
  {
    echo '<tr class="' . $row . '"><td>' . $key . '</td><td>' . $value . '</td></tr>';
    $row = alt_row_class($row);
  }

  echo '</table>';
}
?>
