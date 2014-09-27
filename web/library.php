<?php
$whereat = 'library';
$wiki = 'Library';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/questlib.php';
require_once 'commons/timelib.php';

if(is_thanksgiving())
{
  $thanksgiving_quest = get_quest_value($user['idnum'], 'thanksgiving ' . $now_year);
  if($thanksgiving_quest === false)
  {
    if($_GET['dialog'] == 'thanksgiving')
    {
      $message = '
        <p>Did I tell you I\'m a bit of a cook?  With Thaddeus\' help we bound up entire Thanksgiving dinners I prepared into a magic scroll!</p>
        <p>Here, have one.  I made them to give to people, after all!</p>
        <p><i>(You received a Thanksgiving Scroll!  Find it in <a href="/incoming.php">Incoming</a>.)</i></p>
      ';
      
      add_quest_value($user['idnum'], 'thanksgiving ' . $now_year, 1);
      add_inventory($user['user'], 'u:24628', 'Thanksgiving Scroll', '', 'storage/incoming');
    }
    else
      $options[] = '<a href="?dialog=thanksgiving">Ask her about the Thanksgiving Scroll</a>';
  }
}

$jerky_quest = get_quest_value($user['idnum'], 'spicy jerky quest');
if($jerky_quest['value'] == 2)
{
  if($_GET['dialog'] == 'spicyjerky_despicifying')
  {
    $message = '<p>Fishing, right?  There\'s no other reason.</p><p>But why despicify Spicy Jerky?  That seems kind of roundabout.  Just don\'t buy or make Spicy Jerky.</p>';
    $options[] = '<a href="?dialog=spicyjerky_smokehouse">Explain about the Smokehouse</a>';
  }
  else if($_GET['dialog'] == 'spicyjerky_smokehouse')
  {
    $message = '<p>That... that\'s really weird.</p><p>Well, I think I can help you out.  It\'s nothing I\'ve tried before, but I\'m already starting to put together something in my mind.</p><p>Do you think you can get some equipment for me?  I\'ll need to test this out.</p>';
    $options[] = '<a href="?dialog=spicyjerky_equipment">Agree to get her the equipment she needs</a>';
    update_quest_value($jerky_quest['idnum'], 3);
  }
  else
    $options[] = '<a href="?dialog=spicyjerky_despicifying">Ask about despicifying Spicy Jerky</a>';
}
else if($jerky_quest['value'] == 3)
{
  if($_GET['dialog'] == 'spicyjerky_equipment')
  {
    $message = '<p>I\'m going to need 10 Spicy Jerky, one Colander, one Sugar Beater, and one Milk Separator.';

    $command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE itemname=\'Spicy Jerky\' AND user=' . quote_smart($user['user']) . ' AND location=\'storage\'';
    $jerky_data = $database->FetchSingle($command, 'fetching spicy jerky count');

    $command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE itemname=\'Colander\' AND user=' . quote_smart($user['user']) . ' AND location=\'storage\'';
    $colander_data = $database->FetchSingle($command, 'fetching colander count');

    $command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE itemname=\'Sugar Beater\' AND user=' . quote_smart($user['user']) . ' AND location=\'storage\'';
    $beater_data = $database->FetchSingle($command, 'fetching sugar beater count');

    $command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE itemname=\'Milk Separator\' AND user=' . quote_smart($user['user']) . ' AND location=\'storage\'';
    $separator_data = $database->FetchSingle($command, 'fetching milk separator count');

    if($jerky_data['c'] >= 10 && $colander_data['c'] >= 1 && $beater_data['c'] >= 1 && $separator_data['c'] >= 1)
    {
      $marian = get_user_byuser('mwitford', 'idnum');
    
      $message .= '</p><p>Oh!  Looks like you have everything.  Actually, I have to admit I got impatient waiting for you and bought the supplies myself...</p><p>But that\'s okay!  I figured everything out, and you\'re going to need all that stuff to do it yourself!</p><p>There\'s a lot to it, so I wrote down the instructions for you.</p><p>Have fun fishing!</p>' .
                  '<p><i>(You received Instructions for Despicifying Spicy Jerky!  Find it in Storage.)</i></p>';
      
      add_inventory($user['user'], 'u:' . $marian['idnum'], 'Instructions for Despicifying Spicy Jerky', 'Given to you by Marian', 'storage');
      update_quest_value($jerky_quest['idnum'], 4);
    }
    else
      $message .= '  Once you have all of those things in your storage for me, I\'ll get started.</p>';
  }
  else
    $options[] = '<a href="?dialog=spicyjerky_equipment">Ask about the equipment needed to despicify Spicy Jerky</a>';
}

$library_quest = get_quest_value($user['idnum'], 'library quest');

if($library_quest === false)
{
  if($_GET['dialog'] == 1)
  {
    add_quest_value($user['idnum'], 'library quest', 1);
    add_quest_value($user['idnum'], 'collect books 1', 0);
    $do_library_quest_1 = true;
    $extra_message = '<p>Thanks!  It means a lot to me!</p>';
  }
  else
    $intro_dialog = true;
}
else if($library_quest['value'] == 1) // collect basic books
  $do_library_quest_1 = true;
