<?php
$SITE_LAYOUTS = array(
  'default'  => 'Fixed-width',
  'wide'     => 'Fluid',
);

$SITE_COLORS = array(
  'telkoth'  => 'Wizard Blue',
  'ks'       => 'KS\' Green',
  'kirby'    => 'Kirby\'s Granite',
  'lune'     => 'Lune\'s Con-inspired',
  'vitriol'  => 'Vitriol\'s Gotham City',
  'hara'     => 'Hara\'s Purple',
  'redmetal' => 'Red Metal',
  'traveller'=> 'Traveller\'s Banana',
  'imakoo'   => 'Imakoo!',
);

$style_layout = (array_key_exists($_user->SiteLayout(), $SITE_LAYOUTS) ? $_user->SiteLayout() : 'default');
$style_color = (array_key_exists($_user->SiteColorTheme(), $SITE_COLORS) ? $_user->SiteColorTheme() : 'telkoth');

$commons_style_version = 24;
$layout_style_version = 46;
$color_style_version = 24;

$pp_js_version = 19;
$mm_js_version = '-pp11';
/*
if($user['idnum'] == 1)
{
  $mm_js_version = '-pp8';
}*/
header('Content-Type: text/html; charset=utf-8');
header('X-UA-Compatible: IE=edge');
?>
<!DOCTYPE html>
<html>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= build_title($_title, false) ?></title>
  <link rel="shortcut icon" href="/favicon.ico" type="image/ico" />
  <link rel="copyright" href="/meta/copyright.php" title="Copyright Information" />
  <link rel="privacy" href="/meta/privacy.php" title="Privacy Policy" />
  <link rel="tos" href="/meta/termsofservice.php" title="Terms of Service" />
  <link rel="help" href="/help/" title="Help" />
  <link rel="stylesheet" href="//<?= $SETTINGS['static_domain'] ?>/css/layout_common_<?= $commons_style_version ?>.css" />
  <link rel="stylesheet" href="//<?= $SETTINGS['static_domain'] ?>/css/layout_<?= $style_layout ?>_<?= $layout_style_version ?>.css" />
  <link rel="stylesheet" href="//<?= $SETTINGS['static_domain'] ?>/css/color_<?= $style_color ?>_<?= $color_style_version ?>.css" />
  <link rel="stylesheet" href="//<?= $SETTINGS['static_domain'] ?>/css/pp_markup.css" />
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/jquery-1.7.2.min.js"></script>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/jquery.hoverIntent.minified.js"></script>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/jquery.megamenu<?= $mm_js_version ?>.js"></script>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/jquery.watch.js"></script>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/jquery.makedraggable.js"></script>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/jquery.textarearesizer.compressed.js"></script>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/jquery.jeegoocontext.min.js"></script>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/psypetsjs<?= $pp_js_version ?>.js"></script>
<?php
if($_user->IsLoaded())
  $background_image = $_user->SiteBackground();
else
{
  $backdrops = array(
    'bluestars.png', 'clouds.png', 'chemistry.png', 'floral.png', 'whitestripes.png',
    'icicle.png', 'stars.png', 'floral_inverted.png', 'pinksicle.png', 'gypsygarden.png',
    'orangesicle.png',
  );

  $background_image = $backdrops[(time() / (24 * 60 * 60)) % count($backdrops)];
}
?>
  <style type="text/css">
  body { background-image: url('//<?= $SETTINGS['static_domain'] ?>/gfx/backdrops/<?= $background_image ?>'); }
  </style>
<?php /* include FRAMEWORK_ROOT . 'views/_template/google_analytics.php'; */ ?>
<?php
$_title_items_ = array(
  'fruit/banana.png', 'vase_uncommon.png', 'tool_thimble.png',
  'potion/undex.png', 'sword/clouds.png', 'cape/goodhope.png', 'city/losangeles.png',
  'muffin/blueberry.png', 'alcohol/pinacolada.png', 'wand/amber.png', 'book/dead.png',
  'calc_graphing.png', 'chess/blackqueen.png', 'celery_peanutbutter.png',
  'copperpipe.png', 'fish.png', 'flower/plilac.png', 'hamburger_cheese_dressed.png',
  'honey.png', 'hungry_cherub_4.png', 'instrument_azureguitar.png', 'keyhole.png',
  'kimono_starry.png', 'mac_and_cheese.png', 'megaphone.png', 'necklace/pearltriad.png',
  'onigiri_plain.png', 'pen_magicfeather.png', 'photon.png', 'ring/hactcin.png',
  'ring/nullloop.png', 'scroll1.png', 'slingshot.png', 'staff/looooove.png',
  'steak.png', 'supernova.png', 'telescope.png', 'whip_pleiades.png', 'love.png',
);

if($now_month == 12 || $now_month == 1)
  $_title_ = 'sparkle_winter';
else
  $_title_ = 'sparkle';

$_title_item_ = $_title_items_[array_rand($_title_items_)];
?>
 </head>
 <body>
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
<?php include FRAMEWORK_ROOT . 'views/_template/me.php'; ?>
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