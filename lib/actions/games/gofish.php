<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/petstatlib.php';
  
$info = take_apart(";", $this_inventory["data"]);

if(count($info) != 8)
  $info = array('', '', '', '', '', '', 'newgame', '');

$state = $info[6];

$savedata = false;

if($state == 'newgame')
{
  if($_POST['action'] == 'choosepet')
  {
    $petid = (int)$_POST['petid'];
    $player_hand = array();
    $player_pairs = array();
    $pet_hand = array();
    $pet_pairs = array();

    $deck = array(
      'Ace', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'Jack', 'Queen', 'King',
      'Ace', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'Jack', 'Queen', 'King',
      'Ace', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'Jack', 'Queen', 'King',
      'Ace', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'Jack', 'Queen', 'King'
    );

    shuffle($deck);
    
    for($x = 0; $x < 7; ++$x)
    {
      $newcard = array_pop($deck);

      $j = array_search($newcard, $player_hand);
      if($j !== false)
      {
        unset($player_hand[$j]);
        $player_pairs[] = $newcard;
      }
      else
        $player_hand[] = $newcard;

      $newcard = array_pop($deck);

      $j = array_search($newcard, $pet_hand);
      if($j !== false)
      {
        unset($pet_hand[$j]);
        $pet_pairs[] = $newcard;
      }
      else
        $pet_hand[] = $newcard;
    }

    $pet_memory = array();

    $info[0] = implode(',', $deck);
    $info[1] = implode(',', $player_hand);
    $info[2] = implode(',', $player_pairs);
    $info[3] = $petid;
    $info[4] = implode(',', $pet_hand);
    $info[5] = implode(',', $pet_pairs);
    $info[6] = 'playergo';
    $info[7] = implode(',', $pet_memory);

    $savedata = true;
    
    $pet = get_pet_byid($petid);

    if($pet['user'] != $user['user'] || $pet['dead'] != 'no' || $pet['sleeping'] != 'no' || $pet['changed'] != 'no')
    {
      $info = array('', '', '', '', '', '', 'newgame', '');
      $display = 'ineligiblepet';
      $savedata = true;
    }
    else
      $display = 'taketurn';
  }
  else
    $display = 'choosepet';
}
else
{
  $petid = $info[3];
  $pet = get_pet_byid($petid);

  if($pet['user'] != $user['user'] || $pet['dead'] != 'no' || $pet['sleeping'] != 'no' || $pet['changed'] != 'no')
  {
    $info = array('', '', '', '', '', '', 'newgame', '');
    $display = 'ineligiblepet';
    $savedata = true;
  }
  else
  {
    if($state == 'playergo')
    {
      $deck = take_apart(',', $info[0]);
      $player_hand = take_apart(',', $info[1]);
      $player_pairs = take_apart(',', $info[2]);
      $pet_hand = take_apart(',', $info[4]);
      $pet_pairs = take_apart(',', $info[5]);
      $pet_memory = take_apart(',', $info[7]);

      if($_POST['action'] == 'ask')
      {
        $play = true;
      
        if(count($player_hand) == 0)
        {
          $newcard = array_pop($deck);
          $message = 'You draw a card.  It\'s a ' . $newcard . '.';
          $player_hand[] = $newcard;
          $display = 'taketurn';
          $savedata = true;
        }
        else
        {
          $askid = $_POST['cardnum'];

          if($askid < 0 || $askid >= count($player_hand))
          {
            $display = 'taketurn';
            $play = false;
          }
          else
          {
            $display = 'taketurn';

            // player's turn
            $card = $player_hand[$askid];
            
            $message = 'You ask for a ' . $card . '.  ';

            $i = array_search($card, $pet_hand);

            if($i === false)
            {
              // add a card to a pet's memory if it doesn't already have it
              if(array_search($card, $pet_memory) === false)
              {
                // if memory is not full, add; otherwise, add and remove the oldest one
                if(count($pet_memory) < floor($pet['int'] / 2))
                  $pet_memory[] = $card;
                else
                {
                  array_unshift($pet_memory, $card);
                  array_pop($pet_memory);
                }
              }

              $newcard = array_pop($deck);

              $message .= 'Go fish!  You draw a ' . $newcard . '.';

              $j = array_search($newcard, $player_hand);
              if($j !== false)
              {
                $message .= '  But wait: you had one of those in your hand already!  That\'s a pair!';
                unset($player_hand[$j]);
                $player_pairs[] = $newcard;

                $m = array_search($newcard, $pet_memory);
                if($m !== false)
                  unset($pet_memory[$m]);
              }
              else
                $player_hand[] = $newcard;
            }
            else
            {
              $message .= ucfirst(pronoun($pet['gender'])) . ' hands you the ' . $card . ', making a pair!';

              unset($pet_hand[$i]);
              unset($player_hand[$askid]);

              // remove card from pet's memory, if it's there
              $m = array_search($card, $pet_memory);
              if($m !== false)
                unset($pet_memory[$m]);

              $player_pairs[] = $card;
            }
          }
        }

        if($play)
        {
          if(count($player_pairs) + count($pet_pairs) == 26)
          {
            $info = array('', '', '', '', '', '', 'newgame', '');
            $display = 'gameover';
          }
          else
          {
            if(count($player_hand) == 0)
            {
              $message .= '  Then, having no cards left in your hand, you draw a card.';
              $player_hand[] = array_pop($deck);
            }

            // pet's turn
            $done = false;
            
            if(count($pet_hand) == 0)
            {
              $message2 = 'Having no cards in ' . p_pronoun($pet['gender']) . ' hand, ' . $pet['petname'] . ' draws a card.';
              $pet_hand[] = array_pop($deck);
              $done = true;
            }
            
            if($done === false)
            {
              // pet goes through its hand
              foreach($pet_hand as $j=>$card)
              {
//                echo '$j=>$card = ' . $j . '=>' . $card . ' ';
                // searches to see if it has the card in its memory
                $k = array_search($card, $pet_memory);
                if($k !== false)
                {
                  // if so, guesses that card!
                  $message2 = $pet['petname'] . ' asks for a ' . $card;
                  $i = array_search($card, $player_hand);
                  if($i === false)
                  {
                    $message2 .= ', but has to fish instead.';

                    $newcard = array_pop($deck);
                  
                    $l = array_search($newcard, $pet_hand);
                    if($l !== false)
                    {
                      $message2 .= ' ' . ucfirst(pronoun($pet['gender'])) . ' reveals thet ' . pronoun($pet['gender']) . ' drew a ' . $newcard . ', which ' . pronoun($pet['gender']) . ' already had in ' . p_pronoun($pet['gender']) . ' hand!  That\'s a pair!';
//                      echo '(1) pet should unset $pet_hand[' . $l . ']: ' . $pet_hand[$l] . ' ?= ' . $newcard . ' ';
                      unset($pet_hand[$l]);
                      $pet_pairs[] = $newcard;
                    }
                    else
                      $pet_hand[] = $newcard;
                  }
                  else
                  {
                    $message2 .= '.  You hand yours over, giving ' . t_pronoun($pet['gender']) . ' a pair.';
//                    echo '(2) pet should unset $pet_hand[' . $j . ']: ' . $pet_hand[$j] . ' ?= ' . $card . ' ';
                    unset($pet_hand[$j]);
                    unset($player_hand[$i]);
                    $pet_pairs[] = $card;
                  }

                  unset($pet_memory[$k]);

                  $done = true;
                  break;
                }
              }
            }
            
            if($done === false)
            {
              $j = array_rand($pet_hand);
              $card = $pet_hand[$j];

              $message2 = $pet['petname'] . ' asks for a ' . $card;
              $i = array_search($card, $player_hand);
              if($i === false)
              {
                $message2 .= ', but has to fish instead.';

                $newcard = array_pop($deck);

                $l = array_search($newcard, $pet_hand);
                if($l !== false)
                {
                  $message2 .= ' ' . ucfirst(pronoun($pet['gender'])) . ' reveals that ' . pronoun($pet['gender']) . ' drew ' . $newcard . ', which ' . pronoun($pet['gender']) . ' already had in ' . p_pronoun($pet['gender']) . ' hand!  That\'s a pair!';
                  unset($pet_hand[$l]);
                  $pet_pairs[] = $newcard;
                }
                else
                  $pet_hand[] = $newcard;
              }
              else
              {
                $message2 .= '.  You hand yours over, giving ' . t_pronoun($pet['gender']) . ' a pair.';
//                echo '(3) pet should unset $pet_hand[' . $j . ']: ' . $pet_hand[$j] . ' ?= ' . $card . ' ';
                unset($pet_hand[$j]);
                unset($player_hand[$i]);
                $pet_pairs[] = $card;
              }
            }

            if(count($player_pairs) + count($pet_pairs) == 26)
            {
              record_pet_stat($pet['idnum'], 'Played Go Fish', 1);
              record_pet_stat($pet['idnum'], 'Played Go Fish with ' . $user['display'], 1);
              $info = array('', '', '', '', '', '', 'newgame', '');
              $display = 'gameover';
            }
            else if(count($pet_hand) == 0)
            {
              $message2 .= '  Then, having no cards left in ' . p_pronoun($pet['gender']) . ' hand, ' . $pet['petname'] . ' draws a card.';
              $pet_hand[] = array_pop($deck);
            }
          }

          if($display !== 'gameover')
          {
            // update various values
            $info[0] = implode(',', $deck);
            $info[1] = implode(',', $player_hand);
            $info[2] = implode(',', $player_pairs);
            $info[4] = implode(',', $pet_hand);
            $info[5] = implode(',', $pet_pairs);
            $info[7] = implode(',', $pet_memory);
          }

          $savedata = true;
        }
      }
      else
        $display = 'taketurn';
    }
    else
      $display = 'confused';
  }
}

