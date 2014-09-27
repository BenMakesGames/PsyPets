<?php
if(($now_year == 2010 && $now_day == 18 && $now_month == 11)
  || ($now_year == 2010 && $now_day == 19 && $now_month == 11))
{
  $badges = get_badges_byuserid($user['idnum']);
  
  if($badges['leonids'] == 'no')
  {
    set_badge($user['idnum'], 'leonids');
    
    psymail_user($user['user'], 'thaddeus', 'Did you see the Leonids?', '<i>(You received the The Leonids Badge!)</i>');
  }
}
?>
