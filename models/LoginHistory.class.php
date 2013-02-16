<?php
class LoginHistory extends psyDBObject
{
  public static function CreateRecord($userid, $ip)
  {
    $new_history = new LoginHistory();
    
    $insert_id = $new_history->Insert(array(
      'userid' => $userid,
      'timestamp' => time(),
      'ipaddress' => $ip
    ));
    
    return $insert_id;
  }

  protected function __construct() { parent::__construct('monster_loginhistory'); }
}
?>
