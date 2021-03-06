<?php
//header('Location: ./tempindex.php');
//exit();

$require_login = 'no';
$invisible = 'yes';
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/encryption.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/messages.php';
require_once 'commons/loginlib.php';

$PAGE['force_ad'] = true;

$command = 'SELECT COUNT(idnum) AS c FROM monster_users WHERE lastactivity>=' . ($now - (5 * 60));
$data = fetch_single($command, 'adminstats.php');

$active_count = $data['c'];

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Home</title>
<?php include 'commons/head.php'; ?>
  <link href="rss_news.xml" rel="alternate" type="application/rss+xml" title="<?= $SETTINGS['site_name'] ?> Latest News" />
  <link rel="signup" href="signup.php" title="Sign Up" />
  <link rel="encyclopedia" href="encyclopedia.php" title="Item Encyclopedia" />
  <link rel="pet-encyclopedia" href="petencyclopedia.php" title="Pet Encyclopedia" />
  <link rel="stylesheet" href="styles/pp_markup.css" />
 </head>
 <body>
<?php
include 'commons/header_2.php';

if(strlen($_GET['msg']) > 0)
  $error_message .= form_message(explode(',', $_GET['msg']));

if($error_message) echo '<p class="failure">' . $error_message . '</p>';

$command = '
  SELECT *
  FROM psypets_news
  WHERE `category` IN (\'routine\', \'important\', \'severe\', \'broadcast\', \'comic\', \'event\')
  ORDER BY idnum DESC
  LIMIT 1
';

$general_post = fetch_single($command, 'fetching latest news');

$sender = get_user_byid($general_post['author']);

$category = $general_post['category'];
?>
<!--
    <h5>Important Message</h5>
		<p>The database is down, or whatever.</p>
    <hr />
-->
<?php
$npc = mt_rand(1, 3);

include 'commons/index/' . $npc . '.php';
?>
    <h4 style="clear:both;">Latest News <a href="/rss_news.xml"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/famfamfam/feed.png" width="16" height="16" alt="RSS Feed" class="inlineimage" /></a></h4>
    <div id="latestnews" class="cityhallpost category_<?= $category ?>">
     <h5><?= format_text($general_post['subject']) ?></h5>
     <p><?= format_text($general_post['message']) ?></p>
     <p class="dim nomargin"><i>&mdash; posted by <?= $sender['display'] ?> on <?= local_time($general_post['date'], 0, 'no') ?> UTC</i></p>
    </div>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
