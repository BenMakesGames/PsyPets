<?php
$TIME_SERVERS = array(
  'time-a.nist.gov',
  'time-b.nist.gov',
  'time-a.timefreq.bldrdoc.gov',
  'time-b.timefreq.bldrdoc.gov',
  'time-c.timefreq.bldrdoc.gov',
  'utcnist.colorado.edu',
  'time.nist.gov',
  'time-nw.nist.gov',
  'nist1.symmetricom.com',
  'nist1-dc.WiTime.net',
  'nist1-ny.WiTime.net',
  'nist1-sj.WiTime.net',
  'nist1.aol-ca.symmetricom.com',
  'nist1.aol-va.symmetricom.com',
  'nist1.columbiacountyga.gov',
  'nist.expertsmi.com'
);

function get_time_from_server()
{
  global $TIME_SERVERS;

  return get_time($TIME_SERVERS[array_rand($TIME_SERVERS)]);
}

function get_time($timeserver)
{
  $fp = fsockopen($timeserver, 37, $err, $errstr, 5);
  
  if($fp)
  {
    fputs($fp, "\n");
    $timestamp = fread($fp, 49);
    fclose($fp);
    
    $timestamp = bin2hex($timestamp);
    $timestamp = abs(hexdec('7fffffff') - hexdec($timestamp) - hexdec('7fffffff'));
    $timestamp -= 2208988800;
    
    return $timestamp;
  }
  else
    return false;;
}
?>
