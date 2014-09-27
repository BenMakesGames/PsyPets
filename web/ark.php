<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/arklib.php';
require_once 'commons/questlib.php';

if($user['show_ark'] != 'yes')
{
  header('Location: /404/');
  exit();
}

$options = array();

$item_count = get_user_ark_count($user['idnum']);
$badges = get_badges_byuserid($user['idnum']);

if($item_count > 0)
{
  $page = (int)$_GET['page'];
  $max_pages = ceil($item_count / 20);

  if($page < 1 || $page > $max_pages)
    $page = 1;

  $sort = $_GET['sort'];

  $graphic_sort = '<a href="ark.php?sort=graphic">&#9661;</a>';
  $gender_sort = '<a href="ark.php?sort=gender">&#9661;</a>';
  $time_sort = '<a href="ark.php?sort=time">&#9661;</a>';

  if($sort == 'time')
  {
    $graphic_list = get_user_ark_page_by_time($user['idnum'], $page);
    $time_sort = '&#9660;';
  }
  else if($sort == 'gender')
  {
    $graphic_list = get_user_ark_page_by_gender($user['idnum'], $page);
    $gender_sort = '&#9660;';
  }
  else // by graphic
  {
    $graphic_list = get_user_ark_page_by_graphic($user['idnum'], $page);
    $graphic_sort = '&#9660;';
    $sort = 'graphic';
  }

  $pages = paginate($max_pages, $page, 'ark.php?sort=' . $sort . '&page=%s');
}

$ark_quest = get_quest_value($user['idnum'], 'the ark');

if($ark_quest === false)
{
  add_quest_value($user['idnum'], 'the ark', 0);
  $ark_quest = get_quest_value($user['idnum'], 'the ark');
}

