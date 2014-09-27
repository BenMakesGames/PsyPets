<?php
define(FLASH_MESSAGE_PET_PROGRESS, 1);
define(FLASH_MESSAGE_GENERAL_MESSAGE, 2);
define(FLASH_MESSAGE_ALLOWANCE, 3);
define(FLASH_MESSAGE_BADGE, 4);
define(FLASH_MESSAGE_PET_BADGE, 5);
define(FLASH_MESSAGE_GENERAL_SUCCESS, 6);
define(FLASH_MESSAGE_GENERAL_FAILURE, 7);

$DB_MESSAGE_CATEGORY_NAMES = array(
  FLASH_MESSAGE_PET_PROGRESS => 'pet-progress',
  FLASH_MESSAGE_GENERAL_MESSAGE => 'general-message',
  FLASH_MESSAGE_ALLOWANCE => 'daily-allowance',
  FLASH_MESSAGE_BADGE => 'resident-badges',
  FLASH_MESSAGE_PET_BADGE => 'pet-badges',
  FLASH_MESSAGE_GENERAL_SUCCESS => 'general-success',
  FLASH_MESSAGE_GENERAL_FAILURE => 'general-failure',
);

function add_db_message($userid, $category, $message)
{
  fetch_none('
    INSERT INTO psypets_flash_messages
    (timestamp, userid, category, message)
    VALUES
    (' . time() . ', ' . (int)$userid . ', ' . (int)$category . ', ' . quote_smart($message) . ')
  ');
}

function get_new_db_messages($userid)
{
  $messages = fetch_multiple('
    SELECT timestamp,category,message
    FROM psypets_flash_messages
    WHERE
      userid=' . (int)$userid . '
      AND new=\'yes\'
  ');

  if(count($messages) > 0)
  {
    fetch_none('
      UPDATE psypets_flash_messages
      SET new=\'no\'
      WHERE
        userid=' . (int)$userid . '
        AND new=\'yes\'
    ');
  }

  return $messages;
}

function get_all_db_messages($userid)
{
  $messages = fetch_multiple('
    SELECT timestamp,category,message
    FROM psypets_flash_messages
    WHERE userid=' . (int)$userid . '
    ORDER BY timestamp DESC
  ');

  return $messages;
}
?>
