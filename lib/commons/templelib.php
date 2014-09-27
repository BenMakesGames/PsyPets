<?php
function donate_to_temple($user, $amount)
{
  require_once 'commons/questlib.php';

    $temple_donations = get_quest_value($user['idnum'], 'temple donations');
    $donations = (int)$temple_donations['value'];

    $donations += $amount;

    if($temple_donations === false)
      add_quest_value($user['idnum'], 'temple donations', $donations);
    else
      update_quest_value($temple_donations['idnum'], $donations);

    $badges = get_badges_byuserid($user['idnum']);
    if($badges['pantheon'] == 'no' && $donations >= 5000)
    {
      set_badge($user['idnum'], 'pantheon');

      $body = '5000 moneys donated to the temple!  I cannot thank you enough.<br /><br />' .
              'Please, take this badge.  May the blessings of Ki Ri Kashu\'s children be with you.<br /><br />' .
              '{i}(You earned the Child of Ki Ri Kashu badge!){/}';

      psymail_user($user['user'], 'lsussman', 'Thank you for your contributions!', $body);
    }

    if($badges['pantheon_ii'] == 'no' && $donations >= 50000)
    {
      set_badge($user['idnum'], 'pantheon_ii');

      $body = '50000 moneys donated to the temple!  I cannot thank you enough.<br /><br />' .
              'Please, take this badge.  May the blessings of Ki Ri Kashu\'s children be with you.<br /><br />' .
              '{i}(You earned the Ki Ri Kashu Fanatic badge!){/}';

      psymail_user($user['user'], 'lsussman', 'Thank you for your continued contributions!', $body);
    }
}
?>
