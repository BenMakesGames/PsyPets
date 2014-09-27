<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/arklib.php';

if($user['show_ark'] != 'yes')
{
  header('Location: /404/');
  exit();
}

$dialog_text = '<p>Oh, you\'d like to give me one of your pets?  Hm!  Indeed!</p><p>Well, I have only two rules!  Wait!  Three.  No!  Actually: four!  Four rules!  Yes.  That\'s it: four.  Just four.</p><ol><li>I only accept pets of Level 5 or below!  They start to get attached to their owners after then, and that\'s just complicated for everyone!  It\'s all already complicated enough already, hm-hm?  When dealing with a problem this complex, we must strive to keep things as simple as possible!</li><li>The PsyPets must <strong>not</strong> have been fixed!  Obvious!  I need to breed three generations of pets, after all, at least!</li><li>The PsyPet must still be alive!  Also obvious, I hope...</li><li>Finally, I have no use for those... genetically-engineered... custom pets... or whatever.  Something about that processes mucks with their chromosomes!  Very sloppy job those HERG people do.  Mm.  Can\'t even predict what the babies might be!  Very sloppy!  You almost think they did it on purpose!</li></ol><p>Right!</p><p>So!</p><p>Bearing all that in mind, what do you have for me?</p>';

if($_POST['submit'] == 'Donate')
{
  $dialog_text = '';
  $pets_given = 0;

  foreach($_POST as $key=>$value)
  {
    if($key{0} == 'p')
    {
      $petid = (int)substr($key, 1);
      if($value == 'on' || $value == 'yes')
      {
        $pet = get_pet_byid($petid, 'user,toolid,graphic,gender,prolific,dead,changed,zombie,petname,protected');
        if($pet === false || $pet['user'] != $user['user'])
          $messages[] = '<span class="failure">A selected pet... does not exist?</span>';
        else if($pet['dead'] != 'no')
          $messages[] = '<span class="failure">' . $pet['petname'] . ' is dead!</span>';
        else if($pet['prolific'] == 'no')
          $messages[] = '<span class="failure">' . $pet['petname'] . ' has been fixed!</span>';
        else if($pet['protected'] == 'yes')
          $messages[] = '<span class="failure">' . $pet['petname'] . ' has a custom graphic, or is "protected" for some other reason.</span>';
        else if(in_array($pet['graphic'], $ARK_GRAPHICS_EXCLUDED))
          $messages[] = '<span class="failure">Ah, I can\'t actually accept ' . $pet['petname'] . '.  It has a strange appearance... one which is not natural, and won\'t help with my research.</span>';
        else
        {
          $q_graphic = quote_smart($pet['graphic']);
          $q_gender = quote_smart($pet['gender']);

          $command = 'SELECT timestamp FROM psypets_ark WHERE userid=' . $user['idnum'] . ' AND graphic=' . $q_graphic . ' AND gender=' . $q_gender . ' LIMIT 1';
          $result = $database->FetchSingle($command, 'fetching already-donated pets');

          if($result !== false)
            $messages[] = '<span class="failure">You\'ve already donated a pet just like ' . $pet['petname'] . '.</span>';
          else
          {
            if($pet['toolid'] > 0)
            {
              $command = "UPDATE monster_inventory SET location='home',user=" . quote_smart($user['user']) . ",changed='" . $now . "' WHERE idnum=" . $pet['toolid'] . ' LIMIT 1';
              $database->FetchNone($command, 'unequipping item');
            }

            $command = 'DELETE FROM psypets_pet_market WHERE petid=' . $petid . ' LIMIT 1';
            $database->FetchNone($command, 'deleting pet market listing, if one exists');

            $command = 'UPDATE monster_pets SET user=\'ark\' WHERE idnum=' . $petid . ' LIMIT 1';
            $database->FetchNone($command, 'giving pet to The Ark');
            
            add_pet_to_ark($user['idnum'], $petid, $q_graphic, $q_gender);
            $pets_given++;
          }
        }
      }
    }
  }
  
  if($pets_given > 0)
  {
    $extras = array(
      'Hey!  Excellent!',
      'Hey!  Perfect!',
      'Hey!  That seems like a lot to me.  Does it seem like a lot to you?  It seems like a lot to me.',
      'Seriously?!  Excellent!',
      'Seriously?!  Perfect!',
      'Seriously?!  But what do I do with ' . ($pets_given == 1 ? 'it' : 'them') . '?  Oh!  Right!  For the ark!',
      'That seems like a lot to me.  Does it seem like a lot to you?  It seems like a lot to me.  But what do I do with ' . ($pets_given == 1 ? 'it' : 'them') . '?  Oh!  Right!  For the ark!',
      'But what do I do with ' . ($pets_given == 1 ? 'it' : 'them') . '?  Oh!  Right!  For the ark!  Excellent!',
      'But what do I do with ' . ($pets_given == 1 ? 'it' : 'them') . '?  Oh!  Right!  For the ark!  Perfect!',
      'Excellent!  But what do I do with ' . ($pets_given == 1 ? 'it' : 'them') . '?  Oh!  Right!  For the ark!',
    );

    $dialog_text = '<p>' . $pets_given . ' pet' . ($pets_given != 1 ? 's' : '') . '!? ' . $extras[array_rand($extras)] . '</p>';

    update_ark_count($user['idnum']);
  }
  else
    $dialog_text = '<p>Ah?  Hm?  Which pets?  It doesn\'t seem like you really selected one...</p>';
}

