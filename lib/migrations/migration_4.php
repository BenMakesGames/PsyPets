<?php
class Migration_4
{
    public function Up()
    {
        fetch_none(
            "UPDATE `psypets`.`monster_items` SET `graphic` = 'armorsuit_antique.png' WHERE `monster_items`.`itemname` = 'Antique Armor' LIMIT 1;"
        );
    }

    public function Down()
    {
        fetch_none(
            "UPDATE `psypets`.`monster_items` SET `graphic` = 'armorsuit.png' WHERE `monster_items`.`itemname` = 'Antique Armor' LIMIT 1;"
        );
    }
}
