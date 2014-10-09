<?php
$whereat = 'newmail';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';
require_once 'commons/maillib.php';
require_once 'commons/bannedurls.php';
require_once 'commons/utility.php';
require_once 'commons/friendlib.php';

if($now_month == 1 && $now_day == 18 && $now_year == 2012)
{
    header('Location: /viewthread.php?threadid=72226');
    exit();
}

$error_message = array();
$errors = array();

if($_POST['submit'] == 'Preview')
{
    if(strlen($_POST['message']) <= 2)
        $error_message[] = 38;
    else
    {
        foreach($BANNED_URLS as $url)
        {
            if(strpos($_POST['message'], $url) !== false)
                $errors[] = '<p class="failure">Linking to ' . $url . ' is not allowed.  (<a href="/help/bannedurls.php">Why?</a>)</p>';
        }
    }
}

if(strlen($_POST['submit']) > 0)
{
    if($user['license'] == 'yes')
    {
        // get all the checked items
        $idnums = array();

        foreach($_POST as $key=>$value)
        {
            if(is_numeric($key) && ($value == 'yes' || $value == 'on'))
                $idnums[] = (int)$key;
        }

        if(count($idnums) > 0)
        {
            $command = 'SELECT * FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' ' .
                'AND idnum IN (' . implode(',', $idnums) . ') LIMIT ' . count($idnums);
            $items = $database->FetchMultiple($command, 'writemail.php');
        }
    } // if($user["license"] == "yes")
}
else
{
    $replyto = (int)$_GET['replyto'];
    $quotepost = (int)$_GET['quotepost'];
    $forwardfrom = (int)$_GET['forward'];
    $mailto = urldecode($_GET['sendto']);

    if($replyto > 0)
    {
        $reply = get_mail_byid($replyto);
        if($reply['to'] == $user['user'])
        {
            $fromuser = get_user_byuser($reply['from']);
            $_POST['sendto'] = $fromuser['display'];
            $_POST['subject'] = (strtolower(substr($reply['subject'], 0, 3)) != 're:' ? 'Re: ' : '') . $reply['subject'];
            $_POST['message'] = "<br />\n<br />\n<br />\n&mdash; message from <strong>" . $fromuser['display'] . "</strong> &mdash;<br />\n" . $reply['message'];

            $_POST['replyingto'] = $replyto;
        }
    }
    else if($quotepost > 0)
    {
        $command = '
      SELECT a.body,b.title,c.display
      FROM
        monster_posts AS a
        LEFT JOIN monster_threads AS b
          ON a.threadid=b.idnum
        LEFT JOIN monster_users AS c
          ON a.createdby=c.idnum
      WHERE a.idnum=' . $quotepost . '
      LIMIT 1
    ';
        $data = $database->FetchSingle($command, 'fetching quoted post and related ata');

        $_POST['sendto'] = $data['display'];
        $_POST['subject'] = 'Re: ' . $data['title'];
        $_POST['message'] = "<br />\n<br />\n<br />\n&mdash; post by <strong>" . $data['display'] . "</strong> &mdash;<br />\n" . $data['body'];
    }
    else if($forwardfrom > 0)
    {
        $forward = get_mail_byid($forwardfrom);
        if($forward['to'] == $user['user'])
        {
            $fromuser = get_user_byuser($forward['from']);
            $_POST['sendto'] = '';
            $_POST['subject'] = (strtolower(substr($forward['subject'], 0, 3)) != 'fw:' ? 'Fw: ' : '') . $forward['subject'];
            $_POST['message'] = "<br />\n<br />\n<br />\n&mdash; message from <strong>" . $fromuser['display'] . "</strong> &mdash;<br />\n" . $forward['message'];
        }
    }
    else if(strlen($mailto) > 0)
        $_POST['sendto'] = $mailto;

    $_POST['message'] = br2nl($_POST['message']);
    $_POST['message'] = $user['defaultstyle'] . $_POST['message'];
}

