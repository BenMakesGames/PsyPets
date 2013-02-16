     </div>
<?php
if($user['idnum'] > 0)
  echo '<div id="quicklinks"><a href="#mainbox">&#9650;</a> | <a href="/myhouse.php">My House</a> | <a href="/petlogs.php">Pet Logs</a> | <a href="/storage.php">Storage</a> | <a href="/incoming.php">Incoming</a> | <a href="/bank.php">Bank</a> | <a href="/fleamarket/">Flea Market</a> | <a href="/plaza.php">Plaza</a> | <a href="/sitemap.php">Site Map</a></div>';
?>
    </div>
    <div id="bottom"></div>
   </div>
   <div id="footer">
    <p><?= $SETTINGS['site_name'] ?> &copy; 2004-<?= date('Y') ?> (<a href="/meta/copyright.php">copyright information</a>, <a href="/meta/privacy.php">privacy policy</a>, <a href="/meta/termsofservice.php">terms of service</a>)</p>
   </div>
  </div>
<script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/wz_tooltip.js"></script>
<?php
if($PAGE['checkad'] === true)
{
  $texts = array(
    '<b>' . $PAGE['adlink'] . ' (size 234x60)</b><br /><span class="failure">misses your attention<br />cowers in a corner</span>',
    'You may not love the ad for ' . $PAGE['adlink'] . ',<br />but it loves yooouuuu!!',
    'You know... *sniff*... we worked so hard on the graphic for the<br />' . $PAGE['adlink'] . ' ad... *sniff*... but you just...<br />just tossed it aside!  Like it was nothing! *runs and cries*',
    'Fine, you know, whatever.  I doubt the ad<br />for ' . $PAGE['adlink'] . ' was important,<br />anyway. Why not block it? Why not block it?',
  );
?>
<script type="text/javascript">
function restore_text()
{
  $('#adbox').html('<?= $texts[array_rand($texts)] ?>');
}

if(!document.getElementById('<?= $PAGE['adname'] ?>'))
  $(function() { restore_text(); });
else if(document.getElementById('<?= $PAGE['adname'] ?>').style.display == 'none')
  $(function() { restore_text(); });
</script>
<?php
}
?>
