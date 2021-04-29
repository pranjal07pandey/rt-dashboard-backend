/*!
Rescalendar.js - https://cesarchas.es/rescalendar
Licensed under the MIT license - http://opensource.org/licenses/MIT

Copyright (c) 2019 César Chas
*/



;(function($) {

    $.fn.rescalendar = function( options ) {

        function alert_error( error_message ){

            return [
                '<div class="error_wrapper">',

                      '<div class="thumbnail_image vertical-center">',
                      
                        '<p>',
                            '<span class="nodata">',
                                error_message,
                            '</span>',
                        '</p>',
                      '</div>',

                    '</div>'
            ].join('');
        
        }

        function set_template( targetObj, settings ){

            var template = '',
                id = targetObj.attr('id') || '';

            // if( id == '' || settings.dataKeyValues.length == 0 ){

            //     targetObj.html( alert_error( settings.lang.init_error ) );
            //     return false;
            
            // }

            if( settings.refDate.length != 10 ){

                targetObj.html( alert_error( settings.lang.no_ref_date ) );
                return false;
                
            }


            template = settings.template_html( targetObj, settings );

            targetObj.html( template );

            return true;

        };

        function dateInRange( date, startDate, endDate ){

            if( date == startDate || date == endDate ){
                return true;
            }

            var date1        = moment( startDate, settings.format ),
                date2        = moment( endDate, settings.format ),
                date_compare = moment( date, settings.format);

            return date_compare.isBetween( date1, date2, null, '[]' );

        }

        function dataInSet( data, name, date ){

            var obj_data = {};

            for( var i=0; i < data.length; i++){

                obj_data = data[i];

                if( 
                    name == obj_data.name &&
                    dateInRange( date, obj_data.startDate, obj_data.endDate )
                ){ 
                    
                    return obj_data;

                }

            } 

            return false;

        }

        function setData( targetObj, dataKeyValues, data ){

            var html          = '',
                dataKeyValues = settings.dataKeyValues,
                images        = settings.images,
                data          = settings.data,
                arr_dates     = [],
                name          = '',
                content       = '',
                hasEventClass = '',
                customClass   = '',
                crossClass    = '',
                classInSet    = false,
                leave_id      = '',
                obj_data      = {};
                
            targetObj.find('th.day_cell').each( function(index, value){

                arr_dates.push( $(this).attr('data-cellDate') );

            });

            for( var i=0; i<dataKeyValues.length; i++){

                content = '';
                date    = '';
                name    = dataKeyValues[i];
                image   = images[i]

                html += '<tr class="dataRow">';
                html += `<td scope="row" class="firstColumn"><img src="${image}" onerror="this.src='${settings.onErrorImage}'" class="calendar_img"><br>${name}</td>`;
                var tempArray = [];
                var increment = 1;
                for( var j=0; j < arr_dates.length; j++ ){
                    title    = '';
                    date     = arr_dates[j];
                    obj_data = dataInSet( data, name, date );
                    if( typeof obj_data === 'object' ){
                        
                        if( obj_data.title ){ 
                            title = ' title="' + obj_data.title + '" '; 
                        }
                        
                        if(tempArray.indexOf(obj_data.id) !== -1){
                            var titleValue = '';
                            // var icon = '';
                            // if(new Date(obj_data.startDate).addDays(increment++).getTime() === new Date(obj_data.endDate).getTime()){
                                // var icon = '<i class="fa fa-close" data_id="'+obj_data.id+'" onclick="deleteLeave(this)"></i>';
                            //     increment = 1;
                            // }
                        }else{
                            var titleValue = obj_data.title;
                            // if(new Date(obj_data.startDate).getTime() === new Date(obj_data.endDate).getTime()){
                                // var icon = '<i class="fa fa-close" data_id="'+obj_data.id+'" onclick="deleteLeave(this)"></i>';
                            //     increment = 1;
                            // }else{
                            //     var icon = '';
                            // }
                            tempArray.push(obj_data.id);
                        }
                        var icon = '<i class="fa fa-pencil" data_id="'+obj_data.id+'" data-toggle="modal" data-target="#employeeLeaveEdit" onclick="editLeave(this)"></i><i class="fa fa-close" data_id="'+obj_data.id+'" onclick="deleteLeave(this)"></i>';

                        var trimmedStringTitle = titleValue;
                        // var maxLength = 30 // maximum number of characters to extract
                        // //Trim and re-trim only when necessary (prevent re-trim when string is shorted than maxLength, it causes last word cut) 
                        // if(titleValue.length > maxLength){
                        //     //trim the string to the maximum length
                        //     var trimmedStringTitle = titleValue.substr(0, maxLength);

                        //     //re-trim if we are in the middle of a word and 
                        //     trimmedStringTitle = trimmedStringTitle.substr(0, Math.min(trimmedStringTitle.length, trimmedStringTitle.lastIndexOf(" ")))
                        // }

                        content = '<div class="clearfix table_title" onmouseover="crossButtonHover('+obj_data.id+')" onmouseout="crossButtonHoverHide('+obj_data.id+')"><a>&nbsp;'+trimmedStringTitle+'</a>'+icon+'<span>'+titleValue+'</span></div>';
                        hasEventClass = 'hasEvent';
                        customClass = obj_data.customClass;
                        crossClass = 'employee_leave_'+obj_data.id;
                        leave_id = obj_data.id;
                    }else{
                        content       = ' ';
                        hasEventClass = '';
                        customClass   = '';
                        crossClass    = '';
                        leave_id = '';
                    }

                    html += '<td scope="row" data-date="' + date + '" data-name="' + name + '" data-attr="'+leave_id+'" class="data_cell ' + hasEventClass + ' ' + customClass + ' ' + crossClass +'">' + content + '</td>';
                }

                html += '</tr>';

            }

            targetObj.find('.rescalendar_data_rows').html( html );
        }

        function setDayCells( targetObj, refDate ){
            var format   = settings.format,
                f_inicio = moment( refDate, format ).subtract(settings.jumpSize, 'days'),
                f_fin    = moment( refDate, format ).add(settings.jumpSize, 'days'),
                today    = moment( ).startOf('day'),
                html            = '<th class="firstColumn year_header">'+new Date().getFullYear()+'</th>',
                f_aux           = '',
                f_aux_format    = '',
                dia             = '',
                dia_semana      = '',
                num_dia_semana  = 0,
                mes             = '',
                clase_today     = '',
                clase_middleDay = '',
                clase_disabled  = '',
                middleDay       = targetObj.find('input.refDate').val();

            for( var i = 0; i< (settings.calSize + 1) ; i++){

                clase_disabled = '';

                f_aux        = moment( f_inicio ).add(i, 'days');
                f_aux_format = f_aux.format( format );

                dia        = f_aux.format('DD');
                mes        = f_aux.locale( settings.locale ).format('MMM').replace('.','');
                dia_semana = f_aux.locale( settings.locale ).format('dd');
                num_dia_semana = f_aux.day();

                f_aux_format == today.format( format ) ? clase_today     = 'today'         : clase_today = '';
                f_aux_format == middleDay              ? clase_middleDay = 'middleDay' : clase_middleDay = '';

                if( 
                    settings.disabledDays.indexOf(f_aux_format) > -1 ||
                    settings.disabledWeekDays.indexOf( num_dia_semana ) > -1
                ){
                    
                    clase_disabled = 'disabledDay';
                }

                html += [
                    '<th class="day_cell ' + clase_today + ' ' + clase_middleDay + ' ' + clase_disabled + '" data-cellDate="' + f_aux_format + '">',
                        '<span class="dia_semana">' + dia_semana + '</span>',
                        '<span class="dia">' + dia + '</span>',
                        // '<span class="mes">' + mes + '</span>',
                    '</th>'
                ].join('');

            }

            targetObj.find('.rescalendar_day_cells').html( html );

            addTdClickEvent( targetObj );

            setData( targetObj )

            jqueyDateChange(targetObj);
        }

        function addTdClickEvent(targetObj){

            var day_cell = targetObj.find('td.day_cell');

            day_cell.on('click', function(e){
            
                var cellDate = e.currentTarget.attributes['data-cellDate'].value;

                targetObj.find('input.refDate').val( cellDate );

                setDayCells( targetObj, moment(cellDate, settings.format) );

            });

        }

        function change_day( targetObj, action, num_days ){
            
            var refDate = targetObj.find('input.refDate').val(),
                f_ref = '';

            if( action == 'subtract'){
                f_ref = moment( refDate, settings.format ).subtract(num_days, 'days');    
            }else{
                f_ref = moment( refDate, settings.format ).add(num_days, 'days');
            }
            targetObj.find('input.refDate').val( f_ref.format( settings.format ) );

            setDayCells( targetObj, f_ref );

        }

        var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        Date.prototype.getMonthText = function() {
            return months[this.getMonth()];
        }

        Date.prototype.addDays = function(days) {
            var date = new Date(this.valueOf());
            date.setDate(date.getDate() + days);
            return date;
        }

        function jqueyDateChange(targetObj){
            var date= new Date(targetObj.find('input.refDate').val());
            date.setMonth(date.getMonth());
            var currentDate = date.getMonthText() + ' ' + date.getFullYear();
            $('.currentDate').html(currentDate);
            $('.year_header').html(date.getFullYear());
        }

        function formatDate(date) {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();
        
            if (month.length < 2) 
                month = '0' + month;
            if (day.length < 2) 
                day = '0' + day;
        
            return [year, month, day].join('-');
        }

        // INITIALIZATION
        var settings = $.extend({
            id           : 'rescalendar',
            format       : 'YYYY-MM-DD',
            refDate      : moment().format( 'YYYY-MM-DD' ),
            jumpSize     : 0,
            calSize      : 30,
            locale       : 'en',
            disabledDays : [],
            disabledWeekDays: [],
            dataKeyField: 'name',
            dataKeyValues: [],
            data: {},

            lang: {
                'init_error' : 'No leave data found',
                'no_data_error': 'No data found',
                'no_ref_date'  : 'No refDate found',
                'today'   : 'Today'
            },

            template_html: function( targetObj, settings ){
                console.log(settings);
                var id      = targetObj.attr('id'),
                refDate     = settings.refDate ;
                  
                var date= new Date(refDate);
                date.setMonth(date.getMonth())
                var currentDate = date.getMonthText() + ' ' + date.getFullYear()
                var monthOptions = '';
                for (let index = 0; index < 12; index++) {
                    var tempDate= new Date(refDate);
                    tempDate.setMonth(tempDate.getMonth() + index);

                    var tempDateValue = new Date(tempDate);
                    
                    var selected = "";
                    if(tempDate.getMonthText() == date.getMonthText()){
                        selected = "selected";
                    }
                    monthOptions += `<option value="${tempDateValue}" ${selected}>${tempDate.getMonthText()}</option>`;
                }
                months.forEach(element => {
                    
                });

                var fields = '';
                if(settings.for == "leave"){
                    fields =  `<button data-toggle="modal" data-target="#employeeLeave" class="btn btn-info float-right add_new"> Add New </button>,
                    <div class="float-right srch"><input type="text form-control" class="search employeeSearchCalender" onchange="search(this)" placeholder="Employee/Machine Name"><i class="fa fa-search"></i></div>`; 
                }
                
                return [

                    '<div class="rescalendar ' , id , '_wrapper" >',
                        '<div class="rescalendar_controls form-group">',
                            '<h4 class="currentDate">'+currentDate +'</h4>',

                            '<button class="move_to_last_month"><i class="fa fa-angle-double-left"></i></button>',
                            '<button class="move_to_yesterday"><i class="fa fa-angle-left"></i></button>',

                            '<input class="refDate" type="hidden" value="' + refDate + '" />',
                            // '<button class="move_to_today">Current Month</button>',
                            '<div class="select"><select id="slct" class="move_to_month">'+monthOptions+'</select></div>',
                            
                            '<button class="move_to_tomorrow"><i class="fa fa-angle-right"></i></button>',
                            '<button class="move_to_next_month"><i class="fa fa-angle-double-right"></i></button>',

                            fields,

                            '<br>',
                            // '<button class="move_to_today"> ' + settings.lang.today + ' </button>',

                        '</div>',

                        '<table class="table table-bordered">',
                            '<thead>',
                                '<tr class="rescalendar_day_cells"></tr>',
                            '</thead>',
                            '<tbody class="rescalendar_data_rows">',
                                
                            '</tbody>',
                        '</table>',
                        
                    '</div>',

                ].join('');

            }

        }, options);


        

        return this.each( function() {
            
            var targetObj = $(this);

            set_template( targetObj, settings);

            setDayCells( targetObj, settings.refDate );

            // Events
            var move_to_last_month = targetObj.find('.move_to_last_month'),
                move_to_yesterday  = targetObj.find('.move_to_yesterday'),
                move_to_tomorrow   = targetObj.find('.move_to_tomorrow'),
                move_to_next_month = targetObj.find('.move_to_next_month'),
                move_to_today      = targetObj.find('.move_to_today'),
                move_to_month      = targetObj.find('.move_to_month'),
                refDate            = targetObj.find('.refDate');

            move_to_last_month.on('click', function(e){
                
                change_day( targetObj, 'subtract', 19);

            });

            move_to_yesterday.on('click', function(e){
                
                change_day( targetObj, 'subtract', 1);

            });

            move_to_tomorrow.on('click', function(e){

                change_day( targetObj, 'add', 1);

            });

            move_to_next_month.on('click', function(e){
                
                change_day( targetObj, 'add', 19);

            });

            refDate.on('blur', function(e){
                
                var refDate = targetObj.find('input.refDate').val();
                setDayCells( targetObj, refDate );

            });

            move_to_today.on('click', function(e){
                
                var today = moment().startOf('day').format( settings.format );
                targetObj.find('input.refDate').val( today );

                jqueyDateChange(targetObj);

                setDayCells( targetObj, today );

            });

            move_to_month.on('change',function(e){
                var date = new Date($(this).val());
                var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);

                targetObj.find('input.refDate').val( formatDate(firstDay) );

                setDayCells( targetObj, firstDay );

                jqueyDateChange(targetObj)
            })

            return this;

        });

    } // end rescalendar plugin


}(jQuery));