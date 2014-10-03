<?php
require_once 'commons/HTMLPurifier.standalone.php';
require_once 'commons/cachelib.php';

function FormatNumber($number, $user)
{
    return number_format($number, 0, '.', ',');
}

function GetPurifier()
{
	static $html_purifier = false;
	
	if($html_purifier === false)
	{
		$html_purifier = new HTMLPurifier($config);

		$html_purifier->config->set('CSS.Proprietary', true);

		$html_purifier->config->set('CSS.AllowedProperties', array(
			'color' => true,
			'background-color' => true,

			'font-size' => true,
			'font-family' => true,
			'font-style' => true,
			'font-weight' => true,
			'font-variant' => true,
			'text-decoration' => true,
			'text-transform' => true,
			'text-align' => true,
			'text-indent' => true,

			'border' => true,
//			'border-radius' => true,
//			'box-shadow' => true,

			'opacity' => true,

			'list-style' => true,
			'list-style-type' => true,

			'border-collapse' => true,
			'border-spacing' => true,
//			'direction' => true,
//			'text-shadow' => true,
//			'text-justify' => true,
//			'text-outline' => true,
		));

		$html_purifier->config->set('HTML.AllowedAttributes', array(
			'*.style' => true,
			'*.alt' => true,
			'*.title' => true,
			'a.href' => true,
			'img.src' => true,
			'img.align' => true,
			'font.color' => true,
			'table.border' => true,
		));

		$html_purifier->config->set('HTML.ForbiddenElements', array(
			'h1' => true,
			'h2' => true,
			'h3' => true,
			'h4' => true,
			'h5' => true,
			'h6' => true,
		));
	}
	
	return $html_purifier;
}

function birthdate_to_age($date)
{ 
  global $now_year, $now_month, $now_day;

  list($year, $month, $day) = explode('-', $date);
  
  $year_diff = $now_year - $year;
  $month_diff = $now_month - $month;
  $day_diff = $now_day - $day;
  
  if($month_diff < 0 || ($month_diff == 0 && $day_diff < 0))
    return $year_diff - 1;
  else
    return $year_diff;
}

// A function to return the Roman Numeral, given an integer
// taken from http://www.go4expert.com/forums/showthread.php?t=4948
function Romanize($num)
{
  // Make sure that we only use the integer portion of the value
  $n = intval($num);
  $result = '';

  // Declare a lookup array that we will use to traverse the number:
  $lookup = array(
    'M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400,
    'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40,
    'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1
  );

  foreach($lookup as $roman=>$value)
  {
    // Determine the number of matches
    $matches = intval($n / $value);

    // Store that many characters
    $result .= str_repeat($roman, $matches);

    // Substract that from the number
    $n = $n % $value;
  }

  // The Roman numeral should be built, return it
  return $result;
}

function mystery_string($string)
{
  $bits = explode(' ', $string);
  $pieces = array();

  foreach($bits as $bit)
    $pieces[] = str_repeat('?', strlen($bit));

  return implode(' ', $pieces);
}

function str_insert($insert, $into, $offset)
{
  $part1 = substr($into, 0, $offset);
  $part2 = substr($into, $offset);

  return $part1 . $insert . $part2;
}

function wbr_it_up($text)
{
  $chunks = explode(' ', $text);
  $newchunks = array();
  foreach($chunks as $chunk)
  {
    $skip_it = false;

    if(strpos($chunk, '<') !== false)
      $skip++;
    if(strpos($chunk, '>') !== false && $skip > 0)
    {
      $skip--;
      $skip_it = true;
    }
    
    if($skip > 0)
      $skip_it = true;

    if($skip_it === false && strlen($chunk) > 8)
    {
      $places = floor(strlen($chunk) / 8);
      for($x = $places * 8; $x > 0; $x -= 8)
        $chunk = str_insert('<wbr />', $chunk, $x);
    }

    $newchunks[] = $chunk;
  }

  return implode(' ', $newchunks);
}

function br2nl($text)
{
  return str_replace(array('<br>', '<br />'), array('', ''), $text);
}

