<?php
  $quests = array();

  $badges = get_badges_byuserid($user['idnum']);

  if($badges['hamletcollector'] == 'no')
  {
    $quests[] = 'The edition of Shakespeare\'s <i>Hamlet</i> that\'s printed here is unique in that each Scene comes as a separate book.  It\'s <em>very</em> hard to actually find the complete collection, however!  I would never ask you to give me the collection, should you manage to get it, but I would like to take a few pictures.';

    if($_GET['dialog'] == 2)
      $message = '<p>I\'m sure if you look around the <a href="/fleamarket/">Flea Market</a> you can find many copies of the various Scenes of Hamlet for very cheap.  But believe me, they\'re not worth buying!  You\'ll probably pick up several copies of most of these Scenes on your own before you finally get the last two or three that are <em>really</em> hard to come by.  These last few will be listed at very high prices, if listed there at all!</p><p>Other than buying these rare Scenes from other Residents, I don\'t know where to find them.  Of course the Residents who are selling them will probably know.</p><p>Oh, there\'s also a book out there called <a href="encyclopedia2.php?item=' . link_safe('The Guide to Collecting Hamlet') . '">The Guide to Collecting Hamlet</a> that might help you out.  Unfortunately I do not have a copy to give you, but I don\'t believe they\'re particularly hard to come by.</p>';
    else
      $options[] = '<a href="' . $url . '?dialog=2">Ask for details about Hamlet</a>';
  }

  if($badges['cordonbleu'] == 'no')
  {
    $quests[] = 'It was not by accident that many of the books I asked you for were cookbooks.  I do a bit of cooking myself, and used to be a judge for that TV show Titanium Cook.  Anyway, there are some unique foods found only here, in PsyPettia, and I\'d love to try a few dishes made from these.';

    if($_GET['dialog'] == 3)
      $message = '<p>Well, there\'s a very special kind of Mushroom that grows here, and I\'d really like to try something made from that mushroom; a jar of <a href="encyclopedia2.php?item=' . link_safe('Special Sauce') . '">Special Sauce</a> should do.</p><p>I also understand you have a spice here called <a href="encyclopedia2.php?item=' . link_safe('Fire Spice') . '">Fire Spice</a>.  <a href="encyclopedia2.php?item=' . link_safe('Sushi') . '">Sushi</a> prepared with that spice would be excellent.</p><p>Finally, I hear the <a href="encyclopedia2.php?item=' . link_safe('Ginger Beer') . '">Ginger Beer</a> that is produced here is a bit unique, since it is aged using a particular root found only in the region.</p><p>Come back with these three foods, prepared by <em>your</em> hands, for me to taste, and I will happily give you a particular badge I picked up from my days at Titanium Cook.</p>';
    else
      $options[] = '<a href="' . $url . '?dialog=3">Ask what dishes she\'d like to try</a>';
  }

  if(count($quests) > 0)
  {
    $book_checkout = true;

    if($_GET['dialog'] == 4)
    {
      $hamlet_books = array(
        'Hamlet: Act I Scene I', 'Hamlet: Act I Scene II', 'Hamlet: Act I Scene III', 'Hamlet: Act I Scene IV', 'Hamlet: Act I Scene V',
        'Hamlet: Act II Scene I', 'Hamlet: Act II Scene II',
        'Hamlet: Act III Scene I', 'Hamlet: Act III Scene II', 'Hamlet: Act III Scene III', 'Hamlet: Act III Scene IV',
        'Hamlet: Act IV Scene I', 'Hamlet: Act IV Scene II', 'Hamlet: Act IV Scene III', 'Hamlet: Act IV Scene IV', 'Hamlet: Act IV Scene V', 'Hamlet: Act IV Scene VI', 'Hamlet: Act IV Scene VII',
        'Hamlet: Act V Scene I', 'Hamlet: Act V Scene II'
      );

      $cb_food = array(
        'Sushi', 'Special Sauce', 'Ginger Beer'
      );

      $command = 'SELECT idnum,itemname,creator FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=\'storage\'';
      $storage = fetch_multiple($command, $url);

      foreach($storage as $item)
      {
        if(in_array($item['itemname'], $hamlet_books))
          $hamlet_books_owned[$item['itemname']]++;

        if(in_array($item['itemname'], $cb_food) && $item['creator'] == 'u:' . $user['idnum'])
          $cb_food_owned[$item['itemname']] = $item['idnum'];
      }

      if($badges['cordonbleu'] == 'no' && count($cb_food_owned) == count($cb_food))
      {
        $command = 'DELETE FROM monster_inventory WHERE idnum IN (' . implode(',', $cb_food_owned) . ') LIMIT ' . count($cb_food_owned);
        fetch_none($command, $url);

        $message = '<p>Ooh, this Special Sauce looks wonderful!  I will definitely save this for a special occasion!</p>' .
                   '<p>But this Sushi!  Oh ho!  This will not be saved!</p>' .
                   '<p>Now where did I put those Chopsticks... Ah, here!  <i>*click, click*</i></p>' .
                   '<p>Itadakimasu!</p>';
        $options = array('<a href="' . $url . '">...</a>');
        add_quest_value($user['idnum'], 'food progress', 1);
        $book_checkout = false;
      }
      else if($badges['hamletcollector'] == 'no' && count($hamlet_books_owned) == count($hamlet_books))
      {
        $message = '<p>This is it!  The complete Hamlet collection!  Amazing!  God, I\'m almost too scared to touch them!</p>' .
                   '<p>Here, you hold on to this Hamlet Collector Badge... I\'m going to go get my Eye-Con 5000!</p>' .
                   '<p>I\'ll be right back!</p>' .
                   '<p>Don\'t go <em>anywhere</em>!' .
                   '<p><i>(You won the Hamlet Collector Badge!)</i></p>';
        set_badge($user['idnum'], 'hamletcollector');
      }
      else
        $message = '<p>Hm, no, nothing I\'m quite looking for is here.</p>';
    }
    else
      $options[] = '<a href="' . $url . '?dialog=4">Ask her to look through your Storage for items she wants</a>';

    if(strlen($message) == 0)
    {
      if($library_quest['value'] == 3)
      {
        update_quest_value($library_quest['idnum'], 4);
        $message = '<p>Okay, so the Library thing is just a pretense.  I mean, yes, there are books here, and I run this Library, but that\'s not what I\'m really interested in...</p>' .
                   '<p>Truthfully, I\'m a Collector!  Books were how I got started, but I\'ve been around the world and back collecting all kinds of interesting, and sometimes quite rare, treasures.  Of course, there are a few things I haven\'t managed to get a hold of, but that\'s why I\'m here! This island has a few things that simply can\'t be found anywhere else!</p>' .
                   '<p>So I was thinking maybe you and I could work something out: a trade.  There are some things I\'d like you to get for me, and I have some interesting things to give to you, including a few Badges, of course.  You saw the Badge Archive, right?</p>' .
                   '<p>Well, so how about it?</p>';
      }
      else
      {
        $greetings = array($user['display'] . ', ' . $user['display'] . ', ' . $user['display'] . '...', 'Hello again!', '<i>*nod*</i>');
        $message = '<p>' . $greetings[array_rand($greetings)] . '  Here to claim a Badge?</p>';
      }

      $message .= '<ul class="spacedlist"><li>' . implode('</li><li>', $quests) . '</li></ul>';
    }

    if($book_checkout == true)
      $options[] = '<a href="' . $url . '?dialog=5">Ask about checking out a book</a>';
  }
  else
  {
    $librarian_mode = true;
  }
?>
