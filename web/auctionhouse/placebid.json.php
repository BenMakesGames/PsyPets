<?php
require_once 'commons/init.php';

$require_petload = 'no';
$invisible = 'yes';
$AJAX = true;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/utility.php';
require_once 'commons/itemlib.php';
require_once 'commons/userlib.php';
require_once 'commons/rpgfunctions.php';

$return = array();

$auction_id = (int)$_POST['auction'];
$amount = (int)$_POST['amount'];

if($amount < 1)
  $return['messages'][] = '<span class="failure">You did not specify a bid.</span>';
else if($amount > 10000000)
  $return['messages'][] = '<span class="failure">You may not bid more than 10,000,000<span class="money">m</span>.</span>';
else
{
  $auction = fetch_single('
    SELECT *
    FROM psypets_auctions
    WHERE
      idnum=' . $auction_id . '
      AND expirationtime>' . ($now + 3) . '
    LIMIT 1
  ');

  if($auction === false)
    $return['messages'][] = '<span class="obstacle">That auction no longer exists?  Please reload the page and try again.</span>';
  else
  {
    $return['auctionid'] = $auction_id;

    $details = get_item_byid($auction['itemid']);

    if($auction['ownerid'] == $user['idnum'])
    {
      if($auction['minimumbid'] == $amount)
      {
        $return['messages'][] = '<span class="obstacle">That bid is exactly the same :P</span>';
      }
      else
      {
        $database->FetchNone('UPDATE psypets_auctions SET minimumbid=' . $amount . ' WHERE idnum=' . $auction_id . ' LIMIT 1');
        $return['bid'] = $amount;
        $return['messages'][] = '<span class="success">Minimum bid was updated.</span>';
      }
    }
    else // auction owner != current user
    {
      $bid = fetch_single('
        SELECT *
        FROM psypets_auction_bids
        WHERE
          userid=' . $user['idnum'] . '
          AND auctionid=' . $auction_id . '
        LIMIT 1
      ');

      if($bid === false)
      {
        if($user['money'] >= $amount)
        {
          take_money($user, $amount, 'Bid on auction for ' . $auction['quantity'] . '&times; ' . $details['itemname']);

          $database->FetchNone('
            INSERT INTO psypets_auction_bids
            (userid, auctionid, amount)
            VALUES
            (' . $user['idnum'] . ', ' . $auction_id . ', ' . $amount . ')
          ');

          $return['bid'] = $amount;
          $return['moneys_on_hand'] = $user['money'] - $amount;

          $return['messages'][] = '<span class="success">Bid posted; you paid ' . $amount . '<span class="money">m</span>.  If you do not win the auction, the money will be returned to you.</span>';
        }
        else
          $return['messages'][] = '<span class="failure">You do not have the ' . $amount . '<span class="money">m</span> needed to make that bid.</span>';
      }
      else
      {
        $amount_difference = $amount - $bid['amount'];
        
        if($amount_difference == 0)
        {
          $return['messages'][] = '<span class="obstacle">That bid is exactly the same :P</span>';
        }
        else if($amount_difference < 0)
        {
          $database->FetchNone('
            UPDATE psypets_auction_bids
            SET amount=' . $amount . '
            WHERE idnum=' . $bid['idnum'] . '
            LIMIT 1
          ');

          give_money($user, -$amount_difference, 'Reduced bid on auction for ' . $auction['quantity'] . '&times; ' . $details['itemname']);
          
          $return['bid'] = $amount;
          $return['moneys_on_hand'] = $user['money'] - $amount_difference;

          $return['messages'][] = '<span class="success">Bid posted; ' . (-$amount_difference) . '<span class="money">m</span> was returned to you.</span>';
        }
        else // $amount_difference > 0
        {
          if($user['money'] >= $amount_difference)
          {
            take_money($user, $amount_difference, 'Increased bid on auction for ' . $auction['quantity'] . '&times; ' . $details['itemname']);

            $database->FetchNone('
              UPDATE psypets_auction_bids
              SET amount=' . $amount . '
              WHERE idnum=' . $bid['idnum'] . '
              LIMIT 1
            ');

            $return['bid'] = $amount;
            $return['moneys_on_hand'] = $user['money'] - $amount_difference;

            $return['messages'][] = '<span class="success">Bid posted; you paid ' . $amount_difference . '<span class="money">m</span>.</span>';
          }
          else
            $return['messages'][] = '<span class="failure">You do not have the ' . $amount_difference . '<span class="money">m</span> needed to make that bid.</span>';
        }
      } // you have not yet bid on this auction
    } // you are not the auction host
  } // the auction exists
} // you bid an amount between 1 and 10 million

echo json_encode($return);
?>