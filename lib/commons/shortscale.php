<?php
class shortScale
{
  // Source: Wikipedia (http://en.wikipedia.org/wiki/Names_of_large_numbers)
  private static $scale = array('', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion', 'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quattuordecillion', 'quindecillion', 'sexdecillion', 'septendecillion', 'octodecillion', 'noverndecillion', 'vigintillion');
  private static $digit = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen');
  private static $digith = array('', 'first', 'second', 'third', 'fourth', 'fifth', 'sixth', 'seventh', 'eighth', 'ninth', 'tenth', 'eleventh', 'twelfth', 'thirteenth', 'fourteenth', 'fiftheenth', 'sixteenth', 'seventeenth', 'eighteenth', 'nineteenth');
  private static $ten = array('', '', 'twenty', 'thirty', 'fourty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety');
  private static $tenth = array('', '', 'twentieth', 'thirtieth', 'fortieth', 'fiftieth', 'sixtieth', 'seventieth', 'eightieth', 'ninetieth');

  private static function floatToArray($number, &$int, &$frac)
  {
    // Forced $number as (string), effectively to avoid (float) inprecision
    @list(, $frac) = explode('.', $number);

    if($frac || !is_numeric($number) || (strlen($number) > 60))
      throw new Exception('Not a number or not a supported number type');

    // $int = explode(',', number_format(ltrim($number, '0'), 0, '', ',')); -- Buggy
    $int = str_split(str_pad($number, ceil(strlen($number) / 3) * 3, '0', STR_PAD_LEFT), 3);
  }

  private static function thousandToEnglish($number)
  {
    // Gets numbers from 0 to 999 and returns the cardinal English
    $hundreds = floor($number / 100);
    $tens = $number % 100;
    $pre = ($hundreds ? self::$digit[$hundreds] . ' hundred' : '');

    if($tens < 20)
      $post = self::$digit[$tens];
    else
      $post = trim(self::$ten[floor($tens / 10)] . ' ' . self::$digit[$tens % 10]);

    if($pre && $post)
      return $pre .' and '. $post;

    return $pre . $post;
  }

  private static function cardinalToOrdinal($cardinal)
  {
    // Finds the last word in the cardinal arrays and replaces it with
    // the entry from the ordinal arrays, or appends "th"
    $words = explode(' ', $cardinal);
    $last = &$words[count($words) - 1];

    if(in_array($last, self::$digit))
      $last = self::$digith[array_search($last, self::$digit)];
    else if (in_array($last, self::$ten))
      $last = self::$tenth[array_search($last, self::$ten)];
    else if (substr($last, -2) != 'th')
      $last .= 'th';

    return implode(' ', $words);
  }

  public static function toOrdinal($number)
  {
    // Converts a xth format number to English. e.g. 22nd to twenty-second.
    return trim(self::cardinalToOrdinal(self::toCardinal($number)));
  }

  public static function toCardinal($number)
  {
    // Converts a number to English. e.g. 22 to twenty-two.
    self::floatToArray($number, $int, $frac);

    $int = array_reverse($int);

    for($i = count($int) - 1; $i > -1; $i--)
    {
      $englishnumber = self::thousandToEnglish($int[$i]);

      if($englishnumber)
        $english[] = $englishnumber . ' ' . self::$scale[$i];
    }

    $post = array_pop($english);
    $pre = implode(', ', $english);

    if($pre && $post)
      return trim($pre . ' and ' . $post);

    return trim($pre . $post);
  }
}
?>
