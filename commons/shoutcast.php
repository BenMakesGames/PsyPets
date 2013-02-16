<?php
function shoutcast_status($sc_ip, $sc_port)
{
  $scp = @fsockopen($sc_ip, $sc_port, $errno, $errstr, 30);

	if(!$scp)
    return 'offline';
  else
  {
    fputs($scp, "GET /7.html HTTP/1.0\r\nUser-Agent: SC Status (Mozilla Compatible)\r\n\r\n");

  	while(!feof($scp))
    {
  		$sc7 .= fgets($scp, 1024);
  	}

    @fclose($scp);

    //while we got the page open into memory lets bomb n parse baby.
    $sc7 = ereg_replace('.*<body>', '', $sc7);
    $sc7 = ereg_replace('</body>.*', ',', $sc7);
    $sc_contents = explode(',', $sc7);
    $dummy = $sc_contents[0];
    $dsp_connected = $sc_contents[1];

    //check dsp connection and display the status of the shoutcast server in question
    if($dsp_connected == '1')
      return 'online';
    else
      return 'offline';
 	}
}
?>
