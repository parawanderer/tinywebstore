<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AppSeeder extends Seeder
{
    public function run()
    {
        $exists = $this->db->tableExists('alert');
        if ($exists) {
            echo "table already exists. Will not run seeder.";
            return;
        }

        $this->db->transStart();

        $seed = file_get_contents(ROOTPATH . "seed.sql");
        $this->db->query($seed);

        $this->db->transComplete();        

        echo "Done seeding db with test data. Success: " . ($this->db->transStatus() ? "true" : "false");
    }
}