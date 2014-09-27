<?php
function xhtml_tabs($_tabs)
{
  echo '<ul class="tabbed">';

  foreach($_tabs as $title=>$link)
  {
    if($link === false)
      echo '<li>' . $title . '</li>';
    else if($link == '')
      echo '<li class="activetab"><a href="">' . $title . '</a></li>';
    else
      echo '<li><a href="' . $link . '">' . $title . '</a></li>';
  }

  echo '</ul>';
}

function xhtml_npc($_npc)
{
  if($_npc['graphic'] && $_npc['width'] && $_npc['height'])
  {
    if($_npc['name'])
      echo '<a href="/npcprofile.php?npc=' . $_npc['name'] . '">';

    echo '<img src="' . $_npc['graphic'] . '" width="' . $_npc['width'] . '" height="' . $_npc['height'] . '" alt="' . $_npc['title'] . '" title="' . $_npc['title'] . '" style="float:right;" />';

    if($_npc['name'])
      echo '</a>';
  }

  if($_npc['dialog'])
  {
    require LIB_ROOT . '/views/_template/dialog_open.php';

    echo $_npc['dialog'];

    require LIB_ROOT . '/views/_template/dialog_close.php';
  }

  if($_npc['options'])
    echo '<ul><li>' . implode('</li><li>', $_npc['options']) . '</li></ul>';
}
