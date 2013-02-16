<?php
class PlazaController extends psyFrameworkController
{
  public function RunSearch(&$args = array())
  {
    $user = User::GetBySession();

    $content['user'] = $user;
    $content['title'] = array(array('/plaza.php', 'Plaza Forums'), array(false, 'Search'));

    if(!$user->IsLoaded()) return $this->RequireLogin();

    if($user->Groups())
    {
      $groups = $user->Groups();
      $content['group_separator'] = true;
    }
    else
      $content['group_separator'] = false;

    $plaza = new Plaza();
    
    $plazas = $plaza->GetSections($groups);

    $search_results = array();

    if($args['action'] == 'search')
    {
      $exact = true; //($_POST["exact"] == "on" || $_POST["exact"] == "yes");
      $content['searched'] = true;

      $resident = trim($args['resident']);

      // if no plazas were specified, search all non-group plazas
      if(!$args['plazas'] || count($args['plazas']) == 0)
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

          if(in_array($plaza_id, $args['plazas']))
          {
            $search_plaza[$plaza_id] = true;
            $plazas_to_search[] = $plaza_id;
          }
          else
            $search_plaza[$plaza_id] = false;
        }
      }

      $these_words = array_unique(explode(' ', $args['words']));
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
        $content['resident'] = $resident;

        if(strtolower($search_user->Name()) == strtolower($resident))
          $resident = $search_user->Name();
        else
          $error_message = 'Could not find the resident "' . $resident . '".';
      }

      if(count($plazas_to_search) == 0)
        $error_message = 'You have to select at least one section of the plaza to search!';

      $page = (int)$args['page'];
      if($page < 1)
        $page = 1;

      if($error_message == '')
      {
        $content['words'] = $words;
      
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
      if($args['plaza'] > 0)
        $search_plaza[(int)$args['plaza']] = true;
      else
      {
        foreach($plazas as $plaza)
          $search_plaza[$plaza['idnum']] = true;
      }

      $exact = true;
    }

    $content['page_info'] = 'Took ' . round($search_time / 1000, 4) . 's to perform the search.';
    $content['exact'] = $exact;
    $content['plazas_to_search'] = $plazas_to_search;
    $content['search_results'] = $search_results;
    $content['search_command'] = $command;
    $content['error_message'] = $error_message;
    $content['search_plaza'] = $search_plaza;
    $content['plazas'] = $plazas;
    $content['page'] = $page;
    $content['num_items'] = $num_items;
    $content['num_pages'] = $num_pages;

    return $content;
  }

  public function RunIndex()
  {
    header('Location: /plaza.php');
    exit();
  }
}
?>
