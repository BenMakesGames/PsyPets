<?php
/*
    run from console using:

        php command/migrate.php

    or

        php command/migrate.php [VERSION]
            (not yet implemented)
*/

require_once realpath(dirname(__FILE__)) . '/../lib/commons/init.php';

require_once 'commons/dbconnect_light.php';

// if running migrations forward:
runUp();
// else if running migrations backwards:
//runDown();

function runUp()
{
    $files = scandir(LIB_ROOT . '/migrations');

    $db_migration_version = get_current_migration_version();

    $migrations = array();

    foreach($files as $file)
    {
        if(preg_match('/migration_([1-9][0-9]*)\.php/i', $file, $matches))
        {
            $migration_version = $matches[1];

            // we only care about this migration if it has not been performed yet
            if($migration_version > $db_migration_version)
            {
                require_once LIB_ROOT . '/migrations/' . $matches[0];

                $migrations[$migration_version] = 'Migration_' . $migration_version;
            }
        }
    }

    if(count($migrations) == 0)
        echo 'There are no migrations to run; DB is already up to date (at version ' . $db_migration_version . ').' . "\n\n";
    else
    {
        // sort migrations, so that we execute them in ascending order
        ksort($migrations);

        $count = 0;

        foreach ($migrations as $id => $class_name)
        {
            $migrationClass = new $class_name();

            $count++;

            echo 'Running migration ' . $id . ' (' . $count . ' of ' . count($migrations) . ')...' . "\n";

            try
            {
                $migrationClass->Up();
                fetch_none('UPDATE migration_version SET version=' . quote_smart($id));
                echo '  done!' . "\n";
            }
            catch(Exception $e)
            {
                echo '  Encountered an exception during migration:' . "\n";
                echo '    ' . $e->getMessage() . "\n";
                echo '  Migration was not completed; the database may be left in a weird state.' . "\n";
                if($count < count($migrations))
                {
                    $remaining = (count($migrations) - $count);
                    echo $remaining . ' remaining migration' . ($remaining == 1 ? '' : 's') . ' will not be run.' . "\n";
                }
                echo "\n";
                die();
            }
        }

        echo 'All done!' . "\n\n";
    }
}

function get_current_migration_version()
{
    fetch_none('CREATE TABLE IF NOT EXISTS migration_version (version INT UNSIGNED)');

    $result = fetch_single('SELECT * FROM migration_version');
    if($result)
    {
        return $result['version'];
    }
    else
    {
        fetch_none('INSERT INTO migration_version (version) VALUES (0)');
        return 0;
    }
}
