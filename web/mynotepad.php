<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/notepadlib.php';
require_once 'commons/questlib.php';

$notepad_tutorial_quest = get_quest_value($user['idnum'], 'tutorial: my notepad');
if($notepad_tutorial_quest === false)
  $no_tip = true;

if(array_key_exists('sort', $_GET))
	$sort = (int)$_GET['sort'];
else
	$sort = 1;

$mod_sort = '<a href="mynotepad.php?sort=1">&#9661;</a>';
$cat_sort = '<a href="mynotepad.php?sort=2">&#9661;</a>';
$cre_sort = '<a href="mynotepad.php?sort=0">&#9661;</a>';
$tit_sort = '<a href="mynotepad.php?sort=3">&#9661;</a>';

if($sort == 2)
{
  $notes = get_notes_sort_bycategory($user['idnum']);
  $cat_sort = '&#9660;';
}
else if($sort == 3)
{
  $notes = get_notes_sort_bytitle($user['idnum']);
  $tit_sort = '&#9660;';
}
else if($sort == 0)
{
  $notes = get_notes_sort_bycreation($user['idnum']);
  $cre_sort = '&#9660;';
}
else
{
  $notes = get_notes_sort_bymodification($user['idnum']);
  $mod_sort = '&#9660;';
  $sort = 1;
}

if($_POST['action'] == 'Delete')
{
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; My Notepad</title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript">
   function delete_note(id)
   {
     if(confirm('Really delete this note?'))
     {
       $('#delete' + id).html('<img src="/gfx/throbber.gif" width="16" height="16" />');

       $.ajax({
         type: 'POST',
         url: 'ajax_delete_note.php',
         data: 'id=' + id,
         success: function(msg)
         {
           if(msg == 'success')
             $('#note' + id).remove();
           else
           {
             $('#delete' + id).html('?');
             alert(msg);
           }
         }
       });
     }
   }
  </script>
 </head>
 <body>
<?php
include 'commons/header_2.php';

if($notepad_tutorial_quest === false)
{
  include 'commons/tutorial/mynotepad.php';
  add_quest_value($user['idnum'], 'tutorial: my notepad', 1);
}
?>
     <h4>My Notepad</h4>
     <ul><li><a href="/mynotepad_new.php">Write new note</a></li></ul>
<?php
if(count($notes) == 0)
  echo '<p>You have not written any notes.</p>';
else
{
  echo '
    <table>
     <tr class="titlerow">
      <th></th>
      <th></th>
      <th>Title ' . $tit_sort . '</th>
      <th>Category ' . $cat_sort . '</th>
      <th>Post Date ' . $cre_sort . '</th>
      <th>Edit Date ' . $mod_sort . '</th>
     </tr>
  ';

  $rowclass = begin_row_class();

  foreach($notes as $note)
  {
    if($note['icon'] == '')
      $icon = '';
    else
      $icon = '<img src="gfx/' . $icon . '" alt="" />';

    if($note['modifiedon'] == 0)
      $editdate = '&mdash;';
    else
      $editdate = duration($now - $note['modifiedon'], 2);

    $postdate = duration($now - $note['timestamp'], 2);

    echo '
      <tr class="' . $rowclass . '" id="note' . $note['idnum'] . '">
       <td><span id="delete' . $note['idnum'] . '" /><a href="#" onclick="delete_note(' . $note['idnum'] . '); return false;"><b style="color:red;">X</b></a></span></td>
       <td>' . $icon . '</td>
       <td><a href="/mynotepad_read.php?id=' . $note['idnum'] . '">' . $note['title'] . '</a></td>
       <td>' . $note['category'] . '</td>
       <td>' . $postdate . '</td>
       <td>' . $editdate . '</td>
      </tr>
    ';

    $rowclass = alt_row_class($rowclass);
  }

  echo '
    </table>
  ';
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
