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

$owner = get_user_bydisplay($_GET['resident']);

$command = 'SELECT * FROM psypets_airships WHERE ownerid=' . $owner['idnum'] . ' ORDER BY name ASC';
$airships = fetch_multiple($command, 'fetching this resident\'s airships');

include 'commons/html.php';
?>
 <head>
<?php include 'commons/head.php'; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $owner['display'] ?> &gt; Airship Mooring</title>
  <style type="text/css">
   #family td
   {
     padding-left: 3em;
   }
  </style>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><?= resident_link($owner['display']) ?> &gt; Airship Mooring</h4>
<?php
$airship_count = count($airships);

if($airship_count > 0)
{
?>
     <h5>Airships (<?= $airship_count ?>)</h5>
     <table>
      <tr class="titlerow">
       <th></th><th>Chassis</th><th>Name</th>
      </tr>
<?php
  $rowclass = begin_row_class();

  foreach($airships as $airship)
  {
    $chassis = get_item_byname($airship['chassis']);

    echo '<tr class="' . $rowclass . '">' .
         '<td class="centered">' . item_display($chassis, '') . '</td>' .
         '<td>' . $chassis['itemname'] . '</td>' .
         '<td>' . airship_link($airship)  . '</td></tr>';
    
    $rowclass = alt_row_class($rowclass);
  }
?>
     </table>
<?php
}
else
  echo '<p>This Resident has no Airships.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
