<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/grammar.php';
  
if($_POST['action'] == 'Unleash!')
{
  $itemname = trim($_POST['item']);
  
  $target_item = $database->FetchSingle('
    SELECT idnum
    FROM monster_inventory
    WHERE
      itemname=' . quote_smart($itemname) . '
      AND location=' . quote_smart($this_inventory['location']) . '
      AND user=' . quote_smart($user['user']) . '
      AND idnum!=' . (int)$this_inventory['idnum'] . '
    LIMIT 1
  ');
  
  if($target_item !== false)
  {
    $anagramed = alphabetize_letters($itemname);
    
    $new_item_details = $database->FetchSingle('
      SELECT *
      FROM monster_items
      WHERE
        anagramname=' . quote_smart($anagramed) . '
        AND custom=\'no\'
        AND itemname!=' . quote_smart($itemname) . '
      ORDER BY RAND()
      LIMIT 1
    ');

    delete_inventory_byid($this_inventory['idnum']);
    
    if($new_item_details === false)
    {
      echo '<p>The ' . $this_inventory['itemname'] . ' quivers slightly, then puffs out of existence.</p><p>How disappointing: it doesn\'t seem to have done anything!</p>';
    }
    else
    {
      $new_item = $new_item_details['itemname'];

      $this_item_details = get_item_byname($target_item['itemname']);

      if($new_item_details['durability'] == 0)
        $new_health = 0;
      else if($this_item_details['durability'] == 0)
        $new_health = $new_item_details['durability'];
      else
        $new_health = max(1, floor($target_item['health'] * ($new_item_details['durability'] / $this_item_details['durability'])));

      $database->FetchNone('UPDATE monster_inventory SET itemname=' . quote_smart($new_item) . ',health=' . $new_health . ',changed=' . $now . ' WHERE idnum=' . $target_item['idnum'] . ' LIMIT 1');
      
      echo '<p>The ' . $this_inventory['itemname'] . ' quivers violently, then puffs out of existence!  As the last wisps of smoke clear, you see that the ' . $itemname . ' has been transformed into ' . $new_item . '!</p>';
    }

    $done = true;
  }
  else
    echo '<p class="failure"><i>(It won\'t work on that.)</i></p>';
}

if($done)
  $AGAIN_WITH_ANOTHER = true;
else
{
?>
<script type="text/javascript">
function build_select_one_from_json(element_id, items)
{
  if(items.length == 0)
  {
    $(element_id).html('<p class="failure">No items were found.</p>');
  }

  var i, item;

  var table = $('<table />');
  table.append('<thead><tr><th></th><th></th><th>Item</th><th>Quantity</th></tr></thead>');
  
  var tbody = $('<tbody />');
  
  var rowclass = 'row';
  
  for(i in items)
  {
    item = items[i];
    
    tbody.append(
      '<tr class="' + rowclass + '">' +
        '<td><input type="radio" name="item" value="' + item.itemname + '" /></td>' +
        '<td class="centered"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/' + item.graphic + '" /></td>' +
        '<td>' + item.itemname + '</td>' +
        '<td class="centered">' + item.quantity + '</td>' +
      '</tr>'
    );
    
    rowclass = (rowclass == 'row' ? 'altrow' : 'row');
  }
  
  table.append(tbody);
  
  $(element_id).html(table);
  $(element_id).append('<p><input type="submit" name="action" value="Unleash!" /></p>');
}

$(function() {
  $('#search-submit').submit(function() {
    if($('#search-name').val().length == 0)
    {
      $('#search-results').html('<p class="failure">You didn\'t enter anything to search by!</p>');
    }
    else
    {
      $('#search-results').html('<p>Searching<blink>...</blink></p>');
      $('#search-submit').attr('disabled', 'disabled');

      $.getJSON(
        '/inventory.json.php',
        {
          'location': '<?= $this_inventory['location'] ?>',
          'grouped': true,
          'join': [ 'graphics' ],
          'name-part': $('#search-name').val()
        },
        function(data)
        {
          build_select_one_from_json('#search-results', data);
          $('#search-submit').removeAttr('disabled');
        }
      );
    }

    return false;
  });
});
</script>
<p>Upon which item will you unleash the <?= $this_inventory['itemname'] ?>'s anagramizing power?</p>
<h5>Search This Room</h5>
<form id="search-submit">
<p><input type="text" id="search-name" /> <input type="submit" value="Search" /></p>
</form>
<form method="post">
<div id="search-results">
</div>
</form>
<?php
}
?>