if($savedata)
{
  $data = implode(';', $info);
  $command = 'UPDATE monster_inventory SET data=' . quote_smart($data) . ' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating deck data');
}

//echo 'State, display: ' . $state . ', ' . $display . '<br />' . "\n";

if($display == 'choosepet')
{
  $pets = array();

  foreach($userpets as $pet)
  {
    if($pet['user'] == $user['user'] && $pet['dead'] == 'no' && $pet['sleeping'] == 'no' && $pet['changed'] == 'no')
      $pets[] = $pet;
  }
  
  if(count($pets) == 0)
    echo 'You don\'t have any pets to play with right now... <i>(Pets cannot play if they are dead, sleeping, or otherwise distracted.)</i>';
  else
  {
?>
Play Go Fish with one of your pets?  <i>(Pets cannot play if they are dead, sleeping, or otherwise distracted.)</i></p>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
<p><select name="petid">
<?php
foreach($pets as $pet)
  echo ' <option value="' . $pet['idnum'] . '">' . $pet['petname'] . '</option>' . "\n";
?>
</select>&nbsp;<input type="hidden" name="action" value="choosepet" /><input type="submit" value="This one!" /></p>
</form>
<p>
<?php
  }
}
else if($display == 'taketurn')
{
//  echo '<i>Deck: ' . implode(', ', $deck) . '</i></p>';

  if(strlen($message) > 0)
    echo '<p>' . $message . '</p>';
  if(strlen($message2) > 0)
    echo '<p>' . $message2 . '</p>';
?>
<h6><?= $user['display'] ?></h6>
<?php
  if(count($player_hand) == 0)
  {
?>
<p>You don't have any cards in your hand.</p>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post"><input type="hidden" name="action" value="ask" /><input type="submit" value="Draw a card" class="bigbutton" /></form>
<?php
  }
  else
  {
?>
<p>Click a card from your hand to ask <?= $pet['petname'] ?> if <?= pronoun($pet['gender']) ?> has one.</p>
<?php
    $i = 0;
    foreach($player_hand as $card)
    {
      echo '<form action="itemaction.php?idnum=' . $this_inventory['idnum'] . '" method="post" style="display: inline;"><input type="hidden" name="action" value="ask" /><input type="hidden" name="cardnum" value="' . $i . '" /><input type="submit" value="' . $card . '" /></form>&nbsp;';
      ++$i;
    }
  }
?>
</p>
<p>Pairs collected: <?= implode('s, ', $player_pairs) . (count($player_pairs) > 0 ? 's' : '') ?></p>
<h6><?= $pet['petname'] ?></h6>
<p><?= count($pet_hand) ?> cards in hand.</p>
<p>Pairs collected: <?= implode('s, ', $pet_pairs) . (count($pet_pairs) > 0 ? 's' : '') ?></p>
<h6>The Deck</h6>
<p>There are <?= count($deck) ?> cards remaining in the deck.</p>
<?php
/*
  echo '<p><i>Pet\'s hand: ' . implode(', ', $pet_hand) . '</i></p>' .
       '<p><i>Pet\'s memory: ' . implode(', ', $pet_memory) . ' (max size ' . floor($pet['int'] / 2) . ')</i></p>';
*/
}
else if($display == 'ineligiblepet')
{
  if($petid == 0)
    echo 'The pet chosen no longer <em>exists</em>.  Suffice to say, you can no longer play Go Fish with that pet.';
  else
    echo $pet['petname'] . ' is no longer available to play the game.  <i>(Dead, sleeping, or otherwise distracted pets may not play Go Fish.)</i>';
?>
<ul>
 <li><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>">Start a new game</a></li>
</ul>
<?php
}
else if($display == 'gameover')
{
  if(strlen($message) > 0)
    echo '<p>' . $message . '</p>';
  if(strlen($message2) > 0)
    echo '<p>' . $message2 . '</p>';
?>
<?= $user['display'] ?>'s pairs: <?= implode('s, ', $player_pairs) . (count($player_pairs) > 0 ? 's' : '') ?></p>
<p><?= $pet['petname'] ?>'s pairs: <?= implode('s, ', $pet_pairs) . (count($pet_pairs) > 0 ? 's' : '') ?></p>
<?php
  if(count($player_pairs) > count($pet_pairs))
    echo '<p>You\'re the winner with ' . count($player_pairs) . ' pairs!';
  else if(count($player_pairs) < count($pet_pairs))
    echo '<p>' . $pet['petname'] . ' has won with ' . count($pet_pairs) . ' pairs!';
  else
    echo '<p>A tie game!?';
?>
<ul>
 <li><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>">Start a new game</a></li>
</ul>
<?php
}
else
{
?>
The game state is "<?= $display ?>" - somehow you've broken Go Fish :)  Go report it to <a href="admincontact.php">an administrator</a>.
<?php
}

?>
