<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\Docket;

class DocketRepository implements IRepository
{
    public function getModel()
    {
        return new Docket();
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
        if ($request->has('docket_id')) {
            $docket = $this->getModel()->find($request->docket_id);
        } else {
            $docket = $this->getModel();
        }

        (!$request->has('title'))?:                                    $docket->title   = $request->title;
        (!$request->has('subTitle'))?:                                 $docket->subTitle  = $request->subTitle;
        (!$request->has('user_id'))?:                                  $docket->user_id  = $request->user_id;
        (!$request->has('company_id'))?:                               $docket->company_id  = $request->company_id;
        (!$request->has('invoiceable'))?:                              $docket->invoiceable   = $request->invoiceable;
        (!$request->has('docketApprovalType'))?:                       $docket->docketApprovalType  = $request->docketApprovalType;
        (!$request->has('theme_document_id'))?:                        $docket->theme_document_id  = $request->theme_document_id;
        (!$request->has('timer_attachement'))?:                        $docket->timer_attachement  = $request->timer_attachement;
        (!$request->has('xero_timesheet'))?:                           $docket->xero_timesheet   = $request->xero_timesheet;
        (!$request->has('is_archive'))?:                               $docket->is_archive  = $request->is_archive;
        (!$request->has('prefix'))?:                                   $docket->prefix  = $request->prefix;
        (!$request->has('hide_prefix'))?:                              $docket->hide_prefix  = $request->hide_prefix;
        (!$request->has('is_docket_number'))?:                         $docket->is_docket_number  = $request->is_docket_number;

        $docket->save();
        return $docket;
    }

    public function deleteDataById($request = null)
    {
        $docket = $this->getModel()->find($request->id);
        $docket->delete();
        return $docket;
    }
}
