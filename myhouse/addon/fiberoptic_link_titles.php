<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

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

if(!addon_exists($house, 'Fiberoptic Link'))
{
  header('Location: /myhouse.php');
  exit();
}

$command = 'SELECT idnum,title FROM psypets_fiberoptic_link_titles WHERE residentid=' . $user['idnum'];
$titles = fetch_multiple_by($command, 'idnum', 'fetching titles');

if(count($titles) == 0)
{
  $command = 'INSERT INTO psypets_fiberoptic_link_titles (residentid, title) VALUES (' . $user['idnum'] . ', \'Nothing More Than Fiction\')';
  fetch_none($command, 'adding title');
  
  header('Location: /myhouse/addon/fiberoptic_link_titles.php');
  exit();
}

if(array_key_exists('changeto', $_GET))
{
  $id = (int)$_GET['changeto'];
  
  if(array_key_exists($id, $titles))
  {
    $command = 'UPDATE monster_users SET title=' . quote_smart($titles[$id]['title']) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    fetch_none($command, 'updating title');
    
    $user['title'] = $titles[$id]['title'];
    
    $messages[] = '<span class="success">Your title has been changed!</span>';
  }
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Fiberoptic Link &gt; Title Databank</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Fiberoptic Link &gt; Title Databank</h4>
<?php
if(count($messages) > 0)
  echo '<ul><li>' . implode('</li><li>', $messages) . '</li></ul>';

room_display($house);
?>
<ul class="tabbed">
 <li><a href="/myhouse/addon/fiberoptic_link.php">Pixel Assembler</a></li>
 <li class="activetab"><a href="/myhouse/addon/fiberoptic_link_titles.php">Title Databank</a></li>
</ul>
<h5>Change Title</h5>
<ul>
<?php
foreach($titles as $title)
  echo '<li><a href="/myhouse/addon/fiberoptic_link_titles.php?changeto=' . $title['idnum'] . '">' . $user['display'] . ', ' . $title['title'] . '</a></td>';
?>
</ul>
      </tbody>
     </table>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
