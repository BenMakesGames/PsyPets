<?php
$require_petload = 'no';
$require_login = 'no';
$wiki = 'Changelog';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/changeloglib.php';

if($user['newchangelogentries'] == 'yes')
{
  $user['newchangelogentries'] = 'no';
  
  $database->FetchNone('UPDATE monster_users SET newchangelogentries=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1');
}

if($_GET['action'] == 'search')
  $_POST = $_GET;

if($_POST['action'] == 'Search' || $_GET['action'] == 'search')
{
  $get .= '&amp;action=search';

  foreach($_POST as $key=>$value)
  {
    if(in_array($key, $CHANGELOG_CATEGORIES))
    {
      $view_categories[] = $key;
      $get .= '&amp;' . $key . '=1';
    }
  }
}
else
{
  $view_categories = $CHANGELOG_CATEGORIES;
  $get = '';
}

$notify_settings = array('small', 'medium', 'large', 'never');

if(in_array($_GET['notify'], $notify_settings) && $user['idnum'] > 0)
{
  $user['notifynewchangelogentries'] = $_GET['notify'];
  
  $database->FetchNone('UPDATE monster_users SET notifynewchangelogentries=' . quote_smart($_GET['notify']) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1');
}

$page = (int)$_GET['page'];

$count = get_changelog_count($view_categories);
$num_pages = ceil($count / 20);

if($page < 1 || $page > $num_pages)
  $page = 1;
  
$changelog = get_changelog_page($view_categories, $page);

$pagelist = paginate($num_pages, $page, '/changelog.php?page=%s' . $get);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; City Hall &gt; Changelog</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="cityhall.php">City Hall</a> &gt; Changelog</h4>
<?php
if($user['idnum'] > 0)
{
  if($admin['coder'] == 'yes')
    echo '<ul><li><a href="/admin/changelog.php">Add new entry</a></li></ul>';

  echo '<ul><li>Notify of new changelog entries? ';

  if($user['notifynewchangelogentries'] == 'never')
    echo '<b>never</b>';
  else
    echo '<a href="changelog.php?notify=never" style="color:black;">never</a>';

  echo ' | ';

  if($user['notifynewchangelogentries'] == 'small')
    echo '<b class="impact_small">any and all</b>';
  else
    echo '<a href="changelog.php?notify=small" class="impact_small">any and all</a>';

  echo ' | ';

  if($user['notifynewchangelogentries'] == 'medium')
    echo '<b class="impact_medium">medium or higher</b>';
  else
    echo '<a href="changelog.php?notify=medium" class="impact_medium">medium or higher</a>';

  echo ' | ';

  if($user['notifynewchangelogentries'] == 'large')
    echo '<b class="impact_large">high only</b>';
  else
    echo '<a href="changelog.php?notify=large" class="impact_large">high only</a>';

  echo '</li></ul>';
}

echo '<form action="/changelog.php" method="post"><div style="margin-bottom:1em;">';
foreach($CHANGELOG_CATEGORIES as $cat)
{
  $alt = 'alt="' . $CHANGELOG_CATEGORY_NAMES[$cat] . '" onmouseover="return Tip(\'' . $CHANGELOG_CATEGORY_NAMES[$cat] . '\');"';
  echo '<div style="float:left; text-align: center; padding-right: 0.5em;"><img src="//' . $SETTINGS['static_domain'] . '/gfx/changelog/' . $cat . '.png" width="24" height="24" ' . $alt . ' /><br /><input type="checkbox" name="' . $cat . '" class="inlineimage"' . (in_array($cat, $view_categories) ? ' checked="checked"' : '') . ' /></div>';
}
echo '<div style="float:left;"><br /><input type="submit" name="action" value="Search" /></div><div style="clear:both;"></div></div></form>';

if(count($changelog) > 0)
{
?>
     <div style="border-bottom:1px solid #666; padding-bottom: 1em; margin-bottom: 1em;"><?= $pagelist ?></div>
<?php
  foreach($changelog as $log)
  {
    $alt = 'alt="' . $CHANGELOG_CATEGORY_NAMES[$log['category']] . '" onmouseover="return Tip(\'' . $CHANGELOG_CATEGORY_NAMES[$log['category']] . '\');"';

    switch($log['impact'])
    {
      case 'small': $impact = 'changelogtitle impact_small'; break;
      case 'medium': $impact = 'changelogtitle impact_medium'; break;
      case 'large': $impact = 'changelogtitle impact_large'; break;
      default: $impact = 'changelogtitle'; break;
    }

    if($log['timestamp'] > $now - 7 * 24 * 60 * 60)
      $when = duration($now - $log['timestamp'], 2) . ' ago';
    else
      $when = date('M j, Y', $log['timestamp']);

    echo '
      <div style="border-bottom:1px solid #666; margin-bottom: 1em;">
      <p style="float:right; padding:4px; font-style: italic;" class="dim">' . $when . '</p>
      <p><img src="//' . $SETTINGS['static_domain'] . '/gfx/changelog/' . $log['category'] . '.png" width="24" height="24" class="inlineimage" ' . $alt . ' /> <span class="' . $impact . '">' . ucfirst($log['summary']) . '</span></p>
      <p>' . $log['details'] . '</p>
      </div>
    ';
  }
  
  echo $pagelist;
}
else
  echo '<p>No changelog entries found!</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
