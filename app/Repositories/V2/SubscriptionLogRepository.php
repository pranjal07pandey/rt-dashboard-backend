<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\SubscriptionLog;

class SubscriptionLogRepository implements IRepository
{
    public function getModel()
    {
        return new SubscriptionLog();
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
        if ($request->has('subscription_log_id')) {
            $subscription_log = $this->getModel()->find($request->subscription_log__id);
        } else {
            $subscription_log = $this->getModel();
        }

        (!$request->has('company_id'))?:                    $subscription_log->company_id   = $request->company_id;
        (!$request->has('type'))?:                          $subscription_log->type  = $request->type;
        (!$request->has('subscription_plan_id'))?:          $subscription_log->subscription_plan_id  = $request->subscription_plan_id;

        $subscription_log->save();
        return $subscription_log;
    }

    public function deleteDataById($request = null)
    {
        $subscription_log = $this->getModel()->find($request->id);
        $subscription_log->delete();
        return $subscription_log;
    }
}
