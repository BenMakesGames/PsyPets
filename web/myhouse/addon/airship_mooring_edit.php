<?php
require_once 'commons/init.php';

$whereat = 'home';
$wiki = 'Airship Mooring';
$THIS_ROOM = 'Airship Mooring';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';
require_once 'commons/checkpet.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/blimplib.php';

if($NO_PVP)
{
    header('Location: /lostdata.php');
    exit();
}

if(!addon_exists($house, 'Airship Mooring'))
{
    header('Location: /myhouse.php');
    exit();
}

$shipid = (int)$_GET['idnum'];
$airship = get_airship_by_id($shipid);

if($airship === false || $airship['ownerid'] != $user['idnum'])
{
    header('Location: /myhouse/addon/airship_mooring.php');
    exit();
}

require 'commons/html.php';
?>
<head>
    <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Airship Mooring &gt; <?= $airship['name'] ?></title>
    <?php include "commons/head.php"; ?>
</head>
<body>
    <?php include 'commons/header_2.php'; ?>
    <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; <a href="/myhouse/addon/airship_mooring.php">Airship Mooring</a> &gt; <?= $airship['name'] ?></h4>
    <?php room_display($house); ?>
    <ul class="tabbed">
        <li class="activetab"><a href="/myhouse/addon/airship_mooring_edit.php?idnum=<?= $shipid ?>">Parts</a></li>
        <li><a href="/myhouse/addon/airship_mooring_crew.php?idnum=<?= $shipid ?>">Crew</a></li>
    </ul>
    <?php if(strlen($_GET['msg']) > 0): ?>
        <p><?= form_message(explode(',', $_GET['msg'])) ?></p>
    <?php endif; ?>
    <h5>Parts</h5>
    <ul>
        <li><a href="/myhouse/addon/airship_mooring_recycle.php?idnum=<?= $shipid ?>">Retire this Airship</a></li>
    </ul>
    <?php $rowclass = begin_row_class(); ?>
    <form action="/myhouse/addon/airship_mooring_removeparts.php?idnum=<?= $shipid ?>" method="post">
        <table>
            <tr class="titlerow">
                <th></th><th></th><th>Part</th><th>Weight</th><th>Bulk</th><th>Details</th>
            </tr>
            <?php $details = get_item_byname($airship['chassis']); ?>
            <tr class="<?= $rowclass ?>">
                <td></td>
                <td class="centered"><?= item_display($details, '') ?></td>
                <td><?= $airship['chassis'] ?></td>
                <td class="centered"><?= ($details['weight'] / 10) ?></td>
                <td class="centered">&nbsp;</td>
                <td></td>
            </tr>
            <?php
            if(strlen($airship['parts']) > 0)
            {
                $ship_parts = explode(',', $airship['parts']);

                $rowclass = alt_row_class($rowclass);

                foreach($ship_parts as $i=>$part)
                {
                    $details = get_item_byname($part);
                    ?>
                    <tr class="<?= $rowclass ?>">
                        <td><input type="checkbox" name="i<?= $i ?>" /></td>
                        <td class="centered"><?= item_display($details, '') ?></td>
                        <td><?= $part ?></td>
                        <td class="centered"><?= ($details['weight'] / 10) ?></td>
                        <td class="centered"><?= ($details['bulk'] / 10) ?></td>
                        <td></td>
                    </tr>
                    <?php
                    $rowclass = alt_row_class($rowclass);
                }
            }
            ?>
        </table>
        <p><input type="submit" value="Remove" /></p>
    </form>
    <?php include 'commons/footer_2.php'; ?>
</body>
</html>
