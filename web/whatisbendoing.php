<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/benlib.php';

$command = 'SELECT what FROM psypets_whatisbendoing WHERE userid=' . $user['idnum'] . ' LIMIT 1';
$vote = $database->FetchSingle($command);

if($_POST['action'] == 'vote')
{
  if(array_key_exists($_POST['status'], $statuses))
  {
    if($vote === false)
      $command = 'INSERT INTO psypets_whatisbendoing (userid, lastchange, what) VALUES ' .
        '(' . $user['idnum'] . ', ' . $now . ', ' . quote_smart($_POST['status']) . ')';
    else
      $command = 'UPDATE psypets_whatisbendoing SET lastchange=' . $now .
        ',what=' . quote_smart($_POST['status']) . ' WHERE userid=' . $user['idnum'] . ' LIMIT 1';

    $database->FetchNone($command, 'updating vote');
  
    $vote['what'] = $_POST['status'];
  }
}

$get_max_time = microtime(true);

$max = $database->FetchSingle('SELECT COUNT(*) AS c,what FROM psypets_whatisbendoing GROUP BY(what) ORDER BY c DESC LIMIT 1');

$get_max_time = microtime(true) - $get_max_time;

$get_votes_time = microtime(true);

$vote_data = $database->FetchMultipleBy('SELECT COUNT(*) AS c,what FROM psypets_whatisbendoing GROUP BY(what)');

$get_votes_time = microtime(true) - $get_votes_time;

$footer_note = '<br />Took ' . round($get_max_time, 4) . 's fetching the highest vote, and ' . round($get_votes_time, 4) . 's fetching the vote totals.';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; City Hall &gt; What is That Guy Ben Doing?</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="cityhall.php">City Hall</a> &gt; What is That Guy Ben Doing?</h4>
     <p>According to PsyPets players, That Guy Ben is...</p>
     <p style="padding: 1em 0 1em 4em;"><span style="border: 1px dashed black; background-color: #f8f8f8; padding: 1em; font-weight: bold;"><?= $statuses[$max['what']] ?> *</span></p>
     <form method="post">
     <table>
      <tr class="titlerow">
       <th>My Vote</th><th>Status</th><th>Total Votes</th>
      </tr>
<?php
$rowclass = begin_row_class();

foreach($statuses as $status=>$descript)
{
  if($vote['what'] == $status)
    $checked = ' checked';
  else
    $checked = '';
?>
      <tr class="<?= $rowclass ?>">
       <td class="centered"><input type="radio" name="status" value="<?= $status ?>"<?= $checked ?> /></td>
       <td><?= $descript ?></td>
       <td class="centered"><?= (int)$vote_data[$status]['c'] ?></td>
      </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
     </table>
     <p><input type="hidden" name="action" value="vote" /><input type="submit" value="Vote!" /></p>
     <p><i>* This is merely the opinion/guess of PsyPets Residents, and may not represent the truth.  "Sleeping", "Eating", and "At his day job" are not options here, because they are assumed to happen regularly.</i></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
