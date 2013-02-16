<?php
function record_stat($userid, $stat, $value)
{
  $command = '
    UPDATE psypets_player_stats SET value=value+' . $value . ',lastupdate=' . time() . '
    WHERE userid=' . $userid . ' AND stat=' . quote_smart($stat) . ' LIMIT 1
  ';
  fetch_none($command, 'updating stat record');

  if($GLOBALS['database']->AffectedRows() == 0)
  {
    $command = '
      INSERT INTO psypets_player_stats (userid, stat, value, lastupdate) VALUES
      (' . $userid . ', ' . quote_smart($stat) . ', ' . $value . ', ' . time() . ')
    ';
    fetch_none($command, 'adding stat record');
  }
}

function record_stat_with_badge($userid, $stat, $value, $threshhold, $badge)
{
  $badges = get_badges_byuserid($userid);

  if($badges[$badge] == 'yes')
  {
    record_stat($userid, $stat, $value);

    return false;
  }
  else
  {
    $command = 'SELECT value FROM psypets_player_stats WHERE userid=' . $userid . ' AND stat=' . quote_smart($stat) . ' LIMIT 1';
    $data = fetch_single($command);

    if($data === false)
    {
      $command = '
        INSERT INTO psypets_player_stats (userid, stat, value, lastupdate) VALUES
        (' . $userid . ', ' . quote_smart($stat) . ', ' . $value . ', ' . time() . ')
      ';
      fetch_none($command, 'adding stat record');

      $met_threshhold = ($value >= $threshhold);
    }
    else
    {
      $command = '
        UPDATE psypets_player_stats SET value=value+' . $value . ',lastupdate=' . time() . '
        WHERE userid=' . $userid . ' AND stat=' . quote_smart($stat) . ' LIMIT 1
      ';
      fetch_none($command, 'updating stat record');

      $met_threshhold = ($data['value'] + $value >= $threshhold);
    }

    if($met_threshhold)
    {
      set_badge($userid, $badge);
      return true;
    }
    else
      return false;
  }
}
?>