if($_GET['dialog'] == 'explanation' && $ark_quest['value'] == 0)
{
  $dialog_text = '<p>What?  Why?  A good question!  An excellent question!  Allow me to explain!</p>' .
                 '<p>It\'s a puzzle!</p>' .
                 '<p>Yes!  A puzzle!  And their chromosomes, they contains the pieces!  What\'s the full picture?  Hard to say... a literal picture?  A graphic?  Maybe!  Some kind of note, anyway... maybe a video!  That would be exciting!  Maybe even a 3D video!  Who knows!  So many possibilities!</p>' .
                 '<p>Ah, but it\'s tricky business!  Whoever laid this puzzle was very clever; very sneaky.  The PsyPets\' chromosomes, they\'re not quite like a human\'s!  Not like most animals\'!  No, no, not at all.  So many chromosomes.  It isn\'t just a matter of XX, or XY, or YY... their gender isn\'t even defined that way!  Well, monotremes - such as the platypus - are a bit odd, but not like this... no, this is different...</p>';
  $options[] = '<a href="ark.php?dialog=confusion">(No, this is <em>confusing</em>...)</a>'; 
}
else if($_GET['dialog'] == 'confusion' && $ark_quest['value'] == 0) 
{
  $dialog_text = '<p>Sorry, of course!  This is all <em>much</em> too confusing, isn\'t it?  It\'s all very confusing, even for me!  And I\'ve been studying this for quite some time... mm...</p>' .
                 '<p>But all the more reason to study it further!  Ha, ha!  Yes!</p>' .
                 '<p>But right!  As I was saying: the chromosomes!  Ah, and the point!</p>' .
                 '<p>I need samples!  For breeding!  To get the full spectrum of chromosome combinations, it may take a few generations... shouldn\'t take more than three, if I\'m careful... if I keep good and accurate paperwork...</p>' .
                 '<p>Well, I\'ll be using the computer, of course!  Not actual paper!  You get the idea...</p>' .
                 '<p>Ah!  But!  One PsyPet of each gender!  One male; one female.</p>' .
                 '<p>And that\'s why I call it... The Ark.</p>'; 
  $options[] = '<a href="ark.php?dialog=okay">Agree to help him.</a>';
}
else if($_GET['dialog'] == 'okay' && $ark_quest['value'] == 0)
{
  $dialog_text = '<p>Ha!  Wonderful!  I can\'t wait!  I\'ll keep you appraised of my progress, of course!</p>'; 
  update_quest_value($ark_quest['idnum'], 1);
}
else if($badges['dna'] == 'no' && $item_count >= 50)
{
  $dialog_text = '<p>Oh, ' . $user['display'] . '!  Good to see you!  I have something for you: for your continued efforts!</p><p>And support!</p><p>50 PsyPets!  Quite a lot!</p>' .
                 '<p><i>(You received the DNA Badge.)</i></p>' .
                 '<p>Ah?  Was I supposed to give this to you sooner?  Sorry, must have slipped the mind!  Lots to do, you know!  Lots of things slip by!  That\'s what the computer is for, of course... to make sure nothing important is lost...</p>' .
                 '<p>But badges?  Not part of the computer\'s job, of course!  To burden it with even such a trivial task... nonsense!  Wouldn\'t stand for it!  Every cycle must be devoted to piecing the sequences together!  There are so many combinations!</p>' .
                 '<p>Sometimes one arrangement seems to work, but then another sequence is added, and it can\'t fit in... but you can\'t throw out an arrangement even then!  Would be quite premature!  Maybe yet another sequence <em>will</em> fit, and then on top of that, the first sequence will fit after all... everything must be tried again and again, with every new sequence...</p>' .
                 '<p>Anyway, so it can\'t possibly spend time worrying about badges!</p><p>Badges!</p><p>I can handle that myself!  Maybe not always on time, but you have to admit, more important things are going on!  A badge can wait a little!  Yes!  Quite!</p>';  
  set_badge($user['idnum'], 'dna');
}
else if($ark_quest['value'] == 1)
{
  $dialog_text = '<p>' . $user['display'] . '!  There are more samples, yes?  More to collect, yes?  Many more, maybe!  Hm.  Seems likely!</p>' .
                 '<p>But with each PsyPet, the puzzle is one step toward completion!  Ha, ha, yes!  A little more clear!  I\'ll figure it out, ' . $user['display'] . '!  Without a doubt!</p>';
}
else
{
  $dialog_text = '<p>Oh!  ' . $user['display'] . '!  Welcome to... The Ark!</p>' .
                 '<p>Impressive, right?  It cost a fortune to build - heh - but!  It\'s worth it!  Definitely worth it!</p>' .
                 '<p>But I should introduce myself!  I\'m Professor Maple, and this is my sidekick Mahog-- um... well, she was here a moment ago.  Huh... well, she tends to run off like that quite often.  I\'ll introduce you another time.</p>' .
                 '<p>Anyway, what was I saying?</p>' .
                 '<p>Oh yes!  What this is all for!</p>' .
                 '<p>Well, collecting, of course!  Samples!  Samples of all of the Hollow Earth creatures - all of the PsyPets.</p><p>Well, their chromosomes, to be precise.</p>';

  $options[] = '<a href="ark.php?dialog=explanation">Ask why he\'s doing this.</a>';
}

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; The Ark</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Ark</h4>
     <ul class="tabbed">
      <li class="activetab"><a href="ark.php">My Collection</a></li>
      <li><a href="ark_uncollection.php">My Uncollection</a></li>
      <li><a href="ark_donate.php">Make Donation</a></li>
      <li><a href="ark_collections.php">Collections</a></li>
     </ul>
<?php
//echo '<img src="gfx/npcs/flowergirl.jpg" align="right" width="350" height="706" alt="(Vanessa the Florist)" />';

include 'commons/dialog_open.php';

echo $dialog_text;

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

if($item_count == 0)
  echo '<p>You haven\'t collected anything for The Ark.</p>';
else
{
  echo $pages .
       '<table>' .
       '<tr class="titlerow"><th>Pet ' . $graphic_sort . '</th><th>Gender ' . $gender_sort . '</th><th class="centered">Donated ' . $time_sort . '</th></tr>';

  $rowclass = begin_row_class();

  foreach($graphic_list as $graphic)
  {
    echo '<tr class="' . $rowclass . '"><td class="centered"><img src="gfx/pets/' . $graphic['graphic'] . '" /></td><td class="centered">' . gender_graphic($graphic['gender'], 'yes') . '</td><td class="centered">' . Duration($now - $graphic['timestamp'], 2) . ' ago</td></tr>';

    $rowclass = alt_row_class($rowclass);
  }

  echo '</table>' .
       $pages;
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
