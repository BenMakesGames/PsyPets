<?php
class QuestValue extends psyDBObject
{
  public function ID() { return $this->_data['idnum']; }
  public function Name() { return $this->_data['name']; }
  public function Value() { return $this->_data['value']; }

  public function UpdateValue($value)
  {
    $this->_data['value'] = $value;
  
    $this->Update(array(
      'set' => array('value=' . (int)$value),
      'where' => array('idnum=' . (int)$this->_data['idnum']),
      'limit' => 1,
    ));
  }

  protected function __construct() { parent::__construct('psypets_questvalues'); }

  static public function Get($userid, $name)
  {
    $quest_value = new QuestValue();
  
    $command = '
      SELECT * FROM psypets_questvalues
      WHERE
        userid=' . (int)$userid . '
        AND name=' . $quest_value->QuoteString($name) . '
      LIMIT 1
    ';
    $quest_value->_data = $quest_value->FetchSingle($command);
    
    return $quest_value;
  }
}
