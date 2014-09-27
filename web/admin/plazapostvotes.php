<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/utility.php';

if($admin['abusewatcher'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

$command = 'SELECT postid,COUNT(voterid) AS total FROM psypets_post_thumbs WHERE vote=-1 GROUP BY postid ORDER BY total DESC';
$negative_votes = $database->FetchMultiple(_by($command, 'postid', 'fetching total negative votes');

$command = 'SELECT postid,COUNT(voterid) AS total FROM psypets_post_thumbs WHERE vote=1 GROUP BY postid ORDER BY total DESC';
$positive_votes = $database->FetchMultiple(_by($command, 'postid', 'fetching total positive votes');

$command = 'SELECT postid,SUM(vote) AS total,COUNT(voterid) AS votes FROM psypets_post_thumbs GROUP BY postid ORDER BY votes DESC';
$total_votes = $database->FetchMultiple(_by($command, 'postid', 'fetching total votes');

$detailid = (int)$_GET['details'];

if($detailid > 0 && array_key_exists($detailid, $total_votes))
{
  $command = 'SELECT * FROM monster_posts WHERE idnum=' . $detailid . ' LIMIT 1';
  $post = $database->FetchSingle($command, 'fetching post');

  $command = 'SELECT * FROM psypets_post_thumbs WHERE postid=' . $detailid;
  $voterids = $database->FetchMultiple(($command, 'fetching voters');

  $got_details = true;
}
else
  $got_details = false;

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Plaza Post Votes</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Plaza Post Votes</h4>
<?php
if($got_details)
{
  echo '
    <h5>Written ' . duration($now - $post['creationdate'], 2) . ' ago</h5>
    <p>' . $post['body'] . '</p>
    <ul><li><a href="jumptopost.php?postid=' . $post['idnum'] . '">Jump to post</a></li></ul>
    <h5>Voters</h5><table>
  ';

  $rowclass = begin_row_class();

  foreach($voterids as $voterid)
  {
    $voter = get_user_byid($voterid['voterid'], 'idnum,user,display');

    if($voterid['vote'] == 1)
      $img = 'up';
    else
      $img = 'down';

    echo '<tr class="' . $rowclass . '"><td><img src="//' . $SETTINGS['static_domain'] . '/gfx/forum/thumb' . $img . '.png" alt="" /></td><td>' . $voter['display'] . ' (' . $voter['user'] . '; #' . $voter['idnum'] . ')</tr></tr>';

    $rowclass = alt_row_class($rowclass);
  }
  
  echo '</table>';
}
?>
<table>
 <tr class="titlerow">
  <th>Post #</th>
  <th>Votes</th>
  <th>Up</th>
  <th>Down</th>
  <th>Total</th>
  <th>Plaza Section</th>
  <th></th>
 </tr>
<?php
$rowclass = begin_row_class();

foreach($total_votes as $postid=>$vote)
{
  echo '
    <tr class="' . $rowclass . '">
     <td><a href="jumptopost.php?postid=' . $postid . '">' . $postid . '</a></td>
     <td class="centered">' . $vote['votes'] . '</td>
     <td class="centered">' . $positive_votes[$postid]['total'] . '</td>
     <td class="centered">' . $negative_votes[$postid]['total'] . '</td>
     <td class="centered">' . $vote['total'] . '</td>
     <td>' . ($detailid == $postid ? '<i class="dim">details</i>' : '<a href="adminplazapostvotes.php?details=' . $postid . '">details</a>') . '</td>
    </tr>
  ';

  $rowclass = alt_row_class($rowclass);
}
?>
</table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
