<div class="modal-body">
    <div class="row">
        <div class="col-md-12">

            <div class="headerSubDocket">
                <div class="row">
                    <div style="   margin-bottom: 5px;" class="col-md-2">
                        <strong>Grid Label:</strong>
                    </div>
                    <div style="   margin-bottom: 5px;" class="col-md-10">
                        <span style="font-size: 15px;color: black;font-weight: 300;">{{$docketField->label}}</span>
                    </div>
                </div>
            </div>
            <input type="hidden" value="{{$docketGridField->id}}" id="docketFieldGridIds">


            <div  class="formElement" style="margin-bottom:0px;">
                <ul style="list-style-type: none;    margin-left: -38px;margin-top: 6px;">
{{--                    <li style="float: left;    margin-right: 8px;">--}}
{{--                        <a href="#" class="btn btn-primary themeSecondaryBg  btn-xs withripple formulaset cellFormula " valuetype ="1" fieldid="{{$docketField->id}}" gridfieldid="{{$docketGridField->id}}">--}}
{{--                            <span><i class="fa fa-plus-square"></i> Cell</span>--}}
{{--                        </a>--}}
{{--                    </li>--}}


                    @foreach($filterCell as $row)
                        <li style="float: left;    margin-right: 8px;">
                            <a href="#" class="btn btn-primary themeSecondaryBg  btn-xs withripple formulaset cellFormula " valuetype ="1" fieldid="{{$docketField->id}}"  gridfieldid="{{$row['id']}}">
                                <span><i class="fa fa-plus-square"></i> {{$row['label']}}</span>
                            </a>
                        </li>
                    @endforeach

                </ul>
                <br><br>

                <ul style="list-style-type: none;    margin-left: -38px;margin-top: 6px;">



                    <li style="float: left; margin-right: 8px;">
                        <a href="#" class="btn btn-primary themeSecondaryBg  btn-xs withripple formulaset timeDifference" valuetype ="4" fieldid="{{$docketField->id}}">
                            <span><i class="fa fa-plus-square"></i> Time Difference </span>
                        </a>
                    </li>

                    <li style="float: left; margin-right: 8px;">
                        <a href="#" class="btn btn-primary themeSecondaryBg  btn-xs withripple formulaset numberFormula "   valuetype ="2" fieldid="{{$docketField->id}}">
                            <span><i class="fa fa-plus-square"></i> Digits </span>
                        </a>
                    </li>

                    <li style="float: left;">
                        <a href="#" class="btn btn-primary themeSecondaryBg  btn-xs withripple formulaset operatorFormula disabled"  valuetype ="3" fieldid="{{$docketField->id}}" >
                            <span><i class="fa fa-plus-square"></i> Operator </span>
                        </a>
                    </li>




                </ul><br><br>
                <span class="spinnergridformulaset" style="font-size: 18px; display:none;float: right;margin: -18px 49px 0px 0px;"><i class="fa fa-spinner fa-spin"></i></span>

                <div style=" padding-bottom: 13px;margin: 14px 20px 0px 0px;" class="showingForm">
                    <table>
                        <tr>
                            <th width="50px">fx</th>
                            <th class="formulaShow">
                                <ul class="listFormula">
                                    @foreach($formulaArray as $formulaArrays)

                                         @if($formulaArrays['type'] == "cell")
                                            <li class="cellType">
                                                @foreach($filterCell as $filterCells)
                                                    @if($formulaArrays['value'] == $filterCells['id'])
                                                        <label  style="border: 1px solid #dddddd;padding: 4px;color: black;background: white;font-size: 13px;margin-top: -5px;" class="numberCellChange"  >{{$filterCells['label']}}</label>
                                                        <input type="hidden" class="cellValue" value="cell{{$filterCells['id']}}" >
                                                        <a class="btn btn-raised btn-danger btn-xs btnremoveformula" style="font-size: 9px;color: #ffffffff;border-radius: 17px;padding: 0px 2px;margin: -13px -2px;position: absolute;box-shadow: none;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                                                    @endif
                                                @endforeach
{{--                                                <select class="cellValue">--}}
{{--                                                    @foreach($filterCell as $filterCells)--}}
{{--                                                        <option @if($filterCells['id'] ==$formulaArrays['value']) selected @endif  value="cell{{$filterCells['id']}}">{{$filterCells['label']}}</option>--}}
{{--                                                    @endforeach--}}
{{--                                                </select>--}}
{{--                                                <a class="btn btn-raised btn-danger btn-xs btnremoveformula" style="font-size: 9px;color: #ffffffff;border-radius: 17px;padding: 0px 2px;margin: -13px -2px;position: absolute;box-shadow: none;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>--}}
                                             </li>
                                         @endif




                                         @if($formulaArrays['type'] == "function")
                                                 <?php
                                                 $startTime = substr(explode(",",$formulaArrays['value'])[0],10);
                                                 $endtime  = substr_replace(substr(explode(",",$formulaArrays['value'])[1],4), "", -1);
                                                 ?>
                                                 <li class="timeDifferenceType">
                                                     <input type="hidden" class="cellValue" value="{{$formulaArrays['value']}}">
                                                     <label style="font-size: 14px;font-weight: 700; color: #29d00c;">Time Diff: (</label>
                                                     <select class="timeCell startTime" index="{{$formulaArrays['index']}}">
                                                         @foreach($filterTimeCell as $row)
                                                             <option @if($startTime == $row['id']) selected @endif value="cell{{$row['id']}}">{{$row['label']}}</option>
                                                         @endforeach
                                                     </select> -

                                                     <select class="timeCell endTime" index="{{$formulaArrays['index']}}">
                                                         @foreach($filterTimeCell as $row)
                                                             <option @if($endtime == $row['id']) selected @endif value="cell{{$row['id']}}">{{$row['label']}}</option>
                                                         @endforeach
                                                     </select>

                                                     <label style="font-size: 14px;font-weight: 700; color: #29d00c;">)</label>

                                                     <a   class="btn btn-raised btn-danger btn-xs btnremoveformula" style="font-size: 9px;color: #ffffffff;border-radius: 17px;padding: 0px 2px;margin: -13px -2px;position: absolute;box-shadow: none;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>

                                                 </li>
                                          @endif


                                         @if($formulaArrays['type'] == "number")
                                             <li class="valueType">
                                                <input type="number" class="cellValue" value="{{$formulaArrays['value']}}" style="width: 100px" >
                                                <a class="btn btn-raised btn-danger btn-xs btnremoveformula" style="font-size: 9px;color: #ffffffff;border-radius: 17px;padding: 0px 2px;margin: -13px -2px;position: absolute;box-shadow: none;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                                             </li>
                                            @endif

                                            @if($formulaArrays['type'] == "operator")
                                             <li class="operatorType">
                                                <select class="cellValue " style=" background: #f34444b8;border: 1px solid red;">
                                                    <option @if('+' == $formulaArrays['value']) selected @endif  value="+">+</option>
                                                    <option @if('-' == $formulaArrays['value']) selected @endif  value="-">-</option>
                                                    <option @if('*' == $formulaArrays['value']) selected @endif  value="*" >*</option>
                                                    <option @if('/' == $formulaArrays['value']) selected @endif  value="/" >/</option>
                                                </select>
                                              <a class="btn btn-raised btn-danger btn-xs btnremoveformula" style="font-size: 9px;color: #ffffffff;border-radius: 17px;padding: 0px 2px;margin: -13px -2px;position: absolute;box-shadow: none;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                                              </li>
                                            @endif


                                    @endforeach

                                </ul>

                            </th>
                        </tr>
                    </table>
                </div>

            </div>

        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary" id="saveFormula">Yes</button>
    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
</div>

<style>
    .showingForm table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 97% !important;
    }

    .showingForm table tr td, .showingForm table tr th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }

    .showingForm table tr:nth-child(even) {
        background-color: #dddddd;
    }
    .formulaShow .listFormula li{
        list-style: none;
        display: block;
        float: left;
        padding: 8px 8px 8px 0px;

    }

    .formulaShow .listFormula {
        margin: 0;
        padding: 6px 0px 0px 6px;

    }

    .formulaShow .listFormula li input{
        margin-top: -3px;

    }
    .formulaShow .listFormula li .btnremoveformula{
      display: none;
    }

    /*.listFormula > li:last-of-type:hover {*/
    /*    background: #0ab3ba;*/

    /*}*/

    .listFormula  > li:last-of-type:hover .btnremoveformula{
        display: inline-block;
        cursor: pointer;

    }







</style>