function item_text_link($itemname, $class = false, $alt_name = false)
{
  global $user;

  if($alt_name === false)
    $itemtext = $itemname;
  else
    $itemtext = $alt_name;
  
  if($class !== false)
    $itemtext = '<span class="' . $class . '">' . $itemtext . '</span>';

  if($user['encyclopedia_popup'] == 'yes')
    return '<a href="/encyclopedia2.php?item=' . link_safe($itemname) . '" onclick="encyclopedia_popup(this, \'' . link_safe($itemname) . '\'); return false;">' . $itemtext . '</a>';
  else
    return '<a href="/encyclopedia2.php?item=' . link_safe($itemname) . '">' . $itemtext . '</a>';
}

function item_display_extra($item, $extracode = '', $enc_link = true)
{
	global $SETTINGS;

  if($item['graphictype'] == 'bitmap')
  {
    if(strlen($extracode) == 0)
      $extracode = 'title="' . htmlentities($item['itemname']) . '"';

    if(substr($item['graphic'], 0, 18) == '../../gfx/library/')
      $string = '<img src="//' . $SETTINGS['site_domain'] . '/gfx/library/' . substr($item['graphic'], 18) . '" border="0" height="32" ' . $extracode . ' />';
    else
      $string = '<img src="//' . $SETTINGS['static_domain'] . '/gfx/items/' . $item['graphic'] . '" border="0" height="32" ' . $extracode . ' />';

    if($enc_link)
    {
      global $user;

      if($user['encyclopedia_popup'] == 'yes')
        $string = '<a href="/encyclopedia2.php?item=' . link_safe($item['itemname']) . '" onclick="encyclopedia_popup(this, \'' . link_safe($item['itemname']) . '\'); return false;">' . $string . '</a>';
      else
        $string = '<a href="/encyclopedia2.php?item=' . link_safe($item['itemname']) . '">' . $string . '</a>';
    }

    return $string;
  }
  else if($item['graphictype'] == 'flash')
  {
    return "<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0\" width=\"32\" height=\"32\" id=\"" . str_replace(' ', '', $item['itemname']) . '">' .
           "<param name=\"allowScriptAccess\" value=\"sameDomain\" />" .
           "<param name=\"movie\" value=\"/gfx/items/" . $item["graphic"] . "\" />" .
           "<param name=\"menu\" value=\"false\" />" .
           "<param name=\"quality\" value=\"high\" />" .
           "<param name=\"wmode\" value=\"transparent\" />" .
           "<param name=\"bgcolor\" value=\"#f8f8f8\" />" .
           "<embed src=\"gfx/items/" . $item["graphic"] ."\" menu=\"false\" quality=\"high\" wmode=\"transparent\" bgcolor=\"#f8f8f8\" width=\"32\" height=\"32\" name=\"" . str_replace(' ', '', $item['itemname']) . "\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" $extracode />" .
           "</object>";
  }
}

function item_display($item, $extracode)
{
  return item_display_extra($item, $extracode, true);
}

function time_amount($time)
{
  $unit_day = 24 * 60 * 60;
  $unit_hour = 60 * 60;
  $unit_minute = 60;

  $desc = array();

  if($time >= $unit_day)
  {
    $days = floor($time / $unit_day);
    $time -= $days * $unit_day;
    $desc[] = $days . ' day' . ($days != 1 ? 's' : '');
  }
  if($time >= $unit_hour)
  {
    $hours = floor($time / $unit_hour);
    $time -= $hours * $unit_hour;
    $desc[] = $hours . ' hour' . ($hours != 1 ? 's' : '');
  }
  if($time >= $unit_minute)
  {
    $minutes = floor($time / $unit_minute);
    $time -= $minutes * $unit_minute;
    $desc[] = $minutes . ' minute' . ($minutes != 1 ? 's' : '');
  }

  if($time <= 0)
    $desc[] = 'none';

  if(count($desc) > 0)
    return implode(' ', $desc);
  else
    return 'less than a minute';
}

