<?php
$NO_PIRATE = true;
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/itemlib.php';
require_once 'commons/favorlib.php';

if($now_year > 2010 || ($now_year == 2010 && $now_month >= 4))
  $favor_cost = 500;
else
  $favor_cost = 500;

$book_graphics = array(
  'book/1.png', 'book/2.png', 'book/3.png', 'book/4.png', 'book/5.png', 'book/6.png',
  'book/7.png', 'book/8.png', 'book/9.png', 'book/a.png', 'book/b.png', 'book/c.png',
  'book/d.png', 'book/e.png', 'book/f.png', 'book/10.png', 'book/11.png', 'book/12.png',
  'book/13.png',
);

$printer_graphics = array(
  'printer_generic.png', 'd8printprinter.png',
);

$error_msgs = array();
if($_POST['submit'] == 'Print the Printer!' && $user['favor'] >= $favor_cost)
{
  $errored = false;
  $itemname = trim($_POST['itemname']);
  $itemtype = trim($_POST['itemtype']);
  $graphic = $_POST['graphic'];
  $printer = $_POST['printer'];
  $booktext = htmlentities(trim($_POST['booktext']));

  if($_POST['copyrightok'] != 'yes' && $_POST['copyrightok'] != 'on')
  {
    $errored = true;
    $error_msgs[] = '<span class="failure">You must agree to the brief statement regarding copyright law and your book.</span>';
  }

  if(array_search($graphic, $book_graphics) === false)
  {
    $errored = true;
    $error_msgs[] = '<span class="failure">Please pick a graphic for the book.</span>';
  }

  if(array_search($printer, $printer_graphics) === false)
  {
    $errored = true;
    $error_msgs[] = '<span class="failure">Please pick a graphic for the printer.</span>';
  }

  if(strlen($itemname) < 2 || strlen($itemname) > 48)
  {
    $errored = true;
    $error_msgs[] = '<span class="failure">You forgot to name your item (or it\'s simply too short - 2 character minimum).</span>';
  }
  else if(preg_match("/[^a-zA-Z0-9 .,&#'-]/", $itemname))
  {
    $errored = true;
    $error_msgs[] = '<span class="failure">Item names may only contain letters, numbers, spaces, and the following characters: period, comma, ampersand, pound, apostrophe, hyphen.</span>';
  }
  else
  {
    $item = get_item_byname($itemname);

    if($item !== false)
    {
      $errored = true;
      $error_msgs[] = '<span class="failure">That book name is already in use.</span>';
      
      if($user['idnum'] == 1)
      {
        var_dump($item);
        die();
      }
    }

    $item = get_item_byname($itemname . ' Printer');

    if($item !== false)
    {
      $errored = true;
      $error_msgs[] = '<span class="failure">That printer name is already in use.</span>';
    }
  }

  if(strlen($itemtype) < 4 || strlen($itemtype) > 16)
  {
    $errored = true;
    $error_msgs[] = '<span class="failure">You forgot the item type (or it\'s simply too short - 4 character minimum).</span>';
  }
  else if(preg_match("/[^a-zA-Z\/]/", $itemtype))
  {
    $errored = true;
    $error_msgs[] = '<span class="failure">The item type must only contain letters and slashes (not even spaces or numbers are okay).</span>';
  }

  if($errored === false)
  {
    $q_itemtype = quote_smart('print/book/' . $itemtype);
    $q_itemname = quote_smart($itemname);
    $q_graphic = quote_smart($graphic);

    // create the book
    $command = "INSERT INTO monster_items (`itemname`, `itemtype`, `custom`, `bulk`, `weight`, `graphic`, `recycle_for`, `value`, `rare`, `cancombine`) VALUES " .
               "($q_itemname, $q_itemtype, 'yes', '1', '1', $q_graphic, 'Paper,Black Dye', '1', 'yes', 'no')";
    $database->FetchNone($command, 'creating book item descriptor');

    $book_id = $database->InsertID();
    
    $bookfile = fopen('actions/custom/books/' . $book_id . '.php', 'w') or die('Failed to create the book item.  Ergk.');
    fwrite($bookfile, '<' . "?php\r\n");
    fwrite($bookfile, 'if($okay_to_be_here !== true)' . "\r\n");
    fwrite($bookfile, "exit();\r\n");
    fwrite($bookfile, '?' . ">\r\n");
    fwrite($bookfile, nl2br(format_text($booktext)));
    fclose($bookfile);

    $command = 'UPDATE monster_items SET action=\'Read;custom/books/' . $book_id . '.php\' WHERE idnum=' . $book_id . ' LIMIT 1';
    $database->FetchNone($command, 'updating book item; adding Read action');
    
    $q_printername = quote_smart($itemname . ' Printer');
    $q_printeraction = quote_smart('Print;machines/customprinter.php;' . $itemname);
    $q_printergraphic = quote_smart($printer);

    // create the printer
    $command = "INSERT INTO monster_items (`itemname`, `itemtype`, `custom`, `bulk`, `weight`, `graphic`, `action`, `rare`, `nosellback`) VALUES " .
               "($q_printername, 'electronic/printer', 'yes', '10', '10', $q_printergraphic, $q_printeraction, 'yes', 'yes')";
    $database->FetchNone($command, 'creating printer item descriptor');

    $id = add_inventory($user['user'], 'u:' . $user['idnum'], $itemname . ' Printer', $user['display'] . ' had this item custom-made', 'storage/incoming');

    flag_new_incoming_items($user['user']);

    spend_favor($user, $favor_cost, 'custom item - "' . $itemname . '"', $id);

    $FORCE_GIFT = true;

    $_POST = array();
    
    $step = 2;

    require_once 'commons/dailyreportlib.php';
    record_daily_report_stat('Someone Made a Printer', 1);

    add_inventory('telkoth', 'u:' . $user['idnum'], $itemname, 'Read over for copyright infringement!', 'storage/incoming');
    flag_new_incoming_items('telkoth');
  }
}

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; Favor Dispenser &gt; The Printer-Printer</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="autofavor.php">Favor Dispenser</a> &gt; The Printer-Printer</h4>
<?php
if($step == 2)
{
?>
     <p>Your custom printer, the <?= $itemname ?> Printer, has been created, and put into your Incoming.</p>
<?php
}
else
{
  if(count($error_msgs) > 0)
    echo '<ul><li>' . implode('</li><li>', $error_msgs) . '</li></ul>';

  if($error_message)
    echo "<p style=\"color:red;\">" . $error_message . "</p>\n";

  if($message)
    echo "<p style=\"color:green;\">" . $message . "</p>\n";
?>
     <p><i>The Printer-Printer prints print-printers...</i></p>
<?php
  $options[] = '<a href="/buyfavors.php">Support PsyPets; get Favor</a>';

//include 'commons/dialog_close.php';

  echo '<p>You have ' . $user['favor'] . ' Favor.  A custom printer costs ' . $favor_cost . ' Favor to create.</p>';

  if(count($options) > 0)
    echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

  if($user['favor'] >= $favor_cost)
  {
?>
     <form action="af_printerprinter2.php#preview" method="post">
     <h5>Make a Custom Printer</h5>
     <p>This printer will print a book of your making any number of times for 1 Black Dye and 1 Paper.</p>
     <p>What will the book item be called?  (The printer item you receive will be called "[book name] Printer".)</p>
     <p><input name="itemname" maxlength="32" style="width:250px;" value="<?= $_POST['itemname'] ?>" /></p>
     <p>What will the item type be?</p>
     <p>print/book/<input name="itemtype" maxlength=16" value="<?= $_POST['itemtype'] ?>" /></p>
     <p>Choose a graphic for the book:</p>
     <p><table><tr>
<?php
    foreach($book_graphics as $this_graphic)
      echo '      <td class="centered"><img src="//saffron.psypets.net/gfx/items/' . $this_graphic . '" /><br /><input type="radio" name="graphic" value="' . $this_graphic . '"' . ($graphic == $this_graphic ? ' checked' : '') . ' /></td>' . "\n";
?>
     </tr></table></p>
     <p>Choose a graphic for the <em>printer</em>:</p>
     <p><table><tr>
<?php
    foreach($printer_graphics as $this_graphic)
      echo '      <td class="centered"><img src="//saffron.psypets.net/gfx/items/' . $this_graphic . '" /><br /><input type="radio" name="printer" value="' . $this_graphic . '"' . ($printer == $this_graphic ? ' checked' : '') . ' /></td>' . "\n";
?>
     </tr></table></p>
<?php
    if($_POST['submit'] == 'Preview')
    {
      $booktext = htmlentities(trim($_POST['booktext']));
?>
<h6 id="preview">Preview</h5>
<p><table class="preview"><tr><td>
<?= format_text($_POST['booktext'], false) ?>
</td></tr></table></p>
<?php
    }
?>
     <p>Enter the text for the book.  PsyPets markup is allowed; HTML is not.  The maximum length is 64K of text (65,536 characters).</p>
     <p><strong>Warning:</strong>  Once created, you can not edit the book text.  Take careful consideration of this fact, especially if you choose to link to any images (if the image were later moved or deleted it would leave an ugly <img src="doesnotexist.png"> icon in its place).</p>
     <p><textarea name="booktext" cols="50" style="width:500px;" rows="20"><?= $booktext ?></textarea></p>
     <p><input type="checkbox" name="copyrightok" id="copyrightok" /> <label for="copyrightok">The above text is either: your original creation, used with permission by its original author, or public domain.  If the text is found to be copyright-infringing, the printer and any book items created by it may be modified or deleted (the Favor spent to create the printer would be refunded, however you may be forbidden from creating more book printers in the future).</p>
     <p><input type="submit" name="submit" value="Preview" /> <input type="submit" name="submit" value="Print the Printer!" class="bigbutton" /></p>
     <form>
<?php
  }
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
