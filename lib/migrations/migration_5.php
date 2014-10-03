<?php
class Migration_5
{
    public function Up()
    {
        // quest progress
        fetch_none("
            CREATE TABLE IF NOT EXISTS `psypets_pet_quest_progress` (
              `idnum` int(10) unsigned NOT NULL,
              `quest` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
              `data` text COLLATE utf8_unicode_ci NOT NULL
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ");

        fetch_none("
            ALTER TABLE `psypets_pet_quest_progress`
            ADD PRIMARY KEY (`idnum`);
        ");

        // quest pets
        fetch_none("
            CREATE TABLE IF NOT EXISTS `psypets_pet_quest_pets` (
              `petid` int(10) unsigned NOT NULL,
              `questid` int(10) unsigned NOT NULL
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ");

        fetch_none("
            ALTER TABLE `psypets_pet_quest_pets`
            ADD KEY `petid` (`petid`,`questid`);
        ");

        // quest logs
        fetch_none("
            CREATE TABLE IF NOT EXISTS `psypets_pet_quest_logs` (
              `idnum` int(10) unsigned NOT NULL,
              `timestamp` int(10) unsigned NOT NULL,
              `questid` int(10) unsigned NOT NULL,
              `text` int(11) NOT NULL
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ");

        fetch_none("
            ALTER TABLE `psypets_pet_quest_logs`
            ADD PRIMARY KEY (`idnum`), ADD KEY `questid` (`questid`);
        ");

        // quest journals
        fetch_none("
            CREATE TABLE IF NOT EXISTS `psypets_pet_quest_journals` (
              `idnum` int(10) unsigned NOT NULL,
              `timestamp` int(10) unsigned NOT NULL,
              `questid` int(10) unsigned NOT NULL,
              `text` int(11) NOT NULL
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ");

        fetch_none("
            ALTER TABLE `psypets_pet_quest_journals`
            ADD PRIMARY KEY (`idnum`), ADD KEY `questid` (`questid`);
        ");
    }

    public function Down()
    {
        fetch_none("DROP TABLE psypets_pet_quest_journals");
        fetch_none("DROP TABLE psypets_pet_quest_logs");
        fetch_none("DROP TABLE psypets_pet_quest_pets");
        fetch_none("DROP TABLE psypets_pet_quest_progress");
    }
}
