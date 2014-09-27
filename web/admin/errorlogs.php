<?php
$IGNORE_MAINTENANCE = true;

require_once 'commons/init.php';

$require_petload = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";

if($admin['seeserversettings'] != 'yes')
{
    Header("Location: /");
    exit();
}

$requested = 0;
$deleted = 0;

if($_POST['submit'] == 'Delete')
{
    foreach($_POST as $key=>$value)
    {
        if(preg_match('/^[0-9]{4}(-[0-9]{2}){5}$/', $key) && ($value == 'yes' || $value == 'on'))
        {
            $filename = WEB_ROOT . '/../errorlogs/' . $key . '.html';

            $requested++;

            if(unlink($filename))
                $deleted++;
        }
    }
}

$error_log_contents = false;

if($_GET['view'])
{
    if(preg_match('/[0-9-]+\.html', $_GET['view']))
    {
        $error_file = WEB_ROOT . '/../errorlogs/' . $_GET['view'];
        $error_log_contents = file_get_contents($error_file);
    }
}

require 'commons/html.php';
?>
<head>
    <title><?= $SETTINGS['site_name'] ?> &nbsp; Administrative Tools &nbsp; Error Log Viewer</title>
    <?php include 'commons/head.php'; ?>
</head>
<body>
<?php include 'commons/header_2.php'; ?>
<h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Error Log Viewer</h4>
<?php
if($requested > 0)
    echo '<p>Requested deletion of ' . $requested . ' file' . ($requested != 1 ? 's' : '') . '; ' . $deleted . ' ' . ($deleted == 1 ? 'was' : 'were') . ' deleted.</p>';

if($error_log_contents !== false)
{
    echo '<pre>' . $error_log_contents . '</pre>';
}

if($dh = opendir(WEB_ROOT . '/../errorlogs/'))
{
    $any = false;
    $rowclass = begin_row_class();

    while(($file = readdir($dh)) !== false)
    {
        if(!preg_match('/[0-9-]+\.html', $file))
            continue;

        if(filetype(WEB_ROOT . '/../errorlogs/' . $file) == 'dir')
            continue;

        if(!$any)
        {
            echo '<form method="post">' .
                '<table>' .
                '<tr class="titlerow"><th></th><th>Log File</th></tr>';

            $any = true;
        }

        echo '<tr class="' . $rowclass . '"><td><input type="checkbox" name="' . substr($file, 0, 19) . '" /></td><td><a href="?view=' . $file . '">' . $file . '</a></td></tr>';

        $rowclass = alt_row_class($rowclass);
    }

    if($any)
    {
        echo '</table>' .
            '<p><input type="submit" name="submit" value="Delete" /></p>' .
            '</form>';
    }
    else
        echo '<p>No error logs were found.</p>';

    closedir($dh);
}
else
    echo '<p class="failure">Could not open /errorlogs/</p>';
?>
<?php include 'commons/footer_2.php'; ?>
</body>
</html>
