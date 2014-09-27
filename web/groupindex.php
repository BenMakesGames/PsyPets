<?php
$require_petload = 'no';
$wiki = 'Groups';
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

$group_count = get_group_count();
$num_pages = ceil($group_count / 20);

$page = (int)$_GET['page'];

if($page > $num_pages)
  $page = $num_pages;

if($page < 1)
  $page = 1;

$sort_commands = array(
  'name ASC', 'name DESC',
  'birthdate ASC', 'birthdate DESC',
  'member_count ASC', 'member_count DESC',
  'towntiles ASC', 'towntiles DESC',
);

$sortby = (int)$_GET['sort'];
if(array_key_exists($sortby, $sort_commands))
  $do_sort = $sort_commands[$sortby];
else
{
  $sortby = 0;
  $do_sort = 'name ASC';
}

$groups = get_groups($do_sort, ($page - 1) * 20, 20);

$pages = paginate($num_pages, $page, 'groupindex.php?page=%s&sort=' . $sortby);

if($sortby == 0)
  $name_header = '<a href="groupindex.php?page=' . $page . '&sort=1">&#9660;</a>';
else if($sortby == 1)
  $name_header = '<a href="groupindex.php?page=' . $page . '&sort=0">&#9650;</a>';
else
  $name_header = '<a href="groupindex.php?page=' . $page . '&sort=0">&#9661;</a>';

if($sortby == 2)
  $founded_header = '<a href="groupindex.php?page=' . $page . '&sort=3">&#9660;</a>';
else if($sortby == 3)
  $founded_header = '<a href="groupindex.php?page=' . $page . '&sort=2">&#9650;</a>';
else
  $founded_header = '<a href="groupindex.php?page=' . $page . '&sort=2">&#9661;</a>';

if($sortby == 4)
  $members_header = '<a href="groupindex.php?page=' . $page . '&sort=5">&#9660;</a>';
else if($sortby == 5)
  $members_header = '<a href="groupindex.php?page=' . $page . '&sort=4">&#9650;</a>';
else
  $members_header = '<a href="groupindex.php?page=' . $page . '&sort=5">&#9651;</a>';

if($sortby == 6)
  $town_header = '<a href="groupindex.php?page=' . $page . '&sort=7">&#9660;</a>';
else if($sortby == 7)
  $town_header = '<a href="groupindex.php?page=' . $page . '&sort=6">&#9650;</a>';
else
  $town_header = '<a href="groupindex.php?page=' . $page . '&sort=7">&#9651;</a>';

$recount_members = ($_POST['action'] == 'recount' && $user['admin']['manageaccounts'] == 'yes');

include 'commons/html.php';
?>
 <head>
<?php include "commons/head.php"; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; Groups</title>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Groups</h4>
<img src="gfx/npcs/receptionist.png" align="right" width="350" height="275" alt="(Claire the City Hall receptionist)" />
<?php
include 'commons/dialog_open.php';

if($_GET['dialog'] == 2)
{
?>
<p>Groups are created by <?= $SETTINGS['site_name'] ?> residents, and are a good way for people with similar interests to talk with each other.</p>
<p>Some groups have even been created for the explicit purpose of helping out newbies!  If you're having trouble getting started in <?= $SETTINGS['site_name'] ?>, you might look into joining such a group.</p>
<p>Each group gets its own forum, which only other members can post to (although anyone can read them!), an item box to exchange items through, and a handful of other goodies.</p>
<?php
  $options[] = '<a href="groupindex.php?page=' . $page . '&dialog=3">How do I join a group?</a>';
  $options[] = '<a href="groupindex.php?page=' . $page . '&dialog=4">How do I create a group?</a>';
}
else if($_GET['dialog'] == 3)
{
?>
<p>Only a group's organizer can invite new members (or kick out existing ones).  However, please do not bug group organizers with requests to join.  Instead, look around the <a href="viewplaza.php?plaza=13">Clubs, Clans, Guilds and Groups</a> section of the Plaza.</p>
<?php
  $options[] = '<a href="groupindex.php?page=' . $page . '&dialog=4">How do I create a group?</a>';
}
else if($_GET['dialog'] == 4)
{
?>
<p>Creating a group requires use of a Guild Charter.  These are difficult to find, but your best bet is to find them in piles of Debris, strange though that may sound.</p>
<?php
  $options[] = '<a href="groupindex.php?page=' . $page . '&dialog=3">How do I join a group?</a>';
}
else
{
  echo'     <p>Here you\'ll find a listing of all the ' . $SETTINGS['site_name'] . ' groups.</p>';
  $options[] = '<a href="groupindex.php?page=' . $page . '&dialog=2">Tell me more about groups</a>';
}

include 'commons/dialog_close.php';

echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';
?>
     <?= $pages ?>
     <table>
      <tr class="titlerow"><th>Name <?= $name_header ?></th><th>Organizer</th><th>Members <?= $members_header ?></th><th>Town Size <?= $town_header ?></th><th>Founded <?= $founded_header ?></th></tr>
<?php
$rowclass = begin_row_class();

foreach($groups as $group)
{
  if($recount_members)
  {
    $members = take_apart(',', $group['members']);
    update_group_members($group['idnum'], $members);
    $group['member_count'] = count($members);
  }

  $leader = get_user_byid($group['leaderid']);
?>
      <tr class="<?= $rowclass ?>">
       <td><a href="grouppage.php?id=<?= $group['idnum'] ?>"><?= $group['name'] ?></a></td>
       <td><a href="userprofile.php?user=<?= link_safe($leader['display']) ?>"><?= $leader['display'] ?></a></td>
       <td class="centered"><?= $group['member_count'] ?></td>
       <td class="centered"><a href="grouptown.php?id=<?= $group['idnum'] ?>"><?= $group['towntiles'] ?></a></td>
       <td><?= local_date($group['birthdate'], $user['timezone'], $user['daylightsavings']) ?></td>
      </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
     </table>
     <?= $pages ?>
<?php
if($user['admin']['manageaccounts'] == 'yes')
  echo '<form action="groupindex.php" method="post"><input type="hidden" name="action" value="recount" /><input type="submit" value="Recount Members" /></form>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
