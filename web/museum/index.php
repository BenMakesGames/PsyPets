<?php
require_once 'commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/museumlib.php';
require_once 'commons/questlib.php';

$options = array();

$item_count = get_user_museum_count($user['idnum']);
$badges = get_badges_byuserid($user['idnum']);

if($_GET['dialog'] == 'explainmuseum')
{
  $dialog_text = '<p>Hm?  This?  Why, this is a museum, of course!  Here we catalog and categorize everything!  <em>Everything!</em></p>' .
                 '<p>Well, that\'s not entirely accurate.  We are primarily interested in samples from the Hollow Earth, and the creations of the PsyPets.</p>' .
                 '<p>Perhaps you could help!  In fact, we\'re counting on your help!  Depending on it!</p>' .
                 '<p>You see, we\'d like a single, good sample of everything!  <em>Everything!</em>  Anything you can get your hands on, we\'ll take one!</p>';
}
else
{
  if($badges['museum_wing'] == 'no' && $item_count >= 100)
  {
    $dialog_text = '<p>Oh, ' . $user['display'] . '!  We wanted to thank you for donating 100 items to the Museum.  In addition to the Wing which we will build from your contributions, we\'d like you to have this Museum Wing Badge!</p>' .
                   '<p>Thanks again for all your efforts!</p>' .
                   '<p><i>(You received the Museum Wing Badge!)</i></p>';
    set_badge($user['idnum'], 'museum_wing');
  }
  else if($badges['museum_plus'] == 'no' && $item_count >= 1000)
  {
    $dialog_text = '<p>Oh, ' . $user['display'] . '!  We wanted to thank you for donating <strong>1,000</strong> items to the Museum.  Such a generous contribution is virtually unheard of!</p>' .
                   '<p>Please accept our thanks!  Also, this Museum Favorite Badge.</p>' .
                   '<p><i>(You received the Museum Favorite Badge!)</i></p>';
    set_badge($user['idnum'], 'museum_plus');
  }
  else if($item_count == 0)
    $dialog_text = '<p>Welcome to The Museum!  How can I help you?</p>';
  else
  {
    $dialog_text = '<p>Welcome back to The Museum, ' . $user['display'] . '!</p>';

    if($item_count == 1)
      $dialog_text .= '<p>You\'ve donated one item so far...</p>';
    else if($item_count < 100)
      $dialog_text .= '<p>You\'ve donated ' . $item_count . ' items so far.</p>';
    else
      $dialog_text .= '<p>You\'ve donated ' . $item_count . ' items so far, enough for us to build an entire Wing with!</p>';
  }
  
  $options[] = '<a href="/museum/?dialog=explainmuseum">Ask what this place is about</a>';
}

$dh_voodoo = get_quest_value($user['idnum'], 'DreamHost Voodoo Doll');
if($dh_voodoo === false && $user['idnum'] <= 36759)
{
  if($_GET['dialog'] == 'voodoo')
  {
    $dialog_text = '<p>Ah, you\'ve heard about it as well?  A mysterious entity... you don\'t hear much about it.  What information we have tells us that the name refers to a group of people, but beyond that...</p>' .
                   '<p>Oh!  But there\'s this also: a Voodoo Doll!  Well, or more correctly, many Voodoo Dolls!  We found a site on September 8th of 2009 when one was accidentally unearthed... for about a week afterwards we examined every inch of it, and in so doing uncovered more and more of the things.</p>' .
                   '<p>Anyway, please take this one.  We - The Museum - are giving one out to everyone with an account that existed during that time!</p>' .
                   '<p><i>(You received a DreamHost Voodoo Doll!  Find it in ' . $user['incomingto'] . '!)</i></p>';

    add_quest_value($user['idnum'], 'DreamHost Voodoo Doll', 1);
    add_inventory($user['user'], '', 'DreamHost Voodoo Doll', 'Given to you by The Museum', $user['incomingto']);
  }
  else
    $options[] = '<a href="/museum/?dialog=voodoo">Ask him about DreamHost</a>';
}

