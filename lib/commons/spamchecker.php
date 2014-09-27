<?php
/*

	© Copyright 2007 Ben Boyter (http://www.boyter.org)

	This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
    
*/



class spamchecker {

	/**
	 * Trains the spam checker on the given text. It allows you to train on either spam or ham.
	 * @param text the test to train the filter on
	 * @param boolen value, true if the text is spam, false if it is ham
	 */
	function train($text,$spam)
  {
		$lines = fetch_multiple('SELECT totalsid, totalspam, totalham FROM bay_totals LIMIT 1');

		foreach($lines as $line)
    {
			$totalsid 	= $line[0];
			$totalspam 	= $line[1];
			$totalham 	= $line[2];
		}

		if($spam)
    {
			$totalspam++;
			fetch_none("UPDATE `bay_totals` SET `totalspam` = '".$totalspam."' WHERE `totalsid` =1 LIMIT 1");
		}
		else
    {
			$totalham++;
			fetch_none("UPDATE `bay_totals` SET `totalham` = '".$totalham."' WHERE `totalsid` =1 LIMIT 1");
		}

    $text = $this->sanitizeText($text);

		$temparray = explode(' ', $text);

		foreach($temparray as $token)
    {
			$token = trim($token);

      if($token == '')
        continue;

			$token_data = fetch_single('SELECT * FROM bay_spam WHERE token="' . $token . '" LIMIT 1');

			if($token_data !== false)
      {
        $spamid = $token_data['spamid'];
        $spamcount = $token_data['spamcount'];
				$hamcount = $token_data['hamcount'];

				if($spam)
        {
					$spamcount++;
					$spamrating = 0.4; // default value (slightly trust unseen words; spam words are usually familiar)

					// Work out spam rating
					if($totalham != 0 && $totalspam != 0)
          {
						$hamprob = $hamcount / $totalham;
						$spamprob = $spamcount / $totalspam;

						$spamrating = $spamprob / ($hamprob + $spamprob);

						if($hamcount == 0)
							$spamrating = 0.99;
					}

					fetch_none("UPDATE `bay_spam` SET `spamcount` = '".$spamcount."', `spamrating`='".$spamrating."' WHERE `spamid` =".$spamid." LIMIT 1");
				}
				else
        {
					$hamcount++;

					//work out spam rating
					if($totalham != 0 && $totalspam != 0)
          {
						$hamprob = $hamcount / $totalham;
						$spamprob = $spamcount / $totalspam;

						$spamrating = $spamprob / ($hamprob + $spamprob);

						if($spamcount == 0)
							$spamrating = 0.01;
					}

					fetch_none("UPDATE `bay_spam` SET `hamcount` = '".$hamcount."', `spamrating`='".$spamrating."' WHERE `spamid` =".$spamid." LIMIT 1");
				}
			}
      // not in the database
			else
      {
				if($spam)
					fetch_none("INSERT INTO `bay_spam` ( `token` , `spamcount` , `hamcount` , `spamrating` ) VALUES ( '".$token."', '1', '0', '0.99')");
				else
					fetch_none("INSERT INTO `bay_spam` ( `token` , `spamcount` , `hamcount` , `spamrating` ) VALUES ( '".$token."', '0', '1', '0.01')");
			}
		}
	}

  function sanitizeText($text)
  {
    return preg_replace(
      '/[^a-zA-Z0-9\'-]+/',
      ' ',
      trim($text)
    );
  }
	
	/**
	 * Checks the given text and returns a value from 0 to 1 indicating spamicty level.
	 * @param text to test if it is spam or not
	 * @return double from 0 to 1
	 */
	function checkSpam($text)
  {
    $text = $this->sanitizeText($text);

		$temparray = explode(' ',$text);

		$spamratings = array();

    $real_tokens = array();
    
		foreach($temparray as $token)
    {
      $token = trim($token);

      if($token != '')
        $real_tokens[] = $token;
    }

    // get the 20 "most-interesting" (most-distant from 0.5) tokens
    $lines = fetch_multiple('
      SELECT spamrating
      FROM bay_spam
      WHERE token IN ("' . implode('", "', $real_tokens) . '")
      ORDER BY ABS(spamrating-0.5) DESC
      LIMIT 20
    ');

    $interesting_tokens = array();
    
    foreach($lines as $line)
			$interesting_tokens[] = $line['spamrating'];

    // if we don't know ANYTHING, it's a 50/50 chance
    if(count($interesting_tokens) == 0)
      return 0.5;
      
		$a = null;
		$b = null;

		foreach($interesting_tokens as $token)
    {
			if($a == null)
				$a = (double)$token;
			else
				$a = $a * $token;	
			
			if($b == null)
				$b = 1 - (double)$token;
			else
				$b = $b * (1 - (double)$token);			
		}

		$spam = $a / ($a + $b);

		return $spam;	
	}
	
	/**
	 * Resets the spam filter. This will then require a full retrain to identify spam.
	 */
	function resetSpam()
  {
		fetch_none("UPDATE `bay_totals` SET `totalspam` = '0',`totalham` = '0' WHERE `totalsid` =1 LIMIT 1");
		fetch_none("TRUNCATE TABLE `bay_spam`");
	}
}
	
?>