<?php
$whereat = 'broadcasting';
$wiki = 'Broadcasting';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/shoutcast.php';

$userprofile = get_user_profile($user['idnum']);

$status = shoutcast_status('www.psypets.net', 7000);

if($_GET['status'] == 'online')
  $status = 'online';

if($_POST['action'] == 'sendmail')
{
  $subject .= $user['display'];

  if($userprofile['pronunciation'] != '')
    $subject .= ' (' . $userprofile['pronunciation'] . ')';

  $subject .= ' [' . $userprofile['gender'] . '] says:';
  $message = trim($_POST['body']);

  if(strlen($message) <= 1)
    $error = 'Try a <em>slightly</em> longer message :)';
  else
  {
    $message = format_text($message);

    mail('broadcasting@psypets.net', $subject, $message, "MIME-Version: 1.0\nContent-type: text/html; charset=iso-8859-1\nFrom: sender@telkoth.net");

    $message = 'Your message was sent successfully.';
  }
}
else if($_POST['action'] == 'Update')
{
  $userprofile['pronunciation'] = htmlspecialchars(trim($_POST['pronunciation']));

  $command = 'UPDATE monster_profiles SET pronunciation=' . quote_smart($userprofile['pronunciation']) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating resident\'s pronunciation guide');
}

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; Live Broadcasting</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
<?php
if($status == 'online')
{
?>
<div style="float:right;">
<applet code="javazoom.jlgui.player.amp.PlayerApplet.class" java_codebase="./" archive="jl/jlguiapplet2.3.2.jar,jl/jlgui2.3.2-light.jar,jl/tritonus_share.jar,jl/basicplayer2.3.jar,jl/mp3spi1.9.2.jar,jl/jl1.0.jar,jl/vorbisspi1.0.1.jar,jl/jorbis-0.0.13.jar,jl/jogg-0.0.7.jar,jl/commons-logging-api.jar" width="275" height="232" name="player">
<param name="code" value="javazoom.jlgui.player.amp.PlayerApplet.class">
<param name="codebase" value="./">
<param name="archive" value="jl/jlguiapplet2.3.2.jar,jl/jlgui2.3.2-light.jar,jl/tritonus_share.jar,jl/basicplayer2.3.jar,jl/mp3spi1.9.2.jar,jl/jl1.0.jar,jl/vorbisspi1.0.1.jar,jl/jorbis-0.0.13.jar,jl/jogg-0.0.7.jar,jl/commons-logging-api.jar">
<param name="name" value="player">
<param name="type" value="application/x-java-applet;version=1.4">
<param name="scriptable" value="false">
<param name="song" value="jl/playlist.m3u">
<param name="start" value="no">
<param name="skin" value="jl/skins/netzampvx.wsz">
<param name="init" value="jl/jlgui.ini">
<param name="location" value="none">
<param name="useragent" value="winampMPEG/2.7">
</applet>
<center><i><a href="/meta/copyright_code.php#jlguiapplet">jlGui MP3 Player Applet by JavaZOOM</a></i></center>
</div>
<h4>Live Broadcasting (<span class="success">currently online</span>)</h4>
<p><a href="http://www.psypets.net:7000/listen.pls">Tune in with your favorite media player</a>, or use the Java applet on the right.<p>
<?php
}
else
{
  echo '     <h4>Live Broadcasting (<span class="failure">currently offline</span>)</h4>';
}

if($message)
  echo "     <p class=\"success\">$message</p>\n";

if($error)
  echo "     <p class=\"failure\">$error</p>\n";
?>
     <p><a href="/residentprofile.php?resident=<?= link_safe('That Guy Ben') ?>">That Guy Ben</a> talks about PsyPets and responds to your questions, comments, and suggestions (sent from this page only) <em>with his voice!</em>  Warning: responses may contain rambling.</p>
     <p>If you can't make it for the live broadcasts (Friday at 7:00 PM, US eastern time), MP3 recordings are available at <a href="http://www.psypets.net/broadcasting">psypets.net/broadcasting</a>.</p>
<ul class="tabbed">
 <li><a href="/livebroadcast.php">Suggest a Topic</a></li>
 <li class="activetab"><a href="/broadcast2.php">Send an E-mail</a></li>
</ul>
     <h5>Send A Question, Comment, or Suggestion (or Whatever)</h5>
     <p>Nonsense like "hi" and "visit my store" will be ignored.  Repeated such "questions" may result in you being denied use of this form.  Forever.</p>
     <form action="/broadcast2.php" method="post">
     <p><textarea name="body" style="width: 24em; height: 12em;"></textarea></p>
     <p><input type="hidden" name="action" value="sendmail" /><input type="submit" value="Send" /></p>
     </form>

<h4>Optional!</h4>
<p>When I read your message, I will start with "<?= $user['display'] ?> says...", however I might suck at pronouncing your name...</p>
<form action="/broadcast2.php" method="post">
<p><b>Pronunciation guide:</b> <input type="text" name="pronunciation" maxlength="30" size="30" value="<?= $userprofile['pronunciation'] ?>" /> <input type="submit" name="action" value="Update" /></p>
</form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
