<?php
/* picks new leaders for groups when existing leader is gone
*/

$require_petload = 'no';

$IGNORE_MAINTENANCE = true;

//ini_set('include_path', '/your/web/root');

require_once 'commons/dbconnect.php';
require_once 'commons/grouplib.php';
require_once 'commons/userlib.php';

$now = time();

$command = 'SELECT idnum,name,leaderid,members,forumid FROM psypets_groups WHERE systemgroup=\'no\' AND member_count>1';
$groups = $database->FetchMultiple($command, 'fetching non-system groups with more than one member');

foreach($groups as $group)
{
  $member_list = get_group_member_ids($group['members']);

  $pick_new_leader = false;

  $command = 'SELECT idnum,lastactivity,disabled FROM monster_users WHERE idnum=' . $group['leaderid'] . ' LIMIT 1';
  $user = $database->FetchSingle($command, 'fetching group leader');

  if($user === false)
  {
    $pick_new_leader = true;
    $reason = 'no longer exists';
  }
  else if($user['disabled'] == 'yes')
  {
    $pick_new_leader = true;
    $reason = 'has deactivated their account';
  }
  else if($user['lastactivity'] < $now - (365 * 24 * 60 * 60))
  {
    $pick_new_leader = true;
    $reason = 'has not been active for over one year';
  }
  else if(!in_array($user['idnum'], $member_list))
  {
    $pick_new_leader = true;
    $reason = 'is not a member of the group (due to an earlier bug >_>)';
  }

  if($pick_new_leader === true)
  {
    echo '* Group #' . $group['idnum'] . ' needs a new leader (#' . $group['leaderid'] . ' ' . $reason . ')...', "\r\n";
  
    $member_ids = array();
    $member_list = explode(',', $group['members']);
    foreach($member_list as $member_data)
    {
      list($userid, $rank) = explode('|', $member_data);
      $member_ids[$userid] = $rank;
    }

    $candidate_ids = array();
    $dead = 0;
    
    // for each member
    foreach($member_ids as $userid=>$rankid)
    {
      $command = 'SELECT lastactivity,signupdate,disabled FROM monster_users WHERE idnum=' . $userid . ' LIMIT 1';
      $candidate = $database->FetchSingle($command, 'fetching group member');

      // one-year absence or a banning of any kind == dead
      if($candidate === false || $candidate['lastactivity'] < $now - (365 * 24 * 60 * 60) || $candidate['disabled'] == 'yes')
      {
        $dead++;
        continue;
      }

      // if the member has been active within the last 7 days, signed up more than 7 days ago,
      // then that member is a good candidate
      if($candidate['lastactivity'] >= ($now - 7 * 24 * 60 * 60) && $candidate['signupdate'] <= ($now - 7 * 24 * 60 * 60))
      {
        $candidate_ids[$userid] = $rankid;
      }
    }

    // if there are no good candidates, just pick a member at random
    if(count($candidate_ids) == 0)
    {
      if($dead + 1 >= count($member_ids))
      {
        echo '  * this group is dead.', "\r\n";

        continue; // with the next group >_>
      }
 
      do
      {
        $new_leader = array_rand($member_ids);
      } while($new_leader == $group['leaderid']);

      echo '  * randomly chose ' . $new_leader . '.', "\r\n";
    }
    else if(count($candidate_ids) == 1)
    {
      list($new_leader) = array_keys($candidate_ids); 
      echo '  * chose only good candidate: ' . $new_leader . '.', "\r\n";
    }
    else
    {
      $powers = array();
    
      // get the rank and power of each candidate
      foreach($candidate_ids as $userid=>$rankid)
      {
        if((int)$rankid == 0)
          $powers[0] = 0;
        else if(!array_key_exists($rankid, $powers))
        {
          $command = 'SELECT power FROM psypets_group_ranks WHERE idnum=' . $rankid . ' LIMIT 1';
          $rank = $database->FetchSingle($command, 'fetching group rank');
          $powers[$rankid] = (int)$rank['power'];
        }
      }
      
      echo '  * rank/power array for group among eligible members:', "\r\n";
      print_r($powers);
      
      // find the ranks with the highest power
      arsort($powers);
      $highest_power = reset($powers);

      echo '  * highest power among those ranks: ' . $highest_power, "\r\n";

      $highest_ranks = array();

      foreach($powers as $rankid=>$power)
      {
        if($power == $highest_power)
          $highest_ranks[] = $rankid;
      }

      echo '  * highest-powered rank(s):', "\r\n";
      print_r($highest_ranks);
      
      // find the members of the highest-powered ranks
      $final_candidate_ids = array();
      
      foreach($candidate_ids as $userid=>$rankid)
      {
        if(in_array((int)$rankid, $highest_ranks))
          $final_candidate_ids[] = $userid;
      }
      
      $new_leader = $final_candidate_ids[array_rand($final_candidate_ids)];
      echo '  * chose by activity and rank: user #' . $new_leader . '.', "\r\n";
    }
    
    $new_organizer = get_user_byid($new_leader, 'display');
    
    if($new_organizer === false)
    {
      echo '  * error: user #' . $new_leader . ' (the user we picked as the new leader) does not exist!  That seems bad!', "\r\n";
    }
    else
    {
      echo '  * new organizer: ' . $new_organizer['display'] . '.', "\r\n";

      echo '    * psymailing group...', "\r\n";

      psymail_group_byid($group['idnum'], 'psypets', $group['name'] . ' has been assigned a new Group Organizer',
        'The previous Group Organizer ' . $reason . ', so a new Group Organizer has been chosen: {r ' . $new_organizer['display'] . '}!');

      echo '    * kicking previous organizer...', "\r\n";
      kick_group_member($group, $group['leaderid']);

      echo '    * instituting new group leader...', "\r\n";
      change_group_leader($group, $new_leader);

      echo '    * updating group watchers...', "\r\n";
      $updated_group = get_group_byid($group['idnum']);
      $ranks = get_group_ranks($group['idnum']);
      update_group_watchers($updated_group, $ranks);
    }
  }
}
?>