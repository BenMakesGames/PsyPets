<?php
$whereat = 'petshelter';
$wiki = 'Pet_Shelter';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/globals.php';
require_once 'commons/petlib.php';
require_once 'commons/economylib.php';
require_once 'commons/questlib.php';

$pet_cost = value_with_inflation(275);

$badges = get_badges_byuserid($user['idnum']);

$dialog = false;

if($user['adoptedtoday'] == 'no')
{
  if($_GET['dialog'] == 'randompet')
  {
    $dialog = '
      <p>If you like, I can arrange to get you a pet straight from Hollow Earth.  Eve Heidel and the others will start up The Portal, and see what they can get.</p>
      <p>It\'s an expensive device to operate, so they just take the first pet they see...</p>
      <p>The same fee of ' . $pet_cost . '<span class="money">m</span> applies, and due to the cost to operate The Portal, the same "one pet adoption per day" limit applies as well.</p>
    ';
    
    if($user['money'] >= $pet_cost)
    {
      $dialog .= '<p>Want to go for it?</p>';
      $options[] = '<a href="?dialog=confirmrandom">Pay ' . $pet_cost . '<span class="money">m</span> and cross your fingers</a>';
    }
    
  }
  else if($_GET['dialog'] == 'confirmrandom' && $user['money'] >= $pet_cost)
  {
    // create a random, fixed pet
    $pet_id = create_random_pet($user['user']);
    $database->FetchNone('UPDATE monster_pets SET prolific=\'no\' WHERE idnum=' . $pet_id . ' LIMIT 1');

    $level = mt_rand(1, 5);
  
    $initial_stats = array();
    $updates = array();

    for($i = 0; $i < $level; ++$i)
      $initial_stats[$PET_SKILLS[array_rand($PET_SKILLS)]]++;

    foreach($initial_stats as $stat=>$score)
      $updates[] = '`' . $stat . '`=' . $score;

    $database->FetchNone('UPDATE monster_pets SET ' . implode(',', $updates) . ' WHERE idnum=' . $pet_id . ' LIMIT 1');

    // this matches the buypet.php code
    take_money($user, $pet_cost, 'Bought a pet from the Pet Shelter');

    $command = 'UPDATE monster_users SET adoptedtoday=\'yes\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'marking today\'s adoption');

    require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Bought a Pet at the Pet Shelter', 1);
   
    $badges = get_badges_byuserid($user['idnum']);
    if($badges['adopter'] == 'no')
    {
      set_badge($user['idnum'], 'adopter');

      psymail_user(
        $user['user'],
        'klittrell',
        'Thanks for giving ' . $_POST['petname'] . ' a new home!',
        'I just wanted to thank you for adopting one of the pets!  It may take ' . $_POST['petname'] . ' a little while to feel comfortable in its new home, but have patience.<br /><br />I\'m sure you\'ll raise a wonderful pet!<br /><br />{i}(You received the Pet Adopter badge!  Check it out on your profile!){/}'
      );
    }

    header('Location: /myhouse.php');
    exit();
  }
  else
    $options[] = '<a href="?dialog=randompet">Ask if any other pets are available for adoption</a>';
}

if($dialog === false && $badges['goatherder'] == 'no')
{
  $command = 'SELECT COUNT(idnum) AS c FROM monster_pets WHERE user=' . quote_smart($user['user']) . ' AND graphic=\'goat.png\' AND dead=\'no\'';
  $goats = $database->FetchSingle($command, 'Pet Shelter');
  
  if($goats['c'] >= 10)
  {
    $got_goat_badge = true;

    $dialog = '
      <p>Wow, ' . $user['display'] . '!  ' . $goats['c'] . ' goats!  I only have ' . ($goats['c'] - 1) . ', myself...</p>
      <p>Here: you deserve this Badge more than I do.</p>
      <p><i>(You received the Goat Herder Badge!)</i></p>
    ';

    set_badge($user['idnum'], 'goatherder');
  }
}

