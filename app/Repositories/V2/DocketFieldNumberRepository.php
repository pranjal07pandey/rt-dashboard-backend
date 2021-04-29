<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\DocketFieldNumber;

class DocketFieldNumberRepository implements IRepository
{
    public function getModel()
    {
        return new DocketFieldNumber();
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
        if ($request->has('docket_field_number_id')) {
            $docket_field_number = $this->getModel()->find($request->docket_field_number_id);
        } else {
            $docket_field_number = $this->getModel();
        }

        (!$request->has('title'))?:                                    $docket_field_number->title   = $request->title;
        (!$request->has('subTitle'))?:                                 $docket_field_number->subTitle  = $request->subTitle;
        (!$request->has('user_id'))?:                                  $docket_field_number->user_id  = $request->user_id;
        (!$request->has('company_id'))?:                               $docket_field_number->company_id  = $request->company_id;
        (!$request->has('invoiceable'))?:                              $docket_field_number->invoiceable   = $request->invoiceable;
        (!$request->has('theme_document_id'))?:                        $docket_field_number->theme_document_id  = $request->theme_document_id;
        (!$request->has('timer_attachement'))?:                        $docket_field_number->timer_attachement  = $request->timer_attachement;
        (!$request->has('xero_timesheet'))?:                           $docket_field_number->xero_timesheet   = $request->xero_timesheet;
        (!$request->has('is_archive'))?:                               $docket_field_number->is_archive  = $request->is_archive;
        (!$request->has('prefix'))?:                                   $docket_field_number->prefix  = $request->prefix;
        (!$request->has('hide_prefix'))?:                              $docket_field_number->hide_prefix  = $request->hide_prefix;

        $docket_field_number->save();
        return $docket_field_number;
    }

    public function deleteDataById($request = null)
    {
        $docket_field_number = $this->getModel()->find($request->id);
        $docket_field_number->delete();
        return $docket_field_number;
    }
}
