<?php
class Plaza extends psyDBObject
{
  public function ID() { return $this->_data['idnum']; }

  public function __construct() { parent::__construct('monster_plaza'); }

  public function GetSections($groups = array())
  {
    $groups[] = 0;
    
    return $this->Select(array(
      'where' => array(
        'groupid IN (' . implode(',', $groups) . ')',
        'title NOT LIKE \'#%\''
      ),
      'order' => array(
        '`order` ASC'
      )
    ));
  }
}
?>
