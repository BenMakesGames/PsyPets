<?php
if($npc['graphic'] && $npc['width'] && $npc['height'])
{
  if($npc['name'])
    echo '<a href="/npcprofile.php?npc=' . $npc['name'] . '">';

  echo '<img src="//' . $SETTINGS['static_domain'] . '/gfx/' . $npc['graphic'] . '" width="' . $npc['width'] . '" height="' . $npc['height'] . '" alt="' . $npc['title'] . '" title="' . $npc['title'] . '" style="float:right;" />';

  if($npc['name'])
    echo '</a>';
}

if($npc['dialog'])
{
  require WEB_ROOT . '/views/_template/dialog_open.php';

  echo $npc['dialog'];

  require WEB_ROOT . '/views/_template/dialog_close.php';
}

if($npc['options'])
  echo '<ul><li>' . implode('</li><li>', $npc['options']) . '</li></ul>';
?>
