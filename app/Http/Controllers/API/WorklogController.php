<?php
   
namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Validator;

use App\Imports\ImportWorklog;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\Worklog as WorklogResource;

use App\Models\Worklog;
use App\Models\User;
use App\Models\Project;

/**
 * Controller of the Worklog
 *
 * In this class we will manage logics related to the Worklogs
 *
 * @copyright  2006 Zend Technologies
 * @license    http://www.zend.com/license/3_0.txt   PHP License 3.0
 * @version    Release: @package_version@
 * @link       http://dev.zend.com/package/PackageName
 * @since      Class available since Release 0.0.1
 */ 
class WorklogController extends BaseController
{
    /**
     * show all worklogs
     * 
     * @author Majid Abbasi <majid.abbasi.56@gmail.com>
     * @return List of worklogs
    */ 
    public function index()
    {
        $worklogs = Worklog::all();
        return $this->sendResponse(WorklogResource::collection($worklogs), 'Worklog fetched.');
    }

    /**
     * This function create a new worklog
     *
     * @param Request   $request  request body
     * 
     * @author Majid Abbasi <majid.abbasi.56@gmail.com>
     * @return An object from the created worklog
    */ 
    public function store(Request $request)
    {
        //Validate Request Inputs
        $input = $request->all();
        $validator = Validator::make($input, [
            'user_id' => 'required',
            'project_id' => 'required',
            'source' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'record_date' => 'required',
            'duration_in_minute' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());       
        }

        //Create a worklog and return that
        $worklog = Worklog::create($input);
        return $this->sendResponse(new WorklogResource($worklog), 'Worklog created.');
    }

    /**
     * Show details of specific worklog
     *
     * @param int  $id  id of the worklog
     * 
     * @author Majid Abbasi <majid.abbasi.56@gmail.com>
     * @return An object that has worklog data
    */ 
    public function show($id)
    {
        //Find the worklog by id and return data
        $worklog = Worklog::find($id);
        if (is_null($worklog)) {
            return $this->sendError('Worklog does not exist.');
        }
        return $this->sendResponse(new WorklogResource($worklog), 'Worklog fetched.');
    }
    
    /**
     * Update information of a worklog
     *
     * @param Request   $request  request body
     * @param int   $id  id of worklog
     * 
     * @author Majid Abbasi <majid.abbasi.56@gmail.com>
     * @return An object that has updated worklog data
    */ 
    public function update(Request $request, $id)
    {
        //Validate Request Inputs
        $input = $request->all();
        $validator = Validator::make($input, [
            'user_id' => 'required',
            'project_id' => 'required',
            'source' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'record_date' => 'required',
            'duration_in_minute' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());       
        }

        //Define variables from inputs for any locations that we do need.
        $startTime = $input['start_time'];
        $endTime = $input['end_time'];

        //Get Worklog object in order to update
        $worklog = Worklog::where("id", $id)->first();

        //Return error if the worklog does not exist
        if(!$worklog){
            return $this->sendError('Worklog does not exist.', [], 400);
        }

        //Update data of Worklog object
        $worklog->user_id = $input['user_id'];
        $worklog->project_id = $input['project_id'];
        $worklog->source = $input['source'];
        $worklog->start_time = $startTime;
        $worklog->end_time = $endTime;
        $worklog->record_date = $input['record_date'];
        $worklog->duration_in_minute = $input['duration_in_minute'];
        $worklog->save();
        
        //Return the updated Worklog object
        return $this->sendResponse(new WorklogResource($worklog), 'Worklog updated.');
    }
   
    /**
     * Delete a Worklog
     *
     * @param int   $id  id of worklog
     * 
     * @author Majid Abbasi <majid.abbasi.56@gmail.com>
     * @return Status
    */ 
    public function destroy($id)
    {
        //Get Worklog object in order to update
        $worklog = Worklog::where("id", $id)->first();

        //Return error if the worklog does not exist
        if(!$worklog){
            return $this->sendError('Worklog does not exist.', [], 400);
        }

        //Delete Worklog
        $worklog->delete();
        return $this->sendResponse([], 'Worklog deleted.');
    }

