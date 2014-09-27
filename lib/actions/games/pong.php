<?php
if($okay_to_be_here !== true)
  exit();

$movie_path = $SETTINGS['site_url'] . '/actions/games/psypets_pong.swf';
?>
<!--url's used in the movie-->
<!--text used in the movie-->
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="512" height="384" id="psypets_pong" align="middle">
<param name="allowScriptAccess" value="sameDomain" />
<param name="movie" value="<?= $movie_path ?>" />
<param name="quality" value="high" />
<param name="bgcolor" value="#000000" />
<embed src="<?= $movie_path ?>" quality="high" bgcolor="#000000" width="512" height="384" name="psypets_pong" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>
