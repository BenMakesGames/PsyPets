<?php
class DailyAdventureController extends psyFrameworkController
{
  public function RunIndex(&$args = array())
  {
    header('Location: /challenge.php');
    exit();
  }

  public function RunShop(&$args = array())
  {
    header('Location: /challenge_shop.php');
    exit();
  }

  public function RunRankings(&$args = array())
  {
    $user = User::GetBySession();
    $content['user'] = $user;

    if(!$user->IsLoaded()) return $this->RequireLogin();

    $content['title'] = array(array('/daily_adventure', 'Daily Adventure'), array(false, 'Most Adventurous Residents'));

    $page = (int)$args['page'];

    $num_rankings = PlayerStats::GetCount('Completed a Daily Adventure Challenge');
    $num_pages = ceil($num_rankings / 20);

    if($page < 1 || $page > $num_pages)
      $page = 1;

    $content['rankings'] = PlayerStats::GetRankingsOverAge('Completed a Daily Adventure Challenge', 1267941600, $page);
    $content['page'] = $page;
    $content['num_pages'] = $num_pages;

    $content['npc'] = array(
      'dialog' => '<p>I\'ve been keeping track of when residents complete adventures, when they don\'t, etc, etc, and have made a few tables...</p><p>Well, here, look: this one shows us the percent of adventures a resident has undertaken and succeeded since I started counting on March 7th, 2010.  Neat, huh?  I kind of wish I\'d been keeping track of whether the adventure was gold, silver, or whatever, but... oh well.</p>',
    );

    return $content;
  }
}
?>
