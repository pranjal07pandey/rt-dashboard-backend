<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\Employee;

class EmployeeRepository implements IRepository
{
    public function getModel()
    {
        return new Employee();
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
        if ($request->has('employee_id')) {
            $employee = $this->getModel()->find($request->employee_id);
        } else {
            $employee = $this->getModel();
        }

        (!$request->has('company_id'))?:                        $employee->company_id   = $request->company_id;
        (!$request->has('user_id'))?:                           $employee->user_id  = $request->user_id;
        (!$request->has('is_admin'))?:                          $employee->is_admin  = $request->is_admin;
        (!$request->has('employed'))?:                          $employee->employed  = $request->employed;
        (!$request->has('docket'))?:                            $employee->docket   = $request->docket;
        (!$request->has('invoice'))?:                           $employee->invoice  = $request->invoice;
        (!$request->has('timer'))?:                             $employee->timer  = $request->timer;
        (!$request->has('docket_client'))?:                     $employee->docket_client  = $request->docket_client;
        (!$request->has('appear_on_recipient'))?:               $employee->appear_on_recipient   = $request->appear_on_recipient;
        (!$request->has('can_self_docket'))?:                   $employee->can_self_docket  = $request->can_self_docket;
        (!$request->has('sn'))?:                                $employee->sn  = $request->sn;

        $employee->save();
        return $employee;
    }

    public function deleteDataById($request = null)
    {
        $employee = $this->getModel()->find($request->id);
        $employee->delete();
        return $employee;
    }
}