if($dialog === false && ($badges['level20'] == 'no' || $badges['level50'] == 'no' || $badges['level100'] == 'no'))
{
  $command = 'SELECT MAX(`' . implode('`+`', $PET_SKILLS) . '`) AS m FROM monster_pets WHERE user=' . quote_smart($user['user']) . ' AND original=\'yes\' AND dead=\'no\'';
  $data = $database->FetchSingle($command, 'Pet Shelter');
  
  $max_level = $data['m'];

  if($max_level >= 20 && $badges['level20'] == 'no')
  {
    set_badge($user['idnum'], 'level20');
    $badge_message = true;
    $the_ark = true;
    $badge_messages[] = '<p><i>(You received the Level 20 Pet Badge.)</i></p>';

    $database->FetchNone('UPDATE monster_users SET show_ark=\'yes\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1', 'revealing ark');
    $user['show_ark'] = true;
  }

  if($max_level >= 50 && $badges['level50'] == 'no')
  {
    set_badge($user['idnum'], 'level50');
    $badge_message = true;
    $badge_messages[] = '<p><i>(You received the Level 50 Pet Badge.)</i></p>';
  }

  if($max_level >= 100 && $badges['level100'] == 'no')
  {
    set_badge($user['idnum'], 'level100');
    $badge_message = true;
    $badge_messages[] = '<p><i>(You received the Level 100 Pet Badge.)</i></p>';
  }
  
  if($badge_message)
  {
    $dialog = '<p>Congratulations on raising your pets.  You\'ve done a fine job!  Please, take this badge...</p>';

    foreach($badge_messages as $m)
      $dialog .= $m;

    if($the_ark === true)
      $dialog .= '
        <p>Also, have you heard of something called "The Ark"?  Some weird guy came in earlier today, rambling about some kind of research project called "The Ark"...</p>
        <p>I didn\'t really get it, but I thought it might be something you\'d be interested in?</p>
        <p>I dunno - maybe not.  Couldn\'t hurt to check it out, though!</p>
        <p><i>(The Ark has been revealed to you!  Find it in the Recreation menu.)</i></p>
      ';
  }
}

if($dialog === false && $badges['trained_20'] == 'no')
{
  $training_badges = 0;

  foreach($PET_SKILLS as $skill)
  {
    if($badges[$skill . '_trainer'] == 'yes')
      $training_badges++;
  }
  
  if($training_badges >= 20)
  {
    set_badge($user['idnum'], 'trained_20');
    $dialog = '<p>I see you\'ve become an excellent trainer, ' . $user['display'] . '!</p><p>Wait here a minute, I have a little something for you...</p><p><i>(She dashes off to her office, returning mere moments later.)</i></p><p>Ta-da!  The Master Trainer badge!  Wear it proudly!</p><p><i>(You received the Master Trainer badge!)</i></p>';
  }
}

if($dialog === false && $_GET['dialog'] == 'breeding')
{
  $dialog = '<p>Breeding can be a lot of work, but it\'s worth it!  A female pet will become pregnant on its own, provided you haven\'t spayed it of course.  Since the pets are free to wander outside, you don\'t even need to have a fertile male in the house, although having one (or more) increases the chance of pet pregnancy.</p>' .
       '<p>Once a pet is pregnant, it will take weeks to give birth.  During the later stages of pregnancy, the pet will require more food, and become less active, so it\'s important to make sure to check up on it a lot and make sure it\'s doing alright!</p>' .
       '<p>When the pet finally gives birth, you will be rewarded with a small litter of pets.  The pets may resemble their mother, of course, but they can sometimes be surprisingly different!  The pets are strange creatures...</p>' .
       '<p>There\'s something else you should know:  do you remember when you first signed up, and you got to choose what kind of pet you want?  Well there are some pets that are very uncommon, and so are not offered to new Residents, however these pets <em>can</em> be acquired through breeding!</p>';

  if($badges['goatherder'] == 'no')
  {
    $dialog .= '<p>My favorite such pet is the goat!  They\'re really rare around here, but I have lots of them!  I have to admit, I\'m kind of proud of my collection.';
    if($goats['c'] == 0)
      $dialog .= '  You should get some yourself... I bet you can\'t get as many as I have!';
    else if($goats['c'] == 1)
      $dialog .= '  I see you have one, which is a start, but you\'ll have to do much better than that :P';
    else if($goats['c'] > 4)
      $dialog .= '  Hey, you have ' . $goats['c'] . ' goats?  That\'s actually quite a lot, but I still have mo-ore :P';
    else if($goats['c'] > 8)
      $dialog .= '  Whoa, when\'d you get ' . $goats['c'] . ' goats?!  Oh man, you might really overtake me at this rate!';
    else
      $dialog .= '  I see you have ' . $goats['c'] . ' goats, now... you won\'t beat me with so few, ' . $user['display'] . ' ;)';
    $dialog .= '</p>';
  }
}
else
  $options[] = '<a href="?dialog=2">Ask about breeding</a>';

