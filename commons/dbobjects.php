<?php
class DBObject
{
  public function __construct()
  {
    $this->Delete();
  }

  public function __destruct()
  {
    $this->Update();
  }

  public function Update()
  {
    if($this->Exists() && count($this->_updates) > 0)
    {
      $command = "UPDATE `" . $this->_table . "` SET " . implode(",", $this->_updates) . " WHERE `idnum`=" . $this->_idnum . " LIMIT 1";
      $result = mysql_query($command);

      if(!$result)
      {
        echo "DBObject::Update()<br />\n" .
             "error in $command<br />\n" .
             mysql_error() . "<br />\n";
        exit();
      }

      $this->_updates = array();
    }
  }

  public function LoadByID($idnum)
  {
    if($idnum >= 0 && (int)$idnum == $idnum)
      return $this->LoadBy("idnum", $idnum);
    else
      return false;
  }

  public function Exists() { return($this->_exists === true); }

  public function GetIDNum() { return $this->_idnum; }
  public function GetTable() { return $this->_table; }

  public function LoadDirect($data)
  {
    if($this->Exists() == false)
      return $this->LoadDirectTrusted($data);
    else
      return false;
  }

  protected function LoadBy($name, $value)
  {
    if($this->Exists() == false)
    {
      $command = "SELECT * FROM `" . $this->_table . "` WHERE `$name`=" . quote_smart($value) . " LIMIT 1";
      $result = mysql_query($command);

      if(!$result)
      {
        echo "DBObject::LoadBy($name, $value)<br />\n" .
             "error in $command<br />\n" .
             mysql_error() . "<br />\n";
        exit();
      }

      if(mysql_num_rows($result) == 1)
      {
        $data = mysql_fetch_assoc($result);
        
        mysql_free_result($result);

        return $this->LoadDirectTrusted($data);
      }
      else
        return false;
    }
    // else, this object already exists
    else
      return false;
  }

  protected function Delete()
  {
    $this->_updates = array();
    $this->_exists = false;
    $this->_data = array();
    $this->_idnum = NULL;
  }

  protected function AddUpdateString($s)
  {
    if($this->Exists())
      $this->_updates[] = $s;
  }

  protected function LoadDirectTrusted($data)
  {
    $this->_data = $data;
    $this->_idnum = $data["idnum"];
    $this->_exists = true;
    
    return true;
  }

  protected $_table;

  protected $_data;

  private $_idnum;
  private $_exists;
  private $_updates;
}

?>