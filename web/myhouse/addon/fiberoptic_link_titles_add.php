<?php
require_once 'commons/init.php';

$whereat = 'home';
$wiki = 'Fiberoptic Link';
$THIS_ROOM = 'Fiberoptic Link';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';
require_once 'commons/checkpet.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/favorlib.php';

if(!addon_exists($house, 'Fiberoptic Link'))
{
  header('Location: /myhouse.php');
  exit();
}

if($user['favor'] >= 50)
{
  $command = 'SELECT idnum,title FROM psypets_fiberoptic_link_titles WHERE residentid=' . $user['idnum'];
  $titles = fetch_multiple($command, 'fetching titles');

  if(count($titles) == 0)
  {
    header('Location: /myhouse/addon/fiberoptic_link_titles.php');
    exit();
  }

  if($_POST['action'] == 'Add (50 Favor)')
  {
    $new_title = trim($_POST['title']);
    
    if($new_title == '')
      $messages[] = '<span class="failure">Oop!  You didn\'t type anything in!</span>';
    else
    {
      $okay = true;
    
      foreach($titles as $title)
      {
        if(strtolower($title['title']) == strtolower($new_title))
        {
          $okay = false;
          $messages[] = '<span class="failure">You already have the title "' . $title['title'] . '" in the Databank</span>';
          break;
        }
      }
      
      if($okay)
      {
        $command = 'INSERT INTO psypets_fiberoptic_link_titles (residentid, title) VALUES (' . $user['idnum'] . ', ' . quote_smart($new_title) . ')';
        fetch_none($command, 'adding custom title to the databank');

        spend_favor($user, 50, 'Custom title: ' . $new_title);

        require_once 'commons/dailyreportlib.php';
        record_daily_report_stat('Someone Created a Custom Title', 1);

        psymail_user('telkoth', 'psypets', $user['display'] . ' created a custom title', '{r ' . $user['display'] . '} created the title "' . $new_title . '"');

        // do it!
        $messages[] = '<span class="success">Your title has been added to the Title Databank!</span>';
        $messages[] = '<a href="/myhouse/addon/fiberoptic_link_titles.php">Return to the Title Databank</a>';
      }
    }
  }
}

$disabled = (($user['favor'] < 50) ? ' disabled="disabled"' : '');

require 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; <?= $user['display'] ?>'s House &gt; Fiberoptic Link &gt; Title Databank &gt; Add Entry</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Fiberoptic Link &gt; Title Databank &gt; Add Entry</h4>
<?php
room_display($house);
?>
<ul class="tabbed">
 <li><a href="/myhouse/addon/fiberoptic_link.php">Pixel Assembler</a></li>
 <li class="activetab"><a href="/myhouse/addon/fiberoptic_link_titles.php">Title Databank</a></li>
</ul>
<?php
if(count($messages) > 0)
  echo '<ul><li>' . implode('</li><li>', $messages) . '</li></ul>';
?>
<p>You may add an entry to the Title Databank for 50 Favor.  You currently have <?= $user['favor'] ?> Favor.</p>
<p><i>(Please avoid trademarked names and abusive language.  Such titles will be removed.  It'd also be prudent to avoid PsyPets vocabulary, as such words may become freely available in the future.)</i></p>
<form method="post">
<p><?= $user['display'] ?>, <input type="text" name="title" maxlength="56" size="40" /> <input type="submit" name="action" value="Add (50 Favor)"<?= $disabled ?> class="bigbutton" /></p>
</form>
      </tbody>
     </table>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
