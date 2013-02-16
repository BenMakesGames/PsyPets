<?php
function fetch_none($query) { return $GLOBALS['database']->FetchNone($query); }
function fetch_single($query) { return $GLOBALS['database']->FetchSingle($query); }
function fetch_multiple($query) { return $GLOBALS['database']->FetchMultiple($query); }
function fetch_multiple_by($query, $by) { return $GLOBALS['database']->FetchMultipleBy($query, $by); }
function quote_smart($thing) { return $GLOBALS['database']->Quote($thing); }
