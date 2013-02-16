<?php
 $whereat = "home";

 // confirm the session...
 require_once "commons/dbconnect.php";
 require_once "commons/sessions.php";

 $pet_num = (int)$_GET['petnum'];

 if($_GET["dir"] == "up" || $_GET["dir"] == "down")
 {
   if($_GET["dir"] == "up" && $pet_num > 0)
     $modifier = -1;
   else if($_GET["dir"] == "down" && $pet_num < count($userpets))
     $modifier = 1;
   else
     $modifier = 0;

   if($modifier != 0)
   {
     $id = 0;
     foreach($userpets as $petnum=>$pet)
     {
//       echo "changed pet $petnum's orderid to $id<br>\n";
       $userpets[$petnum]["orderid"] = $id;
       ++$id;
     }

     $newlocation = $userpets[$pet_num + $modifier]["orderid"];
     $oldlocation = $userpets[$pet_num]["orderid"];

     $userpets[$pet_num]["orderid"]             = $newlocation;
     $userpets[$pet_num + $modifier]["orderid"] = $oldlocation;
/*
     echo "changed pet " . $_GET["petnum"] . "'s orderid to $newlocation<br>\n";
     echo "changed pet " . ($_GET["petnum"] + $modifier) . "'s orderid to $oldlocation<br>\n";
*/  
     foreach($userpets as $petnum=>$pet)
     {
       $command = "UPDATE monster_pets SET orderid=" . $pet["orderid"] . " WHERE idnum=" . $pet["idnum"] . " LIMIT 1;";
      $database->FetchNone($command, 'updating pet sort order');
/*
       else
         echo "saved pet $petnum<br>\n";
*/
     }
   }
 }

 Header("Location: ./myhouse.php");
?>
