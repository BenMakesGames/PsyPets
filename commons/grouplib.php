<?php
$ITEM_BOX = 'Item Box';

$GROUP_BADGES = array(
  'year-1' => 'Young',
  'year-2' => 'Adult',
  'year-3' => 'Old',
  'year-4' => 'Ancient',
  'year-plus' => 'Antediluvian',
  'company' => 'Two\'s Company',
  'crowd' => 'Three\'s a Crowd',
  'village' => 'Hamlet',
  'town' => 'Town',
  'city' => 'City',
);

$GROUP_RIGHTS = array(
  'watcher', 'editprofile',
  'memberadd', 'memberkick', 'memberposition',
  'boxadd', 'boxtake', 'boxlogs',
  'groupmail',
  'mapper'
);

function save_rank(&$rank)
{
  $command = '
    UPDATE psypets_group_ranks
    SET
      power=' . (int)$rank['power'] . ',
      name=' . quote_smart($rank['name']) . ',
      rights=' . quote_smart($rank['rights']) . '
    WHERE
      idnum=' . (int)$rank['idnum'] . '
    LIMIT 1
  ';

  fetch_none($command, 'saving rank #' . $rank['idnum']);
}

function new_group_rank($groupid, $name, $power, $rights)
{
  $command = 'INSERT INTO psypets_group_ranks (groupid, name, power, rights) VALUES ' .
    '(' . (int)$groupid . ', ' . quote_smart($name) . ', ' . (int)$power . ', ' .
    quote_smart(implode(',', $rights)) . ')';
  fetch_none($command, 'creating new group rank');
}

function get_rank_by_id($rankid)
{
  $command = 'SELECT * FROM psypets_group_ranks WHERE idnum=' . (int)$rankid . ' LIMIT 1';
  return fetch_single($command, 'fetching rank #' . $rankid);
}

function delete_rank_by_id($rankid)
{
  $command = 'DELETE FROM psypets_group_ranks WHERE idnum=' . (int)$rankid . ' LIMIT 1';
  fetch_none($command, 'deleting rank #' . $rankid);
}

function get_group_ranks($groupid)
{
  $command = 'SELECT * FROM psypets_group_ranks WHERE groupid=' . (int)$groupid . ' ORDER BY power DESC';
  return fetch_multiple_by($command, 'idnum', 'fetching group ranks');
}

function assign_member_rank(&$group, $userid, $rankid)
{
  $members = take_apart(',', $group['members']);

  foreach($members as $i=>$member)
  {
    list($memberid, $rank) = explode('|', $member);

    if($userid == $memberid)
    {
      $members[$i] = $userid . '|' . $rankid;
      $group['members'] = implode(',', $members);
      break;
    }
  }
}

function get_member_rank(&$group, $userid)
{
  $members = take_apart(',', $group['members']);

  foreach($members as $member)
  {
    list($memberid, $rank) = explode('|', $member);

    if($userid == $memberid)
    {
      if((int)$rank == 0)
        return false;
      else
        return $rank;
    }
  }

  return false;
}

function is_a_member(&$group, $userid)
{
  $members = explode(',', $group['members']);
  foreach($members as $member)
  {
    list($memberid, $rankid) = explode('|', $member);
    if($memberid == $userid)
      return true;
  }
  
  return false;
}

function member_has_right(&$group, $userid, $right)
{
  $ranks = get_group_ranks($group['idnum']);
  $rankid = get_member_rank($group, $user['idnum']);
  return rank_has_right($ranks, $rankid, $right);
}

function rank_has_right(&$ranks, $rankid, $right)
{
  if($rankid === false)
    return false;

  $rights = explode(',', $ranks[$rankid]['rights']);

  return in_array($right, $rights);
}

function update_group_watchers(&$group, &$ranks)
{
  $watchers = array($group['leaderid']);

  $members = explode(',', $group['members']);
  foreach($members as $member)
  {
    list($memberid, $rankid) = explode('|', $member);
    if(rank_has_right($ranks, $rankid, 'watcher'))
      $watchers[] = $memberid;
  }
  
  $command = 'UPDATE monster_plaza SET admins=' . quote_smart(implode(',', $watchers)) .
    ' WHERE idnum=' . $group['forumid'] . ' LIMIT 1';
  fetch_none($command, 'updating group watchers');
}