$introductions_quest = get_quest_value($user['idnum'], 'NPC introductions');

if($dialog === false && $introductions_quest['value'] == 1)
{
  $dialog = '
    <p>Oh, hello!  You\'re ' . $user['display'] . ', right?  One of the new residents?</p>
    <p>Hi, I\'m Kim!  I run this Pet Shelter, both to find pets new homes, and take care of those who don\'t yet have one.</p>
    <p>If you ever need help taking care of your pet, don\'t hesitate to ask.</p>
    <p>I can also get you a new pet - we only ask you to cover paperwork and immunization fees - but since you\'re just starting, I\'m sure you\'ll have your hands full with the pet you\'ve already got!</p>
    <p>Anyway, I should get back to work.  But why not stop by The Smithery while you\'re out?  I\'m sure Nina can give you some neat tips.</p>
    <p><i>(The Smithery has been revealed to you!  Find it under The Services menu.)</i></p>
  ';

  $database->FetchNone('UPDATE monster_users SET show_smithery=\'yes\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1');
  $user['show_smithery'] = 'yes';
  
  update_quest_value($introductions_quest['idnum'], 2);
}

if($dialog === false && $_GET['dialog'] == 'petcare')
{
  $dialog = '
    <p>Oh, of course!</p>
    <p>I think one important thing to realize is that pets can do a lot of things on their own.  Every hour, they might go out to gather food, cut down wood, or defeat some monster... they might also stay indoors and make a vase, or sew.</p>
    <p>So besides giving your pet attention and feeding it, you should encourage your pet to do certain "hourly" activities.  For example, if you have some Clay around the house, try sculpting with your pet instead of petting it... doing so will encourage it to develop its sculpting skills!  There are a lot of items that will encourage different behaviors in your pets, some even by just being around the house!</p>
    <p>Anyway, that all assumes your pet is feeling happy.  If your pet isn\'t feeling well, though, your priority should be making it better!</p>
    <ol>
     <li>An exhausted pet will probably pass out on its own, but it\'ll feel better if you put it to bed yourself.</li>
     <li>A starving pet should be fed, obviously!</li>
     <li>A cowering pet is scared - pet it, and make sure you have pillows, plushies, and other comfort items around the house.</li>
     <li>A pet that feels unloved should be played with, and, again, comfort items left around the house will help make pets feel better.</li>
     <li>Depression in a pet usually follows an extended period of hunger, fear, or loneliness.  Once these other problems are addressed, the pet should resume its usual activities, and start feeling better about itself.  Keeping fancy, decorative items around the house can also help.</li>
    </ol>
    <p>If you want to learn more, <a href="/help/petcare.php">check out the Pet Care section of the Help Desk</a>.</p>
    <p>Good luck!</p>
  ';
}
else
  $options[] = '<a href="?dialog=petcare">Ask about taking care of pets</a>';

// get a list of pets in the shelter

$extra_wheres = array();

