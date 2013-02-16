<?php
$child_safe = false;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/formatting.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/checkpet.php';
require_once 'commons/userlib.php';
require_once 'commons/utility.php';
require_once 'commons/grouplib.php';

$groupid = (int)$_GET['id'];
$group = get_group_byid($groupid);

if($group === false)
{
  header('Location: ./groupindex.php');
  exit();
}

if($user['idnum'] != $group['leaderid'])
{
  header('Location: ./grouppage.php?id=' . $groupid);
  exit();
}

$members = take_apart(',', $group['members']);

$command = 'SELECT * FROM monster_plaza WHERE groupid=' . $group['idnum'] . ' LIMIT 1';
$group_plaza = $database->FetchSingle($command, 'fetching group plaza watchers');

$watchers = take_apart(',', $group_plaza['admins']);

if(count($watchers) < 2)
{
  header('Location: ./grouprights.php?id=' . $groupid);
  exit();
}

$resident_name = urldecode($_GET['resident']);

if($resident_name{0} == '#')
  $resident['idnum'] = substr($resident_name, 1);
else
{
  $resident = get_user_bydisplay($resident_name, 'idnum');

  if($resident === false)
  {
    header('Location: ./grouprights.php?id=' . $groupid . '&msg=39:' . link_safe($_POST['resident']));
    exit();
  }
}

$key = array_search($resident['idnum'], $watchers);

if($key !== false)
{
  unset($watchers[$key]);

  $command = 'UPDATE monster_plaza SET admins=\'' . implode(',', $watchers) . '\' WHERE idnum=' . $group_plaza['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating group watchers');
}

header('Location: ./grouprights.php?id=' . $groupid);
?>
