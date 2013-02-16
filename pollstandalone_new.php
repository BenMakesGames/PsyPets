<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/polllib.php';
require_once 'commons/ip.php';

$current_poll = get_global('currentpoll');

$poll = get_poll_byid($current_poll);

if($poll === false)
{
  header('Location: ./cityhall.php');
  exit();
}

$options = explode('|', $poll['options']);
$num_options = count($options);

$total = 0;

for($x = 0; $x < $num_options; $x++)
{
  $votes[$x] = get_poll_results($current_poll, $x);
  $total += $votes[$x];
}

$command = 'SELECT vote FROM psypets_poll_votes WHERE residentid=' . $user['idnum'] . ' AND pollid=' . $current_poll . ' LIMIT 1';
$my_vote = $database->FetchSingle($command, 'fetching your vote');

if($_POST['submit'] == 'Vote' && $my_vote === false)
{
	// paid accounts only!
	if($poll['paidonly'] == 'no' || $user['donated'] == 'yes')
	{
		$vote = (int)$_POST['vote'];
		
		if($vote >= 0 && $vote < $num_options)
		{
			cast_vote($current_poll, $user['idnum'], getip(), $vote);
			$my_vote['vote'] = $vote;
		}
	}
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Polls &gt; <?= $poll['title'] ?></title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="polllist.php">Polls</a> &gt; <?= $poll['title'] ?></h4>
<?php
if($poll['paidonly'] == 'yes')
  echo '<p>Only Residents who have purchased Favors may vote on this particular poll.</p>';

echo $poll['description'];

if($poll['paidonly'] == 'yes' && $user['donated'] == 'no')
  ; // paid accounts only!
else if($my_vote === false)
{
?>
     <form action="pollstandalone.php" method="post">
     <ul class="plainlist">
<?php
  foreach($options as $i=>$option)
  {
?>
     <li><input type="radio" name="vote" id="vote<?= $i ?>"value="<?= $i ?>" /> <label for="vote<?= $i ?>"><?= $option ?></label></li>
<?php
  }
?>
     </ul>
     <p><input type="submit" name="submit" value="Vote" /></p>
     </form>
<?php
}
else
{
  echo '<p>Your vote for this poll has been registered.  You voted "' . $options[$my_vote['vote']] . '".</p>' .
		   '<table>' .
			 '<tr class="titlerow"><th>Option</th><th>Votes</th></tr>';

	$rowclass = begin_row_class();
	foreach($options as $i=>$option)
	{
?>
<tr class="<?= $rowclass ?>">
 <td><?= $option ?></td>
 <td class="centered"><?= $votes[$i] ?></td>
</tr>
<?php
	  $rowclass = alt_row_class($rowclass);
	}
	
	echo '</table>';
}

if($admin['createpolls'] == 'yes' || $admin['viewpolls'] == 'yes')
  echo '<ul><li><a href="/polldetails.php?id=' . $current_poll . '">View this poll\'s results</a></li></ul>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
