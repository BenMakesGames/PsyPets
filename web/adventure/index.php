<?php
require_once 'commons/init.php';

$require_petload = 'no';
$wiki = 'Adventure';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/adventurelib.php';

$adventure = get_adventure($user['idnum']);
if($adventure === false)
{
  create_adventure($user['idnum'], 1);
  $adventure = get_adventure($user['idnum']);
  if($adventure === false)
    die('error loading and/or creating adventure.  this is bad.');
}

$challenge_tokens = get_challenge_tokens($user['idnum']);
if($challenge_tokens === false)
{
  create_challenge_tokens($user['idnum']);
  $challenge_tokens = get_challenge_tokens($user['idnum']);
  if($challenge_tokens === false)
    die('error loading and/or creating adventure tokens.  this is bad.');
}

$progress = floor($adventure['progress'] * 100 / $adventure['difficulty']);

include 'commons/html.php';
?>
<head>
	<title><?= $SETTINGS['site_name'] ?> &gt; Adventure</title>
<?php include 'commons/head.php'; ?>
	<script type="text/javascript">
		$(function() {
			$('#rescrambler').click(function() {
				rescramble_word();
				
				return false;
			});
		});

		function array_shuffle(o)
		{
			for(var j, x, i = o.length; i; j = parseInt(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
			return o;
		}
		
		function rescramble_word()
		{
			var element = $('#scrambled-word');
			
			var letters = element.html().split(' ');
			
			letters.pop();
			
			letters = array_shuffle(letters);
			
			element.html(letters.join(' ') + ' ');
		}
	</script>
</head>
<body>
<?php include 'commons/header_2.php'; ?>
	<h4>Adventure</h4>
	<ul class="tabbed">
		<li class="activetab"><a href="/adventure/">My Adventure</a></li>
		<li><a href="/adventure/shop.php">Adventurer's Shop</a></li>
	</ul>
	<div style="float:right;border:1px dashed #999; padding:10px; width:200px;color:#333;">
		<p>To progress through an adventure, you'll need the help of your pets!  When you're home, instead of choosing to "Pet" your pets, "Adventure" with them!</p>
		<p class="nomargin">When you've completed the adventure, return here to advance to the next.  You may even get a prize!</p>
	</div>
	<p><?= $adventure['description'] ?></p>
<?php
if($adventure['progress'] >= $adventure['difficulty'])
{
  if($adventure['prize'] != '')
  {
		echo '<h5>You Prove Successful!  But Wait: There\'s More</h5>';
    echo render_adventure_prize($adventure['prize']);
		echo '
			<p>But there, between you and it, is one final obstacle: a Magic Scramble!</p>
			<table><tr>
				<td style="padding-left:20px;"><span style="padding: 10px; border: 1px solid #999; font-size: 16px; font-weight: bold;" id="scrambled-word">';

		$scramble = get_adventure_scramble($adventure);
		
		for($i = 0; $i < strlen($scramble); ++$i)
			echo substr($scramble, $i, 1) . ' ';
		
		echo '</span></td>
				<td><a href="#" id="rescrambler"><img src="//' . $SETTINGS['static_domain'] . '/gfx/badges/rollem.png" style="padding:10px;" /></a></td>
			</tr></table>
			<form action="/adventure/claim_prize.php" method="post" style="clear:both;">
			<p><input name="word" type="text" maxlength="' . strlen($scramble) . '" style="width:100px;" placeholder="Unscramble it!" /> <input type="submit" class="bigbutton" name="submit" value="Claim Prize!" /></p>
			</form>
		';
  }
  else if($now >= $adventure['next_adventure'])
  {
		echo '<h5>You Prove Successful!</h5>';

    echo '<ul>';

    if($adventure['level'] > 1)
      echo '<li><a href="/adventure/onward_easier.php">Onward!  To something easier!</a></li>';
    
    echo '<li><a href="/adventure/onward.php">Onward!  (This level of difficulty seems about right.)</a></li>';

    if($adventure['level'] < 10)
      echo '<li><a href="/adventure/onward_harder.php">Onward!  To something more-challenging!</a></li>';

    echo '</ul>';
  }
  else
    echo '
      <p>You really plowed through that last adventure.  Best take a little breather.</p>
      <p><i>(You can advance to the next adventure in ' . duration($adventure['next_adventure'] - $now, 1) . '.)</i></p>
    ';
}
else
  echo '<div><b style="vertical-align:top;">Progress:</b> <div style="display:inline-block;border-radius:4px;width:108px;height:14px;background-color:#ddd;" alt="' . $progress . '%" title="' . $progress . '%"><div style="border-radius:4px;-moz-border-radius:4px;width:' . (8 + $progress) . 'px; background-color:#69c;height:14px;"></div></div></div>';
?>     
<?php include 'commons/footer_2.php'; ?>
</body>
</html>
