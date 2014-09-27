<?php
if($_GET['dialog'] == 3)
{
  $given = delete_inventory_byname($user['user'], $PLUSHY_QUEST_LIST[$collection_step - 1], 1, 'storage');

  if($given == 0)
    $plushy_quest_tease = true;
  else
  {
    if($collection_step == 1)
    {
      $plushy_quest_intro = true;
    }
    else if($collection_step < 10)
    {
      $plushy_quest_update = true;

      if($collection_step == 3)
        $plushy_quest_hint = true;
    }
    else
    {
      $plushy_quest_done = true;
      set_badge($user['idnum'], 'plushycollector');
      add_inventory($user['user'], '', 'Deed to 100 Units', 'Given by Nina the Smith', $user['incomingto']);
      $ask_for_plushy = false;
    }

    $collection_step++;
    update_quest_value($quest_plushy_collection['idnum'], $collection_step);
  }
}
?>
