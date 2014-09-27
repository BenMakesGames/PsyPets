<?php
if($challenge['step'] == 0)
  exit();

$item_request = array(
  0 => array('Chalk'),
  1 => array(''),
  2 => array(''),
  3 => array(''),
  4 => array(''),
);

$descriptions = array(
  'A worried-looking',
  'A spritely',
);

$nouns = array(
  'young man', 'young woman', '',
);

mt_srand($challenge['20101230']);


if($challenge['difficulty'] >= 0 && $challenge['difficulty'] <= 4)
{
  $i = mt_rand(0, count($item_request[$challenge['difficulty']]) - 1);
  $itemname = $item_request[$challenge['difficulty']][$i];

  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'' . $itemname . '\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching pinecones in storage');

  echo '<p>' . $description . ' ' . $noun . ' ' . $approach . '</p><p>' . $says . '</p>';

  if($data['c'] == 0)
    echo '<p><i>(You don\'t have a single ' . $itemname . ' in Storage, unfortunately.)</i></p>';
  else
    echo '<ul><li><a href="challenge.php?action=go">Give the ' . $noun . ' the ' . $itemname . '</a></li></ul>';
}
else
  exit();
?>
