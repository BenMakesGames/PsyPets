<?php
require_once 'commons/settings_light.php';

// "G" is 24-hour hour sans leading zero, "i" is minutes with leading 0
$maintenance_when = (int)date('Gi');

// midnight through 12:20, we are in maitenance
if(($maintenance_when >= 0 && $maintenance_when <= 20 && $_GET['maintenance'] != 'no') || $_GET['maintenance'] == 'yes')
{
    if($AJAX)
    {
        include 'commons/ajax_maintenance.php';
    }
    else
    {
        include 'commons/maintenance.php';
    }

    exit();
}

$SITE_LAYOUTS = array(
    'default'  => 'PsyPets in a Box',
    'wide'     => 'PsyPets Wide',
);

$SITE_COLORS = array(
    'telkoth'  => 'PsyPets Blue',
    'ks'       => 'KS\' Green',
    'kirby'    => 'Kirby\'s Granite',
    'lune'     => 'Lune\'s Con-inspired',
    'vitriol'  => 'Vitriol\'s Gotham City',
    'hara'     => 'Hara\'s Purple',
    'redmetal' => 'Red Metal',
    'traveller'=> 'Traveller\'s Banana',
    'imakoo'   => 'Imakoo!',
);

$CONTENT_CLASS = 'paddedcell';
$CONTENT_STYLE = '';

require_once 'libraries/get_ip.php';

$SETTINGS['secure_server'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on');

$SETTINGS['protocol'] = ($SETTINGS['secure_server'] ? 'https' : 'http');

$SETTINGS['image_server_url'] = $SETTINGS['protocol'] . '://' . $SETTINGS['static_domain'];

$SETTINGS['game_donatetag'] = true;
$SETTINGS['game_postsize'] = 30;
$SETTINGS['game_postsize_bonus'] = 50;

//$SETTINGS['game_broadcastday'] = 'Fri';
$SETTINGS['game_broadcastday'] = '';

// no player-player interactions (in case account data is missing/broken)
//$NO_PVP = true;

// uncomment to disable logins and login-ed-ness
//$NO_LOGIN = true && ($_SERVER['REMOTE_ADDR'] != '76.4.79.154');

// uncommont to disable various time-dependent features
//$TIME_IS_FUCKED = true;

// uncomment to force broadcasting day
//$BROADCAST = true;

set_error_handler('PsyPetsErrorHandler', E_ALL);

function PsyPetsErrorHandler($errno, $errstr, $errfile, $errline, $errcontext)
{
    if($errno == E_STRICT || $errno == E_NOTICE || $errno == E_WARNING)
        return;

    $prepare_to_die = !($errno == E_DEPRECATED);

    global $SETTINGS;

    $now = time();

    $ref_code = date('Y-m-d-H-i-s', $now);

    $errorstring = '<h2>' . date('Y.m.d, h:i:sa', $now) . '</h2>' . "\n" .
        '<p>Error: ' . $errstr . ' (#' . $errno . ').</p>' . "\n";

    ob_start();
    debug_print_backtrace();
    $errorstring .= '<p><pre>' . ob_get_contents() . '</pre></p>' . "\n";
    ob_end_clean();

    if(count($_GET) > 0)
        $errorstring .= '<p>$_GET = ' . nl2br(print_r($_GET, true)) . '</p>' . "\n";

    if(count($_POST) > 0)
        $errorstring .= '<p>$_POST = ' . nl2br(print_r($_POST, true)) . '</p>' . "\n";

    if($prepare_to_die)
    {
        echo
            '<h2>Gack!  Errors!</h2>' .
            '<p>PsyPets has encountered an awful kind of error.  An administrator has been automatically notified, however depending on the situation it might be worth bugging one about this.<p>' .
            '<p>If you do bug an admin about this, please refer to this reference code ' . $ref_code . '.</p>'
        ;
    }

    $logfile = $SETTINGS['error_log_dir'] . '/' . $ref_code . '.html';

    error_log($errorstring, 3, $logfile);
    error_log($errorstring, 1, $SETTINGS['author_email'], '<p>Error log: ' . $logfile . '</p>');

    if($prepare_to_die)
    {
        die();
    }
}
