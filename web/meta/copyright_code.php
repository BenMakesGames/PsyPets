<?php
require_once 'commons/init.php';

$require_login = 'no';
$require_petload = 'no';
$reading_tos = true;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

include 'commons/html.php';
?>
<head>
<title>PsyPets &gt; Help &gt; Copyright Information &gt; Code Libraries</title>
<?php include 'commons/head.php'; ?>
<script type="text/javascript">
$(function() {
  $('#library-tabs li a').click(function() {
    $('#library-tabs li').removeClass('activetab');
    $(this).parent().addClass('activetab');
    
    $('.tab-content').hide();
    $('#' + $(this).attr('id') + '-content').show();
    
    return false;
  });
});
</script>
</head>
<body>
<?php include 'commons/header_2.php'; ?>
<h4><a href="/help/">Help</a> &gt; Copyright Information &gt; Code Libraries</h4>
<ul class="tabbed">
 <li><a href="/meta/copyright.php">General Copyright Information</a></li>
 <li><a href="/meta/copyright_smallgfx.php">Item, Pet and Avatar Graphics</a></li>
 <li><a href="/meta/copyright_mahjong.php">Mahjong Graphics</a></li>
 <li><a href="/meta/copyright_largegfx.php">NPC Graphics</a></li>
 <li class="activetab"><a href="/meta/copyright_code.php">Code Libraries</a></li>
</ul>
<p>PsyPets wouldn't be possible without open-source software, not just the stuff listed below, but Linux, Apache, GZIP, PNG, and hundreds of pieces of software I'm sure I'm not even aware of!</p>
<p>To the thousands of people who came before me and made this all possible, thank you!</p>
<ul class="tabbed" id="library-tabs">
 <li class="activetab"><a href="#" id="jquery-tab">jQuery</a></li>
 <li><a href="#" id="toolman-tab">ToolMan</a></li>
 <li><a href="#" id="wz-tooltips-tab">wz_tooltips</a></li>
 <li><a href="#" id="jquery-megamenu-tab">jQuery MegaMenu</a></li>
 <li><a href="#" id="jquery-textarea-resizer-tab">jQuery Textarea Resizer</a></li>
 <li><a href="#" id="protovis-tab">Protovis</a></li>
 <li><a href="#" id="jquery-tablesorter-tab">jQuery Tablesorter 2.0</a></li>
