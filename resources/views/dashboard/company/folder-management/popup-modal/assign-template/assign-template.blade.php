@php
    $dockets        =   $company->dockets()->with('assignedDockets')->orderBy('id','desc')->get();
    $invoice        =   $company->invoices()->with('assignedInvoice')->orderBy('id','desc')->get();
    $template = array();

    foreach ($dockets as $items){
        if($items->assignedDockets->count()!=0){
            $template[]= array(
              'id'=> $items->id,
              'name'=>$items->title,
              'type'=>1,

            );
        }
    }

      foreach ($invoice as $item){
          if($item->assignedInvoice->count()!=0){
              $template[]= array(
                  'id'=> $item->id,
                  'name'=>$item->title,
                  'type'=>2,

              );
          }
      }
@endphp

<div class="modal fade rt-modal" id="assignTemplateModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
    <div id="second"  class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Assign Template</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <p class="assignTempalteErrorMessage" style="display: none;     color: white;background: red;padding: 0 0 0px 11px;font-size: 15px;">   </p>
                        <p style="    margin-left: 14px;font-size: 15px;font-weight: 600;">Folder Name: <span class="assignFolderName" style="font-weight: 100;"></span> </p>
                        <input type="hidden" id="assignTemplateId">
                        <div class="col-md-6">
                            <strong>Template Type</strong>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group" style="margin-top:0px;">
                                        <div style="position:relative">
                                            <select id="assignTemplateType" class="form-control" name="type">
                                                <option value="">Select Template Type</option>
                                                <option value="1">Docket</option>
                                                <option value="2">Invoice</option>
                                            </select>
                                            <div style="position: absolute;right: 0px;top: 10px;"><i class="fa fa-angle-down"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <strong>Template Name</strong>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group" style="margin-top:0px;">
                                        <div style="position:relative">
                                            <select id="assignTemplateName" class="form-control" name="name">
                                                <option value="">Select Template Name</option>
                                                @foreach($template as $templates)
                                                    <option value="{{$templates['id']}}"  data-chained="{{$templates['type']}}">{{$templates['name']}}</option>
                                                @endforeach
                                            </select>
                                            <div style="position: absolute;right: 0px;top: 10px;"><i class="fa fa-angle-down"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary submit">Submit</button>
            </div>
        </div>
    </div>
</div>