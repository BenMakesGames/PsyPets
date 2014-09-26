<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_login = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/xhtmlhelpers.php';

$npc = array(
  'graphic' => '//' . $SETTINGS['static_domain'] . '/gfx/npcs/receptionist.png',
  'width' => 350,
  'height' => 275,
  'name' => 'Claire Silloway',
  'dialog' => '<p>New to ' . $SETTINGS['site_name'] . '? Feeling a little confused? PsyPets is a big place, and there\'s a lot to it, but hopefully I can point you in the right direction.</p>',
);

if($user !== false)
{
  $tabs = array(
    'Bulletin Board' => '/cityhall.php',
    'Help Desk' => '',
    'Room 106' => '/cityhall_106.php',
    'Room 210' => '/cityhall_210.php',
    'Name Change Application' => '/af_resrename2.php',
    'Pet Transfer' => '/af_movepet2.php',
  );

  $totem_quest = QuestValue::Get($user['idnum'], 'totem quest');

  if($totem_quest->IsLoaded())
  {
    if($totem_quest->Value() == 3)
    {
      if($_GET['dialog'] == 'archaeologist')
      {
        $totem_quest->UpdateValue(4);
        $npc['dialog'] = '
          <p>Oh, you\'re looking for Julio Beiler.  His office is on the second floor.  Take the stairs over there to the second floor, and turn left down the hallway.  I believe his will be the third door on the left.  It\'s room 210, in any case.</p>
          <p><i>(Room 210 has been added to the City Hall.)</i></p>
        ';
      }
      else
        $npc['options'][] = '<a href="?dialog=archaeologist">Ask for HERG\'s archaeologist</a>';
    }
  }

  if(!$totem_quest->IsLoaded() || $totem_quest->Value() < 4)
    unset($tabs['Room 210']);
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; City Hall &gt; Help Desk</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
    <h4><a href="/cityhall.php">City Hall</a> &gt; Help Desk</h4>
<?= xhtml_tabs($tabs); ?>
<?= xhtml_npc($npc); ?>
    <h5>Game Play Basics</h5>
    <ul class="spacedlist">
     <li>
      <a href="/help/petcare.php">Pet Care</a><br />
      Information on how to take care of, raise, and even equip your pets.
     </li>
     <li>
      <a href="http://wiki.psypets.net/The_Psychology_Behind_PsyPets">The Psychology Behind PsyPets</a> (at <a href="http://wiki.psypets.net/">PsyHelp</a>)<br />
      The theories behind the game, and their application.
     </li>
     <li>
      <a href="http://wiki.psypets.net/Game_mechanics">Game Mechanics</a> (at <a href="http://wiki.psypets.net/">PsyHelp</a>)<br />
      Explains some of the mechanics of the game, such as time-flow, leveling up, and so on.
     </li>
     <li>
      <a href="http://wiki.psypets.net/FAQ">Frequently Asked Questions</a> (at <a href="http://wiki.psypets.net/">PsyHelp</a>)<br />
      How to interpret your pet's feelings, how to make money, the rules for Capture the Flag, and more.
     </li>
     <li>
      <a href="/wherethemoneygoes.php">What Is "Favor"?</a><br />
      Information about supporting PsyPets with money (thank you!), receiving "Favor" in exchange, and the history behind the system.
     </li>
    </ul>
    <h5>Other Resources</h5>
    <ul class="spacedlist">
     <li>
      <a href="/help/npclist.php">HERG Staff</a> (NPCs)<br />
      Links to the profiles of the HERG staff, and the various entrepreneurs.
     </li>
     <li>
      <a href="http://wiki.psypets.net/">PsyHelp</a><br />
      A PsyPets wiki maintained by other PsyPets players.  It contains collected knowledge on items, game mechanics, and other things.  Since it is maintained by other players, it's accuracy cannot be guaranteed... but it's pretty good :)
     </li>
     <li>
      <a href="http://wiki.psypets.net/How_PsyPets_Was_Made">How PsyPets Was Made</a> (at <a href="http://wiki.psypets.net/">PsyHelp</a>)<br />
      Lots of people are curious to know how I actually created the game.  This should give you an idea.
     </li>
     <li>
      <a href="/help/design-philosophies.php">Design Philosophies</a><br />
      There are certain feelings <?= $SETTINGS['site_name'] ?> wants to convey, and experiences it wants to create.
     <li>
      <a href="/help/bannedurls.php">Banned URLs</a><br />
      A scant few URLs are banned on <?= $SETTINGS['site_name'] ?>, and cannot be included in Plaza posts or PsyMail.  This page contains a list of the URLs, and the reasons for their banning.
     </li>
     <li>
      <a href="/recreading.php">"Recommended" Reading</a><br />
      A list of books that have given me ideas for <?= $SETTINGS['site_name'] ?> and/or been purchased with the idea that they'll give me ideas for PsyPets :P  Some of these are really fun, and definitely worth checking out, or even buying.
     </li>
    </ul>
    <h5>Contacts</h5>
    <ul class="spacedlist">
     <li>
      <a href="/admincontact.php">Contact an Administrator In-Game</a><br />
      Sends a PsyMail.  You must be logged in to send PsyMails.
     </li>
     <li>
      <a href="/contactme.php">Contact an Administrator Out-of-Game</a><br />
      Sends an e-mail.  You do not have to be logged in to use this form.
     </li>
    </ul>
    <h5>Legal Information</h5>
    <ul class="spacedlist">
     <li>
      <a href="/meta/termsofservice.php">Terms of Service</a><br />
      PsyPets' Terms of Sevice, or "Play <?= $SETTINGS['site_name'] ?> Like This, Or Not At All."
     </li>
     <li>
      <a href="/meta/privacy.php">Privacy Policy</a><br />
      How information you provide about yourself is used.
     </li>
     <li>
      <a href="/meta/copyright.php">Copyright Information</a><br />
      Contains copyright information about <?= $SETTINGS['site_name'] ?> and the graphics used therein.
     </li>
    </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
