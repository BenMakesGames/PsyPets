<?php
  $books_needed = array(1 => 'All You Wanted to Know About Sammiches', 2 => 'The Chiseled Tome of Ta Gilova', 4 => 'Puddings: An Introduction', 8 => 'The Gorgon', 16 => 'The Many Uses of Aging Root', 32 => 'The Smithy\'s Guide', 64 => 'Surviving in the Wild');

  $library_books_1 = get_quest_value($user['idnum'], 'collect books 1');

  if($_GET['give'] > 0)
  {
    $itemid = (int)$_GET['give'];

    $item = get_inventory_byid($itemid);
    if($item['user'] == $user['user'] && $item['location'] == 'storage')
    {
      $quest_id = array_search($item['itemname'], $books_needed);

      if($quest_id > 0 && ((int)$library_books_1['value'] & $quest_id) == 0)
      {
        $library_books_1['value'] += $quest_id;
        update_quest_value($library_books_1['idnum'], $library_books_1['value']);
        delete_inventory_byid($itemid);

        if($library_books_1['value'] == 127) // all books collected?!
        {
          $do_library_quest_1 = false;
          $do_library_quest_2 = true;
          $message = '<p>That\'s the last of them!  Great!</p><p>Well thanks a lot, ' . $user['display'] . '.  Here, take this Magic Quill as a token of my appreciation.  It can write out the contents of any book you want!  Although they tend to be a bit unreliable...</p><p><i>(You received a Magic Quill!  You can find it in Incoming.)</i></p><p>And that\'s about all I need!  I should have things up and running in about a day, maybe less.  Thanks again for all your help!</p>';
          $library_quest['value'] = 2;
          update_quest_value($library_quest['idnum'], 2);
          add_inventory($user['user'], '', 'Magic Quill', 'Given to you by the Librarian', 'storage/incoming');
          flag_new_incoming_items($user['user']);
          add_quest_value($user['idnum'], 'library quest 2 start', $now + (12 * 60 * 60));
        }
        else
        {
          $random_status = array('Good, good!  That\'s one down!', 'Thanks!', '"' . $item['itemname'] . '"... okay, crossed that one off!', 'Perfect, ' . $user['display'] . '!');
          $extra_message = $random_status[array_rand($random_status)];
        }
      }
    }
  }
?>