if(count($messages) > 0)
  $dialog_text = '<p>Hm!  A bit of a problem here...</p><ol><li>' . implode('</li><li>', $messages) . '</li></ol>' . $dialog_text;

if($_GET['where'] == 'daycare')
{
  $pet_where = 'shelter';
  $where_desc = 'in daycare';
}
else
{
  $pet_where = 'home';
  $where_desc = 'at home';
}

$command = 'SELECT idnum,graphic,gender,zombie,prolific,dead,changed,petname FROM monster_pets WHERE user=' . quote_smart($user['user']) . ' AND protected=\'no\' AND dead=\'no\' AND (str+dex+sta+per+`int`+wit+bra+athletics+stealth+sur+gathering+fishing+mining+cra+painting+carpentry+jeweling+sculpting+eng+mechanics+chemistry+smi+tai+binding+pil)<=5 AND location=\'' . $pet_where . '\' ORDER BY orderid ASC';
$pets = $database->FetchMultiple($command, 'selecting eligible pets');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Ark</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Ark</h4>
     <ul class="tabbed">
      <li><a href="ark.php">My Collection</a></li>
      <li><a href="ark_uncollection.php">My Uncollection</a></li>
      <li class="activetab"><a href="ark_donate.php">Make Donation</a></li>
      <li><a href="ark_collections.php">Collections</a></li>
     </ul>
<?php
//echo '<img src="gfx/npcs/flowergirl.jpg" align="right" width="350" height="706" alt="(Vanessa the Florist)" />';

include 'commons/dialog_open.php';

echo $dialog_text;

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';
?>
     <ul class="tabbed">
      <li<?= $pet_where == 'home' ? ' class="activetab"' : '' ?>><a href="ark_donate.php?where=home">Pets at Home</a></li>
      <li<?= $pet_where == 'daycare' ? ' class="activetab"' : '' ?>><a href="ark_donate.php?where=daycare">Pets in Daycare</a></li>
     </ul>
<?php
if(count($pets) > 0)
{
  $first = false;
  $row_class = begin_row_class();

  foreach($pets as $pet)
  {
    // if(...) continue;
    $note = '';
    $disabled = '';
    
    if(!$first)
    {
      $first = true;
      echo '<form action="ark_donate.php" method="post">' .
           '<table><tr class="titlerow"><th></th><th></th><th></th><th>Pet</th><th></th></tr>';
    }

    if($pet['prolific'] == 'no')
    {
      $note .= 'needs to be un-fixed<br />';
      $disabled = ' disabled="disabled"';
    }
    
    if($pet['incarnation'] > 1)
      $note .= 'has reincarnated<br />';

    if($pet['pregnant_asof'] > 0)
      $note .= 'is pregnant<br />';

    echo '<tr class="' . $row_class . '">' .
         '<td><input type="checkbox" name="p' . $pet['idnum'] . '"' . $disabled . ' /></td>' .
         '<td>' . pet_graphic($pet) . '</td><td>' . gender_graphic($pet['gender'], $pet['prolific']) . '</td><td>' . $pet['petname'] . '</td>' .
         '<td>' . $note . '</td>' .
         '</tr>';

    $row_class = alt_row_class($row_class);
  }

  if($first)
  {
    echo '</table>' .
         '<p><input type="submit" name="submit" value="Donate" onclick="return confirm(\'Just to be clear, there\\\'s no way to get the pets back once you\\\'ve donated them...\');" /></p>' .
         '</form>';
  }
}

if(!$first)
  echo '<p>You have no pets ' . $where_desc . ' which are eligible for donation to The Ark.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
