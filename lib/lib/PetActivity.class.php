<?php
// "Activities" are actions that take only one hour; no "quest" record is created

abstract class PetActivity
{
    protected $pets;

    public function __construct(&$pets)
    {
        $this->pets = &$pets;
    }

    abstract public function Work();
}