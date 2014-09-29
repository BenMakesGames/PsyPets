<?php
require_once 'commons/init.php';

$wiki = 'To-do List';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/todolistlib.php';

$tag = trim($_GET['tag']);

if(strlen($tag) > 0)
{
    $ideas = $database->FetchMultipleBy(
        '
			SELECT psypets_ideachart.*
			FROM psypets_ideachart
			LEFT JOIN	psypets_ideachart_tags ON psypets_ideachart.idnum=psypets_ideachart_tags.ideaid
			WHERE psypets_ideachart_tags.tag=' . quote_smart($tag) . '
			ORDER BY psypets_ideachart.idnum DESC
		',
        'idnum'
    );
}
else
{
    $ideas = $database->FetchMultipleBy(
        '
			SELECT *
			FROM psypets_ideachart
			ORDER BY idnum DESC
		',
        'idnum'
    );
}

$command = 'SELECT ideaid,votes FROM psypets_ideavotes WHERE residentid=' . $user['idnum'];
$my_votes = $database->FetchMultipleBy($command, 'ideaid', 'fetching my votes');

foreach($ideas as $id=>$wish)
{
    $votes[$id] = (int)$my_votes[$id]['votes'];

    $these_tags = $database->FetchMultiple(
        'SELECT * FROM psypets_ideachart_tags	WHERE ideaid=' . $id
    );

    foreach($these_tags as $this_tag)
        $tags[$id][] = '<a href="?tag=' . urlencode($this_tag['tag']) . '">' . $this_tag['tag'] . '</a>';
}

if($user['wishlistupdate'] == 'yes')
{
    $command = 'UPDATE monster_users SET wishlistupdate=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'removing wish list notification icon, because that is what we do here');

    $user['wishlistupdate'] = 'no';
}

$is_manager = ($user['admin']['coder'] == 'yes');

$tag_cloud = $database->FetchMultiple('
	SELECT tag,COUNT(idnum) AS weight
	FROM psypets_ideachart_tags
	GROUP BY tag
	ORDER BY tag ASC
');

foreach($tag_cloud as $this_tag)
    $weights[] = $this_tag['weight'];

$tag_max = max($weights);
$tag_min = min($weights);
$tag_range = $tag_max - $tag_min + 1;

include 'commons/html.php';
?>
<head>
    <title><?= $SETTINGS['site_name'] ?> &gt; To-do List <?= (strlen($tag) > 0 ? '(' . $tag . ')' : '') ?> &gt; Your Vote</title>
    <?php include "commons/head.php"; ?>
    <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/todolist.js"></script>
    <script>
        $(function() {
            $('.js-reveal-idea').on('click', function(e) {
                e.preventDefault();

                var id = $(this).attr('data-idea-id');

                $('.idea-details').hide();
                $('#idea-details-' + id).show();
            });
        });
    </script>
</head>
<body>
<?php include 'commons/header_2.php'; ?>
<h4>To-do List <?= (strlen($tag) > 0 ? '(' . $tag . ')' : '') ?></h4>
<p>I (<?= User::Link($SETTINGS['author_resident_name']) ?>) use the To-do List to keep track of work to be done. These items are not necessarily final. If you have questions or comments about any of these items, feel free to ask!</p>
<?php
if($is_manager)
    echo '<ul><li><a href="/admin/todoadd.php">Add a to-do item</a></li></ul>';
?>
<ul class="tabbed">
    <li class="activetab"><a href="/arrangewishes.php">Your Vote</a></li>
    <li><a href="/todolist_completed.php">Completed Items</a></li>
</ul>
<!-- idea list -->
<div style="float:left;height:400px;width:300px;overflow:auto;padding-right:10px;">
    <?php if(count($ideas) > 0): ?>
        <table><tbody>
            <?php
            $rowclass = begin_row_class();

            foreach($votes as $id=>$vote)
            {
                echo '<tr class="' . $rowclass . '"><td></td><td><a href="#" class="js-reveal-idea" data-idea-id="' . $id . '">' . $ideas[$id]['sdesc'] . '</a></td></tr>';

                $rowclass = alt_row_class($rowclass);
            }
            ?>
        </tbody></table>
    <?php else: ?>
        <p>There are no To-do List entries.</p>
    <?php endif; ?>

</div>
<!-- details pane -->
<div style="float:left;height:400px;width:550px;overflow:auto;padding-left:10px;">
    <div class="idea-details">
        <p>&lt;-- Click on an item to see its details.</p>
    </div>
    <?php foreach($votes as $id=>$vote): ?>
        <div id="idea-details-<?= $id ?>" class="idea-details" style="display:none;">
            <?php if($ideas[$id]['ldesc']): ?>
                <p><b>Description:</b> <?= $ideas[$id]['ldesc'] ?></p>
            <?php else: ?>
                <p>No description was provided.</p>
            <?php endif; ?>
            <p><b>Tags:</b> <?php foreach($tags[$id] as $tag): ?><?= $tag ?><?php endforeach; ?></p>
            <?php if($is_manager): ?><p><input type="button" onclick="location.href='/admin/todoedit.php?id=<?= $id ?>'" value="Edit" /></p><?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
<?php include 'commons/footer_2.php'; ?>
</body>
</html>
