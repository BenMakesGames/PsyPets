<?php
class HandyDB
{
    public static $SETTINGS_KEY = 'handydb';

    private $_mysqli;

    public function __construct($settings = false)
    {
        if(!$settings)
        {
            global $SETTINGS;
            $settings = $SETTINGS[HandyDB::$SETTINGS_KEY];
        }

        $this->_mysqli = new mysqli(($settings['persistent'] ? 'p:' : '') . $settings['host'], $settings['user'], $settings['password'], $settings['database']);

        if(mysqli_connect_errno()) {
            $this->HandleError('[error connecting to database: ' . mysqli_connect_errno() . ']');
        }

        $this->_mysqli->set_charset($settings['charset']);
    }

    public function AffectedRows() { return $this->_mysqli->affected_rows; }
    public function InsertID() { return $this->_mysqli->insert_id; }

    public function Page($page_num, $page_size)
    {
        return 'LIMIT ' . (($page_num - 1) * $page_size) . ',' . $page_size;
    }


    public function Query($query)
    {
        do
        {
            $result = $this->_mysqli->query($query);
        } while($this->_mysqli->errno == 1213); // while deadlocked

        return $result;
    }

    public function FetchNone($query)
    {
        $result = $this->Query($query);

        if($this->_mysqli->errno)
            $this->HandleError($query);
    }

    public function FetchSingle($query)
    {
        $result = $this->Query($query);

        if(get_class($result) != 'mysqli_result')
            $this->HandleError($query);

        $row = $result->fetch_assoc();

        return($row === NULL ? false : $row);
    }

    public function FetchMultiple($query)
    {
        $result = $this->Query($query);

        if(get_class($result) != 'mysqli_result')
            $this->HandleError($query);

        $rows = array();

        while($row = $result->fetch_assoc())
            $rows[] = $row;

        return $rows;
    }

    public function FetchMultipleBy($query, $key)
    {
        $result = $this->_mysqli->query($query);

        if(get_class($result) != 'mysqli_result')
            $this->HandleError($query);

        $rows = array();

        while($row = $result->fetch_assoc())
            $rows[$row[$key]] = $row;

        return $rows;
    }

    public function In($data)
    {
        if(is_array($data))
        {
            if(count($data) > 1)
                return ' IN (' . implode(',', $this->Quote($data)) . ')';
            else
                return '=' . $this->Quote(reset($data));
        }
        else
            return '=' . $this->Quote($data);
    }

    public function NotIn($data)
    {
        if(is_array($data))
        {
            if(count($data) > 1)
                return ' NOT IN (' . implode(',', $this->Quote($data)) . ')';
            else
                return '!=' . $this->Quote(reset($data));
        }
        else
            return '!=' . $this->Quote($data);
    }

    public function Quote($data)
    {
        if(is_array($data))
        {
            foreach($data as &$d)
                $d = $this->Quote($d);

            return $data;
        }
        else
            return '\'' . $this->_mysqli->real_escape_string($data) . '\'';
    }

    public function HandleError($query)
    {
        global $SETTINGS;

        if(PHP_SAPI == 'cli')
        {
            throw new Exception('SQL Error: ' . $this->_mysqli->error . ' (#' . $this->_mysqli->errno . ')');
        }
        else
        {
            $message = '
                <h3>' . $_SERVER['REQUEST_URI'] . '</h3>
                <p>Referrer: ' . $_SERVER['HTTP_REFERER'] . '</p>
                <p>SQL Statement: ' . $query . '</p>
                <p>SQL Error: ' . $this->_mysqli->error . ' (#' . $this->_mysqli->errno . ')</p>
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

            die('<p>A particularly-nasty error has occurred.  ' . $SETTINGS['author_resident_name'] . ' has been e-mailed with the details of this error.</p><p>Use your browser\'s back button, and retry doing whatever it was you were trying to do.  If the problem persists, please contact That Guy Ben with details about what you were trying to do.  It\'ll help him fix whatever bug may be at work here.</p><p>Sorry about the inconvenience!</p>');
        }
    }
}
