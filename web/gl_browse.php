<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
/*
if($user['admin']['clairvoyant'] == 'yes')
  $command = 'SELECT * FROM monster_graphicslibrary ORDER BY recipient DESC,idnum DESC';
else*/
  $command = 'SELECT * FROM monster_graphicslibrary WHERE recipient=0 OR recipient=' . $user['idnum'] . ' OR uploader=' . $user['idnum'] . ' ORDER BY recipient DESC,idnum DESC';

$count_command = str_replace('*', 'COUNT(idnum) AS c', $command);
$data = $database->FetchSingle($count_command, 'fetching number of graphics');
$num_graphics = (int)$data['c'];


if($num_graphics > 0)
{
  $num_pages = ceil($num_graphics / 40);
  $page = (int)$_GET['page'];

  if($page < 1 || $page > $num_pages)
    $page = 1;

  $page_list = paginate($num_pages, $page, 'gl_browse.php?page=%s');

	$graphics = $database->FetchMultiple($command . ' LIMIT ' . (($page - 1) * 40) . ',40');
 
  $globjects = array();

  foreach($graphics as $data)
  {
    $uploader = get_user_byid($data['uploader']);
    $data['uploader_resident'] = $uploader;

    if($data['recipient'] > 0)
    {
      $recipient = get_user_byid($data['recipient']);
      $data['recipient_resident'] = $recipient;
    }

    $globjects[] = $data;
  }
}

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; Library &gt; Graphics Library</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="library.php">Library</a> &gt; Graphics Library</h4>
     <ul class="tabbed">
      <li><a href="library.php">Information</a></li>
      <li><a href="badgedb.php">Badge Archive</a></li>
      <li class="activetab"><a href="gl_browse.php">Graphics Library</a></li>
     </ul>
<?php
$options = array();

echo '<a href="/npcprofile.php?npc=Marian+Witford"><img src="//saffron.psypets.net/gfx/npcs/marian-the-librarian.png" align="right" width="350" height="350" alt="(Marian the Librarian)" /></a>';

include 'commons/dialog_open.php';

if($_GET['dialog'] == 2)
{
?>
<p>Lots of things!  For example, if you have Nina forge a custom item for you at the <a href="af_combinationstation3.php">Combination Station</a>, she'll model its appearance based on your choice of a graphic from this library.</p>
<p>I also overheard some HERG scientists talking about genetic experimentation with the PsyPets.  They can apparently change a pet's appearance, which again is picked from this library.</p>
<p>Check out the <a href="autofavor.php">Favor Dispenser</a> for a complete list.</p>
<?php
  $options[] = '<a href="gl_browse.php?dialog=5">Ask why the graphics are shown twice</a>';
}
else if($_GET['dialog'] == 3)
{
?>
<p>Hm... actually, I'm afraid I can't let you upload anything to the Graphics Library.  It looks like you've been put on the list of people who repeatedly abuse it.</p>
<p>If you believe you were put on this list by error, please contact <a href="admincontact.php">an administrator</a>.</p>
<?php
}
else if($_GET['dialog'] == 4)
{
?>
<p>Success!  You should see your image listed below.</p>
<?php
}
else if($_GET['dialog'] == 5)
{
?>
<p>All images are displayed twice, each on a different background, so you can see how the image looks on both.  Sometimes people upload graphics without any kind of transparency, and so there's a big nasty white block around it.  This will look bad in your house inventory, so watch out!</p>
<?php
  $options[] = '<a href="gl_browse.php?dialog=2">Ask what the graphics are used for</a>';
}
else if($_GET['dialog'] == 6)
{
?>
<p>Your image has been removed from the Graphics Library.</p>
<?php
}
else
{
?>
<p>The Graphics Library contains images provided by Residents such as yourself.  Anyone can contribute!</p>
<p>If you believe one of the graphics below violates copyright law, do not hesitate to contact <a href="admincontact.php">an administrator</a>.</p>
<?php
  $options[] = '<a href="gl_browse.php?dialog=2">Ask what the graphics are used for</a>';
  $options[] = '<a href="gl_browse.php?dialog=5">Ask why the graphics are shown multiple times</a>';
}

include 'commons/dialog_close.php';

$options[] = '<a href="gl_upload.php">Upload your own graphic</a>';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';
?>
<h5 style="clear:both;">Browse</h5>
<?php
if($num_graphics > 0)
{
  if($num_pages > 1)
    echo $page_list;
?>
     <table>
<?php
  $itemcount = 0;
  foreach($globjects as $graphic)
  {
    if($itemcount % 4 == 0)
    {
      if($itemcount > 0)
        echo "</tr>\n";
      echo "<tr>\n";
    }

    echo '<td valign="top"><table><tr>' .
         "   <td><img src=\"" . $graphic["url"] . "\" /></td><td bgcolor=\"#888\"><img src=\"" . $graphic["url"] . "\" /></td>\n";

    echo "  </tr></table>\n" .
         '  <p><i>' . $graphic['title'] . '</i> by ' . $graphic['author'] . "<br />\n" .
         '  uploaded by ' . resident_link($graphic['uploader_resident']['display']);

    if($graphic['recipient'] > 0)
      echo ' for ' . resident_link($graphic['recipient_resident']['display']);

    echo "<br />\n";

    if($graphic['h'] == 48)
      echo '  (pet/avatar graphic)<br />';
    else if($graphic['h'] == 32)
      echo '  (item graphic)<br />';

    if($graphic['uploader'] == $user['idnum'])
      echo '</p><form action="gl_delete.php?page=' . $page . '" method="post"><p><input type="hidden" name="id" value="' . $graphic['idnum'] . '" /><input type="submit" value="Delete" /></p></form>';

    echo '</p></td>';

    $itemcount++;
  }
?>
      </tr>
     </table>
<?php
  if($num_pages > 1)
    echo $page_list;
}
else
  echo '<p>No graphics are available to you at this time.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
