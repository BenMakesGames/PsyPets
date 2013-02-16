<?php
class FailedLogins extends psyDBObject
{
  protected function __construct() { parent::__construct('psypets_failedlogins'); }

  public function ID() { return $this->_data['idnum']; }

  public static function RecentFailCount($username)
  {
    $ip = get_ip();
    
    $entries = new FailedLogins();
    
    return $entries->Count(array('where' => array(
      'username=' . $entries->QuoteString($username),
      'ip=' . $entries->QuoteString($ip),
      'timestamp>' . (time() - 10 * 60),
    )));
  }

  public static function Log($username, $exists)
  {
    $ip = get_ip();

    $new_entry = new FailedLogins();
    
    $insert_id = $new_entry->Insert(array(
      'timestamp' => time(),
      'ip' => $ip,
      'username' => $username,
      'user_exists' => ($exists ? 'yes' : 'no'),
    ));
    
    return $insert_id;
  }
}
?>
