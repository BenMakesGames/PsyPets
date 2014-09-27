<?php
require_once 'commons/rsslib.php';

function news_post($authorid, $category, $subject, $message, $writenews = true)
{
  $now = time();

  $q_category = quote_smart($category);
  $q_subject = quote_smart($subject);
  $q_message = quote_smart($message);

  $command = "INSERT INTO `psypets_news` " .
             "(`date`, `author`, `category`, `subject`, `message`) " .
             "VALUES " .
             "($now, $authorid, $q_category, $q_subject, $q_message)";
  fetch_none($command, 'newslib.php/news_post()');

  $id = $GLOBALS['database']->InsertID();

  if($writenews)
    render_xml_latest_news();

  return $id;
}
?>
