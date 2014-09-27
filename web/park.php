<?php
// THE PARK CODE IS IMMUNE TO MISSING ACCOUNTS
// it does not need to be disabled with $NO_PVP

$wiki = 'The_Park';
$require_petload = 'yes';

$url = 'park.php';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/doevent.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/parklib.php';
require_once 'commons/messages.php';
require_once 'commons/questlib.php';

if($user['show_park'] != 'yes')
{
  header('Location: /n404/');
  exit();
}

// THE PARK CODE IS IMMUNE TO MISSING ACCOUNTS
// it does not need to be disabled with $NO_PVP

$park_tutorial_quest = get_quest_value($user['idnum'], 'tutorial: the park');
if($park_tutorial_quest === false)
  $no_tip = true;

$page = (int)$_GET['page'];
$results_per_page = 20;
if($page < 1)
  $page = 1;

$search_errors = array();
$event_results = array();
$searched = false;

$total_event_count = get_event_count('finished=\'no\'');

$SORT_BYS = array(
  1 => '(minlevel + maxlevel) ASC',
  2 => '(minlevel + maxlevel) DESC',
  3 => 'fee ASC',
  4 => 'fee DESC',
  5 => 'minparticipant ASC',
  6 => 'minparticipant DESC',
  7 => 'event ASC',
);

if(!array_key_exists($_GET['sort'], $SORT_BYS))
  $sort = 3;
else
  $sort = $_GET['sort'];

if($_GET['action'] == '')
{
  $_GET['eventtype'] = 'any';
  $_GET['petid'] = 'any';
  $_GET['action'] = 'search';
}

if($_GET['action'] == 'search')
{
  $urlparam = 'action=search&eventtype=' . $_GET['eventtype'] . '&petid=' . $_GET['petid'];

  $type = $_GET['eventtype'];
  $petid = (int)$_GET['petid'];

  $where_clauses = array(
    'finished=\'no\'',
    'host!=' . quote_smart($user['user'])
  );

  if($type == 'any')
    ;
  else if(array_key_exists($type, $EVENT_TYPES))
    $where_clauses[] = 'event=\'' . $type . '\'';
  else
    $search_errors[] = 'An event type was selected that, frankly, does not exist.';

  $max_level = 0;
  $min_level = 1000;

  if($petid == 'any')
  {
    $pet_levels = array();
  
    foreach($userpets as $thispet)
    {
      if($thispet['dead'] != 'no' || $thispet['changed'] == 'yes' || $thispet['park_event_hours'] < 8)
        continue;
    
      $pet_levels[] = pet_level($thispet);
    }
    
    if(count($pet_levels) == 0)
      $search_errors[] = 'You do not have any eligible pets.';
  }
  else
  {
    foreach($userpets as $thispet)
    {
      if($thispet['dead'] != 'no' || $thispet['changed'] == 'yes' || $thispet['park_event_hours'] < 8)
        continue;

      if($thispet['idnum'] == $petid)
      {
        $pet_levels[] = pet_level($thispet);
        break;
      }
    }
    
    if(count($pet_levels) == 0)
      $search_errors[] = 'The pet you selected cannot be signed up for any of the available events.';
  }

  if(count($errors) == 0)
  {
    $pet_levels = array_unique($pet_levels);
  
    foreach($pet_levels as $level)
      $level_clauses[] = 'minlevel<=' . $level . ' AND maxlevel>=' . $level;
    
    if(count($level_clauses) > 0)
    {
      $where_clauses[] = '((' . implode(') OR (', $level_clauses) . '))';

      $where_clause = implode(' AND ', $where_clauses);
      
      $event_count = get_event_count($where_clause);
      $pages = ceil($event_count / $results_per_page);
      if($page > $pages)
        $page = $pages;

      if($event_count > 0)
        $event_results = get_event_details($where_clause, $SORT_BYS[$sort], ($page - 1) * $results_per_page, $results_per_page);
    }
    else
      $event_count = 0;
    
    $searched = true;
  }
}
else if($_GET['action'] == 'residentevents')
{
  $urlparam = 'action=residentevents&resident=' . $_GET['resident'];

  $resident = get_user_bydisplay($_GET['resident']);
  
  if($resident === false)
    $search_errors[] = 'There is no resident by the name of "' . $_GET['resident'] . '".';
  else
  {
    $where_clause = 'host=' . quote_smart($resident['user']);// . ' AND finished=\'no\'';

    $event_count = get_event_count($where_clause);
    $pages = ceil($event_count / $results_per_page);
    if($page > $pages)
      $page = $pages;

    if($event_count > 0)
      $event_results = get_event_details($where_clause, $SORT_BYS[$sort], ($page - 1) * $results_per_page, $results_per_page);

    $searched = true;
  }
}
else if($_GET['action'] == 'petrecord')
{
  $urlparam = 'action=petrecord&resident=' . $_GET['resident'];

  $resident = get_user_bydisplay($_GET['resident']);

  if($resident === false)
    $search_errors[] = 'There is no resident by the name of "' . $_GET['resident'] . '".';
  else
  {
    $these_pets = get_pets_byuser($resident['user'], 'home');

    if(count($these_pets) > 0)
    {
      foreach($these_pets as $this_pet)
        $pet_commands[] = 'participants LIKE \'%<' . $this_pet['idnum'] . '>%\'';

      $where_clause = 'finished=\'no\' AND (' . implode(' OR ', $pet_commands) . ')';

      $event_count = get_event_count($where_clause);
      $pages = ceil($event_count / $results_per_page);
      if($page > $pages)
        $page = $pages;

      if($event_count > 0)
        $event_results = get_event_details($where_clause, $SORT_BYS[$sort], ($page - 1) * $results_per_page, $results_per_page);

      $searched = true;
    }
    else
      $search_errors[] = 'That resident does not have any pets.';
  }
}
else if($_GET['action'] == 'viewall')
{
  $urlparam = 'action=viewall';

  $where_clause = 'finished=\'no\'';

  $event_count = get_event_count($where_clause);
  $pages = ceil($event_count / $results_per_page);
  if($page > $pages)
    $page = $pages;

  if($event_count > 0)
    $event_results = get_event_details($where_clause, $SORT_BYS[$sort], ($page - 1) * $results_per_page, $results_per_page);

  $searched = true;
}
else
{
  header('Location: /park.php');
  exit();
}

