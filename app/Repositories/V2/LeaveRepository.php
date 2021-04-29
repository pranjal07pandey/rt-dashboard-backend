<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\EmployeeLeave;

class LeaveRepository implements IRepository
{
    public function getModel()
    {
        return new EmployeeLeave();
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
        if ($request->has('employee_leave_id')) {
            $employee_leave = $this->getModel()->find($request->employee_leave_id);
        } else {
            $employee_leave = $this->getModel();
        }

        (!$request->has('user_id'))?:               $employee_leave->user_id   = $request->user_id;
        (!$request->has('from_date'))?:             $employee_leave->from_date  = $request->from_date;
        (!$request->has('to_date'))?:               $employee_leave->to_date  = $request->to_date;
        (!$request->has('description'))?:           $employee_leave->description  = $request->description;
        (!$request->has('machine_id'))?:            $employee_leave->machine_id  = $request->machine_id;

        $employee_leave->save();
        return $employee_leave;
    }

    public function deleteDataById($request = null)
    {
        $employee_leave = $this->getModel()->find($request->id);
        $employee_leave->delete();
        return $employee_leave;
    }
}