function render_group_badges_xhtml(&$group)
{
  global $GROUP_BADGES;
  
  foreach($GROUP_BADGES as $badge=>$desc)
  {
    if($group['badge-' . $badge] == 'yes')
      echo '<img src="gfx/badges/group/' . $badge . '.png" width="20" height="20" title="' . $desc . '" /> ';
  }
}

function consider_group_badges(&$group)
{
  global $now;

  $age = $now - $group['birthdate'];
  $year = 365 * 24 * 60 * 60;

  if($group['badge-crowd'] == 'no')
  {
    if($group['member_count'] >= 3)
    {
      $group['badge-crowd'] = 'yes';
      set_group_badge($group['idnum'], 'crowd');
    }

    if($group['badge-company'] == 'no')
    {
      if($group['member_count'] >= 2)
      {
        $group['badge-company'] = 'yes';
        set_group_badge($group['idnum'], 'company');
      }
    } // doesn't have "two's company"
  } // doesn't have "three's a crowd"

  if($group['badge-year-plus'] == 'no')
  {
    if($age >= $year * 5)
    {
      $group['badge-year-plus'] = 'yes';
      set_group_badge($group['idnum'], 'year-plus');
    }

    if($group['badge-year-4'] == 'no')
    {
      if($age >= $year * 4)
      {
        $group['badge-year-4'] = 'yes';
        set_group_badge($group['idnum'], 'year-4');
      }

      if($group['badge-year-3'] == 'no')
      {
        if($age >= $year * 3)
        {
          $group['badge-year-3'] = 'yes';
          set_group_badge($group['idnum'], 'year-3');
        }

        if($group['badge-year-2'] == 'no')
        {
          if($age >= $year * 2)
          {
            $group['badge-year-2'] = 'yes';
            set_group_badge($group['idnum'], 'year-2');
          }

          if($group['badge-year-1'] == 'no')
          {
            if($age >= $year)
            {
              $group['badge-year-1'] = 'yes';
              set_group_badge($group['idnum'], 'year-1');
            }
          } // doesn't have 1-year badge
        } // doesn't have 2-year badge
      } // ...
    } // ...
  } // doesn't have 5+-year badge

  if($group['badge-city'] == 'no')
  {
    if($group['towntiles'] >= 100)
    {
      $group['badge-city'] = 'yes';
      set_group_badge($group['idnum'], 'city');
    }

    if($group['badge-town'] == 'no')
    {
      if($group['towntiles'] >= 50)
      {
        $group['badge-town'] = 'yes';
        set_group_badge($group['idnum'], 'town');
      }

      if($group['badge-village'] == 'no')
      {
        if($group['towntiles'] >= 20)
        {
          $group['badge-village'] = 'yes';
          set_group_badge($group['idnum'], 'village');
        }
      }
    }
  }
}

function set_group_badge($groupid, $badge)
{
  $command = 'UPDATE psypets_groups SET `badge-' . $badge . '`=\'yes\' WHERE idnum=' . $groupid . ' LIMIT 1';
  fetch_none($command, 'setting group badge');
}

function get_itemcount_bygroup($groupid, $locid = 0)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=\'group:' . $groupid . '\'';
  $data = fetch_single($command, 'fetching group box item count');

  return $data['c'];
}

function get_items_bygroup($groupid, $locid = 0)
{
  $command = 'SELECT * FROM monster_inventory WHERE user=\'group:' . $groupid . '\' ORDER BY itemname ASC';
  $items = fetch_multiple($command, 'grouplib.php/get_items_bygroup()');

  return $items;
}

