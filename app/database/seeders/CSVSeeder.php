<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Address;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\DB;

class CSVSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Open the CSV file
        $file = fopen(database_path('seeds/CompanyDB.csv'), 'rb');

        // Set a flag to determine if we are processing the first line
        $firstLine = true;

        // Set an array to hold the column names
        $columnNames = [];

        //Set company array
        $data = [];

        // Loop through each line of the CSV file
        while (($line = fgetcsv($file,null,';')) !== false) {
            if ($firstLine) {
                // If this is the first line, set the column names
                $columnNames = $line;
                // Set the flag to false
                $firstLine = false;
            } else {
                // Insert the data into the database, using the column names as the keys
                $data[] = array_combine($columnNames, $line);
            }
        }

        // Close the file
        fclose($file);

        //Divide data into tables
        $data = $this->divideDataToTables($data);


        $data['company'] = $this->renameCompanyColumns($data['company']);

        // start DB transaction
        $this->insertDataToTables($data);
    }

    /**
     * @param $data
     * @return array[]
     */
    private function divideDataToTables($data)
    {
        $company = [];
        $company_address = [];
        $users = [];
        $activity = [];
        foreach ($data as $key => $value) {
            foreach ($value as $k => $v) {
                if ($k === 'employees' ||
                    $k === 'active' ||
                    $k === 'companyFoundationDate' ||
                    $k === 'companyRegistrationNumber' ||
                    $k === 'companyName' ||
                    $k === 'companyId' ||
                    $k === 'activity')
                {
                    $company[$key][$k] = $v;
                }

                if ($k === 'country' ||
                    $k === 'zipCode' ||
                    $k === 'city' ||
                    $k === 'streetAddress' ||
                    $k === 'latitude' ||
                    $k === 'longitude')
                {
                    $company_address[$key][$k] = $v;
                }

                if ($k === 'email' || $k === 'password' || $k === 'companyOwner'){
                    if ($k === 'companyOwner') {
                        $users[$key]['name'] = $v;
                    } else {
                        $users[$key][$k] = $v;
                    }
                }

                if ($k === 'activity'){
                    $activity[$key]['name'] = $v;
                }
            }
        }

        return [
            'company' => $company,
            'address' => $company_address,
            'users' => $users,
            'activity' => $activity
        ];
    }

    /**
     * @param $data
     * @return void
     */
    private function insertDataToTables($data)
    {
        $companies = $data['company'];
        $company_address = $data['address'];
        $users = $data['users'];
        $activity = $data['activity'];

        // start database transaction
        DB::beginTransaction();

        //reduce activity by name
        $groupedActivityByName = array_reduce($activity, function (array $accumulator, array $element) {
            $accumulator[$element['name']][] = $element;
            return $accumulator;
        }, []);

        //insert activity to database
        $addedActivities = [];
        foreach ($groupedActivityByName as $key => $value) {
            $addedActivities[$key] = Activity::create([
                'name' => $key
            ])->id;
        }

        //connect activity to company
        $companies = $this->addActivityToCompany($companies, $addedActivities);

        // insert company
        foreach ($companies as $key => $value) {
            Company::create($value);
        }

        // insert company address
        foreach ($company_address as $key => $value) {
            Address::create(
                array_merge($value, ['companyId' => $key + 1])
            );
        }

        // insert users
        foreach ($users as $key => $value) {
            User::create(
                array_merge($value, ['companyId' => $key + 1])
            );
        }

        // commit database transaction
        DB::commit();
    }

    /**
     * @param $data
     * @return array
     */
    public function renameCompanyColumns($data): array
    {
        $company = [];
        foreach ($data as $key => $value) {
            foreach ($value as $k => $v) {
                if ($k === 'employees') {
                    $company[$key]['employees'] = $v;
                }

                if ($k === 'active') {
                    $company[$key]['active'] = true;
                }

                if ($k === 'companyFoundationDate') {
                    $company[$key]['foundationDate'] = $v;
                }

                if ($k === 'companyRegistrationNumber') {
                    $company[$key]['registrationNumber'] = $v;
                }

                if ($k === 'companyName') {
                    $company[$key]['name'] = $v;
                }

                if ($k === 'companyId') {
                    $company[$key]['id'] = $v;
                }

                if ($k === 'activity') {
                    $company[$key]['activity'] = $v;
                }
            }
        }
        return $company;
    }

    /**
     * @param $company
     * @param $activity
     * @return mixed
     */
    public function addActivityToCompany($company, $activity)
    {
        foreach ($company as $key => $value) {
            if (isset($activity[$value['activity']])) {
                $company[$key]['activityId'] = $activity[$value['activity']];
                unset($company[$key]['activity']);
            }
        }

        return $company;
    }
}