    /**
     * Add a login record in the worklogs table
     *
     * @param Request   $request  request body
     * 
     * @author Majid Abbasi <majid.abbasi.56@gmail.com>
     * @return An object that has updated worklog data
    */ 
    public function login(Request $request)
    {
        //Validate Request Inputs
        $input = $request->all();
        $validator = Validator::make($input, [
            'user_id' => 'required',
            'record_date' => 'required',
            'start_time' => 'required',
        ]);
        $input['source'] = 'office';
        if($validator->fails()){
            return $this->sendError($validator->errors());       
        }

        //Define variables from inputs for any locations that we do need.
        $userId = $input['user_id'];
        $startTime = $input['start_time'];
        $recordDate = $input['record_date'];

        //Validate user already has any assigned project
        $project_id = User::find($userId)->project_id;
        if(!$project_id){
            return $this->sendError('You do not have any assigned project, contact your supervisor.', [], 400);
        }else{
            $input['project_id'] = $project_id;
        }
        
        //Validate user already has any undinished work log
        if (Worklog::where('user_id', '=', $userId)->whereNull('end_time')->exists()) {
            return $this->sendError('You already have unfinished login, please logout it first.', [], 400);
        }

        //Check overlap times
        $overlap_status = Worklog::where([
            ['user_id', '=', $userId],
            ['record_date', '=', $recordDate],
            ['start_time', '<=', $startTime],
            ['end_time', '>=', $startTime],
        ])->exists();
        if($overlap_status){
            return $this->sendError('Time overlap detected for the time which you sent.', [], 400);
        }

        //Create Login Record
        $worklog = Worklog::create($input);
        return $this->sendResponse(new WorklogResource($worklog), 'Login has been recorded.');
    }

    /**
     * update worklog report for logout
     *
     * @param Request   $request  request body
     * 
     * @author Majid Abbasi <majid.abbasi.56@gmail.com>
     * @return An object that has updated worklog data
    */ 
    public function logout(Request $request)
    {
        //Validate Request Input ID
        $input = $request->all();
        $validator = Validator::make($input, [
            'user_id' => 'required',
            'end_time' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());       
        }

        //Define variables from inputs for any locations that we do need.
        $userId = $input['user_id'];
        $endTime = $input['end_time'];

        //Get the latest started work
        $worklog = Worklog::where('user_id', '=', $userId)->whereNull('end_time')->first();

        //Return error if there is not any started work
        if(!$worklog){
            return $this->sendError('There is not any started work. Start a work first', [], 400);
        }

        //Calculate the time spent on the project in minute
        $duration_in_minute = getDurationTimeInMinute($worklog->start_time, $endTime);

        //Return Error if the spent time is less than 0
        if($duration_in_minute < 0){
            return $this->sendError('End time should be greater than the start time', [], 400);
        }

        //Record the logout data
        $worklog->duration_in_minute = $duration_in_minute;
        $worklog->end_time = $endTime;
        $worklog->save();
        
        //Return the updated record
        return $this->sendResponse(new WorklogResource($worklog), 'Logout has been recorded.');
    }

    /**
     * Add the worklogs reports as bulk from the CSV file
     *
     * @param Request   $request  request body
     * 
     * @author Majid Abbasi <majid.abbasi.56@gmail.com>
     * @return Status
    */ 
    public function bulkUpload(Request $request)
    {
        Excel::import(new ImportWorklog, $request->file('file')->store('files'));
        
        return $this->sendResponse([], 'data has been imported.');
    }

