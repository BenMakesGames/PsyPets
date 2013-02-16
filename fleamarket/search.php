<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$wiki = 'Flea_Market';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/checkpet.php';

function resultsort($a, $b)
{
  $diff = $a['forsale'] - $b['forsale'];

  if($diff == 0)
    return 0;
  else if($diff < 0)
    return -1;
  else
    return 1;
}

$my_inventory = get_inventory($whereat, '', $user);
$num_inventory_items = count($my_inventory);

if(strlen($_GET['search']) > 0 && strlen($_POST['itemname']) == 0)
  $_POST['itemname'] = urldecode($_GET['search']);

$_POST['itemname'] = trim($_POST['itemname']);

$itemnames = array();

if(strlen($_POST['itemname']) == 0)
{
  header('Location: /fleamarket/');
  exit();
}
else
{
  $results = fetch_multiple('SELECT itemname FROM monster_items WHERE itemname LIKE ' . quote_smart('%' . $_POST['itemname'] . '%') . ' AND noexchange=\'no\' AND custom=\'no\' ORDER BY LENGTH(itemname) ASC LIMIT 5');

  foreach($results as $result)
    $itemnames[] = $result['itemname'];
}

if(count($itemnames) > 0)
{
  $results = array();

  $search_time = microtime(true);

  $criteria_note .= '<p>Searching for ' . $homemade_note . ' items named ';

  foreach($itemnames as $itemname)
  {
    $item_list[] = item_text_link($itemname);

    $best_offer = fetch_single('
      SELECT a.itemname,a.forsale,b.display,b.storename
      FROM
        `monster_inventory` AS a
        LEFT JOIN monster_users AS b
          ON a.user=b.user
      WHERE
        a.itemname=' . quote_smart($itemname) . ' AND
        b.openstore=\'yes\' AND
        a.forsale>0
      ORDER BY forsale ASC,RAND()
      LIMIT 1
    ');
    
    if($best_offer === false)
      $results[] = array('itemname' => $itemname, 'forsale' => 0);
    else
      $results[] = $best_offer;
  }

  $search_time = microtime(true) - $search_time;
  
  $criteria_note .= list_nice($item_list, ' or ') . $price_range_note . '.</p>';
}
else
  $found_no_items = true;

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Flea Market &gt; Item Search</title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/adrate3.js"></script>
  <script type="text/javascript">
  $(function() {
    $('.expand').click(function() {
      var me = $(this);

      if(me.html() == '...')
        return false;
    
      var itemname = $(this).attr('data-itemname');
      var target_id = $(this).attr('data-target-id');
    
      var row = $('#' + target_id);
      var row_class = me.parents('tr').attr('class');
      
      if(row.css('display') == 'none')
      {
        me.html('...');
      
        if($('#' + target_id + ' > tr').length == 0)
        {
          $.getJSON(
            '/fleamarket/getdetails.php',
            { 'itemname': itemname },
            function(data)
            {
              for(i in data)
                row.append('<tr class="' + row_class + '"><td></td><td></td><td class="righted">' + data[i].forsale + '<span class="money">m</span></td><td><a href="/userstore.php?user=' + escape(data[i].display) + '">&times;' + data[i].qty + '</a></td><td><a href="/residentprofile.php?resident=' + escape(data[i].display) + '">' + data[i].display + '</a></td></tr>');
            
              row.css({'display': 'table-row-group'});
              me.html('-');
            }
          );
        }
        else
        {
          row.css({'display': 'table-row-group'});
          me.html('-');
        }
      }
      else
      {
        row.css({'display': 'none'});
        me.html('+');
      }

      return false;
    });
  });
  </script>
  <style type="text/css">
  .expand
  {
    border: 1px solid #666;
    background-color: #fff;
    border-radius: 2px;
    padding: 0 2px;
    display: block;
    text-align: center;
    width: 10px;
  }
  </style>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
<?php include 'commons/bcmessage2.php'; ?>
<?php include 'commons/randomstores.php'; ?>
     <h4><a href="/fleamarket/">Flea Market</a> &gt; Item Search</h4>
     <ul class="tabbed">
      <li class="activetab"><a href="/fleamarket/">Flea Market</a></li>
      <li><a href="/favorstores.php">Custom Item Market</a></li>
     </ul>
     <p><?= $criteria_note ?></p>
<?php
if($found_no_items === true)
  echo '<p class="failure">No items exist which match that name (in part or in whole).</p>';
else if(count($results) > 0)
{
  $badges = get_badges_byuserid($user['idnum']);
  if($badges['egyptian'] == 'no')
  {
    set_badge($user['idnum'], 'egyptian');
    echo '<p class="success"><i>(You received the Kid in the Marketplace Badge!)</i></p>';
  }

?>
     <table>
      <thead>
      <tr class="titlerow">
       <th></th>
       <th>Item&nbsp;Name</th>
       <th class="righted">Price</th>
       <th>Store Name</th>
       <th>Owner</th>
      </tr>
      </thead>
<?php
  $rowclass = begin_row_class();
  $row_i = 0;

  foreach($results as $result)
  {
    if($result['forsale'] == 0)
    {
?>
      <tbody>
      <tr class="<?= $rowclass ?>">
       <td></td>
       <td class="dim"><?= $result['itemname'] ?></td>
       <td colspan="3" class="centered dim">not available for sale</td>
      </tr>
      </tbody>
<?php
    }
    else
    {
?>
      <tbody>
      <tr class="<?= $rowclass ?>">
       <td><a href="#" class="expand" data-itemname="<?= $result['itemname'] ?>" data-target-id="item-<?= $row_i ?>">+</a></td>
       <td><?= $result['itemname'] ?></td>
       <td class="righted"><?= $result['forsale'] ?><span class="money">m</span></td>
       <td><a href="/userstore.php?user=<?= link_safe($result['display']) ?>"><?= htmlspecialchars($result['storename']) ?></a></td>
       <td><a href="/residentprofile.php?resident=<?= link_safe($result['display']) ?>"><?= $result['display'] ?></a></td>
      </tr>
      </tbody>
      <tbody style="display:none;" id="item-<?= $row_i ?>">
      </tbody>
<?php
    }

    $rowclass = alt_row_class($rowclass);
    $row_i++;
  }
?>
     </table>
<?php
  $footer_note = '<br />Took ' . round($search_time, 4) . 's querying in the DB, and ' . round($sort_time, 4) . 's sorting the results.';
}
else
  echo '     <p class="failure">No items matching your criteria were found.</p>'
?>
     <h5 id="itemsearch">Search Again</h5>
     <form method="post">
     <p>Item name: <input name="itemname" value="<?= $_POST["itemname"] ?>" /> <input type="submit" value="Search" style="width:100px;" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
