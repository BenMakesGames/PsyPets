<?php
// monthly newsletter

$event_intros = array(
  '<p>There\'s a few exciting events coming up this month that I thought you
  might be interested to know about!</p>',
  '<p>I hope this month will be an exciting one!</p>',
);

// mention also:
//   ice cream truck - the month it starts, and the month it ends

$monthly_events = array(
  1 => '
    <h3>January Events</h3>
    <h4>New Year\'s Day</h4>
    <p>Keep an eye on Incoming, as you may find gifts of wine and other alcohols
    there, just as on New Year\'s Eve.</p>
    <h4>The Twelve Days of Christmas</h4>
    <p>If you have a {i Partridge in a Pear Tree}, be sure to collect the last
    five days of your free goodies during the 1st through 5th.</p>
  ',
  2 => '
    <h3>February Events</h3>
    <h4>Valentine\'s</h4>
    <p>From the 1st through the 14th, Vanessa Roselle sells Hungry Cherubs and
    Candy Hearts at her {link florist.php Flower Shop}.</p>
    <p>Don\'t forget to ask her about the meaning behind the various flowers she
    sells, and consider using her Flower Delivery service to send some to
    friends and lovers :)</p>
    <h4>Lupercalia</h4>
    <p>The day after Valentine\'s - the 15th - is Lupercalia.  A few lucky
    individuals may find a {i Fertility Draught} mysteriously deposited in their
    {link incoming.php Incoming}, so keep your eyes peeled!</p>
  ',
  3 => '
    <h3>March Events</h3>
    <h4>Pi Day</h4>
    <p>March 14th is Pi Day!  Stop by The Smithery to have Nina make you a {i Pi}
    in celebration.</p>
    <h4>St. Patrick\'s</h4>
    <p>Every year during St. Patrick\'s, Lakisha and Matalie compete to collect
    the most items with "green" in their name.  They\'re accepting donations,
    and the winning side will give out a prize.  Lakisha and Matalie have also
    been known to give out prizes to top donators, regardless of who wins.</p>
    <p>The event starts the 16th.  An announcement will be posted that day
    with more details.</p>
    <p>Finally, Nina smiths Silver and Gold 5-Leaf Clovers on St. Patrick\'s
    Day, the 17th.  You\'ll need to provide "raw" 5-Leaf Clovers, however, so
    be sure to set a couple aside.</p>
    <h4>PsyPets\' Birthday</h4>
    <p>The 21st is PsyPets\' birthday.  During this day, and the two days prior,
    pets may find balloons, cake and other party paraphernalia during their
    hourly activities.</p>
  ',
  4 => '
    <h3>April Events</h3>
    <h4>Act Like a T-Rex Day</h4>
    <p>The 2nd is Act Like a T-Rex Day.  Monsters your adventuring pets
    encounter may act a little strange on this day, but it shouldn\'t be
    anything to worry about.</p>
    <p>Residents are encouraged to act like T-Rexes themselves :)</p>
    <h4>St. George\'s Day</h4>
    <p>The Mysterious Shop sells an {i Ascalon} during St. George\'s Day, the
    23rd.  This can be a difficult shop to gain access to, however, as it\'s
    linked to the Wishing Well add-on.  If you don\'t already have this add-on,
    however, you have a few weeks to get one built.</p>
  ',
  5 => '
    <h3>May Events</h3>
    <h4>May the Fourth Be With You</h4>
    <p>The Mysterious Shop sells a {i Model Moon} on the 4th.  This can be a
    difficult shop to gain access to, however, as it\'s linked to the Wishing
    Well add-on.</p>
  ',
  6 => '
    <h3>June Events</h3>
  ',
  7 => '
    <h3>July Events</h3>
    <h4>Bastille Day</h4>
    <p>If you have the Eiffel Tower add-on, don\'t forget that July 14th is
    Bastille Day!  Be sure to collect your celebratory wine and fireworks!</p>
  ',
  8 => '
    <h3>August Events</h3>
  ',
  9 => '
    <h3>September Events</h3>
  ',
  10 => '
    <h3>October Events</h3>
    <h4>Halloween</h4>
    <h4>Columbus Day</h4>
  ',
  11 => '
    <h3>November Events</h3>
    <h4>Thanksgiving</h4>
    <p>During all of November, gathering pets will collect a little more food
    than usual.  You can expect extra Corn, Tomatoes, Avocado, and other New
    World foods.</p>
    <p>On Thanksgiving day, visit {link library.php The Library} for a {i
    Thanksgiving Scroll}, and check out {link park.php The Park} for special,
    Thanksgiving events.</p>
    <h4>Christmas</h4>
    <p>If you don\'t already have the Dungeon add-on, consider getting one!  The
    door in the back is an Advent calendar, giving out free goodies from
    December 1st through Christmas day!</p>
  ',
  12 => '
    <h3>December Events</h3>
    <h4>Christmas</h4>
    <h4>Hannukah (check spelling used in PsyPets)</h4>
    <h4>New Year\'s Eve</h4>
    <p>Keep an eye on Incoming, as you may find gifts of wine and other alcohols
    there.</p>
  ',
);

// mention easter, depending on the date
// leap day, when it happens

$item_intros = array(
  '<p>There are also a few items available for <a href="buyfavors.php">Favor</a>!</p>',
);

$monthly_items = array(
  1 => '
    <h4>Wise Men\'s Day</h4>
  ',
  2 => '
    <h4>ABC Blocks</h4>
    <h4>Andromeda</h4>
  ',
  3 => '
  ',
  4 => '
  ',
  5 => '
  ',
  6 => '
  ',
  7 => '
  ',
  8 => '
  ',
  9 => '
  ',
  10 => '
  ',
  11 => '
  ',
  12 => '
  ',
);

$mail_message = '
  <p>Hello, ' . $user['display'] . '!</p>
  ' . $event_intros[array_rand($event_intros)] . '
  ' . $monthly_events[$now_month] . '
  ' . $

/*
$monthly_template = array(
  1 => '
  ',
  2 => '
  ',
  3 => '
  ',
  4 => '
  ',
  5 => '
  ',
  6 => '
  ',
  7 => '
  ',
  8 => '
  ',
  9 => '
  ',
  10 => '
  ',
  11 => '
  ',
  12 => '
  ',
);
*/
?>