if($_POST['submit'] == 'Send Mail')
{
    $mail_success = false;

    $_POST['subject'] = trim($_POST['subject']);
    $_POST['message'] = trim($_POST['message']);

    if(strlen($_POST['subject']) <= 1)
        $error_message[] = 36;
    else if(strlen($_POST['subject']) > 100)
        $error_message[] = 37;

    if(strlen($_POST['message']) <= 2)
        $error_message[] = 38;
    else
    {
        foreach($BANNED_URLS as $url)
        {
            if(strpos($_POST['message'], $url) !== false)
                $errors[] = '<p class="failure">Linking to ' . $url . ' is not allowed.  (<a href="/help/bannedurls.php">Why?</a>)</p>';
        }
    }

    $that_user = get_user_bydisplay($_POST['sendto']);

    if($that_user === false)
        $error_message[] = '39:' . $_POST['sendto'];
    else if((!is_enemy($user, $that_user) && !is_enemy($that_user, $user)) || $admin['forcemail'] == 'yes')
    {
        if($that_user['is_npc'] == 'yes')
            $error_message[] = '40:' . $_POST['sendto'];
        else if($that_user['childlockout'] == 'yes' && $admin['forcemail'] != 'yes')
            $error_message[] = '40:' . $_POST['sendto'];
        else
        {
            if(!($admin['forcemail'] == 'yes' && ($_POST['forcemail'] == 'yes' || $_POST['forcemail'] == 'on')))
            {
                $command = 'SELECT COUNT(idnum) AS c FROM monster_mail WHERE `to`=' . quote_smart($that_user['user']) . ' AND location!=\'Trash\'';
                $data = $database->FetchSingle($command, 'writemail.php');

                if($data['c'] >= $that_user['postsize'])
                    $error_message[] = '40:' . $_POST['sendto'];
            }
        }
    }
    else
        $error_message[] = '40:' . $_POST['sendto'];

    $totalcost = 0;

    if(count($items) > 0)
    {
        $cursed_items = array();
        foreach($items as $item)
        {
            $command = 'SELECT cursed,noexchange,weight FROM monster_items ' .
                'WHERE itemname=' . quote_smart($item['itemname']);
            $my_item = $database->FetchSingle($command, 'writemail.php');

            if($my_item['cursed'] == 'yes' || $my_item['noexchange'] == 'yes')
                $cursed_items[] = $item['itemname'];
            else
                $totalcost += ceil($my_item['weight'] / 5.0);
        }
    }

    if($totalcost > $user['money'])
        $error_message[] = '35:' . $totalcost;

    if(count($cursed_items) > 0)
    {
        if(count($cursed_items) > 1)
        {
            $cursed_items[count($cursed_items) - 1] = 'and ' . $cursed_items[count($cursed_items) - 1];
            $error_message[] = '31:' . implode(',', $cursed_items);
        }
        else
            $error_message[] = '31:' . $cursed_items[0];
    }

    if(count($error_message) == 0 && count($errors) == 0)
    {
        $message = $_POST['message'];
        $subject = $_POST['subject'];

        psymail_user2($that_user, $user, $subject, $message, count($items));

        $replyid = (int)$_POST['replyingto'];
        $replyto = get_mail_byid($replyid);
        if($replyto['to'] == $user['user'])
        {
            mark_mail_replied($replyid);
            $url_to = '/readmail.php?mail=' . $replyid . '&';
        }
        else
            $url_to = '/post.php?';

        if(count($items) > 0)
        {
            $item_names = array();
            $item_list = array();

            foreach($items as $item)
            {
                if($item['itemname'] == 'Smoke Bombs')
                {
                    $itemname = 'Smoke';
                    $message = $user['display'] . ' set up ' . $that_user['display'] . ' the bomb!';
                }
                else
                {
                    $itemname = $item['itemname'];
                    $message = $user['display'] . ' sent this item to you.';
                }

                $command = 'UPDATE monster_inventory ' .
                    'SET user=' . quote_smart($that_user['user']) . ', ' .
                    'itemname=' . quote_smart($itemname) . ', ' .
                    'location=\'storage/incoming\', ' .
                    'changed=' . $now . ', ' .
                    'message2=' . quote_smart($message) . ', ' .
                    'forsale=0 ' .
                    'WHERE idnum=' . $item['idnum'] . ' LIMIT 1';
                $database->FetchNone($command, 'moving item');

                $item_ids[] = $item['idnum'];
                $item_names[$itemname]++;
            }

            foreach($item_names as $name=>$quantity)
                $item_list[] = $name . ';' . $quantity;

            $command = 'INSERT INTO monster_trades (userid1, userid2, timestamp, step, dialog, items1, itemsdesc1) VALUES ' .
                '(' . $user['idnum'] . ', ' . $that_user['idnum'] . ', ' . $now . ', 3, \'<i>Items sent via Post Office</i>\', ' .
                quote_smart(implode(',', $item_ids)) . ', ' . quote_smart(implode('<br />', $item_list)) . ')';
            $database->FetchNone($command, 'adding trade record for sent items');

            flag_new_incoming_items($that_user['user']);
        }

        // deduct any fees

        $msgs[] = 34;

        if(!is_a_friend($user['idnum'], $that_user['idnum']) && $that_user['idnum'] != $user['idnum'])
            $msgs[] = '152:' . $that_user['display'];

        if($totalcost > 0)
        {
            take_money($user, $totalcost, 'Post Office delivery fee');
            $msgs[] = '32:' . $totalcost;
        }

        header('Location: ' . $url_to . 'msg=' . implode(',', $msgs));
        exit();
    }

    if($mail_success == true)
        $error_message[] = "32:$totalcost";
}

