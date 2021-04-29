<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\FolderItem;

class FolderItemRepository implements IRepository
{
    public function getModel()
    {
        return new FolderItem();
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
        if ($request->has('folder_item_id')) {
            $folder_item = $this->getModel()->find($request->folder_item__id);
        } else {
            $folder_item = $this->getModel();
        }

        (!$request->has('folder_id'))?:                         $folder_item->folder_id   = $request->folder_id;
        (!$request->has('ref_id'))?:                            $folder_item->ref_id  = $request->ref_id;
        (!$request->has('type'))?:                              $folder_item->type  = $request->type;
        (!$request->has('user_id'))?:                           $folder_item->user_id  = $request->user_id;
        (!$request->has('status'))?:                            $folder_item->status   = $request->status;
        (!$request->has('company_id'))?:                        $folder_item->company_id  = $request->company_id;

        $folder_item->save();
        return $folder_item;
    }

    public function deleteDataById($request = null)
    {
        $folder_item = $this->getModel()->find($request->id);
        $folder_item->delete();
        return $folder_item;
    }
}
