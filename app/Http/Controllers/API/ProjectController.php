<?php
   
namespace App\Http\Controllers\API;
   
use Validator;

use Illuminate\Http\Request;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\Project as ProjectResource;

use App\Models\Project;

/**
 * Controller of the Project
 *
 * In this class we will manage logics related to the projects
 *
 * @copyright  2006 Zend Technologies
 * @license    http://www.zend.com/license/3_0.txt   PHP License 3.0
 * @version    Release: @package_version@
 * @link       http://dev.zend.com/package/PackageName
 * @since      Class available since Release 0.0.1
 */ 
class ProjectController extends BaseController
{

    /**
     * show all projects
     * 
     * @author Majid Abbasi <majid.abbasi.56@gmail.com>
     * @return List of worklogs
    */ 
    public function index()
    {
        $projects = Project::all();
        return $this->sendResponse(ProjectResource::collection($projects), 'Projects fetched.');
    }

    /**
     * This function create a new project
     *
     * @param Request   $request  request body
     * 
     * @author Majid Abbasi <majid.abbasi.56@gmail.com>
     * @return An object from the created project
    */ 
    public function store(Request $request)
    {
        //Validate Request Inputs
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());       
        }

        //Create a worklog and return that
        $project = Project::create($input);
        return $this->sendResponse(new ProjectResource($project), 'Project created.');
    }

    /**
     * Show details of specific project
     *
     * @param int  $id  id of the project
     * 
     * @author Majid Abbasi <majid.abbasi.56@gmail.com>
     * @return An object that has project data
    */ 
    public function show($id)
    {
        //Find the worklog by id and return data
        $project = Project::find($id);
        if (is_null($project)) {
            return $this->sendError('Project does not exist.');
        }
        return $this->sendResponse(new ProjectResource($project), 'Project fetched.');
    }
    
    /**
     * Update information of a project
     *
     * @param Request   $request  request body
     * @param int   $id  id of project
     * 
     * @author Majid Abbasi <majid.abbasi.56@gmail.com>
     * @return An object that has updated project data
    */ 
    public function update(Request $request, $id)
    {
        //Validate Request Inputs
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());       
        }

        //Get project object in order to update
        $project = Project::where("id", $id)->first();

        //Return error if the project does not exist
        if(!$project){
            return $this->sendError('Project does not exist.', [], 400);
        }

        //Update data of Project object
        $project->name = $input['name'];
        $project->save();
        
        //Return the updated Project object
        return $this->sendResponse(new ProjectResource($project), 'Project updated.');
    }
   
    /**
     * Delete a Project
     *
     * @param int   $id  id of project
     * 
     * @author Majid Abbasi <majid.abbasi.56@gmail.com>
     * @return Status
    */ 
    public function destroy($id)
    {
        //Get project object in order to update
        $project = Project::where("id", $id)->first();

        //Return error if the worklog does not exist
        if(!$project){
            return $this->sendError('Project does not exist.', [], 400);
        }

        //Delete project
        $project->delete();
        return $this->sendResponse([], 'Project deleted.');
    }
}