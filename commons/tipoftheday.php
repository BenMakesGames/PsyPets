<?php
$TIP_TEXT = array(
  'Did you know that people think better just after they\'ve eaten?  It\'s true!  So go grab a snack, or have a little fruit juice before diving in!  Yeah, right now!  I can wait; it\'s not a problem!',
  'Every hour, your pets may engage themselves in some activity, from hunting animals for food to making works of art to inventing strange devices.  Unhappy pets, however, are much less likely to be productive.',
  'You can sell items back to the game from <a href="storage.php">your Storage</a> for quick moneys!  Select the items to sell, and click the "Sell" button.',
  'Leave fluffy things around the house to make the pets feel safe.',
  'Leave sparkling treasures and expensive decorations around the house to make the pets feel esteemed.  Paintings, statues, and jewelery, for example.',
  'While hungry pets may eat on their own as an hourly activity, wouldn\'t you rather them spend the time being more productive?  Also, when you feed a pet, it gains love, but when a pet feeds itself, it does not.',
  'Save up for a License to Commerce if you don\'t already have one!  It can be puchased from <a href="bank.php">the Bank</a>, and allows you to open your own store, host your own Park events, trade with other residents, and much, much more.',
  'If you\'re using a public computer, remember to Log Out when you\'re done playing.',
  'Putting a pet to bed does not always work, especially if thet pet has a rebellious personality!  The more tired a pet, however, the more likely it is to sleep when you tell it to.',
  'After you\'ve clicked one checkbox, hold down SHIFT when you click the second.  All of the checkboxes in between will also be checked!  This also works for unchecking items.',
  'Signing up your pets for <a href="park.php">Park</a> events is a good way to train young pets.',
  'Please keep chat speak to a minimum, especially in <a href="plaza.php">the Plaza</a>.  It\'s common courtesy!',
  'Remember that if you have a lot of items in <a href="storage.php">your Storage</a>, you have to pay a daily fee.  If you don\'t have enough money to pay for it, your items will be seized, and you\'ll have to buy them back.',
  'Click the hand icon next to a pet to equip it with a tool.  Tools can increase a pet\'s skills significantly!',
  'If your house is full, pets will not be able to bring home new items.  They will instead try to create things using the materials available, or work on existing projects.',
  'If you\'re running out of space in your house, you can buy more from <a href="realestate.php">the Real Estate office</a>, or from other residents in the form of "deeds."',
  'Before discarding an item you no longer want, it may be worth checking <a href="pawnshop.php">the Pawn Shop</a> to see if you can trade it in for something you <em>do</em> want.',
  'There are many useful house add-ons your pets can build, but <em>you</em> have to start the project by using a "blueprint" item.',
  'You can rename your pets at the <a href="petshelter.php">Pet Shelter</a> for a few moneys.  Pet names do not have to be unique, and can contain almost any character or symbol, even those from other alphabets.',
  'You can reset your password if you forget it, but only if your e-mail address is up to date!  Keep it up to date using the <a href="/myaccount/">My Account</a> &gt; <a href="/myaccount/security.php">Account Management</a> page.',
  'You can turn off these tips from the <a href="/myaccount/">My Account</a> &gt; <a href="/myaccount/behavior.php">Behavior Settings</a> page.  On the other hand, something good might happen if you read them all.',
  'Coffee and most teas will caffeinate a pet!  Caffeinated pets still get more tired as time wears on, however they completely ignore the effects of tiredness.',
  'Once your pets are bringing in enough food to support themselves, you should change your allowance settings to get something more valuable than Food Boxes.<br /><br />You can change your allowance settings at <a href="/bank.php">the Bank</a>.',
  'If a pet dies, don\'t "Move On" right away!  There\'s no disadvantage to keeping it in the house, and there are a few ways to revive a dead pet (though it must be said that none are cheap).',
  'Some special items are hidden inside of Paper Airplanes, under Couches, and other odd places.',
  'Unlike most items, Katamaris cannot be repaired with Duct Tape.  However each time a pet plays with one, it will be slightly repaired!',
  'Female pets can become pregnant!  Pregnancy lasts a few weeks, during which time the pet eats more, and is less likely to hunt, defeat monsters, or do other physically-taxing activities.',
  'You can click on a link with your middle mouse button to open it in a new tab.  (Yes, your mouse wheel is clickable!  Weird, right?!)',
  'Werecreatures appear on a full moon, and can transmit their lycanthropy to pets that fight with them!  Only the Werebane Quaff can cure lycanthropy.',
  'Residents can put Gold Stars on plaza posts they think are cool.  You can see how many Gold Stars someone has been given in this way by looking at their profile.  To get Gold Stars, you will need to use a "Gold Star Stickers" item.',
  'Be sure to keep up with <a href="cityhall.php">City Hall</a> posts, so you can keep on top of the changes being made to ' . $SETTINGS['site_name'] . ', as well as other important events.',
  'Click on an item graphic to see it\'s entry in <a href="encyclopedia.php">the Encyclopedia</a>.  This page is especially useful for tools, as it quickly shows you which pets can and cannot equip them.',
  'The boxes that appear when you hover over item graphics are useful, but can slow down your house and storage if you have a lot of items.  You can turn it off from the <a href="/myaccount/">My Account</a> &gt; <a href="/myaccount/display.php">Display Settings</a> page.',
  'There are several color schemes you can choose from.  To change your color scheme, visit the <a href="/myaccount/">My Account</a> &gt; <a href="/myaccount/display.php">Display Settings</a> page.',
  'Congratulations on reading all the tips!  <a href="florist.php">The Florist</a> has a reward for you...',
);

function tip_index($seed)
{
  global $TIP_TEXT;

  $index = $seed % count($TIP_TEXT);

  return $index;
}
?>
