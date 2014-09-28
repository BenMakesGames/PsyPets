<?php
class Migration_1
{
    public function Up()
    {
        fetch_none('ALTER TABLE monster_admins DROP COLUMN alphalevel');
    }

    public function Down()
    {
        fetch_none('ALTER TABLE monster_admins ADD COLUMN alphalevel TINYINT(3)');
    }
}
