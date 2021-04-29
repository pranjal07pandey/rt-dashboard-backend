<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\SentDocketProject;

class SentDocketProjectRepository implements IRepository
{
    public function getModel()
    {
        return new SentDocketProject();
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
        if ($request->has('sent_docket_project_id')) {
            $sent_docket_project = $this->getModel()->find($request->sent_docket_project_id);
        } else {
            $sent_docket_project = $this->getModel();
        }

        (!$request->has('project_id'))?:                 $sent_docket_project->project_id   = $request->project_id;
        (!$request->has('docket_id'))?:                  $sent_docket_project->docket_id  = $request->docket_id;

        $sent_docket_project->save();
        return $sent_docket_project;
    }

    public function deleteDataById($request = null)
    {
        $sent_docket_project = $this->getModel()->find($request->id);
        $sent_docket_project->delete();
        return $sent_docket_project;
    }
}
