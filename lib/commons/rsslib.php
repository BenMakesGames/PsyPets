<?php
require_once 'commons/formatting.php';

function render_xml_latest_news()
{
  global $SETTINGS;

  $string = '<?xml version="1.0" ?' . ">\n";
  $string .= <<<EOS
<rss version="2.0">
 <channel>
  <title><![CDATA[{$SETTING['site_name']} Latest News]]></title>
  <link>{$SETTINGS['site_url']}</link>
  <description><![CDATA[{$SETTING['site_name']} Latest News]]></description>
  <language>en-us</language>
  <image>
   <url>{$SETTINGS['site_url']}/gfx/pets/desikh.gif</url>
   <title><![CDATA[{$SETTING['site_name']} Latest News]]></title>
   <link>{$SETTINGS['site_url']}</link>
   <width>48</width>
   <height>48</height>
  </image>
EOS;

  $items = $GLOBALS['database']->FetchMultiple('SELECT * FROM psypets_news ORDER BY idnum DESC LIMIT 10');

  foreach($items as $data)
  {
    $string .= '  <item>' . "\n" .
               '   <title><![CDATA[' . $data['category'] . ': ' . $data['subject'] . ']]></title>' . "\n" .
               '   <description><![CDATA[' . format_text($data['message']) . ']]></description>';

    if($data['threadid'] > 0)
      $string .= '   <link>http://' . $SETTINGS['site_domain'] . '/viewthread.php?threadid=' . $data['threadid'] . '</link>' . "\n";

    $string .= '   <pubDate>' . date('r', $data['date']) . '</pubDate>' . "\n" .
               '  </item>' . "\n";
  }

  $string .= ' </channel>' . "\n" .
             '</rss>' . "\n";
  
  $fp = fopen($_SERVER['DOCUMENT_ROOT'] . '/rss_news.xml', 'wb');
  fwrite($fp, $string);
  fclose($fp);
}
?>
