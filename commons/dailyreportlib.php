<?php
function record_daily_report_stat($stat, $value)
{
  $now = date('Y-m-d');

  $command = '
    UPDATE psypets_daily_report_stats SET value=value+' . (int)$value . '
    WHERE `date`=\'' . $now . '\' AND `name`=' . quote_smart($stat) . ' LIMIT 1
  ';
  fetch_none($command, 'updating daily report stat record');

  if($GLOBALS['database']->AffectedRows() == 0)
  {
    $command = '
      INSERT INTO psypets_daily_report_stats (`date`, `name`, `value`) VALUES
      (\'' . $now . '\', ' . quote_smart($stat) . ', ' . (int)$value . ')
    ';
    fetch_none($command, 'updating daily report stat record');
  }
}
?>