$urlparam .= '&amp;page=' . $page;

if($sort == 1)
  $level_sort = '<a href="' . $url . '?' . $urlparam . '&amp;sort=2">&#9650;</a>';
else if($sort == 2)
  $level_sort = '<a href="' . $url . '?' . $urlparam . '&amp;sort=1">&#9660;</a>';
else
  $level_sort = '<a href="' . $url . '?' . $urlparam . '&amp;sort=1">&#9651;</a>';

if($sort == 3)
  $fee_sort = '<a href="' . $url . '?' . $urlparam . '&amp;sort=4">&#9650;</a>';
else if($sort == 4)
  $fee_sort = '<a href="' . $url . '?' . $urlparam . '&amp;sort=3">&#9660;</a>';
else
  $fee_sort = '<a href="' . $url . '?' . $urlparam . '&amp;sort=3">&#9651;</a>';

if($sort == 5)
  $size_sort = '<a href="' . $url . '?' . $urlparam . '&amp;sort=6">&#9650;</a>';
else if($sort == 6)
  $size_sort = '<a href="' . $url . '?' . $urlparam . '&amp;sort=5">&#9660;</a>';
else
  $size_sort = '<a href="' . $url . '?' . $urlparam . '&amp;sort=6">&#9661;</a>';

if($sort == 7)
  $event_sort = '&#9660;';
else
  $event_sort = '<a href="' . $url . '?' . $urlparam . '&amp;sort=7">&#9661;</a>';

$urlparam .= '&amp;sort=' . $sort;

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Park</title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/adrate3.js"></script>
 </head>
 <body>
<?php
include 'commons/header_2.php';

if($park_tutorial_quest === false)
{
  include 'commons/tutorial/park.php';
  add_quest_value($user['idnum'], 'tutorial: the park', 1);
}
?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <h4>The Park</h4>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";

$command = 'SELECT idnum,name FROM `monster_events` ' .
           'WHERE host=' . quote_smart($user['user']) . ' AND finished=\'no\' LIMIT 3';
$existing_events = $database->FetchMultiple($command, 'fetching existing event');

echo '
  <ul class="tabbed">
   <li class="activetab"><a href="/park.php">Browse Events</a></li>
';

if($user['license'] == 'yes')
{
  if(count($existing_events) < 3)
  {
    if($user['event_step'] > 0)
      echo '<li><a href="/hostevent' . ($user['event_step'] + 1) . '.php">Continue making an event where I left off</a></li>';
    else
      echo '<li><a href="/hostevent1.php">Host a new event</a></li>';
  }
  else
    echo '<li><a style="color:#999;">Already hosting 3 events</a></li>';
}

echo '
   <li><a href="/park_exchange.php">Exchanges</a></li>
  </ul>
';

include 'commons/bcmessage2.php';

