<ul>
    @if($invoiceFields)

        @foreach($invoiceFields as $item)



            @if($item->invoice_field_category_id==9)
                <li style="     padding-top: 15px; list-style-type: none; margin-left: -25px;    margin-bottom: -15px;">
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tbody>
                        <tr>
                            <td>
                                <p class="docket" style="font-size:13px; font-weight: 500; color: #000; ">{{ $item->label }}</p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </li>
                <li style="text-align: center; padding-top: 10px; list-style-type: none; margin-left: -25px;">
                    <table class="docket-image" style="width:95%;" border="0" cellspacing="0" cellpadding="0">
                        <tbody>
                        <tr>

                            <td>
                                <p style="font-weight: 500; color: #fff;"><img style="width: 16px" src="{{asset('signaturewhite.png')}}">Add Signature</p>

                            </td>
                        </tr>
                        </tbody>
                    </table>
                </li>
            @endif
        @endforeach


        @foreach($invoiceFields as $item)
            @if($item->invoice_field_category_id==12)
                <li style="list-style-type: none;    margin-left: -25px;">
                    <table style=" width:95%;margin-top: 19px;" class="header-title" border="0" cellspacing="0" cellpadding="0">
                        <tbody>
                        <tr>
                            <td>
                                <p class="header-p" style="font-weight: 500;">{{ $item->label }}</p>
                                <hr style="    margin: 0;">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </li>
            @endif

            @if($item->invoice_field_category_id==5)
                <li style="     padding-top: 15px; list-style-type: none; margin-left: -25px;    margin-bottom: -15px;">
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tbody>
                        <tr>
                            <td>
                                <p class="docket" style="font-size:13px; font-weight: 500; color: #000; ">{{ $item->label }}</p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </li>
                <li style="text-align: center; padding-top: 10px; list-style-type: none; margin-left: -25px;">
                    <table class="docket-image" style="width:95%;" border="0" cellspacing="0" cellpadding="0">
                        <tbody>
                        <tr>
                            <td>
                                <p style="font-weight: 500; color: #fff;"><i class="fa fa-camera" aria-hidden="true">&nbsp;</i>
                                    Browses Images</p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </li>
            @endif





        @endforeach
    @endif
</ul>