function highlight_words($text, $words, $exact)
{
  if($exact)
  {
    foreach($words as $word)
    {
      $word = str_replace('+', '\+', $word);
      $text = eregi_replace("([^A-Za-z])(" . $word . ")([^A-Za-z])", "\\1<font style=\"background: #ffff00;\">\\2</font>\\3", $text);
      $text = eregi_replace("^()(" . $word . ")([^A-Za-z])", "\\1<font style=\"background: #ffff00;\">\\2</font>\\3", $text);
      $text = eregi_replace("([^A-Za-z])(" . $word . ")()$", "\\1<font style=\"background: #ffff00;\">\\2</font>\\3", $text);
      $text = eregi_replace("^()(" . $word . ")()$", "\\1<font style=\"background: #ffff00;\">\\2</font>\\3", $text);
    }
  }
  else
  {
    foreach($words as $word)
      $owords[] = '<font style="background: #ffff00;">' . $word . '</font>';

    $text = str_ireplace($words, $owords, $text);
  }

  return $text;
}

function break_long_lines($line)
{
  $new_line = '';
  $consecutive = 0;

  for($i = 0; $i < strlen($line); ++$i)
  {
    $c = $line{$i};
    if(preg_match("/[^ \n\t]/", $c))
      $consecutive++;
    else
      $consecutive = 0;

    if($consecutive == 80)
    {
      $consecutive = 0;
      $new_line .= "\n";
    }

    $new_line .= $c;
  }

  return $new_line;
}

function page_numbers($num_pages, $cur_page)
{
  page_numbers_url($num_pages, $cur_page, 'viewthread.php?threadid=' . $_GET['threadid']);
}

function paginate($num_pages, $cur_page, $furl)
{
  $str = '<ul class="pagination"><li class="skip">Page:</li>';

  $ellipse = false;

  for($i = 1; $i <= $num_pages; $i++)
  {
    if($i == $cur_page)
      $str .= '<li class="current"><span>' . $i . '</span></li>';
    else
    {
      $diff1 = $i - 1;
      $diff2 = abs($i - $cur_page);
      $diff3 = $num_pages - $i;

      $max_diff = min($diff1, $diff2, $diff3);

      if($max_diff > 3)
      {
        if($ellipse == false)
        {
          $str .= '<li class="skip">...</li>';
          $ellipse = true;
        }
      }
      else
      {
        $ellipse = false;

        if($i + 1 == $cur_page)
          $access = ' accesskey="," rel="prev"';
        else if($i - 1 == $cur_page)
          $access = ' accesskey="." rel="next"';
        else
          $access = '';
          
        $str .= '<li><a href="' . sprintf($furl, $i) . '"' . $access . '>' . $i . '</a></li>';
      }
    }
  }

  $str .= '</ul>';
  
  return $str;
}

function page_numbers_url($num_pages, $cur_page, $url)
{
  deprecated('page_numbers_url()');
  $ellipse = false;

  for($i = 1; $i <= $num_pages; $i++)
  {
    if($i > 1 && $ellipse == false)
      echo ' | ';

    if($i == $cur_page)
      echo $i;
    else
    {
      $diff1 = $i - 1;
      $diff2 = abs($i - $cur_page);
      $diff3 = $num_pages - $i;

      $max_diff = min($diff1, $diff2, $diff3);

      if($max_diff > 3)
      {
        if($ellipse == false)
        {
          echo '...';
          $ellipse = true;
        }
      }
      else
      {
        if($ellipse)
          echo ' | ';

        $ellipse = false;      
        echo "<a href=\"$url&page=$i\">$i</a>";
      }
    }
  }
}

function local_date($time, $tz, $dst)
{
  if($dst == 'yes')
    $ds = 1;
  else
    $ds = 0;

  //                     time    user time-zone    daylight savings
  return gmdate('Y.m.d', $time + (60 * 60 * $tz) + (60 * 60 * $ds));
}

