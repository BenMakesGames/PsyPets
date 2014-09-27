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
  header('Location: /groupindex.php');
  exit();
}

if($user['idnum'] != $group['leaderid'])
{
  header('Location: /grouppage.php?id=' . $groupid);
  exit();
}

$members = take_apart(',', $group['members']);
$mappers = take_apart(',', $group['mappers']);

$resident = get_user_bydisplay($_POST['resident'], 'idnum');

if($resident === false)
{
  header('Location: /grouprights.php?id=' . $groupid . '&msg=39:' . link_safe($_POST['resident']));
  exit();
}

$a_member = (array_search($resident['idnum'], $members) !== false);
$a_mapper = (array_search($resident['idnum'], $mappers) !== false);

if($a_mapper)
{
  header('Location: /grouprights.php?id=' . $groupid . '&msg=96:' . link_safe($_POST['resident']));
  exit();
}

if(!$a_member)
{
  header('Location: /grouprights.php?id=' . $groupid . '&msg=97:' . link_safe($_POST['resident']));
  exit();
}

$mappers[] = $resident['idnum'];

update_group_mappers($groupid, $mappers);

header('Location: /grouprights.php?id=' . $groupid);
?>
