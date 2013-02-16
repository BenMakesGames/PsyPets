<?php
//$SITE_DOWN = true;

function quote_smart($value)
{
	return $GLOBALS['database']->Quote($value);
}

function handle_error($where, $details)
{
  $message = '
    <h3>' . $where . ' @ ' . $_SERVER['REQUEST_URI'] . '</h3>
    <p>Referrer: ' . $_SERVER['HTTP_REFERER'] . '</p>
    <p>SQL Statement: ' . $details . '</p>
    <p>SQL Error' . mysql_error() . ' (#' . mysql_errno() . ')</p>
    <h4>Backtrace</h4>
    <pre>' . print_r(debug_backtrace(), true) . '</pre>
  ';

  mail(
    $SETTINGS['author_email'],
    $SETTINGS['site_name'] . ' fatal error',
    $message,
    'MIME-Version: 1.0' . "\n" .
    'Content-type: text/html; charset=utf-8' . "\n" .
    'From: ' . $SETTINGS['site_mailer'] . "\n"
  );
  die('<p>A particularly-nasty error has occurred.  ' . $SETTINGS['author_resident_name'] . ' has been e-mailed with the details of this error.</p><p>Use your browser\'s back button, and retry doing whatever it was you were trying to do.  If the problem persists, please contact ' . $SETTINGS['author_resident_name'] . ' with details about what you were trying to do.  It\'ll help him fix whatever bug may be at work here.</p><p>Sorry about the inconvenience!</p>');
}

function fetch_none($command)
{
	$GLOBALS['database']->FetchNone($command);
}

function fetch_single($command)
{
	return $GLOBALS['database']->FetchSingle($command);
}

function fetch_multiple($command)
{
	return $GLOBALS['database']->FetchMultiple($command);
}

// fetches all available rows into an array indexed by the row's $by field value
function fetch_multiple_by($command, $by)
{
	return $GLOBALS['database']->FetchMultipleBy($command, $by);
}
?>
