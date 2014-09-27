<?php
$require_login = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; City Hall &gt; Help Desk &gt; Recommended Reading</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="cityhall.php">City Hall</a> &gt; <a href="/help/">Help Desk</a> &gt; "Recommended" Reading</h4>
     <p>All lists are sorted in decreasing order of awesomeness.</p>
     <h5>Stuff on The Internet</h5>
     <ul class="spacedlist">
      <li>
       <a href="http://www.penny-arcade.com/patv/show/extra-credits">Extra Credits</a><br />
       Excellent video series on game design.  Highly recommended for anyone who... ... no, just for anyone :P
      </li>
      <li>
       <a href="http://www.youtube.com/show/sequelitis">Sequelitis</a><br />
       Some Internet dude rants angrily about old video game sequels, yet somehow manages to look carefully and intelligently about what makes each good and bad :P  Very fun :)
      </li>
      <li>
       <a href="http://www.nickyee.com/daedalus/">The Daedalus Project</a><br />
       A series of surveys and other research on MMORPG players.
      </li>
      <li><a href="http://en.wikipedia.org/wiki/Grinding_%28video_gaming%29">Grinding (video gaming)</a> at Wikipedia</li>
      <li><a href="http://en.wikipedia.org/wiki/Experience_point">Experience point</a> at Wikipedia</li>
     </ul>
     <h5>Fun-to-read Fiction Books</h5>
     <ul class="spacedlist">
      <li>
       <cite>A Series of Unfortunate Events</cite> by "Lemony Snicket"<br />
       A gothic tridecalogy about a mechanical engineer, a bookworm, a baby, and a conspiracy :P  You'll find several references in PsyPets.
      </li>
      <li>
       <cite>His Dark Materials</cite> by Philip Pullman<br />
       A triology about kids traveling through parallel universe with their familiars/pets.  That makes it sound cute; it can get pretty dark.
      </li>
      <li>
       <cite>Journey to the Center of the Earth</cite> by Jules Verne<br />
       I love Victorian era writing styles, and this is one of the early fictions on the hollow earth.  Can't go wrong :)
      </li>
      <li>
       <cite>The Hobbit</cite> by J.R.R. Tolkein<br />
       Just about every modern fantasy ANYTHING draws from Tolkein's representations of elves and dwarves.  Also, PsyPets has a "One Ring", so... &gt;_&gt;
      </li>
     </ul>
     <h5>Games</h5>
     <ul class="spacedlist">
      <li>
       Every video game I ever played<br />
       PsyPets contains references to Zelda, Mario, Final Fantasy VII, and even text-adventure games and rogue-likes.  And that's just what I can think of off the top of my head.  Some of these references are subtle; most are not.
      </li>
      <li>
       <cite>Mage: The Ascension (Revised Edition)</cite> edited by White Wolf Publishing<br />
       This is the source book for the table-top role-playing game Mage.  PsyPets borrows aspects of White Wolf's dice system.  It is also one of my favorite table-top role-playing game systems and settings.
      </li>
      <li>
       <cite>Dungeons &amp; Dragons</cite> (every edition) by various people<br />
       PsyPets borrows heavily from RPGs, and RPGs (especially western RPGs) borrow heavily from Dungeons &amp; Dragons.  I also like to make fun of D&amp;D, beacuse it's an easy target :P  There is no shortable of D&amp;D references in PsyPets.
      </li>
     </ul>
     <h5>TV &amp; Movies</h5>
     <ul class="spacedlist">
      <li>
       <cite>Star Wars</cite><br />
       Mostly the original series, but I'm sure I've made fun of the newer ones somewhere, too...
      </li>
      <li>
       <cite>Indiana Jones</cite><br />
       But not the fourth one.
      </li>
      <li>
       <cite>The Matrix</cite><br />
       But only the first one &gt;_&gt;
      </li>
      <li>
       <cite>The X-Files</cite><br />
       PsyPets has too many aliens <em>not</em> to include X-Files references :P
      </li>
      <li>
       <cite>Star Trek</cite><br />
       Mostly The Next Generation/DS9.
      </li>
     </ul>
     <h5>Reference-type Books</h5>
     <ul class="spacedlist">
      <li>
       <cite>The Element Encyclopedia of Magical Creatures</cite> by John &amp; Caitlin Matthews<br />
       Exactly what it sounds like - an encyclopedia of creatures from mythology.  A hefty tome that's fun to flip through.
      </li>
      <li>
       <cite>Nature's Ways</cite> by Ruth Binney<br />
       Explains the history behind the lore and legends of various animals, plants, and other natural things, for example why foxes are cunning, or lilies pure.
      </li>
      <li>
       <cite>Fantasy Encyclopedia</cite>, by Judy Allen<br />
       Primarily made for children, this is a visual encyclopedia of monsters, creatures, elves, wizards, and all that.
      </li>
      <li>
       <cite>The Gruesome Guide to World Monsters</cite> by Judy Sierra<br />
       Another children's book, this one provides bizarre illustrations of even bizarrer - yes, "bizarrer" - monsters, and tips for surviving encounters with them.
      </li>
      <li>
       <cite>Dictionary of Ancient Deities</cite> by Patricia Turner &amp; Charles Russell Coulter<br />
       I cannot believe how much information is in this book, not just on deities, but associated monsters and creatures.
      </li>
      <li>
       <cite>A Dictionary of Creation Myths</cite> by David &amp; Margaret Leeming<br />
       I've had a lot of fun reading this one, and writing creation "myths" of my own :)
      </li>
      <li>
       <cite>A Dictionary of Celtic Mythology</cite> by James MacKillop<br />
       I love these dictionary-type mythology books.  It's fun to just flip through and see what you find.
      </li>
      <li>
       <cite>Myths and Legends of Japan</cite> by F. Hadland Davis<br />
       Contains a large number of stories from Japanese mythology.
      </li>
      <li>
       <cite>Sneakier Uses for Everyday Things</cite> by Cy Tymony<br />
       Instructions on how to make everything from metal detectors to hidden pockets for your coat.
      </li>
      <li>
       <cite>Alchemy &amp; Mysticism</cite> by Alexander Roob<br />
       Contains countless pictures from alchemy, Masonry, the bible, and other arcane things, with bits of text about them.  Not so good for reading straight through, but can be fun to flip through and go "ooh, what's that picture all about?"
      </li>
      <li>
       <cite>The History of Atlantis</cite> by Lewis Spence<br />
       This book is a little boring to read, but there's a lot of information on Atlantis, and other things believed to now reside in the hollow earth.
      </li>
      <li>
       <cite>The 22 Immutable Laws of Marketing</cite> by Al Ries &amp; Jack Trout<br />
       Probably not the most entertaining on this list >_>  But there were some interesting ideas here.
      </li>
     </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
