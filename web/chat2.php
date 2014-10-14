<?php
$require_petload = 'no';
$child_safe = false;

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; Chat Room (beta)</title>
<?php include "commons/head.php"; ?>
<script type="text/javascript">
function addText(text)
{
  if(text.length > 0)
  {
    var objDiv = document.getElementById("textfield");

    objDiv.innerHTML += text;

    while(objDiv.innerHTML.length > 4096 && objDiv.innerHTML.indexOf("<br>") >= 0)
      objDiv.innerHTML = objDiv.innerHTML.substring(objDiv.innerHTML.indexOf("<br>") + 4);

    while(objDiv.innerHTML.length > 4096 && objDiv.innerHTML.indexOf("<br />") >= 0)
      objDiv.innerHTML = objDiv.innerHTML.substring(objDiv.innerHTML.indexOf("<br />") + 6);

    objDiv.scrollTop = objDiv.scrollHeight;
  }
}

function showMenu()
{
}
</script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Chat Room (fairly beta)</h4>
     <div id="instructions">
     <p>This is an <strong>experimental</strong> chat system.  This is not a finished product, and may occasionally behave oddly.  Keep this in mind.</p>
     <p>Oh, also, <em>it requires Java!</em></p>
     <p>If you don't like your name color, type "set color 336699" or some other hex color (like HTML colors).</p>
     <p>[ <a href="#" onclick="document.getElementById('instructions').style.display='none';">hide all this silly text.  it's in my way.</a> ]</p>
     </div>
     <div id="textfield" style="width: 600px; height: 400px; overflow: auto; padding: 0.5em; border: 1px solid #888; background-color: #fff"></div>
     <applet code="ChatClient/ChatClient2c.class" width="600" height="55" codebase="http://psypets.net/" alt="You need Java to use PsyPets' Live Chat" cache_version="2.3.0.0" mayscript>
      You need Java to use PsyPets' Live Chat
     </applet>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
