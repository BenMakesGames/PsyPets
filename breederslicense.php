<?php
$login = 'breederslicense';
$whereat = 'petshelter';
$wiki = 'Pet_Shelter';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';
require_once 'commons/questlib.php';
require_once 'commons/economylib.php';

$bl_cost = value_with_inflation(10000);

$badges = get_badges_byuserid($user['idnum']);
$bl_quest = get_quest_value($user['idnum'], 'breeder\'s license');

if($bl_quest === false)
{
  if($badges['level20'] == 'yes' && $badges['ltc'] == 'yes' && $badges['mansion'] == 'yes')
  {
    add_quest_value($user['idnum'], 'breeder\'s license', 1);
    $bl_quest['value'] = 1;
    $met_requirements = true;
  }
}
else if($bl_quest['value'] == 1)
{
  if($_GET['dialog'] == 2 && $user['money'] >= $bl_cost)
  {
    take_money($user, $bl_cost, 'Purchased Breeder\'s License from the Pet Shelter');
    
    $command = 'UPDATE monster_users SET breeder=\'yes\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'giving user the breeder\'s license');

    $user['breeder'] = 'yes';

    update_quest_value($bl_quest['idnum'], 2);
    set_badge($user['idnum'], 'ltb');
    $got_it = true;
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Pet Shelter &gt; Breeder's License</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <h4>Pet Shelter &gt; Breeder's License</h4>
     <ul class="tabbed">
      <li><a href="/petshelter.php">Adopt a Pet</a></li>
      <li><a href="/daycare.php">Daycare</a></li>
      <li><a href="/renameform.php">Rename a Pet</a></li>
      <li><a href="/spayneuter.php">Spay or Neuter a Pet</a></li>
      <li><a href="/giveuppet.php">Give Up a Pet</a></li>
<?php if($user['breeder'] == 'yes') echo '<li><a href="/genetics.php">Genetics Lab</a></li>'; ?>
      <li class="activetab"><a href="/breederslicense.php">Breeder's License</a></li>
     </ul>
<?php
echo '<a href="/npcprofile.php?npc=Kim+Littrell"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/petsheltergirl-2.png" align="right" width="350" height="450" alt="(Kim Littrell)" /></a>';
include 'commons/dialog_open.php';

echo '<p>The Breeder\'s License enables you to buy and sell pets.</p>';

if($got_it)
  echo '<p>It\'s yours now.</p>' .
       '<p><i>(You can now visit the Pet Market!  Find it in the Commerce menu.  Additionally, you now have access to the Genetics Lab at the Pet Shelter.)</i></p>';
else if($badges['ltb'] == 'yes')
  echo '<p>You have already purchased the Breeder\'s License.</p>';
else
{
  if($met_requirements)
    echo '<p>I see you have the License to Commerce, the Mansion Badge, and the Level 20 Pet Badge.  These are the basic requirements for acquiring the Breeder\'s License, however there is more...</p>';

  if($bl_quest === false)
    echo '<p>Before I can even consider selling you the Breeder\'s License, however, you must have three things...</p>' .
         '<ol class="spacedlist"><li>The License to Commerce, which you can purchase at <a href="bank.php">The Bank</a>.  This is a very basic requirement.</li>' .
         '<li>The "Mansion" Badge, which the Real Estate receptionist will give you for having a house of at least size 500.  Breeding requires a lot of space!</li>' .
         '<li>The "Level 20 Pet" Badge, which I award to residents who can raise a pet from level 1 to 20.  And when I say "from level 1 to 20" I mean <em>from level 1</em>; adopting a level 2 pet won\'t cut it.</li></ol>' .
         '<p>When you have these three things, ask me about the Breeder\'s License again.</p>';
  else if($bl_quest['value'] == 1)
  {
    echo '<p>The license itself costs ' . value_with_inflation(10000) . '<span class="money">m</span>.</p>';
    
    if($user['money'] + $user['savings'] < value_with_inflation(10000) * 3)
      echo '<p>If this seems like a lot to you, then frankly, you aren\'t ready to wield the Breeder\'s License.</p>';

    if($user['money'] >= value_with_inflation(10000))
      $options[] = '<a href="breederslicense.php?dialog=2">Purchase the Breeder\'s License</a>';
    else
      echo '<p>Come back when you have the moneys.</p>';
  }
}

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
