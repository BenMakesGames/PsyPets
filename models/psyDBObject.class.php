<?php
abstract class psyDBObject
{
  private static $db_connection = false;

  protected $_table;
  protected $_data = false;

  public function RawData() { return $this->_data; }
  public function IsLoaded() { return($this->_data !== false); }

  protected function Insert($params = array())
  {
    $command = '
      INSERT INTO ' . $this->_table . '
      (' . implode(', ', array_keys($params)) . ')
      VALUES
      (' . implode(', ', $this->QuoteArray($params)) . ')
    ';
    $this->FetchNone($command);
    
    return mysql_insert_id();
  }

  protected function Update($params = array())
  {
    if($params['where'] && count($params['where']) > 0)
      ;
    else
      $this->HandleError('No WHERE given for ::Update');

    $command = '
      UPDATE ' . $this->_table . '
      SET ' . implode(', ', $params['set']) . '
      WHERE ' . implode(' AND ', $params['where']);

    if($params['limit'])
      $command .= ' LIMIT ' . $params['limit'];

    $this->FetchNone($command);
  }

  protected function Count($params = array())
  {
    $command = '
      SELECT COUNT(*) AS qty FROM ' . $this->_table . '
      WHERE ' . implode(' AND ', $params['where']);

    $data = $this->FetchSingle($command);

    return $data['qty'];
  }
  
  protected function Select($params = array())
  {
    $command = '
      SELECT * FROM ' . $this->_table . '
      WHERE ' . implode(' AND ', $params['where']);

    if($params['order'])
      $command .= ' ORDER BY ' . implode(', ', $params['order']);

    if($params['limit'])
      $command .= ' LIMIT ' . $params['limit'];

    $this->_data = $this->FetchMultiple($command);

    return $this->_data;
  }

  protected function SelectOne($params = array())
  {
    $command = '
      SELECT * FROM ' . $this->_table . '
      WHERE ' . implode(' AND ', $params['where']);

    if($params['order'])
      $command .= ' ORDER BY ' . implode(', ', $params['order']);

    $command .= ' LIMIT 1';

    $this->_data = $this->FetchSingle($command);

    return $this->_data;
  }

  protected function __construct($table)
  {
    global $SETTINGS;
  
    if(psyDBObject::$db_connection === false)
    {
      psyDBObject::$db_connection = @mysql_pconnect($SETTINGS['mysql']['uri'], $SETTINGS['mysql']['user'], $SETTINGS['mysql']['password']);

      if(!psyDBObject::$db_connection)
      {
        // 1203 == ER_TOO_MANY_USER_CONNECTIONS
        if(mysql_errno() == 1203)
        {
          header('Location: /mysql_1203.php');
          exit();
        }
        // 1040 == ER_CON_COUNT_ERROR
        else if(mysql_errno() == 1040)
        {
          header('Location: /mysql_1040.php');
          exit();
        }
        else
        {
          header('Location: /mysql_error.php?id=' . mysql_errno());
          exit();
        }
      }

      mysql_select_db($SETTINGS['mysql']['db'], psyDBObject::$db_connection);

      $this->QueryDB('SET NAMES \'utf8\'');
    }
    
    $this->_table = $table;
  }

  public function QuoteArray($array)
  {
    $values = array();

    foreach($array as $value)
      $values[] = $this->QuoteSmart($value);

    return $values;
  }

  public function QuoteString($string)
  {
    // Stripslashes
    if(get_magic_quotes_gpc())
      $string = utf8_stripslashes($string);

    // Quote if not integer
    if(!is_numeric($string))
      $string = "'" . mysql_real_escape_string($string, psyDBObject::$db_connection) . "'";

    return $string;
  }
  
  public function QuoteSmart($value)
  {
    if(is_array($value))
      return $this->QuoteArray($value);
    else
      return $this->QuoteString($value);
  }

  private function QueryDB($command)
  {
    return mysql_query($command);
  }

  private function HandleError($query)
  {
    // e-mail me the details of the error!
    $message =
      '<p>Query: ' . $query . '</h3>' . "\n" .
      '<p>MySQL Error: ' . mysql_error() . '</p>' . "\n" .
      '<pre>' . '' . '</pre>' . "\n"
    ;

    mail($SETTINGS['author_email'], $SETTINGS['site_name'] . ' particularly nasty database error', $message, 'From: ' . $SETTINGS['site_mailer']);
    die('<p>A particularly nasty database error has occurred.  ' . $SETTINGS['author_resident_name'] . ' has been e-mailed with the details of this error.</p><p>Use the refresh button of your browser to retry doing whatever it was you were trying to do.  If the problem persists, please contact ' . $SETTINGS['author_resident_name'] . ' with details about what you were trying to do.  It\'ll help him fix whatever bug may be at work here.</p><p>Sorry about the inconvenience!</p>');
  }

  protected function FetchNone($command)
  {
    $result = $this->QueryDB($command);

    if(!$result)
      $this->HandleError($command);
  }

  protected function FetchSingle($command)
  {
    $result = $this->QueryDB($command);

    if(!$result)
      $this->HandleError($command);

    if(mysql_num_rows($result) == 0)
      return false;

    $data = mysql_fetch_assoc($result);

    mysql_free_result($result);

    return $data;
  }

  protected function FetchMultiple($command)
  {
    $result = $this->QueryDB($command);

    if(!$result)
      $this->HandleError($command);

    if(mysql_num_rows($result) == 0)
      return array();

    $data = array();

    while($row = mysql_fetch_assoc($result))
      $data[] = $row;

    mysql_free_result($result);

    return $data;
  }

  // fetches all available rows into an array indexed by the row's $by field value
  protected function FetchMultipleBy($command, $by)
  {
    $result = $this->QueryDB($command);

    if(!$result)
      $this->HandleError($command);

    if(mysql_num_rows($result) == 0)
      return array();

    $data = array();

    while($row = mysql_fetch_assoc($result))
      $data[$row[$by]] = $row;

    mysql_free_result($result);

    return $data;
  }
}
?>
