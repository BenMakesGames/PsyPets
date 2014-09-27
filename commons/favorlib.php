<?php
function spend_favor(&$user, $amount, $description, $itemid = 0)
{
  global $TIME_OVERRIDE;
  
  if($TIME_OVERRIDE > 0)
    $now = $TIME_OVERRIDE;
  else
    $now = time();

  if($amount > $user['favor'])
  {
    global $SETTINGS;
  
    psymail_user('telkoth', 'psypets', 'Favor over-spend!', '{r ' . $user['display'] . '} spent ' . $amount . ' Favor on "' . $description . '", but only has ' . $user['favor'] . ' Favor!');
    mail('ben@telkoth.net', 'PsyMail notification: a message from PsyPets!', 'Favor over-spend!  Details have been PsyMailed to you.  Best check on this super-quickly!', "MIME-Version: 1.0\nContent-type: text/html; charset=iso-8859-1\nFrom: " . $SETTINGS['site_mailer']);

    $command = 'UPDATE monster_users SET favor=favor-' . $amount . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  }
  else if($amount == $user['favor'])
    $command = 'UPDATE monster_users SET favor=0 WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  else
    $command = 'UPDATE monster_users SET favor=favor-' . $amount . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';

  fetch_none($command, 'spending Favor');

  $command = '
    INSERT INTO psypets_favor_history (timestamp, userid, itemid, favor, value) VALUES
    (' . $now . ', ' . (int)$user['idnum'] . ', ' . $itemid . ',
    ' . quote_smart($description) . ', -' . $amount . ')
  ';
  fetch_none($command, 'adding Favor record');

  $user['favor'] -= $amount;
}

function credit_favor(&$user, $amount, $description, $itemid = 0)
{
  global $TIME_OVERRIDE;

  if($TIME_OVERRIDE > 0)
    $now = $TIME_OVERRIDE;
  else
    $now = time();

  $command = '
    INSERT INTO psypets_favor_history (timestamp, userid, itemid, favor, value) VALUES
    (' . $now . ', ' . (int)$user['idnum'] . ', ' . $itemid . ',
    ' . quote_smart($description) . ', ' . $amount . ')
  ';
  fetch_none($command, 'adding Favor record');

  if($user['idnum'] != '')
  {
    $command = 'UPDATE monster_users SET favor=favor+' . $amount . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    fetch_none($command, 'crediting Favor');

    $user['favor'] += $amount;
  }
}

function credit_favor_paypal(&$user, $payment, $payment_fee, $paypalid, $anonymous, $name, $email)
{
  global $TIME_OVERRIDE;

  if($TIME_OVERRIDE > 0)
    $now = $TIME_OVERRIDE;
  else
    $now = time();

  if($paypalid == '')
    $note = 'payment - ' . $payment . ' USD';
  else
    $note = 'PayPal payment - ' . $payment . ' USD';

  $command = '
    INSERT INTO psypets_favor_history (timestamp, userid, favor, value, paypalid) VALUES
    (' . $now . ', ' . (int)$user['idnum'] . ', ' . quote_smart($note) . ',
    ' . ($payment * 100) . ', \'' . $paypalid . '\')
  ';
  fetch_none($command, 'adding Favor record');

  $command = '
    INSERT INTO psypets_payment_records (timestamp, paypalid, anonymous, name, userid,
      email, amount, fee) VALUES
    (' .$now . ', \'' . $paypalid . '\', ' . quote_smart($anonymous) . ',
    ' . quote_smart($name) . ', ' . (int)$user['idnum'] . ', ' . quote_smart($payer_email) . ',
    ' . ($payment * 100) . ', ' . ($payment_fee * 100) . ')
  ';
  fetch_none($command, 'adding PayPal record');

  if($user['idnum'] != '')
  {
    $command = 'UPDATE monster_users SET favor=favor+' . ($payment * 100) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    fetch_none($command, 'crediting Favor');

    $user['favor'] += $amount;

    // mark recipient as a paid account
    $command = 'UPDATE monster_users SET donated=\'yes\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    fetch_none($command, 'marking resident as paid');

    $user['donated'] = 'yes';

    $command = 'UPDATE psypets_badges SET paidaccount=\'yes\' WHERE userid=' . $user['idnum'] . ' LIMIT 1';
    fetch_none($command, 'awarding Paid Account badge');
  }
}