function create_group($name, $ownerid)
{
  global $now;

  // create group
  $command = 'INSERT INTO psypets_groups (name, leaderid, members, birthdate) VALUES ' .
             '(' . quote_smart($name) . ', ' . $ownerid . ', \'' . $ownerid . '\', ' . $now . ')';
  fetch_none($command, 'grouplib.php/create_group()');

  $groupid = $GLOBALS['database']->InsertID();

  // create borad
  $command = 'INSERT INTO monster_plaza (title, admins, groupid) VALUES ' .
             '(' . quote_smart($name) . ', \'' . $ownerid . '\', ' . $groupid . ')';
  fetch_none($command, 'grouplib.php/create_group()');

  $boardid = $GLOBALS['database']->InsertID();

  // update group to know it's board
  $command = 'UPDATE psypets_groups SET forumid=' . $boardid . ' WHERE idnum=' . $groupid . ' LIMIT 1';
  fetch_none($command, 'grouplib.php/create_group()');

  return $groupid;
}

function change_group_leader(&$group, $leaderid)
{
  $command = 'UPDATE psypets_groups SET leaderid=' . $leaderid . ' WHERE idnum=' . $group['idnum'] . ' LIMIT 1';
  fetch_none($command, 'changing group leader');
}

function psymail_group_byarray($to, $from, $subject, $body)
{
  $now = time();

  $recipients = array();
  $flag_ids = array();

  foreach($to as $residentid)
  {
    $resident = get_user_byid($residentid, 'user');

    if($resident === false)
      continue;

    $recipients[] = '(' . quote_smart($resident['user']) . ', ' . quote_smart($from) . ", $now, " . quote_smart($subject) . ", " . quote_smart($body) . ')';
    $flag_ids[] = $residentid;
  }

  if(count($recipients) > 0)
  {
    $command = 'INSERT INTO `monster_mail` ' .
               '(`to`, `from`, `date`, `subject`, `message`) ' .
               'VALUES ' .
               implode(', ', $recipients);
    fetch_none($command, 'mailing group members');

    $command = 'UPDATE monster_users SET newmail=\'yes\' WHERE idnum IN (' . implode(',', $flag_ids) . ') LIMIT ' . count($flag_ids);
    fetch_none($command, 'flagging group members as having received new mail');
  }
}

function get_group_member_ids($groupstring)
{
  $members = explode(',', $groupstring);
  
  $userids = array();
  
  foreach($members as $member)
  {
    list($userid) = explode('|', $member);
    $userids[] = $userid;
  }
  
  return $userids;
}

function psymail_group_byid($groupid, $from, $subject, $body)
{
  $group = get_group_byid($groupid);

  if($group === false)
    return;

  $members = get_group_member_ids($group['members']);

  psymail_group_byarray($members, $from, $subject, $body);
}

function get_group_count()
{
  $command = 'SELECT COUNT(*) AS c FROM psypets_groups';
  $data = fetch_single($command, 'fetching group count');

  return $data['c'];
}

function get_groups($sort, $start, $limit)
{
  $command = 'SELECT * FROM psypets_groups ORDER BY ' . $sort . ' LIMIT ' . $start . ', ' . $limit;
  $groups = fetch_multiple($command, 'grouplib.php/get_groups()');

  return $groups;
}

function get_invites_bygroup($groupid)
{
  $command = 'SELECT * FROM psypets_group_invites WHERE groupid=' . $groupid;
  $invites = fetch_multiple($command, 'grouplib.php/get_invites_bygroup()');

  return $invites;
}

function get_invites_byuser($userid)
{
  $command = 'SELECT * FROM psypets_group_invites WHERE residentid=' . $userid;
  $invites = fetch_multiple($command, 'grouplib.php/get_invites_byuser()');

  return $invites;
}

function get_group_byid($idnum)
{
  $command = 'SELECT * FROM psypets_groups WHERE idnum=' . $idnum . ' LIMIT 1';
  $group = fetch_single($command, 'grouplib.php/get_group_byid()');

  return $group;
}

function get_group_byname($name)
{
  $command = 'SELECT * FROM psypets_groups WHERE idnum=' . quote_smart($name) . ' LIMIT 1';
  $group = fetch_single($command, 'grouplib.php/get_group_byname()');

  return $group;
}

function delete_invitation($id)
{
  $command = 'DELETE FROM psypets_group_invites WHERE idnum=' . $id . ' LIMIT 1';
  fetch_none($command, 'grouplib.php/delete_invitation()');
}

