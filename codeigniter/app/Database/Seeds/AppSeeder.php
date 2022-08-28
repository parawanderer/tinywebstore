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

        $seedFile = ROOTPATH . "seed.sql";
        echo "Seeding using: $seedFile\n";

        if (!file_exists($seedFile)) {
            echo "Seed file not found. Stopping here.";
            return;
        }

        $queries = AppSeeder::convertToQueries($seedFile);
        echo "Running Queries:";
        echo "<br>";
        echo "<br>";

        $hasErrors = false;

        foreach($queries as $query) {
            echo "<code>";
            echo esc($query);
            echo "</code><br>";

            $result = $this->db->query($query);
            echo $result ? " <span style='background:green; color:white;'>(Success)</span> " : " <span style='background:red; color:white;'>(Failure)</span> ";

            if (!$result) {
                $err = $this->db->error();
                echo " Error (Code: " . $err['code'] ."): " . $err['message'];
                $hasErrors = true;
            }
            echo "<br>";
        }

        echo "<br>";
        echo "<br>";     

        if (!$hasErrors) {
            echo "Success: Done seeding db with test data.";
        } else {
            echo "Failure: Failed to seed db.";
        }
    }

    private static function convertToQueries($sqlFilePath) {
        $clean = AppSeeder::cleanSql($sqlFilePath);

        if (!$clean || empty($clean)) return null;

        $queries = [];

        $exploded = explode(";", $clean);
        foreach($exploded as $query) {
            $trimmed = trim($query);

            if (!empty($trimmed)) {
                $queries[] = $trimmed . ";";
            }
        }

        return $queries;
    }

    private static function cleanSql($sqlFilePath) {
        $handle = fopen($sqlFilePath, "r");
        if ($handle) {
            $clean = '';

            while (($line = fgets($handle)) !== false) {
                if (empty($line)) continue;

                $content = trim($line);
                if (empty($content)) continue;

                $noComments = $content;
                $commentStart = strpos($noComments, '--');

                if ($commentStart !== false) {
                    $noComments = substr($noComments, 0, $commentStart);
                }

                $clean .= $noComments;
                $clean .= " ";
            }

            fclose($handle);

            return $clean;
        } else {
            return null;
        }
    }
}