if($_GET['action'] == 'search')
{
  $searchurl = '&action=search';

  $minlevel = (int)$_GET['minlevel'];
  $maxlevel = (int)$_GET['maxlevel'];
  $gender = $_GET['gender'];
  $prolific = $_GET['prolific'];

  if($minlevel > 0)
  {
    $extra_wheres[] = '(`' . implode('`+`', $PET_SKILLS) . '`)>=' . (int)$_GET['minlevel'];
    $searchurl .= '&amp;minlevel=' . $minlevel;
  }
  else
    $minlevel = '';

  if($maxlevel > 0)
  {
    $extra_wheres[] = '(`' . implode('`+`', $PET_SKILLS) . '`)<=' . (int)$_GET['maxlevel'];
    $searchurl .= '&amp;maxlevel=' . $maxlevel;
  }
  else
    $maxlevel = '';

  if($gender == 'male' || $gender == 'female')
  {
    $extra_wheres[] = 'gender=' . quote_smart($gender);
    $searchurl .= '&amp;gender=' . $gender;
  }
  else
    $gender = 'any';

  if($prolific == 'yes' || $prolific == 'no')
  {
    $extra_wheres[] = 'prolific=' . quote_smart($prolific);
    $searchurl .= '&amp;prolific=' . $prolific;
  }
  else
    $prolific = 'any';
}
else
{
  $gender = 'any';
  $prolific = 'any';
}

if($admin['clairvoyant'] != 'yes')
  $extra_wheres[] = 'last_check<=' . $now;

if(count($extra_wheres) > 0)
  $base_command = 'SELECT * FROM monster_pets WHERE `user`=\'psypets\' AND ' . implode(' AND ', $extra_wheres);
else
  $base_command = 'SELECT * FROM monster_pets WHERE `user`=\'psypets\'';

$command = str_replace('SELECT *', 'SELECT COUNT(idnum)', $base_command);
$data = $database->FetchSingle($command, 'fetching shelter pet count');

$num_pets = $data['COUNT(idnum)'];

if($num_pets > 0)
{
  $num_pages = ceil($num_pets / 10);

  $page = (int)$_GET['page'];
  if($page < 1)
    $page = 1;
  else if($page > $num_pages)
    $page = $num_pages;

  $command = $base_command .= ' ORDER BY last_check ASC LIMIT ' . (($page - 1) * 10) . ',10';
  $shelter_pets = $database->FetchMultiple($command, 'fetching shelter pets');
}

include 'commons/html.php';
/*
  if($user['user'] == 'telkoth')
    echo $command;
*/
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Pet Shelter &gt; Adopt a Pet</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <h4>Pet Shelter &gt; Adopt a Pet</h4>
     <ul class="tabbed">
      <li class="activetab"><a href="petshelter.php">Adopt a Pet</a></li>
      <li><a href="daycare.php">Daycare</a></li>
      <li><a href="renameform.php">Rename a Pet</a></li>
      <li><a href="spayneuter.php">Spay or Neuter a Pet</a></li>
      <li><a href="giveuppet.php">Give Up a Pet</a></li>
<?php if($user['breeder'] == 'yes') echo '<li><a href="genetics.php">Genetics Lab</a></li>'; ?>
      <li><a href="breederslicense.php">Breeder's License</a></li>
     </ul>
<?php
echo '<a href="/npcprofile.php?npc=Kim+Littrell"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/petsheltergirl-2.png" align="right" width="350" height="450" alt="(Kim Littrell)" /></a>';
include 'commons/dialog_open.php';

if($dialog !== false)
  echo $dialog;
else if($user['adoptedtoday'] == 'yes')
{
  echo '
    <p>To give everyone a fair chance to adopt pets, we ask that you adopt only one pet per allowance period.</p>
    <p><i>(You may not adopt another pet until you collect <a href="allowance.php">allowance</a>.)</i></p>
  ';
}
else
{
  echo '<p>A new pet costs ' . $pet_cost . '<span class="money">m</span>.  This fee includes costs for a checkup, vaccinations, and all the city hall paperwork.  Remember that taking care of another pet is a lot of work!  Make sure you really have the time and money before taking one home.</p>' .
       '<p>Choose any pet you want.  While they\'ve already been given names, you may rename the pet when you buy it.  If the pet was not named by another Resident, we have named it ourselves from the <a href="http://www.panix.com/~mittle/names/">Medieval Names Archive</a>.</p>';
}

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

