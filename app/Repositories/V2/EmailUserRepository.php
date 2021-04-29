<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\EmailUser;

class EmailUserRepository implements IRepository
{
    public function getModel()
    {
        return new EmailUser();
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
        if ($request->has('email_user_id')) {
            $email_user = $this->getModel()->find($request->email_user_id);
        } else {
            $email_user = $this->getModel();
        }

        (!$request->has('email'))?:                             $email_user->email   = $request->email;
        (!$request->has('name'))?:                              $email_user->name  = $request->name;
        (!$request->has('company_name'))?:                      $email_user->company_name  = $request->company_name;

        $email_user->save();
        return $email_user;
    }

    public function deleteDataById($request = null)
    {
        $email_user = $this->getModel()->find($request->id);
        $email_user->delete();
        return $email_user;
    }
}
