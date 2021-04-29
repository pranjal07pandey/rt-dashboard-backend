<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\TemplateAssignFolder;

class TemplateAssignFolderRepository implements IRepository
{
    public function getModel()
    {
        return new TemplateAssignFolder();
    }

    public function getDataById($request = null)
    {
        return $this->getModel()->find($request->id);
    }

    public function getDataWhere($array = [])
    {
        return $this->getModel()->where($array);
    }

    public function insertAndUpdate($request = null)
    {
        if ($request->has('template_assign_folder_id')) {
            $template_assign_folder = $this->getModel()->find($request->template_assign_folder__id);
        } else {
            $template_assign_folder = $this->getModel();
        }

        (!$request->has('folder_id'))?:                     $template_assign_folder->folder_id   = $request->folder_id;
        (!$request->has('template_id'))?:                   $template_assign_folder->template_id  = $request->template_id;
        (!$request->has('type'))?:                          $template_assign_folder->type  = $request->type;

        $template_assign_folder->save();
        return $template_assign_folder;
    }

    public function deleteDataById($request = null)
    {
        $template_assign_folder = $this->getModel()->find($request->id);
        $template_assign_folder->delete();
        return $template_assign_folder;
    }
}
