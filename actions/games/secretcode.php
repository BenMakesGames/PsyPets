<?php
if($okay_to_be_here !== true)
  exit();

$info = explode(";", $this_inventory["data"]);
$guesses = $info[0];
$password = $info[1];

$history = take_apart(',', $guesses);

$took_action = false;

if($_GET['action'] == 'reset' || $password == '')
{
  if($password == '')
    $length = 4;
  else
    $length = (int)$_GET['length'];

  if($length < 3)
    $length = 3;
  else if($length > 6)
    $length = 6;

  $guesses = '';
  $history = array();
  $password = '';

  $digits = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);

  for($i = 0; $i < $length; ++$i)
  {
    $d = array_rand($digits);
    $password .= $digits[$d];
    unset($digits[$d]);
  }
   
  $command = "UPDATE monster_inventory SET data=" . quote_smart(";$password") . " WHERE idnum=" . $_GET["idnum"] . " LIMIT 1";
  $database->FetchNone($command, 'starting new game');

  $won = false;
  
  echo '<p>Guess the ' . $length . '-digit password.  Each digit is unique.</p>';
}
else
  $won = ($password == $history[0]);

if($_POST['action'] == 'Guess' && !$won)
{
  $guess = trim($_POST['guess']);
  if(strlen($guess) < strlen($password))
    echo '<p class="failure">Too short!  The password is ' . strlen($password) . ' digits in length.</p>';
  else if(strlen($guess) > strlen($password))
    echo '<p class="failure">Too long!  The password is ' . strlen($password) . ' digits in length.</p>';
  else if(in_array($guess, $history))
    echo '<p class="failure">You already guessed that.</p>';
  else
  {
    $OK = true;
  
    $digits = array();

    for($i = 0; $i < strlen($guess); ++$i)
    {
      $d = $guess{$i};
      
      if($digits[$d] === true)
      {
        echo '<p class="failure">Each digit in the password is unique.</p>';
        $OK = false;
        break;
      }
      else
        $digits[$d] = true;
    }

    if($OK)
    {
      array_unshift($history, $guess);
      
      $won = ($guess == $password);

      $command = 'UPDATE monster_inventory SET data=' . quote_smart(implode(',', $history) . ';' . $password) . ' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'updating game');
    }
  }
}

if($won)
  echo '<p class="success"><b>You won!</b></p>';
else
  echo '
    <form action="itemaction.php?idnum=' . $this_inventory['idnum'] . '" method="post">
    <p><input type="text" name="guess" maxlength="' . strlen($password) . '" size="' . strlen($password) . '" /> <input type="submit" name="action" value="Guess" /></p>
    </form>
  ';

if($won)
  echo '
    <ul>
     <li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&action=reset&length=3">New game, 3-digit password</a></li>
     <li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&action=reset&length=4">New game, 4-digit password</a></li>
     <li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&action=reset&length=5">New game, 5-digit password</a></li>
     <li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&action=reset&length=6">New game, 6-digit password</a></li>
    </ul>
  ';
?>
<h5>History</h5>
<?php
if(count($history) > 0)
{
  echo '
    <table>
     <thead>
      <tr class="titlerow">
       <th>Guess</th><th>Result</th>
      </tr>
     </thead>
     <tbody>
  ';

  $rowclass = begin_row_class();

  foreach($history as $guess)
  {
    echo '<tr class="' . $rowclass . '"><td>' . $guess . '</td><td>';
  
    $all_wrong = 0;
    $right_place = 0;
    $wrong_place = 0;
  
    for($i = 0; $i < strlen($guess); ++$i)
    {
      $d = $guess{$i};

      $p = strpos($password, $d);

      if($p !== false)
      {
        if($p == $i)
          $right_place++;
        else
          $wrong_place++;
      }
      else
        $all_wrong++;
    }
    
    $result = array();
    
    if($right_place > 0)
      $result[] = '<span class="success">' . $right_place . ' digit' . ($right_place != 1 ? 's are' : ' is') . ' in the right place</span>';
    if($wrong_place > 0)
      $result[] = '<span class="obstacle">' . $wrong_place . ' digit' . ($wrong_place != 1 ? 's are' : ' is') . ' in the wrong place</span>';
    if($all_wrong > 0)
      $result[] = '<span class="failure">' . $all_wrong . ' digit' . ($all_wrong != 1 ? 's are' : ' is') . ' not in the password at all</span>';

    echo implode('<br />', $result), '</td></tr>';

    $rowclass = alt_row_class($rowclass);
  }

  echo '
     </tbody>
    </table>
  ';
}
else
  echo '<p>You have not made any guesses yet.</p>';

echo '
  <hr />
  <ul>
   <li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&action=reset&length=3">New game, 3-digit password</a></li>
   <li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&action=reset&length=4">New game, 4-digit password</a></li>
   <li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&action=reset&length=5">New game, 5-digit password</a></li>
   <li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&action=reset&length=6">New game, 6-digit password</a></li>
  </ul>
';
?>