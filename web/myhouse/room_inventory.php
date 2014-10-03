<?php
require_once 'commons/init.php';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/formatting.php';
require_once 'commons/houselib.php';
require_once 'commons/inventory.php';

$house = get_house_byuser($user['idnum']);

if($house === false)
{
    echo '<p>Failed to load your house.  If this problem continues, please contact <a href="/admincontact.php">an administrator</a>.</p>';
    exit();
}

if(strlen($_GET['room']) > 0)
{
    $room = $_GET['room'];

    if($room == 'Common')
        $room = '';
    else if($room == 'Protected')
        $room = 'Protected';
    else if(strlen($house['rooms']) > 0)
    {
        $rooms = explode(',', $house['rooms']);

        if(array_search($room, $rooms) === false)
            $room = '';
    }
}
else if(strlen($_GET['sortby']) > 0)
{
    if($_GET['sortby'] == 'idnum' || $_GET['sortby'] == 'bulk' || $_GET['sortby'] == 'itemname' || $_GET['sortby'] == 'itemtype' || $_GET['sortby'] == 'ediblefood' || $_GET['sortby'] == 'message')
    {
        $house['sort'] = $_GET['sortby'];

        $command = 'UPDATE monster_houses SET sort=' . quote_smart($house['sort']) . ' WHERE idnum=' . $house['idnum'] . ' LIMIT 1';
        $database->FetchNone($command, 'updating house sort');
    }
}
else if($_GET['viewby'] == 'details' || $_GET['viewby'] == 'icons')
{
    $house['view'] = $_GET['viewby'];

    $command = 'UPDATE monster_houses SET view=' . quote_smart($house['view']) . ' WHERE idnum=' . $house['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'updating house view');
}

if(strlen($room) > 0)
{
    $room = 'home/' . $room;

    if($room{0} == '$')
        $sayroom = substr($room, 1);
    else
        $sayroom = $room;

    $THIS_ROOM = $room;
}
else
{
    $room = 'home';
    $sayroom = 'Common';
    $THIS_ROOM = 'Common';
}

$command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=' . quote_smart($room);
$data = $database->FetchSingle($command, 'fetching storage item count');

$num_items = (int)$data['c'];
$num_pages = ceil($num_items / 2000);
$page = (int)$_GET['page'];

if($page < 1 || $page > $num_pages)
    $page = 1;

$inventory = get_room_inventory($user['user'], $room, $num_items, $num_pages, $page, $house['sort']);

if($num_items > 2000)
    $page_note = true;

?>
<h5 class="nomargin">Items <i>(<?= $house['view'] == 'icons' ? 'icon view' : 'list view' ?>, <a href="myhouse.php?viewby=<?= $house['view'] == 'icons' ? 'details' : 'icons' ?>">switch to <?= $house['view'] == 'icons' ? 'list view' : 'icon view' ?></a>)</i></h5>
<p style="padding-left: 2em;">
    <a href="autosort.php?applyto=<?= $room ?>">auto-sort items</a> | <a href="autosort_edit.php">configure auto-sorter</a>
    <?php if($user['autosorterrecording'] == 'yes'): ?>
        | <span id="recordingautosort"><a href="#" onclick="stop_recording(); return false;">&#9632;</a> <blink style="color:red;">recording moves</blink></span>
    <?php else: ?>
        | <span id="recordingautosort"><a href="#" onclick="start_recording(); return false;" style="color:red;">&#9679;</a></span>
    <?php endif; ?>
</p>

<?php if($page_note): ?>
    <p><i>(You have over 2000 items in this room!  When this happens, PsyPets paginates your room's display.)</i></p>
    <?= paginate($num_pages, $page, 'myhouse.php?page=%s') ?>
<?php endif; ?>

<?php house_view($user, $house, $userpets, $inventory); ?>

<?php if($page_note): ?>
    <?= paginate($num_pages, $page, 'myhouse.php?page=%s') ?>
<?php endif; ?>

<p style="padding-top:1em;">(Remember: you can select a range of items by checking one item, then holding shift while checking another.)</p>

<?php if(($house['view'] == 'details' && count($inventory) > 20) || ($house['view'] == 'icons' && count($inventory) / 10 > 15)): ?>
    <?php room_display($house); ?>
<?php endif; ?>
