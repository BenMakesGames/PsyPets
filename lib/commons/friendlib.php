<?php
function is_a_friend($userid, $friendid)
{
  $friend = fetch_single('
    SELECT idnum
    FROM psypets_user_friends
    WHERE
      userid=' . (int)$userid . '
      AND friendid=' . (int)$friendid . '
    LIMIT 1
  ');

  return($friend !== false);
}
?>
