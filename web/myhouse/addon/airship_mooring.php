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
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/blimplib.php';

if($NO_PVP)
{
    header('Location: /lostdata.php');
    exit();
}

if($user['pvp_message'] == 'yes')
{
    $user['pvp_message'] = 'no';

    $command = 'UPDATE monster_users SET pvp_message=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    fetch_none($command, 'clearing PvP notification icon');
}

if(!addon_exists($house, 'Airship Mooring'))
{
    header('Location: /myhouse.php');
    exit();
}

if($user['show_aerosoc'] == 'no')
{
    $user['show_aerosoc'] = 'yes';
    $command = 'UPDATE monster_users SET show_aerosoc=\'yes\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    fetch_none($command, 'reveaing the aeronautical society');

    $message = '<p class="success">The Aeronautical Society has been revealed to you!  Find it on the menu under "Services."</p>';
}

$airships = fetch_multiple('SELECT * FROM psypets_airships WHERE ownerid=' . $user['idnum'] . ' ORDER BY name ASC');

require 'commons/html.php';
?>
<head>
    <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Airship Mooring</title>
    <?php include 'commons/head.php'; ?>
</head>
<body>
    <?php include 'commons/header_2.php'; ?>
    <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Airship Mooring</h4>
    <?= $message ?>
    <?php room_display($house); ?>
    <?php if(count($airships) > 0): ?>
        <table>
            <thead>
            <tr><th></th><th></th><th>Chassis</th><th>Name</th><th>Details</th><th>Status</th></tr>
            </thead>
            <tbody>
            <?php $rowclass = begin_row_class(); ?>
            <?php foreach($airships as $airship): ?>
                <?php
                $part_chassis = get_item_byname($airship['chassis']);
                $part_list = explode(',', $airship['parts']);
                ?>
                <tr class="<?= $rowclass ?>">
                    <td>
                        <a href="/myhouse/addon/airship_mooring_edit.php?idnum=<?= $airship['idnum'] ?>"><img src="/gfx/wrench.png" width="16" height="16" alt="modify parts" title="(modify parts)" /></a>
                        <a href="/myhouse/addon/airship_mooring_crew.php?idnum=<?= $airship['idnum'] ?>"><img src="/gfx/pilot.png" width="16" height="16" alt="change crew" title="(change crew)" /></a>
                    </td>
                    <td class="centered"><?= item_display($part_chassis, '') ?></td>
                    <td><?= $part_chassis['chassis'] ?></td>
                    <td></td>
                    <td></td>
                    <td class="failure">May not be used; please disassemble</td>'
                </tr>
                <?php $rowclass = alt_row_class($rowclass); ?>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    <?php include 'commons/footer_2.php'; ?>
</body>
</html>
