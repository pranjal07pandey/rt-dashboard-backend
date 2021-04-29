<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\CompanyXero;

class CompanyXeroRepository implements IRepository
{
    public function getModel()
    {
        return new CompanyXero();
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
        if ($request->has('company_xero_id')) {
            $company_xero = $this->getModel()->find($request->company_xero__id);
        } else {
            $company_xero = $this->getModel();
        }

        (!$request->has('company_id'))?:                            $company_xero->company_id   = $request->company_id;
        (!$request->has('xero_user_id'))?:                          $company_xero->xero_user_id  = $request->xero_user_id;
        (!$request->has('xero_email'))?:                            $company_xero->xero_email  = $request->xero_email;
        (!$request->has('xero_user_first_name'))?:                  $company_xero->xero_user_first_name  = $request->xero_user_first_name;
        (!$request->has('xero_user_last_name'))?:                   $company_xero->xero_user_last_name  = $request->xero_user_last_name;
        (!$request->has('xero_organization_id'))?:                  $company_xero->xero_organization_id  = $request->xero_organization_id;
        (!$request->has('xero_organization_name'))?:                $company_xero->xero_organization_name  = $request->xero_organization_name;
        (!$request->has('xero_organination_address'))?:             $company_xero->xero_organination_address  = $request->xero_organination_address;
        (!$request->has('xero_organization_contact'))?:             $company_xero->xero_organization_contact  = $request->xero_organization_contact;
        (!$request->has('organization_line_of_business'))?:         $company_xero->organization_line_of_business  = $request->organization_line_of_business;
        (!$request->has('payroll_status'))?:                        $company_xero->payroll_status  = $request->payroll_status;

        $company_xero->save();
        return $company_xero;
    }

    public function deleteDataById($request = null)
    {
        $company_xero = $this->getModel()->find($request->id);
        $company_xero->delete();
        return $company_xero;
    }
}
