<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\Company;

class CompanyRepository implements IRepository
{
    public function getModel()
    {
        return new Company();
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
        if ($request->has('company_id')) {
            $company = $this->getModel()->find($request->company_id);
        } else {
            $company = $this->getModel();
        }

        (!$request->has('user_id'))?:                                     $company->user_id   = $request->user_id;
        (!$request->has('abn'))?:                                         $company->abn  = $request->abn;
        (!$request->has('name'))?:                                        $company->name  = $request->name;
        (!$request->has('contactNumber'))?:                               $company->contactNumber  = $request->contactNumber;
        (!$request->has('logo'))?:                                        $company->logo   = $request->logo;
        (!$request->has('address'))?:                                     $company->address  = $request->address;
        (!$request->has('trial_period'))?:                                $company->trial_period  = $request->trial_period;
        (!$request->has('renew_date'))?:                                  $company->renew_date  = $request->renew_date;
        (!$request->has('expiry_date'))?:                                 $company->expiry_date   = $request->expiry_date;
        (!$request->has('max_user'))?:                                    $company->max_user  = $request->max_user;
        (!$request->has('stripe_user'))?:                                 $company->stripe_user  = $request->stripe_user;
        (!$request->has('subscription_plan_id'))?:                        $company->subscription_plan_id  = $request->subscription_plan_id;
        (!$request->has('time_zone'))?:                                   $company->time_zone   = $request->time_zone;
        (!$request->has('can_invoice'))?:                                 $company->can_invoice  = $request->can_invoice;
        (!$request->has('can_docket'))?:                                  $company->can_docket  = $request->can_docket;
        (!$request->has('can_timer'))?:                                   $company->can_timer  = $request->can_timer;
        (!$request->has('docket_client'))?:                               $company->docket_client  = $request->docket_client;
        (!$request->has('docket_status'))?:                               $company->docket_status  = $request->docket_status;
        (!$request->has('appear_on_recipient'))?:                         $company->appear_on_recipient  = $request->appear_on_recipient;
        (!$request->has('can_self_docket'))?:                             $company->can_self_docket  = $request->can_self_docket;
        (!$request->has('number_system'))?:                               $company->number_system  = $request->number_system;

        $company->save();
        return $company;
    }

    public function deleteDataById($request = null)
    {
        $company = $this->getModel()->find($request->id);
        $company->delete();
        return $company;
    }
}
