<?php

namespace App\Console\Commands;

use App\Models\Patient;
use App\Models\Teacher;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;

class CSVToTeacherInfo extends Command
{
    protected $signature = 'csvtoteach {filepath}';
    protected $description = 'Load CSV source into teachers table';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $path = $this->argument('filepath');

        if (!file_exists($path)) {
            echo "File not found.";
            return 1;
        }

        $file = fopen($path, 'r');

        while (($data = fgetcsv($file)) !== false) {
            $source = [
                'firstname' => $data[0],
                'lastname' => $data[1],
                'sex' => $data[7],
                'type' => "Teacher",
                'contact_no' => $data[4],
            ];

            // $teacherExists = Teacher::where('teacher_no', $source['teacher_no'])->first();

            // if (!$teacherExists) {
                try {
                    $patient = Patient::create([
                        'lastname' => $source['lastname'],
                        'firstname' => $source['firstname'], // Corrected index from 'lastname'
                        'sex' => $source['sex'],
                        'type' => $source['type'],
                        'contact_no' => $source['contact_no'],
                    ]);

                    $teacher = Teacher::create([
                        'patient_id' => $patient->id,

                    ]);

                    echo "Created Patient with ID: $patient->id and Teacher with ID: $teacher->id\n";
                } catch (QueryException $ex) {
                    echo $ex->getMessage() . "\n";
                }
            }
        // }

        fclose($file);

        return 0;
    }
}
