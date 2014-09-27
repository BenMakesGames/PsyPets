<?php
$DAILYAUCTION = array();
$DAILYCOMMENTS = array();

// example:
//   trim_to('hello there, ladies and gentlemen', ' ', 3)
// returns:
//   'hello there, ladies'
// example:
//   trim_to('hello there, ladies and gentlemen', 'e', 3)
// returns:
//   'hello ther'
function trim_to($string, $delim, $i, $trail = '')
{
  $pieces = explode($delim, $string, $i + 1);
  
  if(count($pieces) > $i)
  {
    array_pop($pieces);
    return implode($delim, $pieces) . $trail;
  }
  else
    return implode($delim, $pieces);
}

function ashuffle($array)
{
  while(count($array) > 0)
  {
    $val = array_rand($array);
    $new_arr[$val] = $array[$val];
    unset($array[$val]);
  }

  return $new_arr;
}

function urlize($text)
{
  $text = strtolower($text);
  
  $text = str_replace(array("\n", "\r", "\t", " ", "'", "\""), array("", "", "_", "_", "_", "_"), $text);

  return $text;
}

function sprintf2($str, $vars, $char = '%')
{
  $tmp = array();

  foreach($vars as $k=>$v)
    $tmp[$char . $k . $char] = $v;

  return str_replace(array_keys($tmp), array_values($tmp), $str);
}

// from http://php.net/manual/en/function.substr.php
// by kovacsendre at no_spam_thanks_kfhik dot hungary
function html_strlen($str)
{
  $chars = preg_split('/(&[^;\s]+;)|/', $str, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
  return count($chars);
}

function html_substr($str, $start, $length = NULL)
{
  if($length === 0) return ""; //stop wasting our time ;)

  //check if we can simply use the built-in functions
  if(strpos($str, '&') === false)
  {
    //No entities. Use built-in functions
    if($length === NULL)
      return substr($str, $start);
    else
      return substr($str, $start, $length);
  }

  // create our array of characters and html entities
  $chars = preg_split('/(&[^;\s]+;)|/', $str, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE);
  $html_length = count($chars);

  // check if we can predict the return value and save some processing time
  if
  (
    ($html_length === 0) /* input string was empty */ or
    ($start >= $html_length) /* $start is longer than the input string */ or
    (isset($length) and ($length <= -$html_length)) /* all characters would be omitted */
  )
    return "";

  //calculate start position
  if($start >= 0)
    $real_start = $chars[$start][1];
  else
  {
    //start'th character from the end of string
    $start = max($start, -$html_length);
    $real_start = $chars[$html_length + $start][1];
  }

  if(!isset($length)) // no $length argument passed, return all remaining characters
    return substr($str, $real_start);
  else if($length > 0)
  {
    // copy $length chars
    if($start + $length >= $html_length)
    {
      // return all remaining characters
      return substr($str, $real_start);
    }
    else
    {
      //return $length characters
      return substr($str, $real_start, $chars[max($start,0) + $length][1] - $real_start);
    }
  }
  else
  {
    //negative $length. Omit $length characters from end
    return substr($str, $real_start, $chars[$html_length + $length][1] - $real_start);
  }
}
?>
