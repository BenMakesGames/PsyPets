<?php
$wiki = 'City_Hall#Room_210';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/checkpet.php';
require_once 'commons/questlib.php';

$quest_totem = get_quest_value($user['idnum'], 'totem quest');

if($quest_totem['value'] < 4)
{
    header('Location: ./404.php');
    exit();
}
else if($quest_totem['value'] == 4)
{
    $command = 'SELECT idnum FROM monster_inventory WHERE itemname=\'Silly Totem with Markings\' AND user=' . quote_smart($user['user']) . ' AND location=\'storage\' LIMIT 1';
    $totemid = $database->FetchSingle($command, 'fetching silly totem with markings');

    if($totemid !== false)
    {
        if($_GET['dialog'] == 2)
        {
            $give_totem_dialog = true;

            delete_inventory_byid($totemid['idnum']);

            update_quest_value($quest_totem['idnum'], 5);
            add_quest_value($user['idnum'], 'totem quest delay', $now + 50 * 60);
        }
        else
            $options[] = '<a href="cityhall_210.php?dialog=2">Show Julio the Silly Totem with Markings that Matalie gave you</a>';
    }
    else
        $options[] = '<i class="dim">The Silly Totem with Markings needs to be in your Storage</i></a>';
}
else if($quest_totem['value'] == 5)
{
    $quest_timer = get_quest_value($user['idnum'], 'totem quest delay');
    if($now >= $quest_timer['value'])
    {
        update_quest_value($quest_totem['idnum'], 6);
        $translated_dialog = true;
        add_inventory($user['user'], 'u:19968', 'Julio\'s Notes', 'Given to you by Julio Beiler', $user['incomingto']);
    }
    else if($now >= $quest_timer['value'] - 10 * 60)
        $almost_done_dialog = true;
    else
        $still_working_dialog = true;
}
else if($quest_totem['value'] == 6)
    $julio_is_away = true;
else if($quest_totem['value'] == 7)
{
    update_quest_value($quest_totem['idnum'], 8);
    $complete_totem_quest_dialog = true;
    set_badge($user['idnum'], 'archaeologist');
    add_inventory($user['user'], '', 'Mars', 'Given to you by Julio Beiler', $user['incomingto']);
}

include 'commons/html.php';
?>
<head>
    <title><?= $SETTINGS['site_name'] ?> &gt; City Hall &gt; Room 210</title>
    <?php include "commons/head.php"; ?>
