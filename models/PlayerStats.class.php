<?php
class PlayerStats extends psyDBObject
{
  protected function __construct() { parent::__construct('psypets_player_stats'); }

  static public function GetCount($stat)
  {
    $stats = new PlayerStats();

    $command = '
      SELECT COUNT(userid) AS c
      FROM psypets_player_stats
      WHERE stat=' . $stats->QuoteString($stat) . '
      AND value>0
    ';
    $data = $stats->FetchSingle($command);

    return $data['c'];
  }

  static public function GetRankingsOverAge($stat, $oldest_age, $page)
  {
    $stats = new PlayerStats();

    // 24 * 60 * 60 = 86400
    
    $now_day = floor(time() / 86400);
    
    $command = '
      SELECT
        a.value/(' . $now_day . '-FLOOR(GREATEST(b.signupdate, ' . $oldest_age . ')/86400)) AS value,
        a.userid,
        b.display
      FROM
        psypets_player_stats AS a
        LEFT JOIN monster_users AS b
      ON
        a.userid=b.idnum
      WHERE
        a.stat=' . $stats->QuoteString($stat) . '
        AND value>0
      ORDER BY
        value DESC
      LIMIT
        ' . (($page - 1) * 20) . ',20
    ';
    $results = $stats->FetchMultiple($command);

    foreach($results as $result)
    {
      $rankings[$result['userid']] = $result;
      if($rankings[$result['userid']]['value'] > 1)
        $rankings[$result['userid']]['value'] = 1;
    }

    return $rankings;
  }
  
  static public function GetRankings($stat, $page)
  {
    $stats = new PlayerStats();

    $command = '
      SELECT
        a.value,
        a.userid,
        b.display
      FROM
        psypets_player_stats AS a
        LEFT JOIN monster_users AS b
      ON
        a.userid=b.idnum
      WHERE
        a.stat=' . $stats->QuoteString($stat) . '
        AND value>0
      ORDER BY
        a.value DESC
      LIMIT
        ' . (($page - 1) * 20) . ',20
    ';
    $results = $stats->FetchMultiple($command);

    foreach($results as $result)
      $rankings[$result['userid']] = $result;

    return $rankings;
  }

  static public function Get($userid, $stat)
  {
    $stat_value = new PlayerStats();

    $command = '
      SELECT *
      FROM psypets_player_stats
      WHERE
        userid=' . (int)$userid . '
        AND stat=' . $stat_value->QuoteString($stat) . '
      LIMIT 1
    ';
    $stat_value->_data = $stat_value->FetchSingle($command);

    return $stat_value;
  }
}
?>