if($_POST['submit'] == 'Show Items' || $_POST['packages'] == 1)
    $SEND_PACKAGE = true;
else
    $SEND_PACKAGE = false;

$my_inventory = get_inventory($whereat, '', $user);
$num_inventory_items = count($my_inventory);

$friends = $database->FetchMultiple('
  SELECT b.display
  FROM psypets_user_friends AS a
    LEFT JOIN monster_users AS b
      ON a.friendid=b.idnum
  WHERE a.userid=' . (int)$user['idnum'] . '
  ORDER BY b.display ASC
');

include 'commons/html.php';
?>
<head>
    <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s Mailbox &gt; Write Mail</title>
    <?php include "commons/head.php"; ?>
    <script type="text/javascript">
        $(function() {
            init_textarea_editor();
            $('#buddylist').change(function() {
                $('#recipient').val($('#buddylist').val());
            });
        });
    </script>
</head>
<body>
    <?php include 'commons/header_2.php'; ?>
    <h4><a href="post.php"><?= $user['display'] ?>'s Mailbox</a> &gt; Write Mail</h4>
    <ul class="tabbed">
        <li><a href="post.php">Mailbox</a></li>
        <li><a href="post_sent.php">Sent Mail</a></li>
        <li class="activetab"><a href="writemail.php">Write Mail</a></li>
    </ul>
    <?php
    if(count($errors) > 0)
        echo '<ul><li>' . implode('</li><li>', $errors) . '</li></ul>';

    if(count($error_message) > 0)
        echo "<p>" . form_message($error_message) . "</p>";
    ?>
    <?php if($_POST["submit"] == "Preview"): ?>
        <h4>Preview</h4>
        <p><table class="preview"><tr><td>
                    <?= format_text($_POST["message"], false) ?>
                </td></tr></table></p>
        <h4>Compose Mail</h4>
    <?php endif; ?>
    <?php if($admin["massmail"] == "yes"): ?>
        <p>If you want to message everyone, use the <a href="/admin/tools.php">admin tools</a></p>
    <?php endif; ?>
    <form action="writemail.php" method="post">
        <input type="hidden" name="replyingto" value="<?= $_POST['replyingto'] ?>" />
        <table>
            <tr>
                <th>From:</th>
                <td><?= $user['display'] ?></td>
            </tr>
            <tr>
                <th>To:</th>
                <td>
                    <input name="sendto" id="recipient" value="<?= $_POST['sendto'] ?>" style="width:200px;" />
                    <span class="size13">&larr;</span>
                    <select id="buddylist" style="width:200px;">
                        <option value=""></option>
                        <?php foreach($friends as $friend): ?>
                            <option value="<?= $friend['display'] ?>"><?= $friend['display'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>Subject:</th>
                <td><input name="subject" value="<?= str_replace('"', '&quot;', $_POST['subject']) ?>" style="width:440px;" /></td>
            </tr>
            <tr>
                <th colspan=2>Message:</th>
            </tr>
            <tr>
                <td colspan=2>
                    <ul data-target="message-body" class="textarea-editor"></ul>
                    <textarea id="message-body" name="message" cols="60" rows="10" style="width:600px;"><?= str_replace(array('<', '>'), array('&lt;', '&gt;'), $_POST['message']) ?></textarea>
                </td>
            </tr>
            <tr>
                <td colspan=2 align="right">
                    <?= ($admin['forcemail'] == "yes") ? '<i><input type="checkbox" name="forcemail" />&nbsp;ignore full mailbox</i>&nbsp;' : '' ?>
                    <input type="submit" name="submit" value="Preview" /> <input type="submit" name="submit" value="Send Mail" /><br />
                </td>
            </tr>
        </table>
        <input type="hidden" name="packages" value="<?= ($SEND_PACKAGE === true ? '1' : '0') ?>" />
        <?php if($user['license'] == 'yes' && $num_inventory_items > 0): ?>
            <?php if($SEND_PACKAGE === true): ?>
                <table>
                    <tr>
                        <th colspan=2>Packages:</th>
                    </tr>
                    <tr>
                        <td colspan=2>
                            <?php display_inventory($whereat, $my_inventory, $user, $pet); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan=2 align="right">
                            <input type="submit" name="submit" value="Send Mail">
                        </td>
                    </tr>
                </table>
            <?php else: ?>
                <p><input type="submit" name="submit" value="Show Items" class="bigbutton" /></p>
            <?php endif; ?>
        <?php endif; ?>
    </form>
    <?php include 'commons/formatting_help.php'; ?>
    <?php include 'commons/footer_2.php'; ?>
</body>
</html>
