<?php
$whereat = 'petmarket';
$wiki = 'Pet_Market';
$require_petload = 'no';

require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/petmarketlib.php';
require_once 'commons/messages.php';
require_once 'commons/userlib.php';
require_once 'commons/petlib.php';

if($NO_PVP)
{
  header('Location: /lostdata.php');
  exit();
}

if($user['breeder'] != 'yes')
{
  header('Location: /breederslicense.php?dialog=2');
  exit();
}

$SORTABLE = array(
  1 => 'idnum ASC',
  2 => 'idnum DESC',
  3 => 'price ASC',
  4 => 'price DESC',
);

$extra_wheres = array();

if($_GET['action'] == 'search')
{
  $searchurl = '&action=search';
  
  $minlevel = (int)$_GET['minlevel'];
  $maxlevel = (int)$_GET['maxlevel'];
  $gender = $_GET['gender'];
  $prolific = $_GET['prolific'];
  $owner = urldecode($_GET['owner']);

  if(strlen($owner) > 0)
  {
    $ownedby = get_user_bydisplay($owner, 'user');
    if($ownedby !== false)
    {
      $extra_wheres[] = 'p.user=' . quote_smart($ownedby['user']);
      $searchurl .= '&owner=' . $_GET['owner'];
    }
    else
      $search_message = '<span class="failure">There is no resident by the name "' . $owner . '".</span>';
  }
  else
    $owner = '';

  if($minlevel > 0)
  {
    $extra_wheres[] = '(`' . implode('`+`', $PET_SKILLS) . '`)>=' . (int)$_GET['minlevel'];
    $searchurl .= '&minlevel=' . $minlevel;
  }
  else
    $minlevel = '';

  if($maxlevel > 0)
  {
    $extra_wheres[] = '(`' . implode('`+`', $PET_SKILLS) . '`)<=' . (int)$_GET['maxlevel'];
    $searchurl .= '&maxlevel=' . $maxlevel;
  }
  else
    $maxlevel = '';

  if($gender == 'male' || $gender == 'female')
  {
    $extra_wheres[] = 'p.gender=' . quote_smart($gender);
    $searchurl .= '&gender=' . $gender;
  }
  else
    $gender = 'any';

  if($prolific == 'yes' || $prolific == 'no')
  {
    $extra_wheres[] = 'p.prolific=' . quote_smart($prolific);
    $searchurl .= '&prolific=' . $prolific;
  }
  else
    $prolific = 'any';
}
else
{
  $gender = 'any';
  $prolific = 'any';
}

if(count($extra_wheres) > 0)
  $base_command = 'SELECT m.* FROM psypets_pet_market AS m JOIN monster_pets AS p ON m.petid=p.idnum WHERE m.expiration>=' . $now . ' AND ' . implode(' AND ', $extra_wheres);
else
  $base_command = 'SELECT m.* FROM psypets_pet_market AS m WHERE m.expiration>' . $now;


$command = str_replace('SELECT m.*', 'SELECT COUNT(*)', $base_command);
$data = $database->FetchSingle($command, 'fetching pet market pet count');

$num_pets = (int)$data['COUNT(*)'];

if($num_pets > 0)
{
  $num_pages = ceil($num_pets / 10);

  $page = (int)$_GET['page'];
  $sort = (int)$_GET['sort'];
  
  if(!array_key_exists($sort, $SORTABLE))
    $sort = 1;

  if($page < 1)
    $page = 1;
  else if($page > $num_pages)
    $page = $num_pages;

  $page_list = paginate($num_pages, $page, 'petmarket.php?page=%s' . $searchurl . '&sort=' . $sort);

  $command = $base_command . ' ORDER BY m.' . $SORTABLE[$sort] . ' LIMIT ' . (($page - 1) * 10) . ',10';
  $sale_pets = $database->FetchMultiple($command, 'fetching pet market pets');
}

if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Pet Market</title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/adrate3.js"></script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : '') ?>
<?php include 'commons/bcmessage2.php'; ?>
     <h4>Pet Market</h4>
     <?= ($message ? "<p><span class=\"success\">$message</span></p>" : "") ?>
     <?= ($error_message ? '<p>' . $error_message . '</p>' : '') ?>
     <ul><li><a href="petmarket_list.php">List pet for sale</a></li></ul>
