<?php
require_once 'commons/init.php';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/grammar.php';
require_once 'commons/utility.php';
require_once 'commons/blimplib.php';

$shipid = (int)$_GET['idnum'];

$ship = get_airship_by_id($shipid);

if($ship === false)
{
  header('Location: /airship_nomore.php');
  exit();
}

$owner = get_user_byid($ship['ownerid'], 'display');

if($owner === false)
{
  header('Location: /directory.php');
  exit();
}

$chassis = get_item_byname($ship['chassis']);

include 'commons/html.php';
?>
 <head>
<?php include 'commons/head.php'; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $owner['display'] ?> &gt; Airship Mooring &gt; <?= $ship['name'] ?></title>
  <style type="text/css">
   #family td
   {
     padding-left: 3em;
   }
  </style>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <table border="0" style="padding-top:8px;">
      <tr>
       <td><?= item_display($chassis) ?></td>
       <td>
        <h4><?= resident_link($owner['display']) ?> &gt; <a href="/myhouse/addon/airshipmooring.php?resident=<?= link_safe($owner['display']) ?>">Airship Mooring</a> &gt; <?= $ship['name'] ?></h4>
<?php
/*
foreach($shipbadges as $badge=>$value)
{
  if($value == 'yes')
    echo '<img src="gfx/badges/ship/' . $badge . '.png" height="20" width="20" title="' . $SHIP_BADGE_DESC[$badge] . '" /> ';
}
*/
?>
       </td>
      </tr>
     </table>
<?php
if($user['admin']['manageaccounts'] == 'yes')
{
  require_once 'commons/sqldumpfunc.php';
?>
<h5>Admin</h5>
<?php
  dump_sql_results($ship);
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