else if($library_quest['value'] == 2) // wait 24 hours
  $do_library_quest_2 = true;
else if($library_quest['value'] == 3 || $library_quest['value'] == 4) // library open
  $do_library_quest_3 = true;

$food_progress = get_quest_value($user['idnum'], 'food progress');

if($food_progress['value'] == 1)
{
  $do_library_quest_3 = false;

  $message = '<p>Mmm!  Mm-hm...  Quite nice!  Yes, yes... the Fire Spice\'s flavor adds a subtle <em>twist</em> to the whole thing... Mmm!  That really is good.  Amazing, even!</p>';

  $options = array('<a href="/library.php">...</a>');
  update_quest_value($food_progress['idnum'], 2);
}
else if($food_progress['value'] == 2)
{
  $do_library_quest_3 = false;

  $message = '<p>Ah!  Almost forgot about the Ginger Beer!</p>' .
             '<p>Hm, yes, the color is a little different, indicative of the rapid aging it underwent.  This is definitely the stuff...</p>' .
             '<p>Oh, sorry!  You don\'t want to just sit there and watch me eat.  Here, here: the Cordon Bleu Badge.  Take it.  You deserve every thread of it!</p>' .
             '<p><i>(You won the Cordon Bleu Badge!)</i></p>';

  update_quest_value($food_progress['idnum'], 3);
  set_badge($user['idnum'], 'cordonbleu');
}

if($do_library_quest_1)
{
  include 'commons/library_quest_1.php';
}

if($do_library_quest_1)
{
  $quest_1_dialog = true;

  $command = 'SELECT idnum,itemname FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=\'storage\' AND itemname IN (';

  $comma = false;
  foreach($books_needed as $itemname)
  {
    if($comma == true)
      $command .= ',';
    else
      $comma = true;

    $command .= quote_smart($itemname);
  }

  $command .= ')';
  
  $books = $database->FetchMultiple($command);

  foreach($books as $book)
    $owns_book[array_search($book['itemname'], $books_needed)] = $book['idnum'];
}

if($do_library_quest_2)
{
  $library_quest_time = get_quest_value($user['idnum'], 'library quest 2 start');
  if($library_quest_time['value'] < $now)
  {
    $do_library_quest_3 = true;
    $library_quest['value'] = 3;
    update_quest_value($library_quest['idnum'], 3);
  }
  else
    $wait_a_day_dialog = true;
}

else if($_GET['dialog'] == 5 && $do_library_quest_3)
{
  $do_library_quest_3 = false;
  $librarian_mode = true;
  $extra_message = '<p>Hm?  Check out a book?   Hm...</p>';
}

if($do_library_quest_3)
{
  include 'commons/library_quest_3.php';
}

