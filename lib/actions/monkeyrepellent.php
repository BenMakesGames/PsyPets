<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/towerlib.php';
require_once 'commons/houselib.php';

if(addon_exists($house, 'Tower'))
{
  $tower = get_tower_byuser($user['idnum']);
}
else
  $tower = false;

if($tower === false || $tower['monkeyname'] == '')
{
  echo '
    <p>This powerful repellent fights off Tower Monkeys.  You do not seem to be plagued with any Tower Monkeys, however.</p>
  ';
}
else if($_GET['step'] == 2)
{
  $AGAIN_WITH_ANOTHER = true;
  $RECOUNT_INVENTORY = true;

  delete_inventory_byid($this_inventory['idnum']);

  echo '
    <p>You spray the repellent liberally.  At first ', $tower['monkeyname'], ' only seems slightly annoyed, but as you continue to spray, he eventually flies off - success!</p>
  ';

  clear_tower_monkey($user['idnum']);
}
else
{
  echo '
    <p>This powerful repellent fights off Tower Monkeys.  Do you want to use it?</p>
    <ul>
     <li><a href="itemaction.php?idnum=', $this_inventory['idnum'], '&step=2">Yeah!  I don\'t like that Tower Monkey.  (Especially it\'s name!  ', $tower['monkeyname'], '?  What kind of name is ', $tower['monkeyname'], '?)</a></li>
    </ul>
  ';
}
?>
