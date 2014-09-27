<?php
require_once 'commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/museumlib.php';

$options = array();

$item_count = get_user_museum_count($user['idnum']);

$dialog_text = '<p>This is a list of the Wings we\'ve built for other Residents\' contributions.</p>';

if($item_count < 100)
  $dialg_text = '<p>If you donate 100 items, we\'ll build a Wing for your collection, too.</p>';

if($_GET['friendsonly'])
{
  $friend_list = array($user['idnum']);

  $friends = fetch_multiple('
    SELECT friendid
    FROM psypets_user_friends
    WHERE userid=' . $user['idnum'] . '
  ');
  
  foreach($friends as $friend)
    $friend_list[] = $friend['friendid'];

	$num_wings = get_museum_wing_count($friend_list);
	$num_pages = ceil($num_wings / 20);

	$page = (int)$_GET['page'];
	if($page < 1 || $page > $num_pages)
		$page = 1;

	$wings = fetch_multiple('
		SELECT display,museumcount
		FROM monster_users
		WHERE
			idnum IN (' . implode(',', $friend_list) . ')
			AND museumcount>=100
		ORDER BY museumcount DESC
		LIMIT ' . (($page - 1) * 20) . ',20
	');

	$pages = paginate($num_pages, $page, '/museum/wings.php?friendsonly=1&amp;page=%s');
	
	$options[] = '<a href="/museum/wings.php">Ask to see <em>all</em> wings</a>';
}
else
{
	$num_wings = get_museum_wing_count();
	$num_pages = ceil($num_wings / 20);

	$page = (int)$_GET['page'];
	if($page < 1 || $page > $num_pages)
		$page = 1;

	$wings = fetch_multiple('
		SELECT display,museumcount
		FROM monster_users
		WHERE museumcount>=100
		ORDER BY museumcount DESC
		LIMIT ' . (($page - 1) * 20) . ',20
	');

	$pages = paginate($num_pages, $page, '/museum/wings.php?page=%s');

	$options[] = '<a href="?friendsonly=1">Ask to see only your friends\' wings</a>';
}
  
include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Museum</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Museum &gt; Wing Directory</h4>
     <ul class="tabbed">
      <li><a href="/museum/">My Collection</a></li>
      <li><a href="/museum/uncollection.php">My Uncollection</a></li>
      <li><a href="/museum/donate.php">Make Donation</a></li>
      <li><a href="/museum/exchange.php">Exchanges</a></li>
      <li><a href="/museum/displayeditor.php">My Displays</a></li>
      <li class="activetab"><a href="/museum/wings.php">Wing Directory</a></li>
     </ul>
<?php
echo '<img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/museum.png" align="right" width="350" height="500" alt="(Museum Curator)" />';

include 'commons/dialog_open.php';

echo $dialog_text;

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

echo $pages .
     '<table><tr class="titlerow"><th>Resident</th><th>Wing Size</th></tr>';

$rowclass = begin_row_class();

foreach($wings as $wing)
{
  echo '<tr class="' . $rowclass . '"><td>' . resident_link($wing['display']) . '</td><td class="centered"><a href="/museum/view.php?resident=' . link_safe($wing['display']) . '">' . $wing['museumcount'] . ' items</a></td></tr>';
  $rowclass = alt_row_class($rowclass);
}

echo '</table>' .
     $pages;
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
