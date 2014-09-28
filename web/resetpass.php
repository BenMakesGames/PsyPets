<?php
$require_login = 'no';

require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/encryption.php';
require_once 'commons/formatting.php';

if($_POST["submit"] == "Reset Password")
{
    $user_lookup = trim($_POST['user_lookup']);

    $this_user = $database->FetchSingle('
        SELECT *
        FROM monster_users
        WHERE `user`=' . quote_smart($user_lookup) . '
        OR `email`=' . quote_smart($user_lookup) . '
        LIMIT 1
    ');

    if($this_user === false)
    {
        if(filter_var($user_lookup, FILTER_VALIDATE_EMAIL))
        {
            $error_message = 'Hm. There is no account with that e-mail address :|';
        }
        else
        {
            header('Location: /resetsuccess.php');
        }
    }
    else
    {
        $userid = $this_user['idnum'];

        $reset_info = $database->FetchSingle('SELECT * FROM monster_passreset WHERE userid=' . (int)$userid . ' LIMIT 1');

        if($reset_info !== false)
        {
            if($now >= $reset_info['timestamp'] + (24 * 60 * 60) || $admin['manageaccounts'] == 'yes')
                $database->FetchNone('DELETE FROM `monster_passreset` WHERE userid=' . (int)$userid . ' LIMIT 1');
            else
                $error_message = 'You cannot request a password reset more than once within a 24-hour period.';
        }

        if(!$error_message)
        {

            $resetid = rand(1000000, 9999999);
            $newpassword = random_password();
            $scrambledpass = md5($newpassword);

            $database->FetchNone('
                INSERT INTO monster_passreset
                (`userid`, `resetid`, `newpass`, `timestamp`) VALUES
                (' . $userid . ', ' . $resetid . ', "' . $scrambledpass . '", ' . $now . ')
            ');

            $message = '<p>This ' . $SETTINGS['site_name'] . ' account requested to have its password reset.  If you did not want to reset your password you may disregard this e-mail.</p>
<p><a href="' . $SETTINGS['site_url'] . '/resetpass2.php?userid=' . $userid . '&resetid=' . $resetid . '">Click here to reset your password.</a></p>
<p>Once you have done so, your new password will be:</p>
<p style="padding-left:20px;">' . $newpassword . '</p>
<p>After resetting your password and logging in, please, please, <strong>please</strong> change your password using the My Account pages.  (You\'d probably have a hard time remembering the one I made you just now, anyway! :P)</p>
<p>The link in this e-mail will remain valid for 24 hours, after which point you will need to request another password reset, if you still need one.</p>';

            mail($_POST['email'],  $SETTINGS['site_name'] . ' lost password', $message, "MIME-Version: 1.0\nContent-type: text/html; charset=utf-8\nFrom: " . $SETTINGS['site_mailer']);

            header('Location: /resetsuccess.php');
        }
    }
}

include 'commons/html.php';
?>
<head>
    <title><?= $SETTINGS['site_name'] ?> &gt; Reset Lost Password</title>
    <?php include 'commons/head.php'; ?>
</head>
<body>
    <?php include 'commons/header_2.php'; ?>
    <h5>Reset Lost Password</h5>
    <?php if(strlen($error_message) > 0): ?>
        <p style="color:red;"><?= $error_message ?></p>
    <?php endif; ?>
    <p>If you forgot your password - or even your login name - you can have instructions on how to reset your password e-mailed to you.</p>
    <p>Enter the login name OR e-mail address of the account to get started:</p>
    <form method="post">
        <table>
            <tr>
                <td>Login name or e-mail address:</td>
                <td><input name="user_lookup" maxlength="50" size="40" value="<?= htmlentities($_POST['user_lookup']) ?>" /></td>
            </tr>
        </table>
        <p><input type="submit" name="submit" value="Reset Password" class="bigbutton" /></p>
    </form>
    <?php include 'commons/footer_2.php'; ?>
</body>
</html>
