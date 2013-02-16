<?php
$require_petload = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';

$rooms = take_apart(',', $house['rooms']);
foreach($rooms as $i=>$room)
  $real_rooms[$i] = 'home/' . $room;

$real_rooms[] = 'home';
$real_rooms[] = 'storage';
$real_rooms[] = 'storage/locked';
$real_rooms[] = 'storage/mystore';

if($_GET['action'] == 'deleteall')
{
  $command = 'DELETE FROM psypets_autosort WHERE userid=' . $user['idnum'];
  $database->FetchNone($command, 'deleting all autosort rules for player #' . $user['idnum']);
}

$command = 'SELECT * FROM psypets_autosort WHERE userid=' . $user['idnum'] . ' ORDER BY room ASC';
$rules = $database->FetchMultiple($command, 'fetching rules');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Configure Auto-sorter</title>
<?php include "commons/head.php"; ?>
  <script type="text/javascript">
   var highlighted_row = '';
  
   function CreateRule()
   {
     document.getElementById('submitbutton').innerHTML = '<img src="gfx/throbber.gif" />';
   
     room = document.getElementById('room').value;
     item_name = document.getElementById('itemname').value;

     $.ajax({
       type: 'POST',
       url: 'ajax_autosort_add.php',
       data: 'itemname=' + item_name + '&target=' + room,
       success: function(data) { UpdateRules(data); }
     });
   }

   function DeleteRule(idnum)
   {
     document.getElementById('delete_' + idnum).innerHTML = '<img src="gfx/throbber.gif" />';

     $.ajax({
       type: 'POST',
       url: 'ajax_autosort_delete.php',
       data: 'ruleid=' + idnum,
       success: function(data) { UpdateRules(data); }
     });
   }

   function UpdateRules(data)
   {
     var results = data.split("\n");

     if(highlighted_row != '')
       document.getElementById(highlighted_row).style.backgroundColor = '#fff';

     for(i in results)
     {
       var command = results[i].substr(0, 8);
       var value = results[i].substr(8);

       if(command == 'remrule:')
       {
         var node = document.getElementById('rule_' + value);
         node.parentNode.removeChild(node);
       }
       else if(command == 'addrule:')
       {
         document.getElementById('itemname').style.backgroundColor = '#fff';
         document.getElementById('room').style.backgroundColor = '#fff';
         
         var values = value.split(';');

         var new_rule = document.getElementById('newrule').cloneNode('true');
         new_rule.setAttribute('id', 'rule_' + values[0]);
         new_rule.cells[0].innerHTML = '<span id="delete_' + values[0] + '"><a href="#" onclick="DeleteRule(' + values[0] + '); return false;"><b class="failure">X</b></a></span>';
         new_rule.cells[1].innerHTML = values[1];
         new_rule.cells[2].innerHTML = values[2];

         document.getElementById('rules').insertBefore(new_rule, document.getElementById('newrule'));
       }
       else if(command == 'failure:')
       {
         if(value == 'item')
           document.getElementById('itemname').style.backgroundColor = '#fcc';
         else if(value == 'room')
           document.getElementById('room').style.backgroundColor = '#fcc';
       }
       else if(command == 'hilight:')
       {
         document.getElementById('rule_' + value).style.backgroundColor = '#fcc';
         document.getElementById('itemname').style.backgroundColor = '#ff8';

         highlighted_row = 'rule_' + value;
       }
     }

     document.getElementById('submitbutton').innerHTML = '<input type="submit" onclick="CreateRule(); return false;" value="Add" />';
   }
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Configure Auto-sorter</h4>
<ul>
 <li><a href="autosort.php?applyto=home">Run auto-sorter on Common Room</a></li>
 <li><a href="autosort.php?applyto=storage/incoming">Run auto-sorter on Incoming</a></li>
 <li><i class="dim">You may run the auto-sorter on any room, by visiting that room, and clicking the "auto-sort" link.</i></li>
</ul>
<p>Create a list of items you want to move, and the rooms you want to move those items to.  The list will be saved, so you can perform the move actions again at a later time with a single click!</p>
<form action="" method="" onsubmit="CreateRule(); return false;">
<table>
 <thead>
  <tr class="titlerow"><th></th><th>Item</th><th>Room</th></tr>
 </thead>
 <tbody id="rules">
<?php
foreach($rules as $rule)
{
?>
  <tr id="rule_<?= $rule['idnum'] ?>">
   <td><span id="delete_<?= $rule['idnum'] ?>"><a href="#" onclick="DeleteRule(<?= $rule['idnum'] ?>); return false;"><b class="failure">X</b></a></span></td>
   <td><?= $rule['itemname'] ?></td>
   <td><?= (!in_array($rule['room'], $real_rooms) && $rule['room'] != 'home') ? '<s>' . $rule['room'] . '</s>' : $rule['room'] ?></td>
  </tr>
<?php
}
?>
  <tr id="newrule">
   <td></td>
   <td><input type="text" name="itemname" id="itemname" maxlength="64" /></td>
   <td><select name="room" id="room">
    <option value="storage">Storage</option>
    <option value="storage/locked">Locked Storage</option>
    <option value="storage/mystore">My Store</option>
    <option value="home">Common</option>
<?php
foreach($rooms as $i=>$room)
  echo '<option value="' . $real_rooms[$i] . '">' . $room . '</option>';
?>
   </select> <span id="submitbutton"><input type="submit" onclick="CreateRule(); return false;" value="Add" /></span></td>
  </tr>
 </tbody>
</table>
</form>
<ul>
 <li><a href="autosort.php?applyto=home">Run auto-sorter on Common Room</a></li>
 <li><a href="autosort.php?applyto=storage/incoming">Run auto-sorter on Incoming</a></li>
 <li><i class="dim">You may run the auto-sorter on any room, by visiting that room, and clicking the "auto-sort" link.</i></li>
</ul>
<p><i>(The rules are sorted here by destination room, however new rules will always appear at the end of the list until the page is reloaded.)</i></p>
<ul>
 <li><a href="autosort_edit.php?action=deleteall" onclick="return confirm('Really-really?  Delete them ALL?');">Delete all auto-sorter rules!</a></li>
</ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
