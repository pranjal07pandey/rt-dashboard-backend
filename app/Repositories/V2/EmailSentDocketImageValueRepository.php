<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\EmailSentDocketImageValue;

class EmailSentDocketImageValueRepository implements IRepository
{
    public function getModel()
    {
        return new EmailSentDocketImageValue();
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
        if ($request->has('email_sent_docket_image_value_id')) {
            $email_sent_docket_image_value = $this->getModel()->find($request->email_sent_docket_image_value__id);
        } else {
            $email_sent_docket_image_value = $this->getModel();
        }

        (!$request->has('sent_docket_value_id'))?:                  $email_sent_docket_image_value->sent_docket_value_id   = $request->sent_docket_value_id;
        (!$request->has('value'))?:                                 $email_sent_docket_image_value->value  = $request->value;
        (!$request->has('name'))?:                                  $email_sent_docket_image_value->name  = $request->name;

        $email_sent_docket_image_value->save();
        return $email_sent_docket_image_value;
    }

    public function deleteDataById($request = null)
    {
        $email_sent_docket_image_value = $this->getModel()->find($request->id);
        $email_sent_docket_image_value->delete();
        return $email_sent_docket_image_value;
    }
}
