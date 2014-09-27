<?php
$file = basename($_GET['image']);

if(file_exists($file))
{
  $ext = substr($file, strlen($file) - 4);

  if($ext == '.gif' || $ext == '.png')
  {
    if($ext == '.gif')
      header('Content-Type: image/gif');
    else
      header('Content-Type: image/png');
  
    readfile($file);
  
    exit();
  }
}

header('Content-Type: image/png');

readfile('../badimage.png');
?>
