<?php

namespace App\Controllers;

class Seed extends AppBaseController
{
    public function seed()
    {   
        $seeder = \Config\Database::seeder();
        $seeder->call('AppSeeder');
    }
}
