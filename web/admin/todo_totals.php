<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

if($user['admin']['managewishlist'] != 'yes')
{
  header('Location: /arrangewishes.php');
  exit();
}

$tag = trim($_GET['tag']);

if(strlen($tag) > 0)
{
	$command = '
		SELECT psypets_ideachart.*
		FROM psypets_ideachart
		LEFT JOIN psypets_ideachart_tags ON psypets_ideachart.idnum=psypets_ideachart_tags.ideaid
		WHERE tag=' . quote_smart($tag) . '
	';
}
else
	$command = 'SELECT * FROM psypets_ideachart';

$wishes = $database->FetchMultipleBy($command, 'idnum', 'fetching wishes');

foreach($wishes as $id=>$wish)
{
  $command = 'SELECT SUM(votes) AS votes_total FROM psypets_ideavotes WHERE ideaid=' . $id;
  $data = $database->FetchSingle($command, 'fetching total votes');
  
  $votes[$id] = (int)$data['votes_total'];
  $wishes[$id]['votes_total'] = $votes[$id];

  $command = 'SELECT COUNT(residentid) AS votes FROM psypets_ideavotes WHERE ideaid=' . $id . ' AND votes=2';
  $data = $database->FetchSingle($command, 'fetching vote counts');
  
  $wishes[$id]['votes_2'] = $data['votes'];

  $command = 'SELECT COUNT(residentid) AS votes FROM psypets_ideavotes WHERE ideaid=' . $id . ' AND votes=1';
  $data = $database->FetchSingle($command, 'fetching vote counts');

  $wishes[$id]['votes_1'] = $data['votes'];

  $command = 'SELECT COUNT(residentid) AS votes FROM psypets_ideavotes WHERE ideaid=' . $id . ' AND votes=-1';
  $data = $database->FetchSingle($command, 'fetching vote counts');

  $wishes[$id]['votes_-1'] = $data['votes'];

  $command = 'SELECT COUNT(residentid) AS votes FROM psypets_ideavotes WHERE ideaid=' . $id . ' AND votes=-2';
  $data = $database->FetchSingle($command, 'fetching vote counts');

  $wishes[$id]['votes_-2'] = $data['votes'];

	$these_tags = $database->FetchMultiple(
		'SELECT * FROM psypets_ideachart_tags	WHERE ideaid=' . $id
	);

	foreach($these_tags as $tag)
		$tags[$id][] = '<a href="?tag=' . urlencode($tag['tag']) . '">' . $tag['tag'] . '</a>';
}

arsort($votes);

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Admin Tools &gt; To-do List &gt; Top Rated</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Admin Tools</a> &gt; To-do List &gt; Top Rated</h4>
     <ul>
      <li><a href="/admin/todoadd.php">Add entry</a></li>
     </ul>
<?php
if(count($wishes) > 0)
{
  echo '
    <table>
     <tr class="titlerow">
      <th class="righted"><img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/yeah.gif" /></th>
      <th class="righted"><img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/hee.gif" /></th>
      <th class="righted"><img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/neutral.png" /></th>
      <th class="righted"><img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/mmf.gif" /></th>
      <th class="righted"><img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/grr.png" /></th>
      <th class="righted">Total</th>
      <th>To-do</th>
      <th></th>
     </tr>
  ';

  $rowclass = begin_row_class();

  foreach($votes as $id=>$total_vote)
  {
    $wish = $wishes[$id];
  
    echo '
      <tr class="' . $rowclass . '">
       <td class="righted">' . $wish['votes_2'] . '</td>
       <td class="righted">' . $wish['votes_1'] . '</td>
       <td class="righted"></td>
       <td class="righted">' . $wish['votes_-1'] . '</td>
       <td class="righted">' . $wish['votes_-2'] . '</td>
       <td class="righted">' . $total_vote . '</td>
       <td>
			  ' . $wish['sdesc'] . ($wish['postdate'] > $now - 3 * 24 * 60 * 60 ? ' <i style="color:red;">new!</i>' : '') . '<br />
				<i class="size8">' . implode(', ', $tags[$id]) . '</i>
			 </td>
       <td><nobr>(<a href="/tododetails.php?id=' . $wish['idnum'] . '">read more...</a>)</nobr></td>
      </tr>
    ';

    $rowclass = alt_row_class($rowclass);
  }

  echo '</table>';
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
