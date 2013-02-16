<?php
$CHANGELOG_CATEGORIES = array('bugfix', 'newpet', 'newitem', 'renameditem', 'newfeature', 'removedfeature', 'mechanicschange', 'uichange', 'newtext', 'textcorrection', 'admintool', 'performance');

$CHANGELOG_IMPACTS = array('small', 'medium', 'large');

$CHANGELOG_CATEGORY_NAMES = array(
  'bugfix' => 'Bug fix',
  'newpet' => 'New pet',
  'newitem' => 'New item',
  'renameditem' => 'Item change',
  'newfeature' => 'New feature',
  'removedfeature' => 'Removed feature',
  'mechanicschange' => 'Mechanics change',
  'uichange' => 'UI (User Interface) change',
  'newtext' => 'New text',
  'textcorrection' => 'Text correction or change',
  'admintool' => 'Administrative tools-related change',
  'performance' => 'Server performance improvement',
);

function new_changelog($category, $impact, $summary, $details)
{
  global $now;

  $command = '
    INSERT INTO psypets_changelog (timestamp, category, impact, summary, details)
    VALUES (' . $now . ', ' . quote_smart($category) . ', ' . quote_smart($impact) . ', ' . quote_smart($summary) . ', ' . quote_smart($details) . ')
  ';

  fetch_none($command, 'adding new changelog');
}

function get_latest_changelog()
{
  $command = 'SELECT * FROM psypets_changelog ORDER BY idnum DESC LIMIT 20';
  return fetch_multiple($command, 'fetching changelog');
}

function get_changelog_count($cats)
{
  if(count($cats) == 0)
    return 0;

  $ins = array();

  foreach($cats as $cat)
    $ins[] = quote_smart($cat);

  $command = 'SELECT COUNT(idnum) AS c FROM psypets_changelog WHERE category IN (' . implode(',', $ins) . ')';
  $data = fetch_single($command, 'fetching changelog item count');

  return (int)$data['c'];
}

function get_changelog_page($cats, $page)
{
  if(count($cats) == 0)
    return array();

  $ins = array();

  foreach($cats as $cat)
    $ins[] = quote_smart($cat);

  $command = 'SELECT * FROM psypets_changelog WHERE category IN (' . implode(',', $ins) . ') ORDER BY idnum DESC LIMIT ' . (($page - 1) * 20) . ',20';
  return fetch_multiple($command, 'fetching changelog page #' . $page);
}
?>