    /**
     * Calculate billable hours for a project
     *
     * @param Request   $request  request body
     * 
     * @author Majid Abbasi <majid.abbasi.56@gmail.com>
     * @return An array of the reports
    */ 
    public function billableHours(Request $request)
    {
        //Validate Request Input ID
        $input = $request->all();
        $validator = Validator::make($input, [
            'project_id' => 'required'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());       
        }

        //Define variables from inputs for any locations that we do need.
        $projectId = $input['project_id'];

        //Get the project by id
        $project = Project::where('id', $projectId)->first();

        //Return error if there is not any started project
        if(!$project){
            return $this->sendError('Project is invalid', [], 400);
        }

        // Get sum of hoours which we spend for a project 
        $sum_duration_in_minute = Worklog::where('project_id', '=', $projectId)->sum('duration_in_minute');;

        //Convert mminutes to hours and minutes
        $hours = floor($sum_duration_in_minute / 60);
        $min = $sum_duration_in_minute - ($hours * 60);
        $hours_and_minutes = intval($hours) . ":" . intval($min);
        
        //Return data
        return $this->sendResponse(['total_minutes' =>$sum_duration_in_minute, 'total_hours' => $hours_and_minutes], 'Report has been calculated.');
    }

    /**
     * Get peak time of a project in the specific day
     *
     * @param Request   $request  request body
     * 
     * @author Majid Abbasi <majid.abbasi.56@gmail.com>
     * @return The peak time
    */ 
    public function getPeakTime(Request $request)
    {
        //Validate Request Input ID
        $input = $request->all();
        $validator = Validator::make($input, [
            'project_id' => 'required',
            'record_date' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());       
        }

        //Define variables from inputs for any locations that we do need.
        $projectId = $input['project_id'];
        $recordDate = $input['record_date'];

        //Get the project by id
        $project = Project::where('id', $projectId)->first();

        //Return error if there is not any started project
        if(!$project){
            return $this->sendError('Project is invalid', [], 400);
        }
        $projectName = $project->name;

        //Calculate the start-time most of the employees worked on the given project and date
        $startTimeRange = DB::table("worklogs as a")
                        ->join("worklogs as b", [])
                        
                        ->select(DB::raw("count(*) as row_count"), "a.start_time", "a.end_time" )
                        
                        ->where("a.record_date", "=", $recordDate)
                        ->where("b.record_date", "=", $recordDate)

                        ->where("a.project_id", "=", $projectId)
                        ->where("b.project_id", "=", $projectId)
                        
                        ->whereBetween('a.start_time', [DB::raw("b.start_time"), DB::raw("b.end_time")])

                        ->groupBy("a.start_time")
                        ->groupBy("a.id")

                        ->orderBy("row_count", "desc")
                        ->orderBy("a.start_time", "asc")
                        ->orderBy("a.end_time", "asc")

                        ->first();

        //Return error if the given date does not have any record
        if(!$startTimeRange){
            return $this->sendError('There is not any result', [], 400);
        }

        $fromTime = $startTimeRange->start_time;
        //We take end-time in order to use in the the range of the end-date calculation
        $startRangeEndTime = $startTimeRange->end_time;

        //Calculate the end-time that most of the eemployees working on the given project and date and also is in the range of the start date 
        $endTimeRange = DB::table("worklogs as a")
                        ->join("worklogs as b", [])
                        
                        ->select(DB::raw("count(*) as row_count"), "a.end_time" )
                        
                        ->where("a.record_date", "=", $recordDate)
                        ->where("b.record_date", "=", $recordDate)

                        ->where("a.project_id", "=", $projectId)
                        ->where("b.project_id", "=", $projectId)

                        ->where("a.end_time", ">", $fromTime)
                        ->where("a.end_time", "<=", $startRangeEndTime)
                        
                        ->groupBy("a.end_time")
                        ->groupBy("a.id")

                        ->orderBy("row_count", "desc")
                        ->orderBy("a.end_time", "asc")

                        ->first();

        $toTime = $endTimeRange->end_time;

        //Create the result variable in order to return in the response
        $result = ['from' => $fromTime, 'to' => $toTime, 'on' => $recordDate, 'project' => $projectName];

        //Return result
        $message_format = "The peak time on %s is from %s to %s on %s project.";
        return $this->sendResponse($result, sprintf($message_format, $recordDate, $fromTime, $toTime, $projectName) );
    }
}