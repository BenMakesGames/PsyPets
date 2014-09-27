<?php
require_once 'commons/init.php';

$whereat = 'home';
$wiki = 'Library_(add-on)';
$THIS_ROOM = 'Library';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/librarylib.php';

if(!addon_exists($house, 'Library'))
{
  header('Location: /myhouse.php');
  exit();
}

$book_count = get_library_book_count($user['idnum']);

if($book_count > 0)
{
  $num_pages = ceil($book_count / 20);

  $page = (int)$_GET['page'];

  if($page < 1 || $page > $num_pages)
    $page = 1;

  $books = get_library_books($user['idnum'], $page);

  $page_list = paginate($pages, $page, '/myhouse/addon/library.php?page=%s');
}

if($book_count == 0)
  $book_report = 'no books';
else if($book_count == 1)
  $book_report = '1 distinct book';
else
  $book_report = $book_count . ' distinct books';

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Library</title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript">
  function autofill(itemid, value)
  {
    $('#i_' + itemid).val(value);
  }
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Library (<?= $book_report ?>)</h4>
<?php
room_display($house);

if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";

if($book_count > 0)
{
  $rooms[] = 'Storage';
  $rooms[] = 'My Store';
  $rooms[] = 'Common';

  if(strlen($house['rooms']) > 0)
  {
    $m_rooms = explode(',', $house['rooms']);
    foreach($m_rooms as $room)
      $rooms[] = $room;
  }
  
  $page_list = paginate($num_pages, $page, '/myhouse/addon/library.php?page=%s');

  echo $page_list;
?>
<form action="/myhouse/addon/library_move.php?page=<?= $page ?>" method="post">
<table>
 <thead>
  <tr class="titlerow">
   <th class="centered">Quantity</th><th></th><th>Book</th><th>Action</th>
  </tr>
 </thead>
 <tbody>
<?php
  $rowclass = begin_row_class();

  $i = 0;

  foreach($books as $book)
  {
    $i++;
?>
  <tr class="<?= $rowclass ?>">
   <td><nobr><input size="3" maxlength="<?= strlen($book['quantity']) ?>" name="i_<?= $book['itemid'] ?>" id="i_<?= $book['itemid'] ?>" /> / <a href="#" onclick="autofill(<?= $book['itemid'] . ', ' . $book['quantity'] ?>); return false;"><?= $book['quantity'] ?></a></nobr></td>
   <td class="centered"><?= item_display($book, '') ?></td>
   <td><?= $book['itemname'] ?></td>
   <td><?php
    if(in_array($book['itemname'], $FORBIDDEN_BOOKS) || $book['custom'] == 'yes')
      echo '<i class="dim">This book is <strong>too dangerous</strong> to read in the Library</i>';
    else if($book['action'] != '')
    {
      list($action) = explode(';', $book['action']);
      echo '<a href="/myhouse/addon/library_read.php?id=' . $book['itemid'] . '&page=' . $page . '">' . $action . '</a>';
    }
    
    if($i == 10 && $page == 1)
      echo ', <a href="/myhouse/addon/library_batcave.php">pull</a>';
?></td>
  </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
 </tbody>
</table>
<p><input type="submit" name="submit" value="Move to" />&nbsp;<select name="room">
<?php
  foreach($rooms as $room)
    echo '<option value="' . $room . '">' . ($room{0} == '$' ? substr($room, 1) : $room) . '</option>';
?>
     </select></p>
     </form>
<?php
  echo $page_list;
}
else
  echo '<p>There are no books in your Library.  Add books by moving them to the "Library Add-on" room from any other room of your house.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
