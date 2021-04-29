@extends('layouts.companyDashboard')
@section('css')
<link type="text/css" href="{{ asset('assets/inputtags/bootstrap-tagsinput.css') }}"/>
<style>
    .input-form {
        width: 60%;
    }
</style>
@endsection
@section('content')
    <span class="spinnerCheckgrid" style="font-size: 100px;position: fixed;z-index: 11111;left: 50%;top:300px;display: none;"><i class="fa fa-spinner fa-spin"></i></span>
    <section class="content-header rt-content-header">
        <h1>Update Docket</h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="{{ route('companyDashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{ route('dockets.allDockets') }}">Docket List</a></li>
            <li class="active">Update Docket</li>
        </ol>
        <div class="clearfix"></div>
    </section>

    @include('dashboard.company.include.flashMessages')
    <div id="app">
            <docket-edit-component v-bind:docket_templete="{{  json_encode($docketTemplate) }}" v-bind:custom_email_client="{{  json_encode($emailClients) }}" 
            v-bind:record_time_user="{{  json_encode($employeeList) }}" v-bind:docket_draft="{{  json_encode($docketDraft) }}" v-bind:from_assign="{{ $fromAssign }}"></docket-edit-component>
    </div>
    
@endsection
@section('customScript')
    <script src="{{asset('js/app.js')}}"></script>
    <script src="{{asset('assets/dashboard/plugins/timepicker/Moment.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
    <script src="{{asset('assets/inputtags/bootstrap-tagsinput.min.js')}}"></script>
    <script>
        var sketchPad = new SignaturePad(document.getElementById('sketch-pad'), {
            backgroundColor: 'rgba(255, 255, 255, 0)',
            penColor: 'rgb(0, 0, 0)'
        });
        var sketchPadSaveButton = document.getElementById('sketchPadSave');
        var sketchPadCancelButton = document.getElementById('sketchPadClear');
    
        sketchPadSaveButton.addEventListener('click', function (event) {
            var demoo = sketchPad.isEmpty();
            var classPreview = $(this).attr('classPreview');
            if (demoo){
                $('.sketchPadRequired').show();
                // $('.'+classPreview).hide();
            }else {
                var data = sketchPad.toDataURL('image/png');

                // $(this).closest('form').find('.sketchPadImage').val(data);

                var sketchPadKey = $(this).attr('sketchpad_key');
                var sketchPadId = $(this).attr('sketchPad_id');
                var sketchPadItem = $(this).attr('sketchPad_item');

                $('.sketchPadAppend').append(`
                    <div class="appendedSketchPadImage col-md-4 sketchpad_${sketchPadKey}_${sketchPadId}_${sketchPadItem} style="display:inline">
                        <div>
                            <input type="hidden" name="sketchpad[]" value="${data}">
                            <img src='${data}' class="sketchPadImg" />
                            <i class="material-icons" onClick="removeSketchpadImage(this)" style="cursor:pointer;">close</i>
                        </div>
                    </div>
                `);
                
                $('.sketchPadRequired').hide();
                // $('.'+classPreview).attr('src',data);
                // $('.'+classPreview).show();
                sketchPad.clear();
            }
        });

        sketchPadCancelButton.addEventListener('click', function (event) {
            sketchPad.clear();
        });

        var signaturePad = new SignaturePad(document.getElementById('signature-pad'), {
            backgroundColor: 'rgba(255, 255, 255, 0)',
            penColor: 'rgb(0, 0, 0)'
        });
        var signaturePadSaveButton = document.getElementById('signaturePadSave');
        var signaturePadCancelButton = document.getElementById('signaturePadClear');
        
        var signatureImageCount = Math.floor(Math.random() * 10000000) + 1000;
        signaturePadSaveButton.addEventListener('click', function (event) {
            var demoo = signaturePad.isEmpty();
            if (demoo){
                $('.signaturePadRequired').show();
            }else {
                $('.signaturePadRequired').hide();
                var signature_key = $(this).attr('signature_key');
                var data = signaturePad.toDataURL('image/png');
                var signature_name = $(this).closest('.modal').find('.signature_name').val();
                if(!signature_name){
                    $(this).closest('.modal').find('.error').html('This field is required');
                    return;
                }
                var template_id = $(this).attr('data-template-id');
                $('.signaturePadAppend').append(`
                    <div class="appendedImage signature${template_id} style="display:inline">
                        <div>
                            <input type="text" class="input-form" name="signature_name[]" placeholder="Signature name" value="${signature_name}">
                            <input type="hidden" name="signature_image[]" value="${data}">
                            <input type="hidden" name="signature_unique_count[]" value="${signatureImageCount}">
                            <img src='${data}' class="signaturePadImg" />
                            <i class="material-icons" onClick="removeSignatureImage(this,${signatureImageCount})" style="cursor:pointer;">close</i>
                        </div>
                    </div>
                `);
                signatureImageCount++;
                signaturePad.clear();
                $(this).closest('.modal').find('.signature_name').val("");
            }
        });

        signaturePadCancelButton.addEventListener('click', function (event) {
            signaturePad.clear();
        });

        $('.signaturePadColor').change(function(){
            signaturePad.penColor = $(this).val();
        });

        $('.sketchPadColor').change(function(){
            sketchPad.penColor = $(this).val();
        });

        function removeSketchpadImage(event){
            $(event).closest('.appendedSketchPadImage').remove();
        }

        function removeSignatureImage(event,uniqueSignatureCount){
            $('.unique_signature_'+uniqueSignatureCount).remove();
            $(event).closest('.appendedImage').remove();
        }

        $('.signature_name').focus(function(){
            $(this).closest('div').find('.error').html("");
        });

        function removeExplaination(event){
            $(event).closest('.explanationImage').remove();
        }
    </script>
@endsection