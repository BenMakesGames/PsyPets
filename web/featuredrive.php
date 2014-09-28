<?php
$wiki = 'Feature Drive';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/featuredrivelib.php';

$vote = get_voter_feature_drive_suggestion($user['idnum']);
$suggestions = get_feature_drive_suggestions();

$voting_on = 'New House Add-ons';

$min_time = mktime(0, 0, 0, 2, 1, 2011);
$max_time = mktime(0, 0, 0, 3, 1, 2011);

$can_vote = user_can_vote($user['idnum'], $min_time, $max_time);

include 'commons/html.php';
?>
<head>
    <title><?= $SETTINGS['site_name'] ?> &gt; Feature Drive: <?= $voting_on ?></title>
    <?php include 'commons/head.php'; ?>
</head>
<body>
    <?php include 'commons/header_2.php'; ?>
    <h4>Feature Drive: <?= $voting_on ?></h4>
    <?php
    if($message) echo "     <p class=\"success\">$message</p>\n";
    if($error) echo "     <p class=\"failure\">$error</p>\n";
    ?>
    <p class="failure"><strong>Hey!  Listen!</strong> This page refers to February of 2011.  The page is still up, because I still haven't managed to finish all the add-ons &gt;_&gt;  [won't be doing feature drives again, as a result of this failure]  STILL: they will be done!  I promised that any house add-ons made will be THESE house add-ons before any others, and that promise still stands.</p>
    <p>This February I'm trying something new: a feature drive!  <a href="http://www.psypets.net/polldetails.php?id=40">People said they'd really like to see new house add-ons</a>, and I want to give you guys a chance to directly decide which house add-ons <strong>will be</strong> added!</p>
    <p>Vote by add-on name (ex: "Tower", "Lake", "Apiary", etc, except, you know, something <em>new</em>); I will work out the exact mechanics based on whatever I read on the internet combined with whatever craziness my brain comes up with! :)</p>
    <p>How many will I do?  I'm not honestly sure.  The top 5 to 10ish, I'd guess?  Ideally not less, and for my sake, hopefully not too much more :P  It really depends on how many ideas people get up, how complicated those ideas will be to implement, how excited I am about them, etc.  But I will promise this: if I do the 6th-most-voted (for example), I <em>will</em> do the 1st through 5th!  Add-ons will <em>not</em>, <em>under any circumstances</em>, be skipped!  (Take that as a challenge to get something crazy up top, if you like! :P)</p>
    <p><i>(Okay, but P.S. no - I dunno - "whorehouse", or anything like that.  Let's at least keep it clean :P)</i></p>
    <?php if($can_vote): ?>
        <h5>Your Vote</h5>
        <p>Anyone who <a href="/buyfavors.php">buys Favor</a> during Feburary can vote.  Voting ends March.  (Votes made with alternate accounts will be removed when spotted.)</p>
        <p>You can vote for something new, or something someone else suggested, and you can change your vote at any time!</p>
        <form action="/featuredrive_makesuggestion.php" method="get">
            <p><input type="text" name="suggestion" maxlength="20" style="width:200px;" value="<?= $vote['vote'] ?>" /><input type="submit" value="Suggest" /></p>
        </form>
    <?php else: ?>
        <p class="failure">Anyone who <a href="buyfavors.php">buys Favor</a> during Feburary can vote.  Voting ends March.  (Votes made with alternate accounts will be removed when spotted.)</p>
    <?php endif; ?>

    <h5>Current Standings</h5>
    <?php if(count($suggestions) > 0): ?>
        <table>
            <tr class="titlerow">
                <th>Suggested <?= $voting_on ?></th>
                <th>Votes</th>
            </tr>
            <?php $rowclass = begin_row_class(); ?>
            <?php foreach($suggestions as $suggestion): ?>
                <tr class="<?= $rowclass ?>">
                    <td><?= $suggestion['vote'] ?></td>
                    <td class="righted"><?= $suggestion['total'] ?></td>
                </tr>
                <?php $rowclass = alt_row_class($rowclass); ?>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No one has made any suggestions yet!</p>
    <?php endif; ?>

    <?php include 'commons/footer_2.php'; ?>
</body>
</html>
