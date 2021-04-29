<?php


namespace App\Repositories\V2;

use App\AppleSubscription;
use App\AppInterface\IRepository;

class AppleSubscriptionRepository implements IRepository
{
    public function getModel()
    {
        return new AppleSubscription();
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
        if ($request->has('apple_subscription_id')) {
            $apple_subscription = $this->getModel()->find($request->apple_subscription_id);
        } else {
            $apple_subscription = $this->getModel();
        }

        (!$request->has('product_id'))?:                                   $apple_subscription->product_id   = $request->product_id;
        (!$request->has('company_id'))?:                                   $apple_subscription->company_id  = $request->company_id;
        (!$request->has('transaction_id'))?:                               $apple_subscription->transaction_id  = $request->transaction_id;
        (!$request->has('purchase_date'))?:                                $apple_subscription->purchase_date  = $request->purchase_date;
        (!$request->has('expiry_date'))?:                                  $apple_subscription->expiry_date  = $request->expiry_date;

        $apple_subscription->save();
        return $apple_subscription;
    }

    public function deleteDataById($request = null)
    {
        $apple_subscription = $this->getModel()->find($request->id);
        $apple_subscription->delete();
        return $apple_subscription;
    }
}
