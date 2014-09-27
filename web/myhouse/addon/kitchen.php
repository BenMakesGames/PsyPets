<?php
require_once 'commons/init.php';

$whereat = 'home';
$wiki = 'Kitchen';
$THIS_ROOM = 'Kitchen';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/kitchenlib.php';

if(!addon_exists($house, 'Kitchen'))
{
  header('Location: /myhouse.php');
  exit();
}

if(array_key_exists('search', $_POST))
  $item_search = trim($_POST['search']);
else if(array_key_exists('search', $_GET))
  $item_search = trim($_GET['search']);

$page = (int)$_GET['page'];

if(strlen($item_search) > 0)
{
  $known_recipes_count = search_known_recipes_count($user['idnum'], $item_search);
  $num_pages = ceil($known_recipes_count / 20);

  if($page < 1 || $page > $num_pages)
    $page = 1;

  $known_recipes = search_known_recipes($user['idnum'], $item_search, $page);

  $page_list = paginate($num_pages, $page, '?search=' . $item_search . '&amp;page=%s');
}
else
{
  $known_recipes_count = get_all_known_recipes_count($user['idnum']);
  $num_pages = ceil($known_recipes_count / 20);

  if($page < 1 || $page > $num_pages)
    $page = 1;

  $known_recipes = get_known_recipes($user['idnum'], $page);

  $page_list = paginate($num_pages, $page, '?page=%s');
}

$total_recipes = get_total_recipes();
$standard_recipes_known = get_known_standard_recipes_count($user['idnum']);

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Kitchen</title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript">
  $(function() {
    $('.favorite-recipe').click(function() {
      var T = $(this);
    
      $.post(
        '/myhouse/addon/kitchen_favorite.php',
        { recipe: parseInt(T.attr('data-id')) },
        function(data) { if(!data.error) $('#favorite-recipe-' + data.id).html(data.html); },
        'json'
      );
    
      return false;
    });
  });
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Kitchen</h4>
<?php
echo $message;

room_display($house);
?>
<!--
<ul class="tabbed">
 <li class="activetab"><a href="/myhouse/addon/kitchen.php">All Known Recipes</a></li>
 <li><a href="/myhouse/addon/kitchen_favorites.php">Favorite Recipes</a></li>
</ul>
-->
<?php
echo '<h5>Known Recipes (', $standard_recipes_known, ' / ', $total_recipes;

if($known_recipes_count > $standard_recipes_known)
  echo ', and ', ($known_recipes_count - $standard_recipes_known), ' secret recipes';

echo ')</h5>';

if($known_recipes_count > 0)
{
  echo '<form method="post"><p><input name="search" value="' . $item_search . '" /> <input type="submit" name="submit" value="Search" /></p></form>';

  echo $page_list;

  echo '
    <div style="float:right; width:250px; border:1px dashed #ccc; padding: 5px; margin-left:1em;">Recipes marked with a &#9733; always appear first when preparing food from "show favorite recipes" at home.</div>
    <table>
     <thead>
      <tr><th></th><th>Ingredients</th><th>Makes</th><th class="centered">Times Prepared</th><th class="centered">First Prepared</th></tr>
     </thead>
     <tbody>
  ';

  $rowclass = begin_row_class();

  foreach($known_recipes as $recipe)
  {
    $makes = explode(',', $recipe['makes']);
    $makes_stack = array();
    $makes_list = array();
    
    foreach($makes as $itemname)
      $makes_stack[$itemname]++;
    
    foreach($makes_stack as $itemname=>$qty)
    {
      if($qty == 1)
        $makes_list[] = $itemname;
      else
        $makes_list[] = $qty . '&times; ' . $itemname;
    }
    
    $star = ($recipe['favorite'] == 'yes' ? '&#9733;' : '&#9734;');
?>
      <tr class="<?= $rowclass ?>">
       <td><a href="#" class="favorite-recipe" id="favorite-recipe-<?= $recipe['idnum'] ?>" data-id="<?= $recipe['idnum'] ?>"><?= $star ?></a></td>
       <td><?= str_replace(',', ', ', $recipe['ingredients']) ?></td>
       <td><?= implode('<br />', $makes_list) ?></td>
       <td class="centered"><?= $recipe['times_prepared'] ?></td>
       <td class="centered"><?= local_time_short($recipe['learned_on'], $user['timezone'], $user['daylightsavings']) ?></td>
      </tr>
<?php

    $rowclass = alt_row_class($rowclass);
  }

  echo '
     </tbody>
    </table>
  ';

  echo $page_list;
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
