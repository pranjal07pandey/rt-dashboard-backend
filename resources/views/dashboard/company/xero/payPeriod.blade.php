@if($errorHandel == 1)
    <div class="form-group" style="margin-top:0px;">
        <input type="hidden" name="buttonCheck" class="buttonChecked" value="1">
        <label for="templateId" class="control-label">Pay Period</label>
        <select id="templateId" class="form-control" required name="date" disabled="">
            <option>Select Date</option>
        </select>
        <i style="color: red;">PayrollCalendar create in Xero.</i>
    </div>
@elseif($errorHandel == 4)
    <div class="form-group" style="margin-top:0px;">
        <input type="hidden" name="buttonCheck" class="buttonChecked" value="1">
        <label for="templateId" class="control-label">Pay Period</label>
        <select id="templateId" class="form-control" required name="date" disabled="">
            <option>Select Date</option>
        </select>
        <i style="color: red;">Set an Ordinary Earnings Rate or Payroll Calendar for the employee on the Employment tab under Payroll...Employees before creating a timesheet.</i>
    </div>
@elseif($errorHandel == 3)
    <div class="form-group" style="margin-top:0px;">
        <input type="hidden" name="buttonCheck" class="buttonChecked" value="1">
        <label for="templateId" class="control-label">Pay Period</label>
        <select id="templateId" class="form-control" required name="date" disabled="">
            <option>Select Date</option>
        </select>
        <i style="color: red;">Twice Monthly PayrollCalendar not available.</i>
    </div>
@else
    @if(count($timeArray)==0)
        <div class="form-group" style="margin-top:0px;">
            <input type="hidden" name="buttonCheck" class="buttonChecked" value="1">
            <label for="templateId" class="control-label">Pay Period</label>
            <select id="templateId" class="form-control" required name="date" disabled="">
                <option>Select Date</option>
            </select>
            <i style="color: red;">Xero Employe Start Date is required.</i>
        </div>
    @else
        <div class="form-group" style="margin-top:0px;">
            <input type="hidden" name="buttonCheck" class="buttonChecked" value="0">
            <input type="hidden" name="XeroEmployeId" value="{{$xeroEmployee}}">
            <label for="templateId" class="control-label">Pay Period</label>
            <select id="templateId" class="form-control" required name="date">
                @foreach($timeArray as $rows)
                    <option value="{{$rows['endDate']}}|{{$rows['startDate']}}"  >Week ending {{\Carbon\Carbon::parse($rows['endDate'])->format("d")}} {{\Carbon\Carbon::parse($rows['endDate'])->format("M")}}  {{\Carbon\Carbon::parse($rows['endDate'])->format("Y")}} </option>
                @endforeach
            </select>
        </div>
    @endif

@endif