echo '
  <ul>
   <li><a href="?action=residentevents&amp;resident=' . link_safe($user['display']) . '">Events I am currently hosting, or have previously hosted</a></li>
   <li><a href="?action=petrecord&amp;resident=' . link_safe($user['display']) . '">Events my pets are signed up for</a></li>
   <li><a href="?action=viewall">Browse all waiting events</a></li>
  </ul>
';

if(count($userpets) > 0)
{
  foreach($userpets as $thispet)
  {
    if($thispet['dead'] == 'no' && $thispet['changed'] == 'no' && $thispet['zombie'] == 'no')
      $eligible_pets++;
  }
  
  if($eligible_pets > 0)
  {
    $showtip = true;

    if(count($search_errors) > 0)
      echo '<ul><li class="failure">' . implode('</li><li class="failure">', $search_errors) . '</li></ul>';
?>
     <h5>Search</h5>
     <p>There <?= $total_event_count != 1 ? 'are' : 'is' ?> <?= $total_event_count ?> waiting event<?= $total_event_count != 1 ? 's' : '' ?> in the park.</p>
     <form action="<?= $url ?>" method="get">
     <table>
      <tr>
       <th>Event Type</th>
       <th>Pet Eligibility *</th>
      </tr>
      <tr>
       <td>
<?php
/*
foreach($EVENT_TYPES as $key=>$desc)
  echo '<li><input type="checkbox" id="search_' . $key . '" value="' . $key . '" /> <label for="search_' . $key . '">' . $desc . '</label></li>';
*/
?>
        <select name="eventtype">
         <option value="any">Any and all</option>
<?php
    foreach($EVENT_TYPES as $key=>$desc)
      echo '<option value="' . $key . '"' . ($type == $key ? ' selected' : '') . ' />' . $desc . '</option>';
?>
        </select>
       </td>
       <td>
        <select name="petid">
         <option value="any">Any and all</option>
<?php
    foreach($userpets as $this_pet)
    {
      if($this_pet['dead'] == 'no' && $this_pet['changed'] == 'no' && $this_pet['zombie'] == 'no')
        echo '<option value="' . $this_pet['idnum'] . '"' . ($petid == $this_pet['idnum'] ? ' selected' : '') . ' />' . $this_pet['petname'] . '</option>';
    }
?>
        </select>
       </td>
      </tr>
      <tr>
       <td colspan="2" align="right"><input type="hidden" name="action" value="search" /><input type="submit" value="Search" /></td>
      </tr>
     </table>
     </form>
<?php
  }
  else
    echo '<p>None of your pets may be signed up for park events at this time.<i>  (Dead pets and pets in wereform cannot participate in park events.)</i></p>';
}
else
  echo '<p>You do not have any pets!</p>';
  
