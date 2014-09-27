<?php
// by mykl (from http://www.idealog.us/2006/07/zip_code_distan.html)
function getDistance($lat1, $log1, $lat2, $log2)
{
  $r = 3963.1; //3963.1 statute miles; 3443.9 nautical miles; 6378 km
  $pi = 3.14159265358979323846;

  $lat1 = $lat1*($pi/180);
  $lat2 = $lat2*($pi/180);
  $log1 = $log1*($pi/180);
  $log2 = $log2*($pi/180);

  $ret = (acos(cos($lat1) * cos($log1) * cos($lat2) * cos($log2) + cos($lat1) * sin($log1) * cos($lat2) * sin($log2) + sin($lat1) * sin($lat2)) * $r);
  return $ret;
}

// download new ZIP databases from http://www.populardata.com/downloads.html
?>
