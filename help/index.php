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
  'name' => 'The Receptionist',
  'dialog' => '<p>New to ' . $SETTINGS['site_name'] . '? Feeling a little confused? ' . $SETTINGS['site_name'] . ' is a big place, and there\'s a lot to it, but hopefully I can point you in the right direction.</p>',
);

if($user !== false)
{
  $tabs = array(
    'Bulletin Board' => '/cityhall.php',
    'Help Desk' => '',
    'Room 106' => '/cityhall_106.php',
    'Room 210' => '/cityhall_210.php',
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
    </ul>
    <h5>Other Resources</h5>
    <ul class="spacedlist">
     <li>
      <a href="/help/npclist.php">NPC Directory</a><br />
      Links to the profiles of the various game NPCs (non-player characters).
     </li>
     <li>
      <a href="/help/design-philosophies.php">Design Philosophies</a><br />
      There are certain feelings <?= $SETTINGS['site_name'] ?> wants to convey, and experiences it wants to create.
     <li>
      <a href="/help/bannedurls.php">Banned URLs</a><br />
      A scant few URLs are banned on <?= $SETTINGS['site_name'] ?>, and cannot be included in Plaza posts or messages.  This page contains a list of the URLs, and the reasons for their banning.
     </li>
    </ul>
    <h5>Contacts</h5>
    <ul class="spacedlist">
     <li>
      <a href="/admincontact.php">Contact an Administrator In-Game</a><br />
      Sends an in-game message.  You must be logged in..
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
      The Terms of Sevice, or "Play Like This, Or Not At All."
     </li>
     <li>
      <a href="/meta/privacy.php">Privacy Policy</a><br />
      Policy on how information you provide about yourself is used.
     </li>
     <li>
      <a href="/meta/copyright.php">Copyright Information</a><br />
      Contains copyright information about <?= $SETTINGS['site_name'] ?> and the graphics used therein.
     </li>
    </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
