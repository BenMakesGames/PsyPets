<?php
$ARK_GRAPHICS_EXCLUDED = array(
  'special/panda.png',
  'special/marlin.png',
  'special/bunny_mocha.png',
  'phoenix/firebird_red.png',
  'phoenix/firebird_yellow.png',
  'phoenix/icebird.png',
);

function add_pet_to_ark($userid, $petid, $q_graphic, $q_gender)
{
  global $now;
  
  $GLOBALS['database']->FetchNone('
		INSERT INTO psypets_ark
		(`userid`, `timestamp`, `graphic`, `gender`, petid)
		VALUES
		(
			' . $userid . ',
			' . $now . ',
			' . $q_graphic . ',
			' . $q_gender . ',
			' . $petid . '
		)
	');
}

function get_collection_count()
{
  $data = $GLOBALS['database']->FetchSingle('SELECT COUNT(idnum) AS c FROM monster_users WHERE arkcount>0');
  
  return (int)$data['c'];
}

function get_collection_page($page)
{
  return fetch_multiple('
		SELECT display,arkcount
		FROM monster_users
		WHERE arkcount>0
		ORDER BY arkcount DESC
		' . $GLOBALS['database']->Page($page, 20) . '
	');
}

function get_user_ark_count($userid)
{
  $data = $GLOBALS['database']->FetchSingle('SELECT COUNT(*) AS c FROM psypets_ark WHERE userid=' . $userid);
  return (int)$data['c'];
}

function get_user_ark_page_by_time($userid, $page)
{
  return $GLOBALS['database']->FetchMultiple('
		SELECT timestamp,graphic,gender
		FROM psypets_ark
		WHERE userid=' . (int)$userid . '
		ORDER BY timestamp DESC
		' . $GLOBALS['database']->Page($page, 20) . '
	');
}

function get_user_ark_page_by_gender($userid, $page)
{
  return $GLOBALS['database']->FetchMultiple('
		SELECT timestamp,graphic,gender
		FROM psypets_ark
		WHERE userid=' . (int)$userid . '
		ORDER BY gender ASC,graphic ASC
		' . $GLOBALS['database']->Page($page, 20) . '
	');
}

function get_user_ark_page_by_graphic($userid, $page)
{
  return $GLOBALS['database']->FetchMultiple('
		SELECT timestamp,graphic,gender
		FROM psypets_ark
		WHERE userid=' . (int)$userid . '
		ORDER BY graphic ASC,gender ASC
		' . $GLOBALS['database']->Page($page, 20) . '
	');
}

function update_ark_count($userid)
{
  $count = get_user_ark_count($userid);
  
  $GLOBALS['database']->FetchNone('UPDATE monster_users SET arkcount=' . $count . ' WHERE idnum=' . $userid . ' LIMIT 1');
}
?>
