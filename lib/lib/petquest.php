<?php
abstract class PetQuest
{
    private $questProgress;
    private $participantIds;
    private $petArrays;

    public function __construct(&$progress, &$userpets)
    {
        $this->questProgress = &$progress;
        $this->petArrays = &$userpets;

        $this->participantIds = fetch_multiple_by('SELECT petid FROM psypets_pet_quest_pets WHERE questid=' . $progress['idnum'], 'petid');
    }

    public static function CreateQuestObject(&$progress, &$userpets)
    {
        $questClass = $progress['quest'];
        return new $questClass($progress, $userpets);
    }
}