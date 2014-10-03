<?php
class PetQuestCraft extends PetQuest
{
    protected $craftTable;
    protected $craftIdnum;
    protected $craftRecord;

    /*
        questProgressData = {
            table: 'psypets_smiths',
            idnum: 5,
            materialsCollected: [ 'Staff' ],
            workPerformed: 1
        }
    */

    public function __construct($progress, &$userpets)
    {
        parent::__construct($progress, $userpets);

        $this->craftTable = $this->questProgressData['table'];
        $this->craftIdnum = $this->questProgressData['idnum'];
        $this->craftRecord = fetch_single('SELECT * FROM `' . $this->craftTable . '` WHERE idnum=' . (int)$this->craftIdnum . ' LIMIT 1');
    }

    public function Init($args)
    {
        global $now;

        switch($args['type'])
        {
            case 'smith': $table = 'psypets_smiths'; break;
            case 'tailor': $table = 'psypets_tailors'; break;
            default: throw new Exception('unknown craft quest type: ' . $args['type']);
        }

        $difficulty = (int)$args['difficulty'];
        $minDifficulty = $difficulty - 5;
        $maxDifficulty = $difficulty + 2;

        $month = date('n', $now);

        $possibilities = fetch_multiple('
            SELECT idnum
            FROM ' . $table . '
            WHERE
                difficulty>' . $minDifficulty . ' AND difficulty<' . $maxDifficulty . ' AND
                min_month<=' . $month . ' AND max_month >=' . $month . '
        ');

        $idnum = $possibilities[array_rand($possibilities)]['idnum'];

        return array(
            'table' => $table,
            'idnum' => $idnum,
            'materialsCollected' => array(),
            'workPerformed' => 0,
        );
    }

    public function Work()
    {

    }
}