if($searched)
{
  echo '     <h5>Events</h5>';

  if(count($event_results) == 0)
    echo '<p>No matching events were found.</p>';
  else
  {
    if($_GET['action'] != 'viewall')
      echo '     <p>Found ' . $event_count . ' matching event' . ($event_count != 1 ? 's' : '') . '.</p>';
    else
      echo '     <p>Browsing all ' . $event_count . ' waiting events.  Use search to narrow results.</p>';

    $page_list = paginate($pages, $page, $url . '?' . $urlparam . '&page=%s');
    echo $page_list;
?>
<form action="/parkaction2.php?<?= $urlparam ?>" method="post">
<table id="eventlisting">
 <tr class="titlerow">
  <th rowspan="2"></th>
  <th rowspan="2">Name</th>
  <th rowspan="2">Host</th>
  <th rowspan="2">Type <?= $event_sort ?></th>
  <th rowspan="2" class="centered"><nobr>Level Range <?= $level_sort ?></nobr></th>
  <th rowspan="2" class="centered"><nobr>Fee <?= $fee_sort ?></nobr></th>
  <th rowspan="2" class="centered">Prizes</th>
  <th colspan="2" class="centered" style="padding-bottom:0px;">Pets <?= $size_sort ?></th>
  <th rowspan="2" class="centered">Sign Up*</th>
 </tr>
 <tr class="titlerow">
  <th style="padding-top:0px;">Needed</th>
  <th style="padding-top:0px;">Attending</th>
 </tr>
<?php
  $rowclass = begin_row_class();

  foreach($event_results as $this_event)
  {
    $host = get_user_byuser($this_event['host'], 'display');

    $participants = array();
    if(strlen($this_event['participants']) > 0)
      $participants = explode(',', $this_event['participants']);

    if(strlen($this_event['prizes']) > 0)
      $prizes = explode(',', $this_event['prizes']);
    else
      $prizes = array();
      
    if($this_event['event'] == 'swim')
      $type_desc = '<span style="font-weight:bold;">' . $EVENT_TYPES[$this_event['event']] . '</span>';
    else
      $type_desc = $EVENT_TYPES[$this_event['event']];
?>
 <tr class="<?= $rowclass ?>">
  <td><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/events/<?= $this_event['graphic'] ?>" /></td>
  <td><a href="eventdetails.php?idnum=<?= $this_event['idnum'] ?>"><?= format_text($this_event['name']) ?></a></td>
  <td><?= ($host === false ? '<i class="dim">[departed]</i>' : resident_link($host['display'])) ?></td>
  <td><nobr><?= $type_desc ?><a href="<?= $EVENT_HELP_PAGES_BY_TYPE[$this_event['event']] ?>" class="help">?</a></nobr></td>
  <td class="centered"><?= $this_event['minlevel'] ?> - <?= $this_event['maxlevel'] ?></td>
  <td class="centered"><?= $this_event['fee'] ?><span class="money">m</span></td>
  <td class="centered">
   <?php
    if(count($prizes) == 0)
      echo '(none)';
    else
    {
      if($this_event['event'] == 'ctf' || $this_event['event'] == 'pictio')
      {
        if(count($prizes) > $this_event['minparticipant'] / 2)
          echo "(<a href=\"#\" onclick=\"return false;\" onmouseover=\"Tip('" . str_replace(array("'", "\""), array("\\'", "&quot;"), $this_event['prizedescript']) . "')\">both teams</a>)";
        else
          echo "(<a href=\"#\" onclick=\"return false;\" onmouseover=\"Tip('" . str_replace(array("'", "\""), array("\\'", "&quot;"), $this_event['prizedescript']) . "')\">winning team</a>)";
      }
      else
        echo "<a href=\"#\" onclick=\"return false;\" onmouseover=\"Tip('" . str_replace(array("'", "\""), array("\\'", "&quot;"), $this_event['prizedescript']) . "')\">" . count($prizes) . '</a>';
    }
?>
  </td>
  <td class="centered"><?= $this_event['minparticipant'] ?></td>
  <td class="centered"><?= count($participants) ?></td>
  <td>
<?php
    if(strlen($this_event['report']) == 0)
    {
      if($this_event['host'] == $user['user'])
      {
?>
   <i>(You are hosting this event.)</i>
<?php
      }
      else
      {
        $pet_options = array();
        $signed_up = array();
        $num_pets = 0;

        if(count($userpets) > 0)
        {
          foreach($userpets as $pet)
          {
            if(strpos($this_event['participants'], '<' . $pet['idnum'] . '>') === false)
            {
              if(pet_level($pet) >= $this_event['minlevel'] && pet_level($pet) <= $this_event['maxlevel'])
              {
                if($pet['dead'] == 'no' && $pet['changed'] == 'no' && $pet['zombie'] == 'no')
                  $pet_options[$pet['idnum']] = $pet;
              }
            }
            else
              $signed_up[$pet['idnum']] = $pet['petname'];
          }
        }

        if(count($signed_up) == 0)
        {
          if(count($pet_options) > 0)
          {
?>
   <select name="e_<?= $this_event['idnum'] ?>" style="width:120px;"><option value="0"><i>&mdash;</i></option><?php
          foreach($pet_options as $this_petid=>$pet)
          {
            if($pet['park_event_hours'] >= 8)
              echo '<option value="' . $this_petid . '">' . $pet['petname'] . str_repeat(' ♦', floor($pet['park_event_hours'] / 8)) . '</option>';
            else
              echo '<option disabled="disabled">' . $pet['petname'] . '</option>';
          }
?></select>
<?php
          }
        }
        else
        {
          echo '<i>';
          $first = 1;
          $count = 1;
          foreach($signed_up as $this_petid=>$petname)
          {
            if($first == 1)
              $first = 0;
            else
            {
              if($count == count($signed_up))
                echo ' and ';
              else
                echo ', ';
            }

            echo $petname;

            $count++;
          }

          if(count($signed_up) == 1)
            echo ' is';
          else
            echo ' are';

          echo ' signed up.</i>';
        }
      }
?>
  </td>
<?php
    }
    else
    {
?>
   <i>(This event has already run.)</i>
  </td>
<?php
    }
?>
 </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
</table>
<p><input type="submit" name="submit" value="Sign Up" /></p>
</form>
<?php
    echo $page_list;
  }
}

if($showtip)
  echo '     <p><i>* Dead pets and pets in wereform cannot participate in park events.</i></p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
