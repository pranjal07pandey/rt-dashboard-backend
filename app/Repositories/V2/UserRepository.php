<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\User;

class UserRepository implements IRepository
{
    public function getModel()
    {
        return new User();
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
        if ($request->has('user_id')) {
            $user = $this->getModel()->find($request->user_id);
        } else {
            $user = $this->getModel();
        }

        (!$request->has('user_type'))?:                             $user->user_type   = $request->user_type;
        (!$request->has('first_name'))?:                            $user->first_name  = $request->first_name;
        (!$request->has('last_name'))?:                             $user->last_name  = $request->last_name;
        (!$request->has('email'))?:                                 $user->email  = $request->email;
        (!$request->has('image'))?:                                 $user->image   = $request->image;
        (!$request->has('password'))?:                              $user->password  = $request->password;
        (!$request->has('device_type'))?:                           $user->device_type  = $request->device_type;
        (!$request->has('deviceToken'))?:                           $user->deviceToken  = $request->deviceToken;
        (!$request->has('hashToken'))?:                             $user->hashToken   = $request->hashToken;
        (!$request->has('remember_token'))?:                        $user->remember_token  = $request->remember_token;
        (!$request->has('email_verification'))?:                    $user->email_verification  = $request->email_verification;
        (!$request->has('isActive'))?:                              $user->isActive  = $request->isActive;
        (!$request->has('receive_docket_copy'))?:                   $user->receive_docket_copy   = $request->receive_docket_copy;

        $user->save();
        return $user;
    }

    public function deleteDataById($request = null)
    {
        $user = $this->getModel()->find($request->id);
        $user->delete();
        return $user;
    }
}