function update_user_groups($userid, $groups)
{
  $command = 'UPDATE monster_users SET groups=' . quote_smart(implode(',', $groups)) . ' WHERE idnum=' . $userid . ' LIMIT 1';
  fetch_none($command, 'grouplib.php/update_user_groups()');
}

function update_group_members($groupid, $members)
{
  $command = 'UPDATE psypets_groups SET members=' . quote_smart(implode(',', $members)) . ',member_count=' . count($members) . ' WHERE idnum=' . $groupid . ' LIMIT 1';
  fetch_none($command, 'updating group members');
}

function create_invite($groupid, $userid, $message)
{
  global $now;

  $command = 'INSERT INTO psypets_group_invites (timestamp, groupid, residentid, message) VALUES ' .
             '(' . $now . ', ' . $groupid . ', ' . $userid . ', ' . quote_smart($message) . ')';
  fetch_none($command, 'sending group invitation');
}

function get_invitation_byid($idnum)
{
  $command = 'SELECT * FROM psypets_group_invites WHERE idnum=' . $idnum . ' LIMIT 1';
  $invite = fetch_single($command, 'fetching invitation #' . $idnum);

  return $invite;
}

function get_invitation($groupid, $userid)
{
  $command = 'SELECT * FROM psypets_group_invites WHERE groupid=' . $groupid . ' AND residentid=' . $userid . ' LIMIT 1';
  $invite = fetch_single($command, 'grouplib.php/get_invitation()');

  return $invite;
}

function update_group_profile($groupid, $profile)
{
  $command = 'UPDATE psypets_groups SET profile=' . quote_smart($profile) . ' WHERE idnum=' . $groupid . ' LIMIT 1';
  fetch_none($command, 'updating group profile');
}

function get_group_name_byid($idnum)
{
  $command = 'SELECT name FROM psypets_groups WHERE idnum=' . $idnum . ' LIMIT 1';
  $data = fetch_single($command, 'fetching group name');

  return $data['name'];
}

function kick_group_member(&$group, $targetid, $notify_member = true)
{
  $members = explode(',', $group['members']);

  $i = false;
  $j = false;

  foreach($members as $id=>$member)
  {
    list($memberid, $rank) = explode('|', $member);
    if($memberid == $targetid)
    {
      $i = $id;
      unset($members[$i]);
    }
  }

  if($i !== false)
    update_group_members($group['idnum'], $members);

  // if the resident has not departed, remove this group from their list of
  // memberships, and psymail a notification of their dismissal
  $target = get_user_byid($targetid);

  if($target !== false)
  {
    $groups = take_apart(',', $target['groups']);
    $j = array_search($group['idnum'], $groups);

    if($j !== false)
    {
      unset($groups[$j]);
      update_user_groups($targetid, $groups);
    }

    if($i !== false || $j !== false)
    {
      if($notify_member)
        psymail_user($target['user'], $SETTINGS['site_ingame_mailer'], 'You are no longer a member of ' . $group['name'], 'You were dismissed from the group.');
    }
  }

  return($i !== false || $j !== false);
}

function check_for_group_invites($userid)
{
  $data = $GLOBALS['database']->FetchSingle('SELECT COUNT(idnum) FROM psypets_group_invites WHERE residentid=' . $userid . ' LIMIT 1');

  if($data[0] > 0)
    $command = 'UPDATE monster_users SET newgroupinvite=\'yes\' WHERE idnum=' . $userid . ' LIMIT 1';
  else
    $command = 'UPDATE monster_users SET newgroupinvite=\'no\' WHERE idnum=' . $userid . ' LIMIT 1';

  fetch_none($command, 'updating new group invite flag');
}

function get_group_max_currencies(&$group)
{
  global $now;

  $group_weeks = floor(($now - $group['birthdate']) / (7 * 24 * 60 * 60));

  return ceil(log($group_weeks, 2.5));
}

function get_group_cur_currencies($groupid)
{
  $command = 'SELECT COUNT(idnum) AS c FROM psypets_group_currencies WHERE groupid=' . $groupid;
  $data = fetch_single($command, 'fetching currency count');
  
  return (int)$data['c'];
}
?>
