<?php
//require_once "commons/security.php";

function get_graphic_byid($id)
{
  $command = "SELECT * FROM `monster_graphicslibrary` WHERE `idnum`=" . quote_smart($id) . " LIMIT 1";
  $graphic = fetch_single($command, 'fetching graphics library graphic by id');

  return $graphic;
}

function get_graphics_byuserid($userid, $height)
{
  $command = "SELECT * FROM `monster_graphicslibrary` WHERE `h`=$height AND (`recipient`=0 OR `recipient`=" . quote_smart($userid) . ")";
  $graphics = fetch_multiple($command, 'fetching graphics library graphics for resident');

  return $graphics;
}

function record_graphic_use($id, $graphic, $uploader)
{
  // update copyright information
  $command = 'INSERT INTO `monster_graphics` (`title`, `graphic`, `text`, `names`, `year`, `rights`, `source`) VALUES ' .
             '(' . quote_smart($graphic['title']) . ', ' . quote_smart($graphic['url']) . ', ' .
             quote_smart('Uploaded by ' . $uploader['display']) . ', ' .
             quote_smart($graphic['author']) . ', ' . quote_smart(date('Y')) . ', ' . quote_smart($graphic['rights']) . ', ' . quote_smart($graphic['source']) . ')';
  $result = fetch_none($command, 'adding entry to copyright information page');

  $command = "DELETE FROM `monster_graphicslibrary` WHERE `idnum`=$id LIMIT 1";
  $result = fetch_none($command, 'deleting graphic from graphics library');
}
?>
