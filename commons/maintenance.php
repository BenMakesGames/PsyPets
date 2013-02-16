<?php
require_once 'commons/settings_light.php';

$now = time();
$then = mktime(0, 20);

$diff = $then - $now;

header('HTTP/1.1 503 Service Temporarily Unavailable');
header('Status: 503 Service Temporarily Unavailable');
header('Retry-After: ' . ($diff + 30));
?>
<!DOCTYPE html>
<html>
<head>
<title><?= $SETTINGS['site_name'] ?> &gt; Scheduled Maintenance</title>
<style type="text/css">
body { margin: 0; }
#horizon
{
  text-align: center;
  position: absolute;
  top: 50%;
  left: 0;
  width: 100%;
  display: block;
  visibility: visible;
}

#content
{
  margin-left: -200px;
  position: absolute;
  top: -75px;
  left: 50%;
  width: 400px;
  height: 150px;
  visibility: visible;
}

#prettygraphic
{
  border: 0;
  margin: 0;
  padding: 0;
  display: block;
}

#content p
{
  margin: 1em 0 0 0;
  padding: 0;
  font-family: Arial, Helvitica, sans-serif;
  font-size: 13px;
}

#content p img
{
  position: relative;
  top: 2px;
}
</style>
</head>
<body>
<div id="horizon">
<div id="content">
<img src="/gfx/maintenance.png" width="400" height="150" id="prettygraphic" alt="<?= $SETTINGS['site_name'] ?> is down for scheduled maintenance." />
<p>It's upsetting, but fear not: <?= $SETTINGS['site_name'] ?> will be back in <?= ceil($diff / 60) ?> minutes <img src="/gfx/emote/hee.gif" alt="" /></p>
</div>
</div>
</body>
</html>
