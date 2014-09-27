<?php
$PLUSHY_QUEST_LIST = array(
  'Desikh Plushy', 'Duck Plushy', 'Mongoose Plushy', 'Mole Plushy',
  'Purple Dragon Plushy', 'Chickie Plushy', 'Turtle Plushy', 'Wereplushy',
  'Mouse Plushy'
);

if($user['idnum'] % 2 == 0)
  $PLUSHY_QUEST_LIST[] = 'Ghosty Plushy';
else
  $PLUSHY_QUEST_LIST[] = 'Lion Plushy';

function get_quest_value($userid, $name)
{
  return fetch_single('
    SELECT * FROM psypets_questvalues
    WHERE
      userid=' . $userid . '
      AND name=' . quote_smart($name) . '
    LIMIT 1
  ');
}


function get_quest_values_byuserid($userid)
{
	return $GLOBALS['database']->FetchMultipleBy('SELECT * FROM psypets_questvalues WHERE userid=' . (int)$userid, 'name');
}

function add_quest_value($userid, $name, $value)
{
  fetch_none('
    INSERT INTO psypets_questvalues
      (userid, name, value)
    VALUES
      (' . $userid . ', ' . quote_smart($name) . ', ' . $value . ')
  ');
}

function update_quest_value($idnum, $value)
{
  fetch_none('
    UPDATE psypets_questvalues
    SET value=' . $value . '
    WHERE idnum=' . $idnum . '
    LIMIT 1
  ');
}
?>
