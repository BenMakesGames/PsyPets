<?php
class Migration_2
{
    public function Up()
    {
        fetch_none('UPDATE monster_users SET money=money+CONVERT(savings,SIGNED INTEGER),savings=0');
        fetch_none('ALTER TABLE monster_users DROP COLUMN savings');
        fetch_none('ALTER TABLE monster_users DROP COLUMN savings_pay_storage');
    }

    public function Down()
    {
        fetch_none('ALTER TABLE monster_admins ADD COLUMN savings VARCHAR(32)');
        fetch_none('ALTER TABLE monster_admins ADD COLUMN savings_pay_storage ENUM(\'yes\',\'no\')');
    }
}