</ul>
<div id="jquery-tab-content" class="tab-content">
<h4>jQuery</h4>
<p>Copyright (c) 2010 John Resig, <a href="http://jquery.com/">http://jquery.com/</a></p>
<p>Distributed under <a href="http://www.opensource.org/licenses/mit-license.php">the MIT License</a>.</p>
<h5>How PsyPets Uses It</h5>
<p>When you click on something on a web site, and the content magically changes without the page reloading, JavaScript - a programming language understood by all modern browsers - is probably responsible.  (Sometimes it's Flash, but that's... well, nevermind Flash right now.)</p>
<p>All the cool web pages you know and love, from Google to Facebook, use JavaScript because of how awesome it can make web pages.  JavaScript, however, can be a bit difficult to work with from time to time, and has a number of infamous quirks and misbehaviors...</p>
<p>Thankfully, jQuery exists.  It's got its own quirks, to be sure, but jQuery not only makes dealing with JavaScript a bearable task by eliminating cross-browser issues and wrapping common tasks into easy-to-use functions, it also fills in a lot of gaps and provides some niceties that plain JavaScript would otherwise be missing.</p>
<p>jQuery is a pretty popular library among web developers, and is working its magic behind the scenes on a lot of the web pages you visit on a regular basis, PsyPets included!  (You visit regularly, right!? :P)</p>
</div>
<div id="toolman-tab-content" class="tab-content" style="display:none;">
<h4>ToolMan</h4>
<p>Copyright (c) 2005 Tim Taylor Consulting <a href="http://tool-man.org/">http://tool-man.org/</a></p>
<p>Distributed under <a href="http://www.opensource.org/licenses/mit-license.php">the MIT License</a>.</p>
<h5>How PsyPets Uses It</h5>
<p>ToolMan is another JavaScript library, and to be completely honest, a lot of what ToolMan does, jQuery can also do.</p>
<p>So why have both?  Partly, historical reasons: I found ToolMan before I found jQuery.  But the reason I've stuck with ToolMan instead of going all jQuery is that ToolMan still does a couple specific jobs more easily and succinctly than jQuery.  Anywhere on PsyPets you can drag-and-drop stuff around - rearranging pets or your rooms, for example - ToolMan's doing all the heavy lifting.</p>
</div>
<div id="wz-tooltips-tab-content" class="tab-content" style="display:none;">
<h4>wz_tooltips</h4>
<p>Copyright (c) 2002-2007 Walter Zorn.</p>
<p>Distributed under <a href="http://www.gnu.org/licenses/lgpl-2.1.html">LGPL 2.1</a>.</p>
<h5>How PsyPets Uses It</h5>
<p>wz_tooltips is another older JavaScript library, this one responsible for the tooltips on PsyPets.  Again, sure, a jQuery plugin could do this, but wz_tooltips was designed for the purpose, and avoids all the overhead that comes with working in jQuery.</p>
</div>
<div id="jquery-megamenu-tab-content" class="tab-content" style="display:none;">
<h4>jQuery MegaMenu</h4>
<p>The jQuery MegaMenu by GeekTantra is distributed under the <a href="http://www.apache.org/licenses/LICENSE-2.0">Apache License, 2.0</a></p>
<p>The code has been modified by Ben Hendel-Doying for use in PsyPets.</p>
<h5>How PsyPets Uses It</h5>
<p>See that menu up on top?  Well, it's nothing fancy when you're logged out, but logged in, it's amazing!</p>
<p>First of all, it follows you as you scroll around the page, but second of all, "megamenus" are wider than the common drop-down menus we're more-familiar with, often having two or three columns.  Some columns might even have navigation content that doesn't even quite look like a menu.</p>
<p>Used right, megamenus are very powerful; I hope you find PsyPets has made good use of the megamenu concept.</p>
</div>
<div id="jquery-textarea-resizer-tab-content" class="tab-content" style="display:none;">
<h4>jQuery Textarea Resizer</h4>
<p>Copyright 2008 "binarybasher" (<a href="http://plugins.jquery.com/users/binarybasher">http://plugins.jquery.com/users/binarybasher</a>). All rights reserved.</p>
<p>Distributed under <a href="http://www.opensource.org/licenses/bsd-license.php">the BSD License</a>, with the following addition:</p>
<p>The views and conclusions contained in the software and documentation are those of the
authors and should not be interpreted as representing official policies, either expressed
or implied, of "binarybasher".</p>
<h5>How PsyPets Uses It</h5>
<p>You know how sometimes text fields aren't quite big enough?  How you find yourself wanting just a bit more room?</p>
<p>"Yeah, Ben... but so I just drag them bigger..."</p>
<p>Well aren't you fancy, using a progressive browser like Chrome or Firefox.</p>
<p>Well, actually, you totally are.  Keep that up.</p>
<p>But some people insist on IE, or other, <em>even-weirder</em> browsers.  jQuery Textarea Resizer gives resizeable text fields to everyone.</p>
</div>
<div id="protovis-tab-content" class="tab-content" style="display:none;">
<h4><a href="http://mbostock.github.com/protovis/">Protovis</a></h4>
<p>Distributed under <a href="http://www.opensource.org/licenses/bsd-license.php">the BSD License</a>.</p>
<h5>How PsyPets Uses It</h5>
<p>Protovis is a graphing library.  With very little code, it can render nice-looking, interactive charts.</p>
<p>This is mainly used for for <a href="/statistics.php">graphing certain site statistics</a>, but also for some internal administrative tools.  I also hope to apply it to PsyPets market data, but I keep getting distracted with other things :P</p>
</div>
<div id="jquery-tablesorter-tab-content" class="tab-content" style="display:none;">
<h4>jQuery Tablesorter 2.0</h4>
<p>By Christian Bach, distributed under the <a href="http://www.opensource.org/licenses/mit-license.php">MIT License</a>.</p>
<h5>How PsyPets Uses It</h5>
<p>jQuery Tablesorter lets tables be sorted without reloading the page!  (More sneaky JavaScript stuff!)  PsyPets doesn't use this nearly as much as it should, but it's getting there, one step at a time.</p>
<p>The most noteable place it's used at the moment is probably the PsyPets Mailbox.</p>
</div>
<?php include 'commons/footer_2.php'; ?>
</body>
</html>
