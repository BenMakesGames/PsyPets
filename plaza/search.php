<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/encryption.php';
require_once 'commons/formatting.php';

if($user_object->Groups())
{
  $groups = $user_object->Groups();
  $group_separator = true;
}
else
  $group_separator = false;

$plaza = new Plaza();

$plazas = $plaza->GetSections($groups);

$search_results = array();

if($_GET['action'] == 'search')
{
  $exact = true; //($_GET["exact"] == "on" || $_GET["exact"] == "yes");
  $searched = true;

  $resident = trim($_GET['resident']);

  // if no plazas were specified, search all non-group plazas
  if(!$_GET['plazas'] || count($_GET['plazas']) == 0)
  {
    foreach($plazas as $plaza)
    {
      $plaza_id = $plaza['idnum'];

      if($plaza['groupid'] == 0)
      {
        $search_plaza[$plaza_id] = true;
        $plazas_to_search[] = $plaza_id;
      }
    }
  }
  else
  {
    foreach($plazas as $plaza)
    {
      $plaza_id = $plaza['idnum'];

      if(in_array($plaza_id, $_GET['plazas']))
      {
        $search_plaza[$plaza_id] = true;
        $plazas_to_search[] = $plaza_id;
      }
      else
        $search_plaza[$plaza_id] = false;
    }
  }

  $these_words = array_unique(explode(' ', $_GET['words']));
  $words = array();

  foreach($these_words as $index=>$word)
  {
    if(strlen($word) > 0)
      $words[] = $word;
  }

  $consider_post = array();
  $matching_post = array();

  $search_user = false;

  if($resident != '')
  {
    $search_user = User::GetByName($resident);

    if(strtolower($search_user->Name()) == strtolower($resident))
      $resident = $search_user->Name();
    else
      $error_message = 'Could not find the resident "' . $resident . '".';
  }

  if(count($plazas_to_search) == 0)
    $error_message = 'You have to select at least one section of the plaza to search!';

  $page = (int)$_GET['page'];
  if($page < 1)
    $page = 1;

  if($error_message == '')
  {
    if(count($words) > 0)
    {
      if(count($words) == 1)
        $search_for = implode(' ', $words);
      else
        $search_for = '+' . implode(' +', $words);

      $search_time = microtime(true);

      if($search_user === false)
      {
        $count_command = 'SELECT COUNT(a.idnum) AS c ' .
          'FROM (monster_posts AS a LEFT JOIN monster_threads AS b ON a.threadid=b.idnum) ' .
          'LEFT JOIN monster_plaza AS c ON b.plaza=c.idnum ' .
          'WHERE c.idnum IN (' . implode(', ', $plazas_to_search) . ') ' .
          'AND MATCH(a.title, a.body) AGAINST(' . quote_smart($search_for) . ' IN BOOLEAN MODE)';

        $data = fetch_single($count_command, 'fetching result size');

        $num_items = (int)$data['c'];

        if($num_items > 0)
        {
          $num_pages = ceil($num_items / 10);

          if($page > $num_pages) $page = $num_pages;

          $command = 'SELECT a.*,d.display,b.title AS thread_title,c.title AS plaza_title,c.idnum AS plaza_id ' .
                     'FROM ((monster_posts AS a LEFT JOIN monster_users AS d ON a.createdby=d.idnum) ' .
                     'LEFT JOIN monster_threads AS b ON a.threadid=b.idnum) ' .
                     'LEFT JOIN monster_plaza AS c ON b.plaza=c.idnum ' .
                     'WHERE c.idnum IN (' . implode(', ', $plazas_to_search) . ') ' .
                     'AND MATCH(a.title, a.body) AGAINST(' . quote_smart($search_for) . ' IN BOOLEAN MODE) ' .
                     'ORDER BY a.idnum DESC LIMIT ' . (($page - 1) * 10) . ',10';

          $search_results = fetch_multiple($command, 'fetching search results');
        }
      }
      else
      {
        $count_command = 'SELECT COUNT(a.idnum) AS c ' .
          'FROM ((monster_posts AS a LEFT JOIN monster_threads AS b ON a.threadid=b.idnum) ' .
          'LEFT JOIN monster_plaza AS c ON b.plaza=c.idnum) ' .
          'LEFT JOIN monster_users AS d ON a.createdby=d.idnum ' .
          'WHERE a.createdby=' . $search_user->ID() . ' ' .
          'AND c.idnum IN (' . implode(', ', $plazas_to_search) . ') ' .
          'AND MATCH(a.title, a.body) AGAINST(' . quote_smart($search_for) . ' IN BOOLEAN MODE)';
        $data = fetch_single($count_command, 'fetching result size');

        $num_items = (int)$data['c'];

        if($num_items > 0)
        {
          $num_pages = ceil($num_items / 10);

          if($page > $num_pages) $page = $num_pages;

          $command = 'SELECT a.*,d.display,b.title AS thread_title,c.title AS plaza_title,c.idnum AS plaza_id ' .
            'FROM ((monster_posts AS a LEFT JOIN monster_threads AS b ON a.threadid=b.idnum) ' .
            'LEFT JOIN monster_plaza AS c ON b.plaza=c.idnum) ' .
            'LEFT JOIN monster_users AS d ON a.createdby=d.idnum ' .
            'WHERE a.createdby=' . $search_user->ID() . ' ' .
            'AND c.idnum IN (' . implode(', ', $plazas_to_search) . ') ' .
            'AND MATCH(a.title, a.body) AGAINST(' . quote_smart($search_for) . ' IN BOOLEAN MODE) ' .
            'ORDER BY a.idnum DESC LIMIT ' . (($page - 1) * 10) . ',10';

          $search_results = fetch_multiple($command, 'fetching search results');
        }
      }

      $search_time = microtime(true) - $search_time;
    }
    else if($resident != '')
    {
      $search_time = microtime(true);

      $count_command = 'SELECT COUNT(a.idnum) AS c ' .
        'FROM (monster_posts AS a LEFT JOIN monster_threads AS b ON a.threadid=b.idnum) ' .
        'LEFT JOIN monster_plaza AS c ON b.plaza=c.idnum ' .
        'WHERE a.createdby=' . $search_user->ID() . ' ' .
        'AND c.idnum IN (' . implode(', ', $plazas_to_search) . ')';
      $data = fetch_single($count_command, 'fetching result size');

      $num_items = (int)$data['c'];

      if($num_items > 0)
      {
        $num_pages = ceil($num_items / 10);

        if($page > $num_pages) $page = $num_pages;

        $command = 'SELECT a.*,d.display,b.title AS thread_title,c.title AS plaza_title,c.idnum AS plaza_id ' .
          'FROM (monster_posts AS a LEFT JOIN monster_threads AS b ON a.threadid=b.idnum) ' .
          'LEFT JOIN monster_plaza AS c ON b.plaza=c.idnum ' .
          'LEFT JOIN monster_users AS d ON a.createdby=d.idnum ' .
          'WHERE a.createdby=' . $search_user->ID() . ' ' .
          'AND c.idnum IN (' . implode(', ', $plazas_to_search) . ') ' .
          'ORDER BY a.idnum DESC LIMIT ' . (($page - 1) * 10) . ',10';

        $search_results = fetch_multiple($command, 'fetching search results');
      }

      $search_time = microtime(true) - $search_time;
    } // resident-name only search
    else
      $error_message = 'You didn\'t provide anything to search by...';

    if(count($search_results) == 0 && $error_message == '')
      $error_message = 'No results found.  Try taking out some extraneous words to broaden your search.';
  } // there was no error
}
else
{
  if($_GET['plaza'] > 0)
    $search_plaza[(int)$_GET['plaza']] = true;
  else
  {
    foreach($plazas as $plaza)
      $search_plaza[$plaza['idnum']] = true;
  }

  $exact = true;
}