if($num_pets == 0)
{
  if($_GET['action'] == 'search')
  {
    include 'commons/petsheltersearch.php';
    echo '<p>No pets matched your search critera.</p>';
  }
  else if($_GET['dialog'] != 'randompet')
  {
    echo '
      <p class="failure">There are no pets available in the shelter at this time.</p>
      <p><i>(Pets are placed in the Pet Shelter by other players, usually when that player finds themselves with more baby pets than they can handle!  So if you\'re looking for a specific pet, check on the Pet Shelter again later!</i></p><p><i>If you\'d be happy to receive a random pet, <a href="?dialog=randompet">ask Kim if any other pets are available for adoption</a> - she may be able to work something out.)</i></p>
    ';
  }
}
else
{
  include 'commons/petsheltersearch.php';

  $pages = paginate($num_pages, $page, 'petshelter.php?page=%s' . $searchurl);
?>
     <?= $pages ?>
     <form action="buypet.php" method="post">
     <table class="nomargin">
<?php
  $rowclass = begin_row_class();

  foreach($shelter_pets as $this_pet)
  {
    $description = 'Level ' . pet_level($this_pet);

    if($this_pet['pregnant_asof'] > 0)
    {
      if($this_pet['pregnant_asof'] >= 20 * 24)
        $description .= '<br /><span class="success">is near birthing!</span>';
      else if($this_pet['pregnant_asof'] >= 10 * 24)
        $description .= '<br /><span class="success">is very pregnant!</span>';
      else if($this_pet['pregnant_asof'] > 0)
        $description .= '<br /><span class="success">is pregnant!</span>';
    }

    if($this_pet['ascend'] == 'yes')
      $description .= '<br />can be reincarnated!';
    
    if($this_pet['last_check'] <= $now)
    {
?>
      <tr class="<?= $rowclass ?>">
       <td><input type="radio" name="petid" value="<?= $this_pet["idnum"] ?>" /></td>
       <td><a href="/petprofile.php?petid=<?= $this_pet['idnum'] ?>"><img src="gfx/pets/<?= $this_pet["graphic"] ?>" alt="" border="0" /></a></td>
       <td>
        <?= gender_graphic($this_pet['gender'], $this_pet['prolific']) ?> <?= $this_pet['petname'] ?>
       </td>
       <td><?= $description ?></td>
      </tr>
<?php
    }
    else
    {
?>
      <tr class="<?= $rowclass ?>">
       <td><input type="radio" name="petid" disabled /></td>
       <td><a href="/petprofile.php?petid=<?= $this_pet['idnum'] ?>"><img src="gfx/pets/<?= $this_pet['graphic'] ?>" alt="" border="0" /></a></td>
       <td class="dim">
        <?= gender_graphic($this_pet['gender'], $this_pet['prolific']) ?> <?= $this_pet['petname'] ?><br />
        <?= ($this_pet['prolific'] == 'no' ? ' (' . ($this_pet['gender'] == 'male' ? 'neutered' : 'spayed') . ')' : '') ?>
       </td>
       <td class="dim">
        <?= $description ?><br />
        available on <?= local_time($this_pet['last_check'], $user['timezone'], $user['daylightsavings']) ?>
       </td>
      </tr>
<?php
    }

    $rowclass = alt_row_class($rowclass);
  }

?>
     </table>
<?php
  if($user['adoptedtoday'] == 'no')
    echo '     <p><input type="submit" name="submit" value="Buy Pet ' . $pet_cost . 'm"' . ($user['money'] < $pet_cost ? ' disabled="yes"' : '') . ' class="bigbutton" /></p>';
?>
     </form>
     <?= $pages ?>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
