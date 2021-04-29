<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\Folder;

class FolderRepository implements IRepository
{
    public function getModel()
    {
        return new Folder();
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
        if ($request->has('folder_id')) {
            $folder = $this->getModel()->find($request->folder__id);
        } else {
            $folder = $this->getModel();
        }

        (!$request->has('name'))?:                              $folder->name   = $request->name;
        (!$request->has('slug'))?:                              $folder->slug  = $request->slug;
        (!$request->has('user_id'))?:                           $folder->user_id  = $request->user_id;
        (!$request->has('status'))?:                            $folder->status  = $request->status;
        (!$request->has('root_id'))?:                           $folder->root_id   = $request->root_id;
        (!$request->has('company_id'))?:                        $folder->company_id  = $request->company_id;
        (!$request->has('type'))?:                              $folder->type  = $request->type;

        $folder->save();
        return $folder;
    }

    public function deleteDataById($request = null)
    {
        $folder = $this->getModel()->find($request->id);
        $folder->delete();
        return $folder;
    }
}