</head>
<body>
    <?php include 'commons/header_2.php'; ?>
    <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
    <h4>City Hall &gt; Room 210</h4>
    <ul class="tabbed">
        <li><a href="cityhall.php">Bulletin Board</a></li>
        <li><a href="/help/">Help Desk</a></li>
        <li><a href="cityhall_106.php">Room 106</a></li>
        <li class="activetab"><a href="cityhall_210.php">Room 210</a></li>
        <li><a href="af_resrename2.php">Name Change Application</a></li>
        <li><a href="af_movepet2.php">Pet Exchange</a></li>
    </ul>
    <?php if($julio_is_away): ?>
        <p><i>A desk is here, covered with papers under which a placard bearing the name "Julio Beiler" can be seen.</i></p>
        <p><i>Julio himself is nowhere to be seen.</i></p>
    <?php else: ?>
        <!--     <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/npcs/julio_beiler.png" align="right" width="350" alt="(HERG Archaeologist Julio Beiler)" />-->
        <?php
        include 'commons/dialog_open.php';

        if($complete_totem_quest_dialog)
        {
            echo '<p>I hope you had more luck with Lance than I did with Matalie.  She says she noticed the Silly Totem with Markings on one of the poles in her garden - one of the totem poles you Residents build there.  Not only that, she doesn\'t remember which one.</p>' .
                '<p>So there are a few possibilities, and most of them aren\'t looking good...</p>' .
                '<ol><li>A Resident etched this poem in, either as a hoax, or because they were just bored, who knows.  Since we don\'t know whose pole it was on, we can\'t even investigate that.</li>' .
                '<li>Someone else etched the poem into the totem after it was put on the pole.</li>' .
                '<li>A pet, in creating the totem, included the poem in its craft.  If so, it\'s anybody\'s guess as to why.  And again, since we don\'t know who made it, we can\'t investigate this possibility.</li>' .
                '<li>This totem was found somewhere, by a pet or Resident, who failed to notice the markings for what they are, took it as a free Silly Totem, and added it to their pole.  This is one of the only possibilities that allows this "discovery" to be of any value.</li></ol>' .
                '<p>I mean, we might even say there\'s a possibility five, in which Matalie is messing with me, though I personally don\'t think she\'d even consider doing such a thing.</p>' .
                '<p>There\'s just too much unknown.  I guess I\'ll have to break the bad news to Lance... I think I\'ll let him call me first...</p>' .
                '<p>Augh.</p>' .
                '<p>Well, I\'m terribly sorry about all of this.  Without knowing where it came from, this totem is useless, and that\'s that.  The only hope I see now is that we find some old text that tells a similar story to the poem we have here.  If this poem is the real thing, then we should find similar texts elsewhere.</p>' .
                '<p>Still, thanks for your help.  It made for an interesting day anyway, I suppose.</p>' .
                '<p>Oh, and here\'s something for you, as promised.  It\'s an original Mars sword.  Pets have been making swords based on its design, but this is one of the originals.  There are lots of them there, buried in the Hollow Earth, though who knows why.</p>' .
                '<p>Take a look here, at the hilt, and you\'ll see why we call it a Mars sword.  See that symbol there?  It\'s the same symbol given to the fourth planet in Ancient depictions of the solar system.  Mars.  The fact that the Ancients were drawing our solar system is a whole other puzzle entirely, but there it is.</p>' .
                '<p>Anyway, thanks again.  I\'ll see you around, eh ' . $user['display'] . '?</p>' .
                '<p><i>(You received Mars!  Find it in ' . $user['incomingto'] . '.  You also received the Archaeologist Badge!)</i></p>';
        }
        else if($give_totem_dialog)
        {
            echo '<p>!  What is this?  <em>What. is. this?</em>  I mean, it\'s definitely the language of the Ancients, but...</p>' .
                '<p>A poem?</p>' .
                '<p>You said Matalie gave this to you?  Where in the world did she come by such a thing?  I am definitely going to have to ask...</p>' .
                '<p>"Children of Ki Ri," it says here... this must be one of the stories!</p>' .
                '<p>Fascinating...</p>' .
                '<p>I\'ll tell you what: come back here in about... 45 minutes -- no!  An hour.  I should have the poem translated by then.</p>' .
                '<p>But really, what a find!</p>' .
                '<p>Where <em>did</em> she find this?</p>' .
                '<p><em>Amazing...</em></p>' .
                '<p><i>(Julio takes the Silly Totem With Markings.)</i></p>';
        }
        else if($still_working_dialog)
            echo '<p>I still have a bit of work to do on translating this, but I shouldn\'t be too long.  Check back in about half an hour, and I\'ll let you know how things are coming along.</p>';
        else if($almost_done_dialog)
            echo '<p>I\'m very close to finishing now, ' . $user['display'] . '!  Give me just... 10 minutes.  Maybe less.</p>';
        else if($translated_dialog)
        {
            echo '<p>Wow!  ' . $user['display'] . '!  Just... wow!</p>' .
                '<p>If this poem is authentic, it tells us something very new and different about the Ancient\'s mythology!</p>' .
                '<p>For one... look here:  This refers to Ki Ri Kashu as only having two children, not the three we know of.  And then here, we have Gizubi and Rizi Vizi; there\'s no mention of Kaera anywhere.</p>' .
                '<p>And then this last line... well, it might be interpreted a couple ways, but it seems to suggest that the universe was not born from Ki Ri Kashu\'s dead body, but that the creation of the universe is what killed him?  I\'m not really sure.</p>' .
                '<p>At any rate, this all conflicts with any other documents we have at the moment!  If we could date this story, we could see if it came before or after the other texts I\'ve found.  I suspect it comes before - an earlier form of the religion - but that really is only a guess.  It could be, I dunno, a variation of the same religion.</p>' .
                '<p>I have to go talk to Matalie about this.  If we can find out where she pulled this thing from, we might be able to use radiocarbon dating to determine how old it is... Agh, why didn\'t she call me over to the site, rather than sending it here?  She\'s made this much more complicated than it needs to be.</p>' .
                '<p>I\'m sorry, I\'m rambling.</p>' .
                '<p>' . $user['display'] . ', I have a favor to ask you.  I\'m going to keep the totem here, but I\'d like you to take this copy of the poem, along with my translation and some notes, down to Lance at the temple.  He\'s familiar with Ancient script as well, and I\'d very much like to hear his interpretation.  I\'m going to head over to Matalie\'s and ask her where she found this, but once you\'ve given Lance the notes, come back here.  I think I might have something for you at that time, to repay you for your efforts.</p>' .
                '<p><i>(You received Julio\'s Notes.  Find them in ' . $user['incomingto'] . '.)</i></p>';
        }
        else
            echo '<p>' . $user['display'] . '?  What brings you here?</p>';

        include 'commons/dialog_close.php';

        if(count($options) > 0)
            echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';
        ?>
    <?php endif; ?>
    <?php include 'commons/footer_2.php'; ?>
</body>
</html>
