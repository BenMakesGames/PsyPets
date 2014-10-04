<?php
abstract class PetQuest
{
    private $questProgress;

    protected $questProgressData;
    protected $participantIds;
    protected $petArrays;

    public function __construct($progress, &$userpets)
    {
        $this->questProgress = $progress;
        $this->questProgressData = json_decode($progress['data']);
        $this->petArrays = &$userpets;

        $this->participantIds = fetch_multiple_by('SELECT petid FROM psypets_pet_quest_pets WHERE questid=' . $progress['idnum'], 'petid');
    }

    abstract public function Work();
    abstract protected function Init($args);

    public static function Insert($questClass, &$userpets, $args)
    {
        $progress = $questClass::Init($args);
        return new $questClass($progress, $userpets);
    }

    protected function Update()
    {
        $this->questProgress['data'] = json_encode($this->questProgressData);

        fetch_none('
            UPDATE psypets_pet_quest_progress
            SET data=' . quote_smart($this->questProgress['data']) . '
            WHERE idnum=' . (int)$this->questProgress['idnum'] . '
            LIMIT 1
        ');
    }

    /**
     * @param string $text
     * @return int
     */
    protected function AddLog($text)
    {
        global $now;

        fetch_none('
            INSERT INTO psypets_pet_quest_logs (timestamp, questid, text) VALUES
            (' . (int)$now . ', ' . (int)$this->questProgress['idnum'] . ', ' . quote_smart($text) . ')
        ');

        return $GLOBALS['database']->InsertID();
    }

    /**
     * @param string $text
     * @return int
     */
    protected function AddJournal($text, $logId)
    {
        global $now;

        fetch_none('
            INSERT INTO psypets_pet_quest_logs (timestamp, questid, logid, text) VALUES
            (' . (int)$now . ', ' . (int)$this->questProgress['idnum'] . ', ' . (int)$logId . ', ' . quote_smart($text) . ')
        ');

        return $GLOBALS['database']->InsertID();
    }

    /**
     * @param int $progressId
     * @param array $userpets
     * @return mixed
     */
    public static function Select($progressId, &$userpets)
    {
        $progress = fetch_single('SELECT * FROM psypets_pet_quest_progress WHERE idnum=' . (int)$progressId . ' LIMIT 1');
        $questClass = $progress['quest'];
        return new $questClass($progress, $userpets);
    }
}