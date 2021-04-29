<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\SynXeroContact;

class SynXeroContactRepository implements IRepository
{
    public function getModel()
    {
        return new SynXeroContact();
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
        if ($request->has('syn_xero_contacts_id')) {
            $syn_xero_contacts = $this->getModel()->find($request->syn_xero_contacts__id);
        } else {
            $syn_xero_contacts = $this->getModel();
        }

        (!$request->has('contact_name'))?:              $syn_xero_contacts->contact_name   = $request->contact_name;
        (!$request->has('first_name'))?:                $syn_xero_contacts->first_name  = $request->first_name;
        (!$request->has('last_name'))?:                 $syn_xero_contacts->last_name  = $request->last_name;
        (!$request->has('email'))?:                     $syn_xero_contacts->email   = $request->email;
        (!$request->has('xero_contact_id'))?:           $syn_xero_contacts->xero_contact_id  = $request->xero_contact_id;
        (!$request->has('company_id'))?:                $syn_xero_contacts->company_id  = $request->company_id;
        (!$request->has('company_xero_id'))?:           $syn_xero_contacts->company_xero_id  = $request->company_xero_id;

        $syn_xero_contacts->save();
        return $syn_xero_contacts;
    }

    public function deleteDataById($request = null)
    {
        $syn_xero_contacts = $this->getModel()->find($request->id);
        $syn_xero_contacts->delete();
        return $syn_xero_contacts;
    }
}