if($item_count > 0)
{
  $page = (int)$_GET['page'];
  $max_pages = ceil($item_count / 20);

  if($page < 1 || $page > $max_pages)
    $page = 1;

  $sort = $_GET['sort'];

  $item_sort = '<a href="/museum/?sort=item">&#9661;</a>';
  $time_sort = '<a href="/museum/?sort=time">&#9661;</a>';

  if($sort == 'time')
  {
    $item_list = get_user_museum_page_by_time($user['idnum'], $page);
    $time_sort = '&#9660;';
  }
  else //if($sort == 'item')
  {
    $item_list = get_user_museum_page($user['idnum'], $page);
    $item_sort = '&#9660;';
    $sort = 'item';
  }

  $pages = paginate($max_pages, $page, '/museum/?sort=' . $sort . '&page=%s');
}

$displays = fetch_multiple('
  SELECT idnum,name,num_items
  FROM psypets_museum_displays
  WHERE userid=' . $user['idnum'] . '
');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Museum</title>
  <?php include 'commons/head.php'; ?>
  <script type="text/javascript">
  $(function() {
    $('.add_to_display').click(function() {
      $('.add_to_display').attr('disabled', 'disabled');
    
      the_data = {};
    
      $.each($('#encyclopedia_page').serializeArray(), function(index, value) {
        if(the_data[value.name] == undefined)
          the_data[value.name] = value.value;
        else if($.isArray(the_data[value.name]))
          the_data[value.name].push(value.value);
        else
          the_data[value.name] = [ the_data[value.name], value.value ];
      });

      $.ajax({
        type: 'POST',
        url: '/museum/ajax_add_to_wing.php',
        data: the_data,
        success: function(data)
        {
          $('#messages').append(data);
          $('#encyclopedia_page input[type="checkbox"]:checked').removeAttr('checked');
          $('.add_to_display').removeAttr('disabled');
        }
      });
    });
  });
  </script>
 </head>
 <body>
  <?php include 'commons/header_2.php'; ?>
  <h4>The Museum</h4>
  <ul class="tabbed">
   <li class="activetab"><a href="/museum/">My Collection</a></li>
   <li><a href="/museum/uncollection.php">My Uncollection</a></li>
   <li><a href="/museum/donate.php">Make Donation</a></li>
   <li><a href="/museum/exchange.php">Exchanges</a></li>
   <li><a href="/museum/displayeditor.php">My Displays</a></li>
   <li><a href="/museum/wings.php">Wing Directory</a></li>
  </ul>
<?php
echo '<img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/museum.png" align="right" width="350" height="500" alt="(Museum Curator)" />';

include 'commons/dialog_open.php';

echo $dialog_text;

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

if($item_count == 0)
  echo '<p>You haven\'t collected anything for The Museum.</p>';
else
{
  echo
    $pages .
    '<hr /><div id="messages"></div><form id="encyclopedia_page">'
  ;

  if(count($displays) > 0)
  {
    echo '<p><input type="button" class="add_to_display" value="Add To" /> <select name="display">';

    foreach($displays as $display)
      echo '<option value="' . $display['idnum'] . '">' . $display['name'] . '</option>';

    echo '</select></p>';
  }

  echo '<table>' .
       '<tr class="titlerow"><th></th><th></th><th>Item ' . $item_sort . '</th><th class="centered">Donated ' . $time_sort . '</th><th>Donators</th></tr>';

  $rowclass = begin_row_class();

  foreach($item_list as $item)
  {
    $donators = get_museum_item_count($item['itemid']);

    echo '<tr class="' . $rowclass . '"><td>';

    if(count($displays) > 0)
      echo '<input type="checkbox" name="item_id[]" value="' . $item['itemid'] . '" />';

    echo '</td><td class="centered">' . item_display($item, '') . '</td><td>' . $item['itemname'] . '</td><td class="centered">' . Duration($now - $item['timestamp'], 2) . ' ago</td><td class="centered"><a href="/museum/donators.php?item=' . $item['itemid'] . '">' . $donators . '</a></td></tr>';

    $rowclass = alt_row_class($rowclass);
  }

  echo '</table></form><hr />' . $pages;
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
