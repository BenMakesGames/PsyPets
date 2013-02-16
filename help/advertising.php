<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_login = 'no';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; In-game Advertising</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
  <h4><a href="/help/">Help Desk</a> &gt; In-game Advertising</h4>
  <p>To clarify, this is not about the ads that appear in the upper-right of the page; but rather the in-game messages people can post via <a href="/broadcast.php">Advertising</a>.</p>
  <p>Generally speaking, as long as an ad does not violate the ToS (porn, copyright infringement, etc), you are free to post just about anything.</p>
  <p><strong>However:</strong> the Advertising system has been created in such a way that you, the players, may determine whether or not an ad is appropriate. If enough people rate an ad as being "inappropriate" the ad will be removed.</p>
  <p>For example, it's not against the Terms of Service to swear, but if people generally feel that advertising is not a place to swear, they can rate an ad that swears as being "inappropriate".  If enough other people feel this way, and rate this way, the ad may be removed.</p>
  <p>So please rate ads!  If you like them, rate that you like them, and if you think they should not be there, feel free to rate them as inappropriate.  Moderation of the ads is in your hands!</p>
  <p><i>(If an ad does violate the Terms of Service, please <a href="/residentprofile.php?resident=<?= $SETTINGS['author_resident_name'] ?>">contact me, <?= $SETTINGS['author_resident_name'] ?></a>, rather than rating it as being inappropriate, so that I can ensure its speedy removal.)</i></p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
