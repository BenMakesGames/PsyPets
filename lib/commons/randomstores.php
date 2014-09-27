<?php
$store_seed_time = ceil($now / (60 * 60));

$query_time = microtime(true);

$command = '
  SELECT idnum,display,storename
  FROM monster_users
  WHERE openstore=\'yes\'
  ORDER BY ((idnum*71 + ' . $store_seed_time . ')%113)
  LIMIT 10
';
$openstores = fetch_multiple($command, 'fetching pseudo-random open stores');

$query_time = microtime(true) - $query_time;

$footer_note .= '<br />Took ' . round($query_time, 4) . 's fetching 10 random open stores.';

$rowclass = begin_row_class();

echo '
  <div style="float: right; clear: right; width: 260px; padding: 4px; border: 1px solid #000; margin: 0 1em 1em 1em;">
  <h5>Consider One of These Fine Stores</h5>
  <table class="nomargin" width="100%">
   <tr class="titlerow">
    <th>Store Name</th><!--<th>Owner</th>-->
   </tr>
';

foreach($openstores as $store)
{
  echo '
    <tr class="' . $rowclass . '">
     <td><a href="//' . $SETTINGS['site_domain'] . '/userstore.php?user=' . link_safe($store['display']) . '">' . $store['storename'] . '</a></td>
     <!--<td>' . resident_link($store['display']) . '</td>-->
    </tr>
  ';
  $rowclass = alt_row_class($rowclass);
}

echo '</table></div>';
?>
