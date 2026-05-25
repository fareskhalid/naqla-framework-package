<?php

namespace NaqlaSehia\Database\Concerns;

use NaqlaSehia\Database\Managers\Contracts\DatabaseManager;

trait ConnectsTo
{
    public static function connect(DatabaseManager $manager)
    {
        return $manager->connect();
    }
}
