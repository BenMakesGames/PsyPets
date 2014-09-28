<?php
abstract class psyDBObject
{
    private static $db_connection = false;

    protected $_table;
    protected $_data = false;

    public function RawData() { return $this->_data; }
    public function IsLoaded() { return($this->_data !== false); }

    protected function Insert($params = array())
    {
        $this->FetchNone('
          INSERT INTO ' . $this->_table . '
            (' . implode(', ', array_keys($params)) . ')
            VALUES
            (' . implode(', ', $this->QuoteArray($params)) . ')
        ');

        return mysqli_insert_id(self::$db_connection);
    }

    protected function Update($params = array())
    {
        if($params['where'] && count($params['where']) > 0)
            ;
        else
            $this->HandleError('No WHERE given for ::Update');

        $command = '
            UPDATE ' . $this->_table . '
            SET ' . implode(', ', $params['set']) . '
            WHERE ' . implode(' AND ', $params['where']);

        if($params['limit'])
            $command .= ' LIMIT ' . $params['limit'];

        $this->FetchNone($command);
    }

    protected function Count($params = array())
    {
        $command = 'SELECT COUNT(*) AS qty FROM ' . $this->_table . ' WHERE ' . implode(' AND ', $params['where']);

        $data = $this->FetchSingle($command);

        return $data['qty'];
    }

    protected function Select($params = array())
    {
        $command = 'SELECT * FROM ' . $this->_table . ' WHERE ' . implode(' AND ', $params['where']);

        if($params['order'])
            $command .= ' ORDER BY ' . implode(', ', $params['order']);

        if($params['limit'])
            $command .= ' LIMIT ' . $params['limit'];

        $this->_data = $this->FetchMultiple($command);

        return $this->_data;
    }

    protected function SelectOne($params = array())
    {
        $command = '
            SELECT * FROM ' . $this->_table . '
            WHERE ' . implode(' AND ', $params['where']);

        if($params['order'])
            $command .= ' ORDER BY ' . implode(', ', $params['order']);

        $command .= ' LIMIT 1';

        $this->_data = $this->FetchSingle($command);

        return $this->_data;
    }

    protected function __construct($table)
    {
        global $SETTINGS;

        if(psyDBObject::$db_connection === false)
        {
            try
            {
                psyDBObject::$db_connection = mysqli_connect(
                    ($SETTINGS['handydb']['persistent'] ? 'p:' : '') . $SETTINGS['handydb']['host'],
                    $SETTINGS['handydb']['user'],
                    $SETTINGS['handydb']['password'],
                    $SETTINGS['handydb']['database']
                );
            }
            catch(Exception $e)
            {
                var_dump($e);
                die();
            }

            if(mysqli_connect_errno())
            {
                // 1203 == ER_TOO_MANY_USER_CONNECTIONS
                if(mysqli_connect_errno() == 1203)
                {
                    header('Location: /mysql_1203.php');
                    exit();
                }
                // 1040 == ER_CON_COUNT_ERROR
                else if(mysqli_connect_errno() == 1040)
                {
                    header('Location: /mysql_1040.php');
                    exit();
                }
                else
                {
                    header('Location: /mysql_error.php?id=' . mysqli_connect_errno());
                    exit();
                }
            }

            $this->QueryDB('SET NAMES \'utf8\'');
        }

        $this->_table = $table;
    }

    public function QuoteArray($array)
    {
        $values = array();

        foreach($array as $value)
            $values[] = $this->QuoteSmart($value);

        return $values;
    }

    public function QuoteString($string)
    {
        // Quote if not numeric
        if(!is_numeric($string))
            $string = "'" . mysqli_real_escape_string(psyDBObject::$db_connection, $string) . "'";

        return $string;
    }

    public function QuoteSmart($value)
    {
        if(is_array($value))
            return $this->QuoteArray($value);
        else
            return $this->QuoteString($value);
    }

    /**
     * @return mysqli_result|bool
     */
    private function QueryDB($command)
    {
        return mysqli_query(self::$db_connection, $command);
    }

    private function HandleError($query)
    {
        // e-mail me the details of the error!
        $message =
            '<p>Query: ' . $query . '</h3>' . "\n" .
            '<p>MySQL Error: ' . mysqli_error(self::$db_connection) . '</p>' . "\n" .
            '<pre>' . '' . '</pre>' . "\n"
        ;

        mail('admin@psypets.net', 'PsyPets particularly nasty database error', $message, 'From: sender@psypets.net');
        die('<p>A particularly nasty database error has occurred.  That Guy Ben has been e-mailed with the details of this error.</p><p>Use the refresh button of your browser to retry doing whatever it was you were trying to do.  If the problem persists, please contact That Guy Ben with details about what you were trying to do.  It\'ll help him fix whatever bug may be at work here.</p><p>Sorry about the inconvenience!</p>');
    }

    protected function FetchNone($command)
    {
        $result = $this->QueryDB($command);

        if(!$result)
            $this->HandleError($command);
    }

    /**
     * @return array|bool|null
     */
    protected function FetchSingle($command)
    {
        $result = $this->QueryDB($command);

        if(!$result)
            $this->HandleError($command);

        if(mysqli_num_rows($result) == 0)
            return false;

        $data = mysqli_fetch_assoc($result);

        mysqli_free_result($result);

        return $data;
    }

    /**
     * @return array
     */
    protected function FetchMultiple($command)
    {
        $result = $this->QueryDB($command);

        if(!$result)
            $this->HandleError($command);

        if(mysqli_num_rows($result) == 0)
            return array();

        $data = array();

        while($row = mysqli_fetch_assoc($result))
            $data[] = $row;

        mysqli_free_result($result);

        return $data;
    }

    /**
     * fetches all available rows into an array indexed by the row's $by field value
     * @return array
     */
    protected function FetchMultipleBy($command, $by)
    {
        $result = $this->QueryDB($command);

        if(!$result)
            $this->HandleError($command);

        if(mysqli_num_rows($result) == 0)
            return array();

        $data = array();

        while($row = mysqli_fetch_assoc($result))
            $data[$row[$by]] = $row;

        mysqli_free_result($result);

        return $data;
    }
}
