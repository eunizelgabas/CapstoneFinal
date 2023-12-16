<?php

namespace App\Console\Commands;

use App\Models\Patient;
use App\Models\Student;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException; // Add the use statement for QueryException

class CSVToStudentInfo extends Command
{
    protected $signature = 'csvtostud {filepath}';
    protected $description = 'Load CSV source into students table';

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
                'student_no' => $data[0],
                'firstname' => $data[3],
                'lastname' => $data[2],
                'middlename' => $data[4],
                'address' => $data[10] . ' ' . $data[11] . ' ' . $data[12],
                'sex' => $data[5],
                'dob' => $data[6] == '0000-00-00' ? null : $data[6],
                'type' => "Student",
                'contact_no' => $data[13],
            ];

            $studentExists = Student::where('student_no', $source['student_no'])->first();

            if (!$studentExists) {
                try {
                    $patient = Patient::create([
                        'lastname' => $source['lastname'],
                        'firstname' => $source['firstname'], // Corrected index from 'lastname'
                        'middlename' => $source['middlename'],
                        'sex' => $source['sex'],
                        'address' => $source['address'],
                        'dob' => $source['dob'],
                        'type' => $source['type'],
                        'contact_no' => $source['contact_no'],
                    ]);

                    $student = Student::create([
                        'patient_id' => $patient->id,
                        'student_no' => $source['student_no'],
                        // Add other fields specific to the Student model
                    ]);

                    echo "Created Patient with ID: $patient->id and Student with ID: $student->id\n";
                } catch (QueryException $ex) {
                    echo $ex->getMessage() . "\n";
                }
            }
        }

        fclose($file);

        return 0;
    }
}
