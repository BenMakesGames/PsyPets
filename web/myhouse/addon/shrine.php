<?php
require_once 'commons/init.php';

$whereat = 'home';
$wiki = 'Shrine';

$THIS_ROOM = 'Shrine';

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
require_once 'commons/shrinelib.php';

if(!addon_exists($house, 'Shrine'))
{
    header('Location: /myhouse.php');
    exit();
}

$shrine_hours = simulate_shrine($user['idnum']);

$shrine = get_shrine_byuserid($user['idnum']);

if($shrine === false)
    $shrine = create_shrine($user['idnum']);

if($shrine === false)
    die('Error loading or creating Shrine.  This shouldn\'t happen unless the game is having weird database problems >_>');

$candles = take_apart(';', $shrine['candles']);
$spells = take_apart(';', $shrine['spells']);

$command = 'SELECT idnum,graphic,itemname FROM monster_items WHERE itemname IN (\'' . implode('\',\'', $CANDLE_LIST) . '\') LIMIT ' . count($CANDLE_LIST);
$candle_data = fetch_multiple_by($command, 'itemname', 'fetching candle item data');

$command = 'SELECT itemname,COUNT(itemname) AS c FROM monster_inventory WHERE itemname IN (\'' . implode('\',\'', $CANDLE_LIST) . '\') AND location LIKE \'home%\' AND location NOT LIKE \'home/$%\' AND user=' . quote_smart($user['user']) . ' GROUP BY itemname';
$inventory = fetch_multiple_by($command, 'itemname', 'fetching candles in inventory');

require 'commons/html.php';
?>
<head>
    <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Shrine</title>
    <?php include 'commons/head.php'; ?>
    <style type="text/css">
        #shrine td { width: 48px; height: 48px; padding: 0; margin: 0; }
    </style>
</head>
<body>
    <?php include 'commons/header_2.php'; ?>
    <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Shrine</h4>
    <?= $message ?>
    <?php room_display($house); ?>
    <table id="shrine">
        <tr>
            <?php
            $free = 10;

            for($x = 0; $x < 10; ++$x)
            {
                echo '<td align="center" style="background-image: url(/gfx/shrine/' . ($x + 1) . '.png);">';

                if(strlen($candles[$x]) > 0)
                {
                    $candle = explode(',', $candles[$x]);

                    $candle_list[] = $candle[0];

                    echo '<img src="//' . $SETTINGS['static_domain'] . '/gfx/items/' . $candle_data[$CANDLE_LIST[$candle[0]]]['graphic'] . '" height="32" /><br />' .
                        $candle[1];

                    $free--;
                }
                else
                    $candle_list[] = '';

                echo '</td>';
            }
            ?>
        </tr>
    </table>
    <h5>Add Candle</h5>
    <?php if($free > 0): ?>
        <?php if(count($inventory) > 0): ?>
            <form action="/myhouse/addon/shrine_addcandles.php" method="post">
                <select name="slot">
                    <?php for($x = 0; $x < 10; ++$x): ?>
                        <?php if(strlen($candles[$x]) == 0): ?>
                            <option value="<?= $x ?>"><?= Romanize($x + 1) ?></option>
                        <?php else: ?>
                            <option disabled><?= Romanize($x + 1) ?></option>
                        <?php endif; ?>
                    <?php endfor; ?>
                </select>
                <select name="candle">
                    <?php foreach($inventory as $itemname=>$data): ?>
                        <option value="<?= $candle_data[$itemname]['idnum'] ?>"><?= $itemname ?> (<?= $data['c'] ?>)</option>
                    <?php endforeach; ?>
                </select>
                <input type="submit" value="Add" />
            </form>
        <?php else: ?>
            <p>There are no Candles available in your house. (Candles in protected rooms are not available here.)</p>
        <?php endif; ?>
    <?php else: ?>
        <p>There are no free spaces in your Shrine.  You\'ll have to wait until a candle burns out.</p>
    <?php endif; ?>
    <h5>Spells</h5>
    <?php if(count($spells) > 0): ?>
        <table>
            <tr class="titlerow">
                <th>Spell</th>
                <th>Progress</th>
                <th></th>
            </tr>
            <?php $rowclass = begin_row_class(); ?>

            <?php foreach($spells as $spell): ?>
                <?php $data = explode(',', $spell); ?>
                <?php if(array_key_exists($data[0], $SPELL_DETAILS)): ?>
                    <?php
                    $casts = 0;
                    $built_up = $data[1];
                    while($built_up >= $SPELL_DETAILS[$data[0]][0])
                    {
                        $casts++;
                        $built_up = ($built_up - $SPELL_DETAILS[$data[0]][0]) / 2;
                    }

                    if($casts > 0)
                        $progress = '<a href="/myhouse/addon/shrine_spell.php?spell=' . $data[0] . '">Cast</a>';
                    else
                        $progress = floor($data[1] * 100 / $SPELL_DETAILS[$data[0]][0]) . '%';
                    ?>
                    <tr class="<?= $rowclass ?>">
                        <td><?= $SPELL_DETAILS[$data[0]][1] ?></td>
                        <td class="centered"><?= $progress ?></td>
                        <td><?= ($casts > 1 ? '(' . $casts . ' cast' . ($casts != 1 ? 's' : '') . ' ready)' : '') ?></td>
                    </tr>
                <?php else: ?>
                    <tr class="<?= $rowclass ?>">
                        <td class="failure">spell does not exist</td>
                        <td></td>
                        <td></td>
                    </tr>
                <?php endif; ?>
                <?php $rowclass = alt_row_class($rowclass); ?>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No spells are in progress.</p>
    <?php endif; ?>

    <?php if($user['admin']['clairvoyant'] == 'yes'): ?>
        <h5>Debugging Info</h5>
        <p><?= $shrine_hours ?> hours have passed since your last visit to your shrine.</p>
        <p>Last update was <?= Duration($now - $shrine['lastcheck']) . ' (' . ($now - $shrine['lastcheck']) ?> seconds) ago.</p>
    <?php endif; ?>
    <?php include 'commons/footer_2.php'; ?>
</body>
</html>