function local_hour($hour, $tz, $dst)
{
  if($dst == 'yes')
    $ds = 1;
  else
    $ds = 0;

  //                    time                  user time-zone    daylight savings
  return gmdate('g:ia', mktime($hour, 0, 0) + (60 * 60 * $tz) + (60 * 60 * $ds));
}

function local_time_short($time, $tz, $dst)
{
  if($dst == 'yes')
    $ds = 1;
  else
    $ds = 0;

  //                             time    user time-zone    daylight savings
  return gmdate('Y.m.d, h:ia', $time + (60 * 60 * $tz) + (60 * 60 * $ds));
}

function local_time($time, $tz, $dst)
{
  if($dst == 'yes')
    $ds = 1;
  else
    $ds = 0;

  //                             time    user time-zone    daylight savings
  return gmdate('Y.m.d, h:i:sa', $time + (60 * 60 * $tz) + (60 * 60 * $ds));
}

function force_spaces($str)
{
  return str_replace(' ', '&nbsp;', $str);
}

function begin_row_class()
{
  return 'altrow';
}

function alt_row_class($c)
{
  if($c == 'altrow')
    return 'row';

  if($c == 'row')
    return 'altrow';
}

function backlight_alert($c)
{
  if($c == 'row')
    return 'row_alert';

  if($c == 'altrow')
    return 'altrow_alert';
}

function backlight($c)
{
  if($c == 'row')
    return 'row_backlit';

  if($c == 'altrow')
    return 'altrow_backlit';
}

$ALERTED = false;

function deprecated($func)
{
  global $ALERTED;
  if(!$ALERTED)
  {
    echo '<div style="background-color: #fff; border: 1px solid black; padding: 0.5em; position: absolute; top: 150px; left: 200px;"><strong>Use of deprecated function ' . $func . '!?<br />' . $SETTINGS['author_resident_name'] . ' should really update this ancient code!</strong></div>';
    $ALERTED = true;
  }
}

function begin_row_color()
{
  deprecated('begin_row_color()');
  return "#f0f0f0";
}

function alt_row_color($c)
{
  deprecated('alt_row_color($c)');
  if($c == "#f0f0f0")
    $c = "#ffffff";

  else if($c == "#ffffff")
    $c = "#f0f0f0";

  return $c;
}

function formatting_help()
{
  @include 'commons/formatting_help.php';
  return '';
}

function unlink_safe($text)
{
  return rawurldecode($text);
}

function link_safe($text)
{
  return rawurlencode($text);
}

function tip_safe($text)
{
  return str_replace('\'', "\\'", htmlentities($text, ENT_COMPAT, 'UTF-8'));
}

function java_safe($text)
{
  $text = str_replace(array("'", "\"", chr(10), chr(13)), array("\\'", "\\'", "", ""), $text);
  return $text;
}

function format_text($line, $cache = true)
{
	if($cache === false)
	{
		return _format_text_uncached($line);
	}
	else
	{
		$key = md5('user text:' . $line);

		$text = Cache::Get($key);

		if($text === false)
		{
			$text = _format_text_uncached($line);
			
			Cache::Add($key, $text);
		}
		
		return $text;
	}
}

function _format_text_uncached($line)
{
	$line = smilize($line);
	
	if(date('M j') == 'Sep 19')
		$line = piratize($line);

	return nl2br(GetPurifier()->purify($line));
}

function bytesize($value)
{
  $multiples = array('B', 'KB', 'MB', 'GB', 'TB');

  $count = 0;

  if($value > 1024 * 1024)
    bcscale(2);
  else
    bcscale(0);
  
  while($value > 1024)
  {
    $value = bcdiv($value, 1024);
    ++$count;
  }

  return $value . '&nbsp;' . $multiples[$count];
}

