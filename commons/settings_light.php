<?php
header('X-Frame-Options: sameorigin');

date_default_timezone_set('America/Los_Angeles');
ini_set('default_charset', 'UTF-8');

// if you uncomment the following line, login will be disabled. you should edit a message into /views/_template/me.php, after the line "if($NO_LOGIN === true)"
//$NO_LOGIN = true && ($_SERVER['REMOTE_ADDR'] != '184.6.30.60');

$SETTINGS = array();

$SETTINGS['author_email'] = 'ADMINISTRATIVE E-MAILS GET SENT HERE';
$SETTINGS['author_real_name'] = 'YOUR FULL NAME HERE';
$SETTINGS['author_resident_name'] = 'YOUR RESIDENT NAME HERE'; // once you sign up and create an account for yourself, put the resident name here
$SETTINGS['site_name'] = 'YOUR PET GAME NAME';
$SETTINGS['site_domain'] = 'www.YOURDOMAIN.COM';
// $SETTINGS['static_domain'] = $SETTINGS['site_domain']; // use this instead of the next line if you only have one machine
$SETTINGS['static_domain'] = 'static.YOURDOMAIN.COM';
$SETTINGS['wiki_domain'] = 'wiki.YOURDOMAIN.COM'; // could also be something like "YOURGAME.wikia.com/wiki"
$SETTINGS['site_url'] = 'http://' . $SETTINGS['site_domain'];
$SETTINGS['site_mailer'] = 'sender@YOURDOMAIN.COM'; // mail from the game to others comes from this address

$SETTINGS['site_ingame_mailer'] = 'sender'; // the login name that should send people in-game messages. hand-create an account with this login name, and change "is_npc" to "yes" in the database to prevent anyone from logging into it!

$SETTINGS['cookie_rememberme'] = 'COOKIENAME';
$SETTINGS['cookie_path'] = '/';
$SETTINGS['cookie_domain'] = '.YOURDOMAIN.COM'; // be sure to leave that first "."

$SETTINGS['handydb']['url'] = 'localhost';
$SETTINGS['handydb']['user'] = 'DATABASE-USERNAME';
$SETTINGS['handydb']['password'] = 'DATABASE-PASSWORD';
$SETTINGS['handydb']['database'] = 'DATABASE-NAME';
$SETTINGS['handydb']['charset'] = 'utf8';
