<?php

namespace App\Imports;

use App\Models\Worklog;
use Maatwebsite\Excel\Concerns\ToModel;

/**
 * Handle bulk upload for the CSV file for the worklogs
 *
 * In this class we will manage logics related to the Worklogs
 *
 * @copyright  2006 Zend Technologies
 * @license    http://www.zend.com/license/3_0.txt   PHP License 3.0
 * @version    Release: @package_version@
 * @link       http://dev.zend.com/package/PackageName
 * @since      Class available since Release 0.0.1
 */ 
class ImportWorklog implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Worklog([
            'user_id' => $row[0],
            'project_id' => $row[1],
            'source' => 'customer',
            'record_date' => $row[2],
            'start_time' => $row[3],
            'end_time' => $row[4],
            'duration_in_minute' => getDurationTimeInMinute($row[3], $row[4]),
        ]);
    }
}
