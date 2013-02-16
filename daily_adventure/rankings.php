<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/encryption.php';
require_once 'commons/formatting.php';

require_once 'libraries/zebra_stripes.php';

$page = (int)$_GET['page'];

$num_rankings = PlayerStats::GetCount('Completed a Daily Adventure Challenge');
$num_pages = ceil($num_rankings / 20);

if($page < 1 || $page > $num_pages)
  $page = 1;

$rankings = PlayerStats::GetRankingsOverAge('Completed a Daily Adventure Challenge', 1267941600, $page);

$npc = array(
  'graphic' => 'npcs/adventurer.png',
  'width' => 350,
  'height' => 410,
  'name' => 'Jerrad Shiflett',
  'title' => 'Jerrad the Adventurer',
  'dialog' => '<p>I\'ve been keeping track of when residents complete adventures, when they don\'t, etc, etc, and have made a few tables...</p><p>Well, here, look: this one shows us the percent of adventures a resident has undertaken and succeeded since I started counting on March 7th, 2010.  Neat, huh?  I kind of wish I\'d been keeping track of whether the adventure was gold, silver, or whatever, but... oh well.</p>',
);

$pages = paginate($num_pages, $page, '?page=%s');

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Plaza &gt; Search</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
	<h4><a href="/daily_adventure/">Daily Adventure</a> &gt; Most Adventurous Residents</h4>

	<ul class="tabbed">
	 <li><a href="/daily_adventure/">Go On an Adventure</a></li>
	 <li class="activetab"><a href="/daily_adventure/rankings.php">Most Adventurous Residents</a></li>
	 <li><a href="/daily_adventure/shop.php">Adventurer's Shop</a></li>
	</ul>
<?php
require WEB_ROOT . '/views/_template/npc.php';
?>
	<?= $pages ?>
	<table>
	 <thead>
		<tr>
		 <th><!-- place --></th>
		 <th>Resident</th>
		 <th>Adventurousness</th>
		</tr>
	 </thead>
	 <tbody>
<?php
$place = 1 + ($page - 1) * 20;
foreach($rankings as $ranking)
{
  echo '
    <tr class="' . zebra_stripe() . '">
     <td class="righted">' . $place . '.</td>
     <td>' . User::Link($ranking['display']) . '</td>
     <td class="centered">' . round($ranking['value'] * 100) . '%</td>
    </tr>
  ';

  $place++;
}
?>
	 </tbody>
	</table>
	<?= $pages ?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
