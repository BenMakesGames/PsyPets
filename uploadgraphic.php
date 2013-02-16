<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/globals.php';

// must have the appropriate rights to do this
if($admin['uploadpetgraphics'] != 'yes')
{
  header('Location: /');
  exit();
}

$petgfx = get_global('petgfx');
$avatargfx = get_global('avatargfx');
$avatardesc = get_global('avatardesc');

// upload pet graphic
if($_POST['submit'] == 'Upload')
{
  if($_POST['gfxtype'] == 'publicpet')
  {
    $uploaddir = 'gfx/pets/';
  }
  else if($_POST['gfxtype'] == 'publicavatar')
  {
    $uploaddir = 'gfx/avatars/';
  }
  else if($_POST['gfxtype'] == 'item')
  {
    $uploaddir = 'gfx/items/';
  }
  else
  {
    echo "graphic type was not selected.<br>\n";
    exit();
  }

  if($_FILES["gfxfile"])
  {
    $uploadfile = $uploaddir . $_FILES["gfxfile"]["name"];

    echo $uploadfile . "<br>\n";

    if(file_exists($uploadfile))
    {
      if($_POST["overwrite"] == "yes" || $_POST["overwrite"] == "on")
      {
        if(move_uploaded_file($_FILES["gfxfile"]["tmp_name"], $uploadfile))
          echo "overwrite successful.<br>\n";
        else
          echo "overwrite failed.<br>\n";
      }
      else
        echo "file already exists.<br>\n";
    }
    else
    {
      if(move_uploaded_file($_FILES["gfxfile"]["tmp_name"], $uploadfile))
      {
        echo "file uploaded successfully.<br>\n";

        if($_POST["gfxtype"] == "publicpet")
        {
          $petgfx[] = $_FILES["gfxfile"]["name"];

          $command = "UPDATE monster_globals SET `value`=" . quote_smart(implode(",", $petgfx)) . " WHERE `name`='petgfx' LIMIT 1";
         $database->FetchNone($command, 'adding graphic to list of standard pet graphics');
        }
        else if($_POST["gfxtype"] == "publicavatar")
        {
        }
      }
      else
        echo "file upload failed.<br>\n";
    }
  }
  else
  {
    echo "no file given.<br>\n";
  }
}
?>
