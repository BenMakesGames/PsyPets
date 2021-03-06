<?php
$style_layout = (array_key_exists($user['style_layout'], $SITE_LAYOUTS) ? $user['style_layout'] : 'default');
$style_color = (array_key_exists($user['style_color'], $SITE_COLORS) ? $user['style_color'] : 'telkoth');

$commons_style_version = 24;
$layout_style_version = 46;
$color_style_version = 24;

$pp_js_version = 19;
$mm_js_version = '-pp11';
/*
if($user['idnum'] == 1)
{
  $commons_style_version = 13;
}
*/
?>
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="author" content="<?= $SETTINGS['author_real_name'] ?>" />
  <meta name="designer" content="<?= $SETTINGS['author_real_name'] ?>" />
  <link rel="shortcut icon" href="/favicon.ico" type="image/ico" />
  <link rel="copyright" href="/meta/copyright.php" title="Copyright Information" />
  <link rel="privacy" href="/meta/privacy.php" title="Privacy Policy" />
  <link rel="tos" href="/meta/termsofservice.php" title="Terms of Service" />
  <link rel="help" href="/help/" title="Help" />
  <link rel="sitemap" href="/sitemap.php" title="Site Map" />
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
if($user['idnum'] == 0)
{
  $backdrops = array(
    'bluestars.png', 'clouds.png', 'chemistry.png', 'floral.png', 'whitestripes.png',
    'icicle.png', 'stars.png', 'floral_inverted.png', 'pinksicle.png', 'gypsygarden.png',
    'orangesicle.png',
  );

  if(array_key_exists($_GET['bg'], $backdrops))
    $background_image = $backdrops[$_GET['bg']];
  else
    $background_image = $backdrops[(time() / (24 * 60 * 60)) % count($backdrops)];
}
else
  $background_image = $user['style_background'];
?>
  <style type="text/css">
  body { background-image: url(//<?= $SETTINGS['static_domain'] ?>/gfx/backdrops/<?= $background_image ?>); }
  </style>
<?php /* include 'commons/google_analytics.php'; */ ?>