function piratize($strings)
{
  global $NO_PIRATE;
  
  if($NO_PIRATE === true)
    return $strings;

  $patterns = array(
    'LOL'    => 'Har, har!',
    'ROFL'   => 'Har, har!',
    'rofl'   => 'Har, har!',
    'LMAO'   => 'Har, har!',
    'lmao'   => 'Har, har!',
    'ROFLOL' => 'Har, har, har!',
    'roflol' => 'Har, har, har!',
    ' is '   => ' be ',
    ' are '  => ' be ',
    ' my '   => ' me ',
    'myself' => 'm\'self',
    ' hey!'  => ' yarr!',
    ' hey '  => ' avast ',
    ' hey,'  => ' avast,',
    'Hey!'  => 'Yarr!',
    'Hey '  => 'Avast ',
    'Hey,'  => 'Avast,',
    'they\'re' => 'they be',
    'we\'re' => 'we be',
    'you\'re' => 'y\' be',
    ' friends' => ' mateys',
    'They\'re' => 'They be',
    'We\'re' => 'We be',
    'You\'re' => 'Y\' be',
    ' you ' => ' ye ',
    'You ' => 'Ye ',
    ' for ' => ' fer ',
    ' your ' => ' yer ',
    ' to ' => ' t\' ',
    'money ' => 'dubloons ',
    'moneys ' => 'dubloons ',
    'money.' => 'dubloons.',
    'moneys.' => 'dubloons.',
    'money?' => 'dubloons?',
    'moneys?' => 'dubloons?',
    'money,' => 'dubloons,',
    'moneys,' => 'dubloons,',
    '{m}' => ' dubloons',
    'ing ' => 'in\' ',
    'ing.' => 'in\'.',
    'ing,' => 'in\',',
    'ing!' => 'in\'!',
    'ing?' => 'in\'?',
    ' and ' => ' an\' ',
    'you\'ll' => 'y\'ll',
    ' was ' => ' were ',
    'Yeah' => 'Aye',
    'yeah' => 'aye',
    'Yes ' => 'Aye ',
    ' yes ' => 'aye ',
    'Yes,' => 'Aye,',
    ' yes,' => 'aye,',
    'Yes!' => 'Aye!',
    ' yes!' => 'aye!',
    'Yes.' => 'Aye.',
    ' yes.' => 'aye.',
    'Yes?' => 'Aye?',
    ' yes?' => 'aye?',
    'dollars' => 'dubloons',
  );

  $strings = strtr($strings, $patterns);

  return $strings;
}

function symbolize($strings)
{
  $patterns = array(
    '{star}' => '&#9734;',
    '{*}' => '&#9734;',
    '{circle}' => '&#9675;',
    '{heart}' => '&#9825;',
    '{love}' => '&#9825;',
    '{l}' => '&#9825;',
    '{square}' => '&#9633;',
    '{therefore}' => '&there4;',
    '{tf}' => '&there4;',
    '{space}' => '&nbsp;&nbsp;&nbsp;',
  );
  
  $strings = strtr($strings, $patterns);
  
  return $strings;
}

