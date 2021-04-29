@if($docket_field->id == $docketValue->form_field_id )
    <tr>
        <td><strong>{{ $docket_field->label }}</strong></td>
        <td>
            <div class="table-responsive">
                <table class="table table-striped" style="white-space: nowrap;">
                    <thead>
                        <tr>
                            @foreach ($docket_field->modularGrid as $modularGrid)
                                <th>{{ $modularGrid->label }}</th>
                            @endforeach
                        <tr>
                    </thead>
                    <tbody>
                        @foreach ($docketValue->grid_value as $grid_value_row)
                            <tr>
                                @foreach ($docket_field->modularGrid as $modularGrid)
                                    @foreach ($grid_value_row as $grid_value)
                                        @if($modularGrid->id == $grid_value->form_field_id)
                                            @if($grid_value->category_id == 1 || $grid_value->category_id == 2 || $grid_value->category_id == 3 || 
                                                $grid_value->category_id == 4 || $grid_value->category_id == 6 || $grid_value->category_id == 8 || 
                                                $grid_value->category_id == 26)
                                                <td>{{ @$grid_value->value }}</td>

                                            @elseif($grid_value->category_id == 5)
                                                <td>
                                                    @foreach ($grid_value->image_value as $image_value)
                                                        <img src="{{ $image_value }}" width="50"><br>
                                                    @endforeach
                                                </td>

                                            @elseif($grid_value->category_id == 9)
                                                <td>
                                                    @foreach ($grid_value->signature_value as $signature_value)
                                                    <div class="form-group">
                                                        {{ $signature_value->name }}<br>
                                                        <img src="{{ $signature_value->image }}" width="50">
                                                    </div><br>
                                                    @endforeach
                                                </td>

                                            @elseif($grid_value->category_id == 14)
                                                <td>
                                                    @foreach ($grid_value->image_value as $image_value)
                                                        <img src="{{ $image_value }}" width="50"><br>
                                                    @endforeach
                                                </td>
                                            
                                            @elseif($grid_value->category_id == 20)
                                                <td> 
                                                    from : {{ $grid_value->manual_timer_value->from }} <br>
                                                    to : {{ $grid_value->manual_timer_value->to }} <br>
                                                    breakDuration : {{ $grid_value->manual_timer_value->breakDuration }} <br>
                                                    explanation : {{ $grid_value->manual_timer_value->explanation }} <br>
                                                    totalDuration : {{ $grid_value->manual_timer_value->totalDuration }}
                                                </td>

                                            @elseif($grid_value->category_id == 29)
                                                <td>
                                                    @foreach ($grid_value->email_list_value->email_list as $key => $email_list)
                                                    <div class="form-group">
                                                        {{ $email_list->email }}<br>
                                                        Send Copy : {{( $email_list->send_copy == 1) ? "true" : "false" }}
                                                    </div><br>
                                                    @endforeach
                                                </td>

                                            @else
                                                <td></td>
                                            @endif
                                        @endif
                                    @endforeach
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>    
            </div>
        </td>
    </tr>
@endif