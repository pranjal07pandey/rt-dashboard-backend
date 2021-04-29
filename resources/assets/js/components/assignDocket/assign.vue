<template>
    <div>
        <div class="form-group">
            <select v-model="view" class="form-control">
                <option value="day">day</option>
                <option value="week">week</option>
                <option value="month">month</option>
            </select>
        </div>
    <calendar style="height: 800px;"
        :calendars="calendarList"
        :schedules="scheduleList"
        :view="view"
        :taskView="taskView"
        :scheduleView="scheduleView"
        :theme="theme"
        :week="week"
        :month="month"
        :timezones="timezones"
        :disableDblClick="disableDblClick"
        :isReadOnly="isReadOnly"
        :template="template"
        :useCreationPopup="useCreationPopup"
        :useDetailPopup="useDetailPopup"
        @afterRenderSchedule="onAfterRenderSchedule"
        @beforeCreateSchedule="onBeforeCreateSchedule"
        @beforeDeleteSchedule="onBeforeDeleteSchedule"
        @beforeUpdateSchedule="onBeforeUpdateSchedule"
        @clickDayname="onClickDayname"
        @clickSchedule="onClickSchedule"
        @clickTimezonesCollapseBtn="onClickTimezonesCollapseBtn"
    />
    </div>
</template>
<script>
import 'tui-calendar/dist/tui-calendar.css'
import { Calendar } from '@toast-ui/vue-calendar';
// If you use the default popups, use this.
import 'tui-date-picker/dist/tui-date-picker.css';
import 'tui-time-picker/dist/tui-time-picker.css';


export default {
    name: 'myCalendar',
    components: {
        'calendar': Calendar
    },
    data() {
        return {          
            calendarList: [
                {
                    id: '0',
                    name: 'home'
                },
                {
                    id: '1',
                    name: 'office'
                }
            ],
            scheduleList: [
                {
                    id: '1',
                    calendarId: '1',
                    title: 'my schedule',
                    category: 'time',
                    dueDateClass: '',
                    start: '2021-01-18 01:30:00',
                    end: '2021-01-18 02:30:00'
                },
                {
                    id: '2',
                    calendarId: '1',
                    title: 'second schedule',
                    category: 'time',
                    dueDateClass: '',
                    start: '2021-01-22 17:30:00',
                    end: '2021-01-26 17:31:00'
                }
            ],
            view: "month",
            taskView: false,
            scheduleView: true,
            theme: {
                'month.dayname.height': '30px',
                'month.dayname.borderLeft': '1px solid #ff0000',
                'month.dayname.textAlign': 'center',
                'week.today.color': '#333',
                'week.daygridLeft.width': '100px',
                'week.timegridLeft.width': '100px'
            },
            week: {
                narrowWeekend: true,
                // showTimezoneCollapseButton: true,
                // timezonesCollapsed: false
            },
            month: {
                visibleWeeksCount: 6,
                startDayOfWeek: 1
            },
            timezones: [{
                timezoneOffset: 540,
                displayLabel: 'GMT+09:00',
                tooltip: 'Seoul'
            }, {
                timezoneOffset: -420,
                displayLabel: 'GMT-08:00',
                tooltip: 'Los Angeles'
            }],
            disableDblClick: true,
            isReadOnly: false,
            template: {
                milestone: function(schedule) {
                    return `<span style="color:red;">${schedule.title}</span>`;
                },
                milestoneTitle: function() {
                    return 'MILESTONE';
                },
            },
            useCreationPopup: true,
            useDetailPopup: false,
            scheduleListData:[],
        }
    },
    mounted(){
        this.scheduleListData = [
            {
                id: '1',
                calendarId: '1',
                title: 'my schedule',
                category: 'time',
                dueDateClass: '',
                start: '2021-01-18 22:30:00',
                end: '2021-01-19 02:30:00'
            },
            {
                id: '2',
                calendarId: '1',
                title: 'second schedule',
                category: 'time',
                dueDateClass: '',
                start: '2021-01-22 17:30:00',
                end: '2021-01-26 17:31:00'
            }
        ];
    },
    methods: {
        onAfterRenderSchedule(e) {
            // alert('1')
        },
        onBeforeCreateSchedule(e) {
            this.scheduleListData.push({
                // id: '22',
                // calendarId: '1',
                // title: e.title,
                // category: 'time',
                // dueDateClass: '',
                // start: e.start,
                // end: e.end
                id: '3',
                calendarId: '1',
                title: 'second schedule',
                category: 'time',
                dueDateClass: '',
                start: '2021-01-28T17:30:00+09:00',
                end: '2021-01-29T17:31:00+09:00'
            });
            console.log(this.scheduleListData);
            // alert('10')
        },
        onBeforeDeleteSchedule(e) {
            alert('11')
        },
        onBeforeUpdateSchedule(e) {
            console.log(e);
            var datediff = moment(e.schedule.start).diff(e.schedule.end, 'minutes')
            console.log(datediff);
            this.scheduleListData.filter(x => {
                if(x.id == e.schedule.id){
                    x.start = moment(e.start).format('YYYY-MM-DD HH:mm:ss');
                    x.end = moment(e.end).format('YYYY-MM-DD HH:mm:ss');
                }
            })
            console.log(this.scheduleListData);
            alert('12')
        },
        onClickDayname(e) {
            alert('13')
        },
        onClickSchedule(e) {
            alert('14')
        },
        onClickTimezonesCollapseBtn(e) {
            alert('15')
        }
    }
}
</script>