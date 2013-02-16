<?php
// THE PARK CODE IS IMMUNE TO MISSING ACCOUNTS
// it does not need to be disabled with $NO_PVP

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/doevent.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

if($user['show_park'] != 'yes')
{
  header('Location: /404');
  exit();
}

// THE PARK CODE IS IMMUNE TO MISSING ACCOUNTS
// it does not need to be disabled with $NO_PVP

$msgs = array();
$pets_signed_up = 0;

if($_POST['submit'] == 'Sign Up')
{
  foreach($_POST as $key=>$value)
  {
    if(substr($key, 0, 2) == 'e_' && (int)$value > 0)
    {
      $parkid = (int)substr($key, 2);
      $petid = (int)$value;
      
      $command = 'SELECT * FROM monster_events WHERE idnum=' . $parkid . ' AND finished=\'no\' LIMIT 1';
      $event = $database->FetchSingle($command, 'fetching park event');
      
      if($event === false)
      {
        $msgs[] = 76;
      }
      else
      {
        $ok = false;

        for($i = 0; $i < count($userpets); ++$i)
        {
          if($userpets[$i]['idnum'] == $petid && $userpets[$i]['dead'] == 'no' && $userpets[$i]['changed'] == 'no' && $userpets[$i]['zombie'] == 'no')
          {
            $ok = true;
            $index = $i;
            break;
          }
        }

        if(!$ok)
          $msgs[] = 9;

        for($i = 0; $i < count($userpets); ++$i)
        {
          if(strpos($event['participants'], '<' . $userpets[$i]['idnum'] . '>') !== false)
          {
            $ok = false;
            $msgs[] = '75:' . $userpets[$i]['petname'];
            break;
          }
        }

        if($user['user'] == $event['host'])
          $msgs[] = 74;
        else if($ok == true)
        {
          $participants = array();

          if(strlen($event['participants']) > 0)
          {
            $participants = explode(',', $event['participants']);
            $event['participants'] .= ',';
          }

          $event['participants'] .= '<' . $userpets[$index]['idnum'] . '>';

          if(count($participants) >= $event['minparticipant'])
            $msgs[] = 107;
          else if(pet_level($userpets[$index]) < $event['minlevel'] || pet_level($userpets[$index]) > $event['maxlevel'])
            $msgs[] = '108:' . $userpets[$index]['petname'];
          else if($userpets[$index]['park_event_hours'] < 8)
            $msgs[] = '155:' . $userpets[$index]['petname'];
          else if($user['money'] < $event['fee'])
          {
            $msgs[] = 22;
            break;
          }
          else
          {
            if($event['fee'] > 0)
            {
              $user['money'] -= $event['fee'];
              $command = 'UPDATE monster_users SET money=money-' . $event['fee'] . ' WHERE `user`=' . quote_smart($user["user"]) . ' LIMIT 1';
              $database->FetchNone($command, 'taking park event entrance fee');

              add_transaction($user['user'], $now, 'Park event entrance fee', -$event["fee"]);
            }

            $command = 'UPDATE `monster_events` ' .
                       'SET `participants`=' . quote_smart($event['participants']) . ' ' .
                       'WHERE idnum=' . $event['idnum'] . ' LIMIT 1';
            $database->FetchNone($command, 'update participant count');

            if(count($participants) + 1 == $event['minparticipant'])
            {
//              echo "DoEvent(" . $event["idnum"] . ");<br>\n";
              DoEvent($event['idnum']);
            }

            $database->FetchNone('UPDATE monster_pets SET park_event_hours=park_event_hours-8 WHERE idnum=' . $userpets[$index]['idnum'] . ' LIMIT 1');
            $userpets[$index]['park_event_hours'] -= 8;
            
            $pets_signed_up++;

            $msgs[] = '79:' . $userpets[$index]['petname'] . ':' . $event['fee'];
          }
        }
      }
    }
  }
  
  if(count($msgs) == 0)
    $msgs[] = 109;
}

if($pets_signed_up > 0)
{
  require_once 'commons/statlib.php';
  record_stat($user['idnum'], 'Signed Up a Pet For a Park Event', $pets_signed_up);
}

$urlparam = 'action=' . $_GET['action'] . '&eventtype=' . $_GET['eventtype'] . '&petid=' . $_GET['petid'] . '&page=' . $_GET['page'] . '&sort=' . $_GET['sort'];

header('Location: ./park.php?' . $urlparam . '&msg=' . implode(',', $msgs));
?>
