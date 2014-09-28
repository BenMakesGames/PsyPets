<?php
$require_login = 'no';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/doevent.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/parklib.php';

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; Support PsyPets</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php
include 'commons/header_2.php';

  echo '';
?>
    <h4>Favor? I want Favor? How do I get Favor?</h4>
    <p>Many years ago, people would support PsyPets by donating money. Unwilling to accept money for nothing, I offered to create custom pet graphics, items, and other content in exchange.</p>
    <p>As time went on, it sort of became expected that a donation would get you this stuff, and as more people began playing, it was impossible for me to keep up with requests, so I coded a system to automatically handle most requests, and eventually "Favor" was born: a special currency you could buy with dollars, and spend on making custom stuff.</p>
    <p></p>
    <p>This remained for many years, but at this point, I really just want you guys to have fun with the game, and never mind the money. I have a full-time job, and am not working on PsyPets nearly as much as I used to, anyway, so: just play; just have fun! If you feel like PsyPets is just SO AWESOME, you HAVE to give me a few dollars: thank you! I appreciate the thought! I'm glad PsyPets is so much fun! I'm having fun hosting it, and that is enough :)</p>
    <p>(Although, if you want to take me out for Indian food, maybe we could arrange something... :P)</p>
    <p>"But Ben: what if I still want custom content and all those fun things Favor could buy?"</p>
    <p>Fair! Fortunately, there are ways to get Favor just by playing the game! If you build an EXCEPTIONAL Totem Pole, you can trade it in for Favor, and Favor can sometimes be found in The Pattern. And I think maybe there was another way, but I forget...</p>
    <p>Anyway, it is currently difficult to get Favor by playing, but I'm looking into making it - or some of the services it bought - more easily available.</p>
    <p>Until then, have fun, and thanks for playing! :)</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
