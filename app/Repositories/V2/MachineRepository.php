<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\Machine;

class MachineRepository implements IRepository
{
    public function getModel()
    {
        return new Machine();
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
        if ($request->has('machine_id')) {
            $machine = $this->getModel()->find($request->machine_id);
        } else {
            $machine = $this->getModel();
        }

        (!$request->has('name'))?:                  $machine->name   = $request->name;
        (!$request->has('registration'))?:          $machine->registration  = $request->registration;
        (!$request->has('image'))?:                 $machine->image  = $request->image;
        (!$request->has('company_id'))?:            $machine->company_id  = $request->company_id;

        $machine->save();
        return $machine;
    }

    public function deleteDataById($request = null)
    {
        $machine = $this->getModel()->find($request->id);
        $machine->delete();
        return $machine;
    }
}
