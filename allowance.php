<?php
$wiki = 'Bank#Daily_Allowance';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/questlib.php';
require_once 'commons/economylib.php';

$questvalues = get_quest_values_byuserid($user['idnum']);
$options = array();

$econ_factor = get_global('economy_factor');

$allowance_value = value_with_inflation(50);
$donation_value = $allowance_value * 4;

if(!array_key_exists('Allowance Miniquest', $questvalues))
{
  if($_GET['dialog'] == 2)
  {
    $allowance_miniquest_dialog = true;
    add_quest_value($user['idnum'], 'Allowance Miniquest', 1);
  }
  else
    $options[] = '<a href="allowance.php?dialog=2">Ask where the money to pay for allowance comes from...</a>';
}

if($_POST['action'] == 'updateallowance')
{
  $option = $_POST['allowance'];

  if(($option == 'standard' ||
    $option == 'food' ||
    $option == 'resources' ||
    $option == 'rizivizi' ||
    $option == 'gizubi' ||
    $option == 'kaera') &&
    $option != $user['allowance'])
  {

    $command = "UPDATE monster_users SET allowance='$option' WHERE idnum=" . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'updating allowance option');
    
    $user['allowance'] = $option;

    $message = '<p class="success">Your allowance preference has been updated.</p><p>Let me know if there\'s anything else I can help you with.</p>';

    require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Changed Your Allowance Preference', 1);
  }
}

if($_GET['dialog'] == 'debris')
{
  $message = '
    <p>Oh!  Hahahaha!  I guess that would seem a little strange.  It was probably Ian\'s idea, really... he\'s kind of, well...</p>
    <p>Well, anyway: Debris can contain all kinds of useful stuff for your pets, especially if they like to make things.  Dyes, some metals, fluff...</p>
    <p>If you\'re just starting out, definitely go for the food boxes - to keep your pets fed - but if you\'ve outgrown the need for those, Debris can be really helpful.</p>
  ';
}
else
  $options[] = '<a href="allowance.php?dialog=debris">Ask why anyone would want to receive Debris...</a>';


include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Bank &gt; Daily Allowance</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Bank &gt; Daily Allowance</h4>
     <ul class="tabbed">
      <li><a href="/bank.php">The Bank</a></li>
      <li><a href="/bank_groupcurrencies.php">Group Currencies</a></li>
      <li><a href="/bank_exchange.php">Exchanges</a></li>
      <li><a href="/ltc.php">License to Commerce</a></li>
      <li class="activetab"><a href="/allowance.php">Allowance Preference</a></li>
      <li><a href="/af_favortickets.php">Get Favor Tickets</a></li>
      <li><a href="/af_favortransfer2.php">Transfer Favor</a></li>
<?= $st_patricks ? '<li class="stpatrick"><a href="/stpatricks.php?where=bank">St. Patrick\'s Day Competition</a></li>' : '' ?>
     </ul>
<?php
// BANKER LAKISHA
echo '<a href="/npcprofile.php?npc=Lakisha+Pawlak"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/banker_lakisha.png" align="right" width="350" height="427" alt="(Lakisha the Banker)" /></a>';

include 'commons/dialog_open.php';
if($message)
  echo $message;
else
{
  if($allowance_miniquest_dialog)
  {
?>
     <p>Hm?  Oh, allowance is paid out by HERG, but I'm not sure where they get the money.  Probably a government grant, since they're researching string theory, or some such, but I'm really not the person to ask.</p>
     <p>If you really want to know, try asking at the lab.</p>
<?php
  }
  else if($_GET['dialog'] == 2)
  {
?>
     <p>Charity money doesn't come out of your bank or on-hand moneys, if that's what you're concerned about.  It's just that instead of you receiving allowance, the temple receives money for the god you specify.</p>
<?php
    $options[] = '<a href="/allowance.php?dialog=storage">Ask how storage fees are calculated</a>';
  }
  else if($_GET['dialog'] == 'storage')
  {
?>
     <p>Storage fees are adjusted daily to keep pace with the changing economy.</p>
     <p>Currently, every additional unit of storage space you use costs <?= round($econ_factor / 5, 4) ?><span class="money">m</span>.  If you have a Colossus, it costs 20% less: <?= round($econ_factor / 6, 4) ?><span class="money">m</span>.  This fee is charged daily, rounded down.</p>
     <p>For an example, if you were using 100 space more than you had, it would cost <?= floor($econ_factor * 100 / 5) ?><span class="money">m</span> per day.  (Without a Colossus.)</p>
     <p>Compared to your daily allowance, this is not that much, but it can add up, so keep an eye on your storage fees.</p>
     <p>By the way, you can always see the daily charge for your Storage <em>from</em> your Storage.  On the top where it says "<?= $user['display'] ?>'s Storage..." there will be a money value if you will owe any daily storage fees, or "no fee" if you won't.</p>
<?php
    $options[] = '<a href="allowance.php?dialog=2">Ask who pays for the charities</a>';
  }
  else
  {
?>
     <p>Every 24 hours of pet activity, you receive your Allowance.  Hours you skip don't count, of course!</p>
     <p>We have several allowance options available.  If you'd like to change yours, please let me know.</p>
<?php
    $options[] = '<a href="allowance.php?dialog=2">Ask who pays for the charities</a>';
    $options[] = '<a href="allowance.php?dialog=storage">Ask how storage fees are calculated</a>';
  }
}
include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';
?>
     <form action="allowance.php" method="post">
     <table>
      <tr>
       <td><input type="radio" name="allowance" value="standard" id="standard"<?= $user["allowance"] == "standard" ? " checked" : "" ?> /></td>
       <td><label for="standard"><?= $allowance_value ?><span class="money">m</span>, Debris, and a 12-hour Food Box</label></td>
      </tr>
      <tr>
       <td><input type="radio" name="allowance" value="food" id="food"<?= $user["allowance"] == "food" ? " checked" : "" ?> /></td>
       <td><label for="food">Three 12-hour Food Boxes</label></td>
      </tr>
      <tr>
       <td><input type="radio" name="allowance" value="resources" id="resources"<?= $user["allowance"] == "resources" ? " checked" : "" ?> /></td>
       <td><label for="resources">Two Debris</label></td>
      </tr>
      <tr>
       <td colspan="2"><i>The following options are <strong>charities</strong></i>.</td>
      </tr>
      <tr>
       <td><input type="radio" name="allowance" value="rizivizi" id="rizivizi"<?= $user["allowance"] == "rizivizi" ? " checked" : "" ?> /></td>
       <td><label for="rizivizi"><?= $donation_value ?><span class="money">m</span> donated to Rizi Vizi</label></td>
      </tr>
      <tr>
       <td><input type="radio" name="allowance" value="gizubi" id="gizubi"<?= $user["allowance"] == "gizubi" ? " checked" : "" ?> /></td>
       <td><label for="gizubi"><?= $donation_value ?><span class="money">m</span> donated to Gizubi</label></td>
      </tr>
      <tr>
       <td><input type="radio" name="allowance" value="kaera" id="kaera"<?= $user["allowance"] == "kaera" ? " checked" : "" ?> /></td>
       <td><label for="kaera"><?= $donation_value ?><span class="money">m</span> donated to Kaera</label></td>
      </tr>
     </table>
     <p><input type="hidden" name="action" value="updateallowance" /><input type="submit" value="Update" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