function smilize($strings, $do_it = true)
{
  if(!$do_it)
    return $strings;

  $patterns = array(
    '/\B>[_\.]<\B/' => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/ergk.gif" alt="[ergk!]" class="smiley inlineimage" />',

    '/\B�_?�\B/'   => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/suspicious.gif" alt="[is suspicious]" class="smiley inlineimage" />',
    '/\B>_?>\B/' => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/suspicious.gif" alt="[is suspicious]" class="smiley inlineimage" />',

    '/\B:-?\(\B/'   => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/aw.gif" alt="[sad]" class="smiley inlineimage" />',
    '/:sad:/'   => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/aw.gif" alt="[sad]" class="smiley inlineimage" />',

    '/\B:-?\|\B/'   => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/eh.gif" alt="[neutral]" class="smiley inlineimage" />',

    '/\B:-?\)\B/'   => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/hee.gif" alt="[happy]" class="smiley inlineimage" />',

    '/\B\^[_-]?\^\B/' => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/anime.gif" alt="[happy]" class="smiley inlineimage" />',

    '/\bx_x\b/i'   => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/dead.gif" alt="[dies]" class="smiley inlineimage" />',
    '/:dies?:/i'   => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/dead.gif" alt="[dies]" class="smiley inlineimage" />',

    '/\bLOL\b/i'   => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/lol.gif" alt="LOL" class="smiley inlineimage" />',
    '/\bROFL\b/i'  => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/lol.gif" alt="ROFL" class="smiley inlineimage" />',
    '/\bLMAO\b/i'  => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/lol.gif" alt="LMAO" class="smiley inlineimage" />',
    '/\bROFLOL\b/' => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/lol.gif" alt="ROFLOL" class="smiley inlineimage" />',

    '/\B<3\b/'  => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/loves.gif" alt="[love]" class="smiley inlineimage" />',
    '/:loves?:/'  => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/loves.gif" alt="[love]" class="smiley inlineimage" />',

    '/:rants?:/'  => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/rant-and-rage.png" alt="[rants!]" class="smiley inlineimage" />',
    '/:zombie:/'  => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/zombie.gif" alt="[zombie]" class="smiley inlineimage" />',
    
    '/\B>:-?O\b/i' => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/grr.png" alt="[grr!]" class="smiley inlineimage" />',

    '/\B:-?P\b/i'  => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/nyeh.gif" alt="[razz]" class="smiley inlineimage" />',
    '/:razz:/i'  => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/nyeh.gif" alt="[razz]" class="smiley inlineimage" />',

    '/\B:-?O\b/i'  => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/gasp.gif" alt="[gasp]" class="smiley inlineimage" />',
    '/:gasps?:/i'  => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/gasp.gif" alt="[gasp]" class="smiley inlineimage" />',

    '/\B:-?S\b/i'  => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/mmf.gif" alt="[mmf]" class="smiley inlineimage" />',

    '/\bO[_\.]O\b/i' => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/ohgod.gif" alt="[eep!]" class="smiley inlineimage" />',

    '/\bn_n\b/'    => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/uhhuh.gif" alt="[rolls eyes]" class="smiley inlineimage" />',
    '/\be_e\b/'    => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/uhhuh.gif" alt="[rolls eyes]" class="smiley inlineimage" />',

    '/\B:-?D\b/'   => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/yeah.png" alt="[awesome!]" class="smiley inlineimage" />',

    '/\B-\.-\B/'    => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/zzz.gif" alt="[disbelief]" class="smiley inlineimage" />',
    '/\B-_-\B/'    => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/zzz.gif" alt="[sleepy]" class="smiley inlineimage" />',

		'/tl;dr/'       => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/tldr.png" alt="tl;dr" class="smiley inlineimage" />',
		
    '/\B;-?\)\B/'   => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/wink.gif" alt="[wink]" class="smiley inlineimage" />',
    '/:winks?:/'   => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/wink.gif" alt="[wink]" class="smiley inlineimage" />',

    '/\B:\'-?\(\B/' => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/tear.gif" alt="[cries]" class="smiley inlineimage" />',
    '/\bT_T\b/'    => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/tear.gif" alt="[cries]" class="smiley inlineimage" />',
    '/\B;_;\B/'    => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/tear.gif" alt="[cries]" class="smiley inlineimage" />',
    '/:cr(y|ies):/'    => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/tear.gif" alt="[cries]" class="smiley inlineimage" />',
    '/:tears?:/'    => '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/tear.gif" alt="[cries]" class="smiley inlineimage" />',
  );

  $strings = preg_replace(array_keys($patterns), array_values($patterns), $strings);

  return $strings;
}

function link_to_room($room)
{
  if($room == 'home')
    return '<a href="/myhouse.php">My House</a>';
  else if(substr($room, 0, 5) == 'home/')
  {
    $room = substr($room, 5);
    return '<a href="/myhouse.php?room=' . $room . '">' . ($room{0} == '$' ? substr($room, 1) : $room) . '</a>';
  }
  else if($room == 'storage')
    return '<a href="/storage.php">Storage</a>';
  else if($room == 'storage/incoming')
    return '<a href="/incoming.php">Incoming</a>';
  else
    return $room;
}
?>
