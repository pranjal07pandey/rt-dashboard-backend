<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\SentEmailAttachment;

class SentEmailAttachmentRepository implements IRepository
{
    public function getModel()
    {
        return new SentEmailAttachment();
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
        if ($request->has('sent_email_attachment_id')) {
            $sent_email_attachment = $this->getModel()->find($request->sent_email_attachment__id);
        } else {
            $sent_email_attachment = $this->getModel();
        }

        (!$request->has('url'))?:                       $sent_email_attachment->url   = $request->url;
        (!$request->has('document_name'))?:             $sent_email_attachment->document_name  = $request->document_name;
        (!$request->has('sent_email_value_id'))?:       $sent_email_attachment->sent_email_value_id  = $request->sent_email_value_id;

        $sent_email_attachment->save();
        return $sent_email_attachment;
    }

    public function deleteDataById($request = null)
    {
        $sent_email_attachment = $this->getModel()->find($request->id);
        $sent_email_attachment->delete();
        return $sent_email_attachment;
    }
}
