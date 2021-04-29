<div style="height: 568px;overflow-x: auto;">

    <div class="row">
        <div class="col-md-8">
            <a style="background: #00A9F4;color: #ffffff;font-size: 12px;padding: 4px 8px 4px 8px;font-weight: 500;"  class="btn btn-info add_field_button">Add New Field</a>

            <div class="input_fields_wrap">
                <div></div>
                <div class="multipleInvoiceField" style="margin-bottom: 20px;">
                    <div class="row">
                        <div class="col-md-8">
                            <label style="color:#000000;font-size: 14px; ">Invoice Description  &nbsp;&nbsp; </label>  <a href="#" style="color: #D45750; font-size: 12px;margin: 0px" class="remove_field "> Remove</a><br>
                            <input type="text" name="describ[]" style="height: 50px;width: 100%;">
                        </div>
                        <div class="col-md-4">
                            <label style="color:#000000;font-size: 14px;">Amount</label><br>
                            <input type="number" name="amount[]" class="amountdata" style="height: 50px;width: 100%;">
                        </div>
                    </div>
                </div>
            </div>

            <p></p>
            @if ($invoiceableDocket)
                <?php
                $sum = 0.0
                ?>
                @foreach($invoiceableDocket as $row)
                    <div>
                        <div class="col-md-2" style="background: #F7F7F7; border-radius: 5px;margin: 0px 7px 0 0; text-align: center;">
                            <p style="margin: 6px 0px -10px 0px;"># {{$row['companyDocketId']}}</p>
                            <h3 style="    color: #D78C10;font-size: 12px;font-weight: 500;">$ {{$row['invoiceAmount']}}</h3>
                            <input type="hidden" class="invoiceAmount" name="invoiceAmount[]" value="{{$row['invoiceAmount'] }}">
                        </div>
                    </div>
                    <?php
                    $sum += $row['invoiceAmount'];
                    ?>
                @endforeach
            @endif


            @if ($invoice)
                <div style="margin-top: 95px;">
                    @foreach($invoice as $items)
                        @if ($items->invoice_field_category_id == 9)
                            <div class="signatureValue" id="signatureWrapper{{ $items->id  }}"  style="margin-bottom: 20px;">
                                <strong>{{$items->label}}  <a class=" btn signatureWindowBtn pull-right" field_id ="{{$items->id}}" style="background: #00A9F4;color: #ffffff;font-size: 12px;padding: 4px 8px 4px 8px;font-weight: 500;margin-top: 0px;">Add Signature</a></strong>
                                <div class="clearfix"></div>
                                <div id="signatureList{{ $items->id }}" class="signatureList" field_id ="{{$items->id}}" ></div>
                                {{--<div class="input-file-container" field_id ="{{$items->id}}" >--}}
                                    {{--<input type="file" class="sig-{{$items->id}}  form-control"  multiple >--}}
                                {{--</div>--}}
                                <p class="file-return"></p>
                                <hr/>
                            </div>
                        @endif
                    @endforeach

                    @foreach($invoice as $items)
                        @if ($items->invoice_field_category_id == 5)
                            <div class="col-md-12  imageValue"  style="    margin-bottom: 20px;">
                                <strong>{{$items->label}}</strong><br>
                                <div class="input-file-container" field_id ="{{$items->id}}">
                                    <input class="img-{{$items->id}}  form-control"  multiple type="file">
                                </div>
                                <p class="file-return"></p>
                            </div>
                        @endif

                        @if ($items->invoice_field_category_id == 12)
                            <div class="col-md-12"  style="    margin-bottom: 20px;">
                                <strong>{{$items->label}}</strong>
                                <hr>
                            </div>

                        @endif

                    @endforeach
                </div>
            @endif

            <div class="clearfix"></div>
        </div>

        <div class="col-md-4" style="margin-top: 70px;">
        <div  style="background: #F4F4F4; height: 100px; padding-top: 9px;">
            <div class="col-md-6">
                <span>Subtotal</span>
            </div>
            <div class="col-md-6" style="text-align: right;">
                <span class="subTotalValue"> ${{$sum}}</span>
            </div>
            <div class="col-md-6">
                <span>Tax</span>
            </div>
            <div class="col-md-6" style="text-align: right;">
                <span class="taxValue" value="{{$invoiceDetail->gst_value}}">{{$invoiceDetail->gst_value}}%</span>
            </div>
            <div class="col-md-12">
                <hr style="margin: 8px 0 8px 0px;">

            </div>
            <div class="col-md-6">
                <strong>Total</strong>
            </div>
            <div class="col-md-6" style="text-align: right;">
                <span class="TotalValue">$ {{ (($sum * ($invoiceDetail->gst_value/100)) + $sum)}}</span>
            </div>
        </div>


    </div>
    </div>
</div>




