@if($invoiceField->count())
    {{--@foreach($invoiceField as $row)--}}
        {{--@if($row->invoice_field_category_id==9)--}}
            {{--<div class="col-md-12 signatureTextDiv docketField ">--}}
                {{--<span  style="display: inline-block;">--}}
                    {{--<a style="font-weight: bold;" href="#" >{{ $row->label }}</a>--}}
                {{--</span>--}}
                {{--<div class="form-group" style="margin: 6px 0 0 0;">--}}
                    {{--<input type="file" id="signature" name="profile" multiple>--}}
                    {{--<input type="text" readonly="" class="form-control" placeholder="Signature">--}}
                    {{--<div class="sigPad" id="linear" style="width:600px;">--}}
                        {{--<ul class="sigNav">--}}
                            {{--<li class="drawIt"><a href="#draw-it" >Draw It</a></li>--}}
                            {{--<li class="clearButton"><a href="#clear">Clear</a></li>--}}
                        {{--</ul>--}}
                        {{--<div class="sig sigWrapper" style="height:auto;">--}}
                            {{--<div class="typed"></div>--}}
                            {{--<canvas class="pad" width="500" height="250"></canvas>--}}
                            {{--<input type="hidden" name="output" class="output">--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div><br>--}}
            {{--</div>--}}
        {{--@endif--}}
    {{--@endforeach--}}
    @foreach($invoiceField as $row)
        @if($row->invoice_field_category_id==12)
            <div class="col-md-12 headerTextDiv docketField ">
                <span  style="display: inline-block;">
                    <a style="font-weight: bold;" href="#" >{{ $row->label }}</a>
                </span>
                <div class="form-group" style="margin: 6px 0 0 0;">
                    <input  type="text"  class="form-control"  name="header" placeholder="Header"  required autofocus>
                </div><br>
            </div>
        @endif
        @if($row->invoice_field_category_id==5)
            <div class="col-md-12 shortTextDiv docketField ">
                <span  style="display: inline-block;">
                    <a style="font-weight: bold;" href="#" >{{ $row->label }}</a>
                </span>
                <div class="form-group" style="margin: 6px 0 0 0;">
                    <input type="file" id="image" name="profile" multiple>
                    <input type="text" readonly="" class="form-control" placeholder="Image">
                </div><br>
            </div>
        @endif

    @endforeach
@endif
<div class="col-xs-12 table-responsive"  style="overflow: hidden">
    <fieldset id="buildyourform">
        {{--<legend>Build your own form!</legend>--}}
    </fieldset>
    <input style="float: right;border: 1px solid;width: 27%;" type="button" value="Add a field" class="add btn btn-info" id="add" />
    <table class="table table-striped   ">
        <tr>
            <th>Subtotal</th>
            <th>$ ...................</th>
        </tr>
        <tr  class="gstTableList " @if($invoices->gst==0) style="display:none" @endif >
            <th colspan="2" style="padding: 0px;">
                <div class="invoicethird">
                    <div style="width: 45%; float: left;">
                        <a style="position: relative;"  href="#" id="longText"   data-title="Enter Label Text">{{ $invoices->gst_label }}</a>
                    </div>
                    <div style="width: 53%; float: right;">
                        <a style="position: relative;"  href="#" id="longText"  data-title="Enter Value">{{ $invoices->gst_value }} </a>%
                    </div>
                    <div class="clearfix"></div>
                </div>
            </th>

        </tr>
        <tr>
            <th>Total</th>
            <th>$ ...................</th>
        </tr>
    </table>
</div>
<script>
    $(document).ready(function() {
        $('#linear').signaturePad({
            drawOnly:true,
            lineTop:200
        });
        $('#smoothed').signaturePad({
            drawOnly:true,
            drawBezierCurves:true,
            lineTop:200
        });
        $('#smoothed-variableStrokeWidth').signaturePad({
            drawOnly:true,
            drawBezierCurves:true,
            variableStrokeWidth:true,
            lineTop:200
        });
    });
    $(document).ready(function() {
        $("#add").click(function() {
            var lastField = $("#buildyourform div:last");
            var intId = (lastField && lastField.length && lastField.data("idx") + 1) || 1;
            var fieldWrapper = $("<div class=\"fieldwrapper\" id=\"field" + intId + "\"/>");
            fieldWrapper.data("idx", intId);
            var fName = $("<input type=\"text\" style=\"width: 85%;float: left;\" class=\"fieldname\ form-control\" placeholder='Amount' />");
//            var fType = $("<select class=\"fieldtype\"><option value=\"checkbox\">Checked</option><option value=\"textbox\">Text</option><option value=\"textarea\">Paragraph</option></select>");
            var removeButton = $("<input type=\"button\" class=\"remove\" style=\"width: 12%;margin-left: 27px;margin-top: 9px;\" value=\"-\" />");
            removeButton.click(function() {
                $(this).parent().remove();
            });
            fieldWrapper.append(fName);
            fieldWrapper.append(removeButton);
            $("#buildyourform").append(fieldWrapper);
        });
//        $("#preview").click(function() {
//            $("#yourform").remove();
//            var fieldSet = $("<fieldset id=\"yourform\"><legend>Your Form</legend></fieldset>");
//            $("#buildyourform div").each(function() {
//                var id = "input" + $(this).attr("id").replace("field","");
//                var label = $("<label for=\"" + id + "\">" + $(this).find("input.fieldname").first().val() + "</label>");
//                var input;
//                switch ($(this).find("select.fieldtype").first().val()) {
//                    case "checkbox":
//                        input = $("<input type=\"checkbox\" id=\"" + id + "\" name=\"" + id + "\" />");
//                        break;
//                    case "textbox":
//                        input = $("<input type=\"text\" id=\"" + id + "\" name=\"" + id + "\" />");
//                        break;
//                    case "textarea":
//                        input = $("<textarea id=\"" + id + "\" name=\"" + id + "\" ></textarea>");
//                        break;
//                }
//                fieldSet.append(label);
//                fieldSet.append(input);
//            });
//            $("body").append(fieldSet);
//        });
    });
</script>