<?php
if($num_pets == 0)
{
  if($_GET['action'] == 'search')
  {
    include 'commons/petmarketsearch.php';
    echo '<p>No pets matched your search criteria.</p>';
  }
  else
    echo '<p>There are no pets listed for sale at this time.</p>';
}
else
{
  include 'commons/petmarketsearch.php';

  if($_GET['action'] == 'search')
    echo '<p>' . $num_pets . ' pet' . ($num_pets == 1 ? '' : 's') . ' matched your search critera.</p>';
  else
    echo '<p>There are ' . $num_pets . ' pet' . ($num_pets == 1 ? '' : 's') . ' listed for sale at this time.</p>';

  if($sort == 1)
    $expiry = '<a href="?page=' . $page . $searchurl . '&sort=2">&#9660;</a>';
  else if($sort == 2)
    $expiry = '<a href="?page=' . $page . $searchurl . '&sort=1">&#9650;</a>';
  else
    $expiry = '<a href="?page=' . $page . $searchurl . '&sort=1">&#9661;</a>';

  if($sort == 3)
    $price = '<a href="?page=' . $page . $searchurl . '&sort=4">&#9660;</a>';
  else if($sort == 4)
    $price = '<a href="?page=' . $page . $searchurl . '&sort=3">&#9650;</a>';
  else
    $price = '<a href="?page=' . $page . $searchurl . '&sort=3">&#9661;</a>';
  
?>
     <?= $page_list ?>
     <table>
      <tr class="titlerow">
       <th></th>
       <th>Name</th>
       <th></th>
       <th>Details</th>
       <th>Owner</th>
       <th class="centered">Sale Expires <?= $expiry ?></th>
       <th class="centered">Buy <?= $price ?></th>
      </tr>
<?php
  $rowclass = begin_row_class();

  foreach($sale_pets as $sale)
  {
    $pet = get_pet_byid($sale['petid']);
    $owner = get_user_byid($sale['ownerid'], 'user,display');

    if($owner === false || $pet === false || $pet['user'] != $owner['user'])
    {
			if($owner === false)
				$reason = 'no owner';
			else if($pet === false)
				$reason = 'no pet';
			else if($pet['user'] != $owner['user'])
				$reason = 'switched owner';
?>
      <tr class="<?= $rowclass ?>">
       <td></td>
       <td colspan="4" data-reason="<?= $reason ?>; pet #<?= $pet['idnum'] ?>">Broken pet sale #<?= $sale['idnum'] ?></td>
       <td class="centered">In <?= Duration($sale['expiration'] - $now, 2) ?></td>
       <td></td>
      </tr>
<?php
    }
    else
    {
      $details = pet_market_details($pet);

      if($sale['ownerid'] != $user['idnum'])
        $action = '<form action="/petmarket_buy.php?id=' . $sale['idnum'] . '" method="post"><input type="submit" value="' . $sale['price'] . 'm"' . ($sale['price'] > $user['money'] ? ' disabled' : '') . ' /></form>';
      else
        $action = '<input type="button" value="' . $sale['price'] . 'm" disabled /><br /><a href="petmarket_unlist.php?id=' . $sale['idnum'] . '">remove listing</a>';
?>
      <tr class="<?= $rowclass ?>">
       <td><a href="/petprofile.php?petid=<?= $pet['idnum'] ?>"><?= pet_graphic($pet) ?></a></td>
       <td><?= $pet['petname'] ?></td>
       <td><a href="salepetlogs.php?id=<?= $sale['idnum'] ?>"><img src="gfx/petlog_new.png" width="18" height="16" alt="(view pet logs)" border="0" /></a></td>
       <td><ul class="plainlist"><li><?= implode('</li><li>', $details) ?></li></ul></td>
       <td><?= resident_link($owner['display']) ?>
       <td class="centered">In <?= Duration($sale['expiration'] - $now, 2) ?></td>
       <td class="centered"><?= $action ?></td>
      </tr>
<?php
    }

    $rowclass = alt_row_class($rowclass);
  }
  
  echo
    '</table>',
    $page_list
  ;
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
