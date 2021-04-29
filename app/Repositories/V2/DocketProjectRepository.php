<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\DocketProject;

class DocketProjectRepository implements IRepository
{
    public function getModel()
    {
        return new DocketProject();
    }

    public function getDataById($request = null)
    {
        return $this->getModel()->find($request->id);
    }

    public function getDataWhere($array = [])
    {
        return $this->getModel()->where($array);
    }

    public function getDataWhereIn($col,$value)
    {
        return $this->getModel()->whereIn($col,$value);
    }

    public function insertAndUpdate($request = null)
    {
        if ($request->has('docket_project_id')) {
            $docket_project = $this->getModel()->find($request->docket_project_id);
        } else {
            $docket_project = $this->getModel();
        }

        (!$request->has('project_id'))?:                 $docket_project->project_id   = $request->project_id;
        (!$request->has('docket_id'))?:                  $docket_project->docket_id  = $request->docket_id;

        $docket_project->save();
        return $docket_project;
    }

    public function deleteDataById($request = null)
    {
        $docket_project = $this->getModel()->find($request->id);
        $docket_project->delete();
        return $docket_project;
    }
}
