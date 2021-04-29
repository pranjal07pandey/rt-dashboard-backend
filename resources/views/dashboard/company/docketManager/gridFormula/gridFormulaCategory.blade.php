@if($categoryType == 1)
    <li class="cellType">
{{--        <select class="cellValue ">--}}
{{--            @foreach($filterCell as $filterCells)--}}
{{--             <option value="cell{{$filterCells['id']}}">{{$filterCells['label']}}</option>--}}
{{--            @endforeach--}}

{{--        </select>--}}

        <label  style="border: 1px solid #dddddd;padding: 4px;color: black;background: white;font-size: 13px;margin-top: -5px;"   >{{$filterCell->label}}</label>
        <input type="hidden" class="cellValue" value="cell{{$filterCell['id']}}" >
        <a class="btn btn-raised btn-danger btn-xs btnremoveformula" style="font-size: 9px;color: #ffffffff;border-radius: 17px;padding: 0px 2px;margin: -13px -2px;position: absolute;box-shadow: none;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
    </li>

@elseif($categoryType == 2)
   <li class="valueType">
       <input type="number" class="cellValue " style="width: 100px" value="0">
       <a   class="btn btn-raised btn-danger btn-xs btnremoveformula" style="font-size: 9px;color: #ffffffff;border-radius: 17px;padding: 0px 2px;margin: -13px -2px;position: absolute;box-shadow: none;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>

   </li>

@elseif($categoryType == 4)
    <li class="timeDifferenceType">
        <input type="hidden" class="cellValue" value="">
        <label style="font-size: 14px;font-weight: 700; color: #29d00c;">Time Diff: (</label>
        <select class="timeCell startTime">
            @foreach($filterTimeCell as $row)
                <option value="cell{{$row['id']}}">{{$row['label']}}</option>
            @endforeach
        </select> -

        <select class="timeCell endTime">
            @foreach($filterTimeCell as $row)
                <option value="cell{{$row['id']}}">{{$row['label']}}</option>
            @endforeach
        </select>

        <label style="font-size: 14px;font-weight: 700; color: #29d00c;">)</label>

        <a   class="btn btn-raised btn-danger btn-xs btnremoveformula" style="font-size: 9px;color: #ffffffff;border-radius: 17px;padding: 0px 2px;margin: -13px -2px;position: absolute;box-shadow: none;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>

    </li>

@elseif($categoryType == 3)
    <li class="operatorType">
        <select class="cellValue " style=" background: #f34444b8;border: 1px solid red;">
            <option value="+">+</option>
            <option value="-">-</option>
            <option value="*" selected>*</option>
            <option value="/" selected>/</option>
        </select>
        <a   class="btn btn-raised btn-danger btn-xs btnremoveformula" style="font-size: 9px;color: #ffffffff;border-radius: 17px;padding: 0px 2px;margin: -13px -2px;position: absolute;box-shadow: none;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>

    </li>


@endif