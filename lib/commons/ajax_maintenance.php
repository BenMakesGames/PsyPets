<?php
require_once 'settings_light.php';

$now = time();
$then = mktime(0, 20);

$diff = $then - $now;

header('HTTP/1.1 503 Service Temporarily Unavailable');
header('Status: 503 Service Temporarily Unavailable');
header('Retry-After: ' . ($diff + 30));
?>
<p><?= $SETTINGS['site_name'] ?> is down for scheduled maintenance.</p>
<p>It's upsetting, but fear not: <?= $SETTINGS['site_name'] ?> will be back in <?= ceil($diff / 60) ?> minutes <img src="gfx/emote/hee.gif" alt="" /></p>