if($librarian_mode)
{
  $exchanges = array(
    1 => array('All You Wanted to Know About Sammiches', 'A Drop Fell'),
    2 => array('Asian Cuisine', 'Robin Hood aideth a Sorrowful Knight'),
    3 => array('Book of Creatures I', 'The Were-Wolf'),
    4 => array('Book of Minerals', 'Woodworker\'s Handbook'),
    5 => array('Beef Stroganon and On and On', 'Anyone Lived in a Pretty How Town'),
    6 => array('Egg Yolk-Red Bean Pastry', 'The Bun Bible'),
    7 => array('Muffins: How I Made Them', 'How Sir Richard of the Lea paid his Debts to Emmet'),
    8 => array('Surviving in the Wild', 'The Turnip'),
    9 => array('The Devious Electrician', 'You Looked So Tempting In The Pew'),
    10 => array('The Diary of Commander Mekono', 'The Emperor\'s New Clothes'),
    11 => array('Wintergreen Diary', 'The Story of Aladdin'),
  );

  $exchangeid = (int)$_GET['exchange'];
  
  if(count($exchanges[$exchangeid]) == 2)
  {
    $exchange = $exchanges[$exchangeid];

    $item = $database->FetchSingle('
      SELECT idnum
      FROM monster_inventory
      WHERE
        user=' . quote_smart($user['user']) . ' AND
        location=\'storage\' AND
        itemname=' . quote_smart($exchange[0]) . '
      LIMIT 1
    ');
    
    if($item === false)
      $report_message = '<p>Hm... actually, you don\'t seem to have one of a ' . $exchange[0] . ' in your Storage after all.  Was it on sale in your store, maybe, and someone bought it up?</p>';
    else
    {
      delete_inventory_byid($item['idnum']);
      add_inventory($user['user'], '', $exchange[1], 'Traded at the Library', 'storage/incoming');
      flag_new_incoming_items($user['user']);
      $report_message = '<p>Great!  You\'ll find your copy of ' . $exchange[1] . ' in your Incoming box-thing, or whatever it is.</p>';

      require_once 'commons/statlib.php';
      record_stat($user['idnum'], 'Made a Book Exchange at The Library', 1);
    }
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Library</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Library</h4>
     <ul class="tabbed">
      <li class="activetab"><a href="library.php">Information</a></li>
      <li><a href="badgedb.php">Badge Archive</a></li>
      <li><a href="gl_browse.php">Graphics Library</a></li>
     </ul>
<?php
if($error_message)
  echo '     <p style="color:red;">' . $error_message . '</p>';
?>
<?php
echo '<a href="/npcprofile.php?npc=Marian+Witford"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/marian-the-librarian.png" align="right" width="350" height="350" alt="(Marian the Librarian)" /></a>';

include 'commons/dialog_open.php';

if(strlen($message) > 0)
  echo $message;
else if($quest_1_dialog === true)
{
  echo $extra_message;
?>
<p>So, here's a list of the books I need.  I'll cross them off as you get them for me:</p>
<ul><?php
  foreach($books_needed as $index=>$itemname)
  {
    echo '<li>';
    if(((int)$library_books_1['value'] & $index) == 0)
    {
      echo item_text_link($itemname);
      if($owns_book[$index] > 0)
        $options[] = '<a href="?give=' . $owns_book[$index] . '">Give copy of ' . $itemname . '</a>';
    }
    else
      echo '<s>' . $itemname . '</s>';
    echo '</li>';
  }
?></ul>
<p>Oh, and remember, I can only take books from your Storage, and not your house directly.</p>
<?php
}
else if($wait_a_day_dialog)
{
?>
<p>Please wait just a little longer.  I'll have this place up and running in no time!</p>
<?php
}
else if($intro_dialog === true)
{
?>
<p>Hi, <?= $user['display'] ?>.  Welcome to PsyPettia!  And sorry about the library being a bit of a wreck; I'm new here myself, and still only just setting up.  I have set up the Badge Archive and Graphics Library, but...</p>
<p>Hm, anyway, I'm a bit embarrassed to ask, but do you think you could help me get some supplies together?  Books, to be specific.</p>
<?php
  $options[] = '<a href="?dialog=1">Accept</a>';
}
else if($librarian_mode)
{
  if(strlen($report_message) > 0)
    echo $report_message;
  else if($_GET['option'] == 'buns')
  {
?>
<p>Oh, yeah, I guess that one is a little out of place.  Honestly, I just happen to really love Egg Yolk-Red Bean Pastries <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/emote/hee.gif" alt="[smile]" width="16" height="16" class="inlineimage" /></p>
<p>The recipe is not complicated, so let me give you a hint: it only has 4 ingredients, and half of those are revealed in the pastry's name.</p>
<?php
  }
  else
  {
    if(strlen($extra_message) > 0)
      echo $extra_message;
?>
<p>Unfortunately all the books here are either highly boring, highly technical, or in most cases, both.  It's all reference material for the HERG researchers, and honestly, I don't know that I'm even allowed to loan them out to anyone <em>but</em> HERG researchers.</p>
<p>But maybe we can arrange something else... I brought a number of books with me for leisure reading when things around here get slow, but, well, it's always slow around here, and I've read just about every book I brought!  But surely you have a few books I haven't read, and I'm almost positive I have books you haven't read... so let's trade.</p>
<?php
    $options[] = '<a href="?dialog=5&amp;option=buns">Ask about the Egg Yolk-Red Bean Pastry exchange</a>';
  }
}
else
  echo '<p>Hm?  Oh, you should report that I\'m saying this to <a href="admincontact.php">an administrator</a>.  I don\'t mean to break the fourth wall, but it seems like there\'s an error in my dialog logic &gt;_&gt;</p><p>Anyway, sorry about the inconvenience.  It\'s kind of embarrassing for me, too, really, so I hope someone fixes it up soon.</p>';

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

if($librarian_mode)
{
?>
<h4>Book Exchange</h4>
<p><i>(You can only exchange books which are in your Storage.)</i></p>
<table>
 <tr class="titlerow">
  <th></th>
  <th></th>
  <th>Asking</th>
  <th></th>
  <th></th>
  <th>Offering</th>
 </tr>
<?php
  $class = begin_row_class();

  foreach($exchanges as $idnum=>$exchange)
  {
    $asking = $exchange[0];
    $offering = $exchange[1];
    
    $asking_item = get_item_byname($asking);
    $offering_item = get_item_byname($offering);

    echo '<tr class="' . $class . '">' . "\n";

    $item = $database->FetchSingle('
      SELECT idnum
      FROM monster_inventory
      WHERE
        user=' . quote_smart($user['user']) . ' AND
        location=\'storage\' AND
        itemname=' . quote_smart($asking) . '
      LIMIT 1
    ');

    if($item === false)
      echo ' <td class="dim">Accept</td>';
    else
      echo ' <td><a href="?dialog=5&amp;exchange=' . $idnum . '">Accept</a></td>';
?>

  <td class="centered"><?= item_display($asking_item) ?></td>
  <td><?= $asking ?></td>
  <td><img src="gfx/lookright.gif" alt="" /></td>
  <td class="centered"><?= item_display($offering_item) ?></td>
  <td><?= $offering ?></td>
 </tr>
<?php

    $class = alt_row_class($class);
  }
?>
</table>
<?php
}
?>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
