@if ($gridFieldFormula != null)
    fx =
    <?php
    $formulaValue = unserialize(@$gridFieldFormula->formula);
    ?>

    @foreach ($formulaValue as $formulaValues)
        @if (is_numeric($formulaValues))
            <label class="textsie">{{$formulaValues}}</label>
        @elseif (preg_match("/TDiff/i", $formulaValues))
            <?php
            $startTime = substr(explode(",", $formulaValues)[0], 10);
            $endtime = substr_replace(substr(explode(",", $formulaValues)[1], 4), "", -1);
            $docketFieldGrid = \App\DocketFieldGrid::where('docket_field_id',$docketFieldId)->where('docket_field_category_id', 26)->get();
            ?>
            <label class="textsie">Time
                Diff: (</label>
            @foreach($docketFieldGrid as $row)
                @if($startTime == $row->id)
                    <label class="textsie">{{@$row->label}} </label>
                @endif
            @endforeach
            -
            @foreach($docketFieldGrid as $row)
                @if($endtime == $row->id)
                    <label class="textsie">{{@$row->label}} </label>
                @endif
            @endforeach

            <label class="textsie" >)</label>
        @elseif (preg_match("/cell/i", $formulaValues))
            <?php
            $docketFieldGrid = \App\DocketFieldGrid::where('docket_field_id', $docketFieldId)->where('docket_field_category_id', 3)->first();
            ?>
            <label class="textsie">{{$docketFieldGrid->label}}</label>
        @else
            @if ('+' == $formulaValues)
                <label class="textsie">+</label>
            @elseif('-' == $formulaValues)
                <label class="textsie">-</label>
            @elseif('*' == $formulaValues)
                <label class="textsie">*</label>
            @elseif('/' == $formulaValues)
                <label class="textsie">/</label>

            @endif
        @endif
    @endforeach
@endif