if($words && count($words) > 0)
  $word_list = implode(' ', $words);
else
  $word_list = '';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Plaza &gt; Search</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
	<h4><a href="/plaza.php">Plaza</a> &gt; Search</h4>
		 <form method="get">
     <table>
      <tr>
       <td>Keywords:</td>
       <td><input name="words" style="width:240px;" value="<?= htmlentities($word_list) ?>" speech x-webkit-speech /></td>
      </tr>
      <tr>
       <td>Resident:</td>
       <td><input name="resident" style="width:240px;" value="<?= $resident ?>" speech x-webkit-speech /></td>
      </tr>
      <tr>
       <td colspan="2"></td>
      </tr>
      <tr>
       <td valign="top">Search in:</td>
       <td valign="top">
        <p>
<?php
foreach($plazas as $plaza)
{
  if($plaza['order'] > 0 && $group_separator)
  {
    echo '<br />';
    $group_separator = false;
  }

  echo '<input type="checkbox" name="plazas[]" value="' . $plaza['idnum'] . '" id="p' .  $plaza['idnum'] . '"' . ($search_plaza[$plaza['idnum']] ? ' checked' : '') . ' /> <label for="p' .  $plaza['idnum'] . '">' . $plaza['title'] . '</label><br />';
}
?>
        </p>
       </td>
      </tr>
      <tr>
       <td colspan="2" align="right">
        <input type="hidden" name="action" value="search" />
        <input type="submit" name="submit" value="Search" />
       </td>
      </tr>
     </table>
     </form>
<?php
if($error_message != '')
  echo '<hr /><p>' . $error_message . '</p>';

else if($searched)
{
  $page_list = paginate($num_pages, $page, '?action=search&words=' . str_replace('%', '%%', urlencode($word_list)) . '&resident=' . str_replace('%', '%%', urlencode($resident)) . '&plazas[]=' . implode('&plazas[]=', $plazas_to_search) . '&page=%s');

  echo '<hr />' .
       '<p>Found ' . $num_items . ' result' . ($num_items == 1 ? '' : 's') . '.</p>' .
       $page_list;

  $rowclass = begin_row_class();

  foreach($search_results as $post)
  {
    if($post['display'] === '')
      $author_display = '<i class="dim">[Departed #' . $post['createdby'] . ']</i>';
    else
      $author_display = User::Link($post['display']);

    if(strlen($post['title']) > 0)
      $post_title = highlight_words($post['title'], $words, $exact);
    else
      $post_title = '[untitled]';
?>
      <div style="border-top: 1px #000 solid; padding: 1em;" class="<?= $rowclass ?>">
      <h5>
       <a href="/viewplaza.php?plaza=<?= $post['plaza_id'] ?>"><?= $post['plaza_title'] ?></a> &gt;
       <a href="/viewthread.php?threadid=<?= $post['threadid'] ?>"><?= $post['thread_title'] ?></a> &gt;
       <a href="/jumptopost.php?postid=<?= $post['idnum'] ?>"><?= $post_title ?></a>
      </h5>
      <p><?= highlight_words($post['body'], $words, $exact) ?>
      <p>&mdash; by <?= $author_display ?> on <?= $user_object->LocalTime($post['creationdate']) ?></p>
      </div>
<?php
    $rowclass = alt_row_class($rowclass);
  }

  echo $page_list;
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
