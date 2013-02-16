<?php
$_title_items_ = array(
  'item1.png', 'item2.png', 'item3.png',
);

if($now_month == 12 || $now_month == 1)
  $_title_ = 'sparkle_winter';
else
  $_title_ = 'sparkle';

$_title_item_ = $_title_items_[array_rand($_title_items_)];
?>
  <script type="text/javascript">
  if(window.XMLHttpRequest || window.ActiveXObject)
    document.body.className += ' js-enabled';
  </script>
  <div id="mainbox">
   <div id="title"><div id="randomtitleimage"><div><a href="/"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/<?= $_title_item_ ?>" alt="" /></a></div></div><a href="/"><img src="/gfx/title_<?= $_title_ ?>.png" height="60" width="512" alt="<?= $SETTINGS['site_name'] ?>" /></a></div>
   <div id="adbox"><?= get_ad_text() ?></div>
   <div id="contentbox">
    <div id="main">
     <div id="me">
<?php require /*FRAMEWORK_ROOT .*/ 'views/_template/me.php'; ?>
     </div>
     <div id="content" class="<?= $CONTENT_CLASS ?>" style="<?= $CONTENT_STYLE ?>">
     <div id="jsnotify" class="robots-nocontent">
      <p>Many aspects of <?= $SETTINGS['site_name'] ?> require JavaScript, but it looks like your browser either doesn't support JavaScript, or JavaScript's been disabled!</p>
      <ul class="nomargin">
       <li><p>If you're using a browser without JavaScript support, or with poor JavaScript support, you should probably switch browsers for playing <?= $SETTINGS['site_name'] ?>.</p></li>
       <li><p>If you've disabled JavaScript within your browser, please enable it while playing <?= $SETTINGS['site_name'] ?>.</p></li>
       <li><p class="nomargin">If you're using NoScript, or a similar plugin, or some other internet security software that blocks JavaScript, you should inform it that <?= $SETTINGS['site_name'] ?> is cool and awesome and trustworthy.  <a href="/meta/privacy.php">Because it is :)</a></p></li>
      </ul>
     </div>
     <div id="encyclopedia_entry_box">
     <div id="encyclopedia_entry_close" class="titlerow draggybit"><div id="encyclopedia_entry_title"></div><a href="#" onclick="$('#encyclopedia_entry_box').fadeOut(); return false;">close window</a></div>
     <div id="encyclopedia_entry"></div>
     </div>
<?php
if(is_array($CONTENT['messages']) && count($CONTENT['messages']) > 0)
  echo '<ul class="plainlist"><li>' . implode('</li><li>', $CONTENT['messages']) . '</li></ul>';
?>