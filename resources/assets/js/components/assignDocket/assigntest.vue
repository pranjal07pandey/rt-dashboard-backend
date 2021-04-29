<template>
<v-app>
  <v-row class="fill-height">
    <v-col>
      <v-sheet height="64">
        <v-toolbar flat color="white">
          <v-btn color="primary" dark @click.stop="dialog = true">
            New Event
          </v-btn>
          <v-btn outlined class="mr-4" @click="setToday">
            Today
          </v-btn>
          <v-btn fab text small @click="prev">
            <v-icon small>mdi-chevron-left</v-icon>
          </v-btn>
          <v-btn fab text small @click="next">
            <v-icon small>mdi-chevron-right</v-icon>
          </v-btn>
          <v-toolbar-title>{{ title }}</v-toolbar-title>
          <div class="flex-grow-1"></div>
          <v-menu bottom right>
            <template v-slot:activator="{ on }">
              <v-btn outlined v-on="on">
                <span>{{ typeToLabel[type] }}</span>
                <v-icon right>mdi-menu-down</v-icon>
              </v-btn>
            </template>
            <v-list>
              <v-list-item @click="type = 'day'">
                <v-list-item-title>Day</v-list-item-title>
              </v-list-item>
              <v-list-item @click="type = 'week'">
                <v-list-item-title>Week</v-list-item-title>
              </v-list-item>
              <v-list-item @click="type = 'month'">
                <v-list-item-title>Month</v-list-item-title>
              </v-list-item>
              <v-list-item @click="type = '4day'">
                <v-list-item-title>4 days</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </v-toolbar>
      </v-sheet>

      <v-dialog v-model="dialog" max-width="500">
        <v-card>
          <v-container>
            <v-card color="grey lighten-4" flat>
                <v-toolbar dark>
                <v-toolbar-title>Add Event</v-toolbar-title>
                    <div class="flex-grow-1"></div>
                </v-toolbar>
                <form @submit.prevent="addEvent">
                    <v-card-text>
                        <v-text-field v-model="newEvent.name" type="text" label="event name (required)"></v-text-field>
                        <v-text-field v-model="newEvent.location" type="text" label="location"></v-text-field>
                        <v-text-field v-model="newEvent.start" type="datetime-local" label="start (required)"></v-text-field>
                        <v-text-field v-model="newEvent.end" type="datetime-local" label="end (required)"></v-text-field>
                        <v-text-field v-model="newEvent.color" type="color" label="color (click to open color menu)"></v-text-field>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn text color="secondary" @click="dialog = false"> close </v-btn>
                        <v-btn text type="submit"> Save </v-btn>
                    </v-card-actions>
                </form>
            </v-card>
          </v-container>
        </v-card>
      </v-dialog>

      <v-dialog v-model="dialogDate" max-width="500">
        <v-card>
            <v-container>
                <v-card color="grey lighten-4" flat>
                    <v-toolbar dark>
                    <v-toolbar-title>Add Event</v-toolbar-title>
                        <div class="flex-grow-1"></div>
                    </v-toolbar>
                    <form @submit.prevent="addEvent">
                        <v-card-text>
                            <v-text-field v-model="newEvent.name" type="text" label="event name (required)"></v-text-field>
                            <v-text-field v-model="newEvent.location" type="text" label="location"></v-text-field>
                            <v-text-field v-model="newEvent.start" type="datetime-local" label="start (required)"></v-text-field>
                            <v-text-field v-model="newEvent.end" type="datetime-local" label="end (required)"></v-text-field>
                            <v-text-field v-model="newEvent.color" type="color" label="color (click to open color menu)"></v-text-field>
                        </v-card-text>
                        <v-card-actions>
                            <v-btn text color="secondary" @click="dialogDate = false"> close </v-btn>
                            <v-btn text type="submit"> Save </v-btn>
                        </v-card-actions>
                    </form>
                </v-card>
            </v-container>
        </v-card>
      </v-dialog>

    <v-sheet height="600">
        <v-calendar ref="calendar" v-model="focus" color="primary"
        :events="events" :event-color="getEventColor" :event-margin-bottom="3" :now="today" :type="type"
        @click:event="showEvent" @click:more="viewDay" @click:date="setDialogDate" @change="updateRange" 
        @mousedown:event="startDrag"
          @mousedown:time="startTime"
          @mousemove:time="mouseMove"
          @mouseup:time="endDrag"
          @mouseleave.native="cancelDrag">
          <template #event="{ event, timed }">
            <div class="pl-1" 
              v-html="getEventHTML(event, timed)"
            ></div>
            <div v-if="timed" 
              class="v-event-drag-bottom"
              @mousedown.stop="extendBottom(event)"
            ></div>
          </template>
          <template #day-body="{ date, week }">
            <div class="v-current-time"
               :class="{ first: date === week[0].date }"
               :style="{ top: nowY }">
              <div class="v-current-time-time">
                {{ nowTime }}
              </div>
            </div>
          </template>
        </v-calendar>
        <v-menu v-model="selectedOpen" :close-on-content-click="false" :activator="selectedElement" full-width offset-x >
            <v-card color="grey lighten-4" :width="350" flat>
                <v-toolbar :color="selectedEvent.color" dark>
                    <v-btn @click="deleteEvent(selectedEvent.id)" icon>
                        <v-icon>mdi-delete</v-icon>
                    </v-btn>
                    <v-toolbar-title v-html="selectedEvent.name"></v-toolbar-title>
                    <div class="flex-grow-1"></div>
                </v-toolbar>

                <v-card-text>
                    <form>
                        <v-text-field v-model="selectedEvent.name" type="text" label="event name (required)"></v-text-field>
                        <v-text-field v-model="selectedEvent.location" type="text" label="location"></v-text-field>
                        <v-text-field v-model="selectedEvent.start" type="date" label="start (required)"></v-text-field>
                        <v-text-field v-model="selectedEvent.end" type="date" label="end (required)"></v-text-field>
                        <v-text-field v-model="selectedEvent.color" type="color" label="color (click to open color menu)"></v-text-field>
                    </form>
                </v-card-text>

                <v-card-actions>
                    <v-btn text color="secondary" @click="editEventClose()">
                    close
                    </v-btn>
                    <!-- <v-btn v-if="currentlyEditing !== selectedEvent.id" text @click.prevent="editEvent(selectedEvent)">
                    edit
                    </v-btn> -->
                    <v-btn text type="submit" @click.prevent="updateEvent(selectedEvent)">
                    Save
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-menu>
    </v-sheet>
</v-col>
</v-row>
</v-app>
</template>

<script>
export default {
  data: () => ({
    today: new Date().toISOString().substr(0, 10),
    focus: new Date().toISOString().substr(0, 10),
    type: 'month',
    typeToLabel: {
      month: 'Month',
      week: 'Week',
      day: 'Day',
      '4day': '4 Days',
    },
    name: null,
    details: null,
    start: null,
    end: null,
    color: '#1976D2', // default event color
    currentlyEditing: null,
    selectedEvent: {},
    newEvent: {},
    selectedElement: null,
    selectedOpen: false,
    events: [],
    dialog: false,
    dialogDate: false,

    dragEvent: null,
    dragStart: null,
    lastEvent: '',
    createEvent: null,
    createStart: null,
    extendOriginal: null,
  }),
  mounted () {
    this.getEvents()
  },
  computed: {
    title () {
      const { start, end } = this
      if (!start || !end) {
        return ''
      }
      const startMonth = this.monthFormatter(start)
      const endMonth = this.monthFormatter(end)
      const suffixMonth = startMonth === endMonth ? '' : endMonth
      const startYear = start.year
      const endYear = end.year
      const suffixYear = startYear === endYear ? '' : endYear
      const startDay = start.day + this.nth(start.day)
      const endDay = end.day + this.nth(end.day)
      switch (this.type) {
        case 'month':
        return `${startMonth} ${startYear}`
        case 'week':
        case '4day':
        return `${startMonth} ${startDay} ${startYear} - ${suffixMonth} ${endDay} ${suffixYear}`
        case 'day':
        return `${startMonth} ${startDay} ${startYear}`
      }
      return ''
    },
    monthFormatter () {
      return this.$refs.calendar.getFormatter({
        timeZone: 'UTC', month: 'long',
      })
    },
      nowY() {
      const cal = this.$refs.calendar;
      if (!cal && !this.isMounted) {
        return -1;
      }
      
      return cal.timeToY(cal.times.now) + 'px';
    },
    nowTime() {
      const cal = this.$refs.calendar;
      if (!cal && !this.isMounted) {
        return -1;
      }
      
      return cal.formatTime(cal.times.now);
    },
  },
  methods: {
    getEvents () {
        const events = [
            {
                id:'1',
                name: 'second 10',
                location: 'location 1',
                start: new Date(`2021-01-19 01:00:00`).getTime(),
                end: new Date(`2021-01-19 02:00:00`).getTime(),
                color: 'blue',
                timed: true,
            },
            {
                id:'2',
                name: 'second 11',
                location: 'location 1',
                start: new Date(`2021-01-19 04:00:00`).getTime(),
                end: new Date(`2021-01-19 05:00:00`).getTime(),
                color: 'yellow',
                timed: true,
            },
            {
                id:'3',
                name: 'second 12',
                location: 'location 1',
                start: new Date(`2021-01-19 06:00:00`).getTime(),
                end: new Date(`2021-01-19 07:00:00`).getTime(),
                color: 'red',
                timed: true,
            },
            {
                id:'4',
                name: 'second 2',
                location: 'location 2',
                start: new Date(`2021-01-20 06:00:00`).getTime(),
                end: new Date(`2021-01-21 07:00:00`).getTime(),
                color: 'red',
                timed: true,
            },
        ]

        // for (let i = 0; i < eventCount; i++) {
        //   events.push({
        //     name: 'ads',
        //     start: first,
        //     end: second,
        //     color: 'red',
        //     timed: !allDay,
        //   })
        // }
        this.events = events
    },
   startDrag(e) {
       console.log('1');
       console.log(e);
      if (e.event && e.timed) {
        this.dragEvent = e.event;
        this.dragTime = null;
        this.extendOriginal = null;
      }

      this.lastEvent = 'startDrag';
    },
    startTime (tms) {
      const mouse = this.toTime(tms)

      if (this.dragEvent && this.dragTime === null) {
        const start = this.dragEvent.start
        this.dragTime = mouse - start
      } else {
        alert('a');
        this.createStart = this.roundTime(mouse)
        this.createEvent = {
          name: `Event #${this.events.length}`,
          color: 'red',
          start: this.createStart,
          end: this.createStart,
          timed: true,
        }

        this.events.push(this.createEvent)
      }
    },
    extendBottom (event) {
      this.createEvent = event
      this.createStart = event.start
      this.extendOriginal = event.end
    },
    mouseMove (tms) {
      const mouse = this.toTime(tms)

      if (this.dragEvent && this.dragTime !== null) {
        const start = this.dragEvent.start
        const end = this.dragEvent.end
        const duration = end - start
        const newStartTime = mouse - this.dragTime
        const newStart = this.roundTime(newStartTime)
        const newEnd = newStart + duration

        this.dragEvent.start = newStart
        this.dragEvent.end = newEnd
      } else if (this.createEvent && this.createStart !== null) {
        const mouseRounded = this.roundTime(mouse, false)
        const min = Math.min(mouseRounded, this.createStart)
        const max = Math.max(mouseRounded, this.createStart)

        this.createEvent.start = min
        this.createEvent.end = max
      }
    },
    endDrag () {
      this.dragTime = null
      this.dragEvent = null
      this.createEvent = null
      this.createStart = null
      this.extendOriginal = null
    },
    cancelDrag () {
      if (this.createEvent) {
        if (this.extendOriginal) {
          this.createEvent.end = this.extendOriginal
        } else {
          const i = this.events.indexOf(this.createEvent)
          if (i !== -1) {
            this.events.splice(i, 1)
          }
        }
      }

      this.createEvent = null
      this.createStart = null
      this.dragTime = null
      this.dragEvent = null
    },
    roundTime (time, down = true) {
      const roundTo = 15 // minutes
      const roundDownTime = roundTo * 60 * 1000

      return down
        ? time - time % roundDownTime
        : time + (roundDownTime - (time % roundDownTime))
    },
    toTime (tms) {
      return new Date(tms.year, tms.month - 1, tms.day, tms.hour, tms.minute).getTime()
    },
    getEventHTML(event, timed) {
      const cal = this.$refs.calendar;
      let name = event.name;
      if (event.start.hasTime) {
        if (timed) {
          const showStart = event.start.hour < 12 && event.end.hour >= 12;
          const start = cal.formatTime(event.start, showStart);
          const end = cal.formatTime(event.end, true);
          const singline = diffMinutes(event.start, event.end) <= this.parsedEventOverlapThreshold
          const separator = singline ? ', ' : '<br>'
          return `<strong>${name}</strong>${separator}${start} - ${end}`
        } else {
          const time = this.formatTime(event.start, true)
          return `<strong>${time}</strong> ${name}`
        }
      }
      return name;
    },

    editEventClose(){
        this.selectedOpen = false;
        this.getEvents();
    },
    rnd (a, b) {
        return Math.floor((b - a + 1) * Math.random()) + a
    },
    setDialogDate( { date }) {
      this.dialogDate = true
      this.focus = date
      this.newEvent.start = date+"T00:00";
      this.newEvent.end = date+"T00:00";
    },
    viewDay ({ date }) {
      this.focus = date
      this.type = 'day'
    },
    getEventColor (event) {
      return event.color
    },
    setToday () {
      this.focus = this.today
    },
    prev () {
      this.$refs.calendar.prev()
    },
    next () {
      this.$refs.calendar.next()
    },
    async addEvent () {
      if (this.name && this.start && this.end) {
        // await db.collection("calEvent").add({
        //   name: this.name,
        //   details: this.details,
        //   start: this.start,
        //   end: this.end,
        //   color: this.color
        // })
        this.getEvents()
        this.name = '',
        this.details = '',
        this.start = '',
        this.end = '',
        this.color = ''
      } else {
        alert('You must enter event name, start, and end time');
        this.dialog = false;
      }
    },
    editEvent (ev) {
        console.log(ev);
      this.currentlyEditing = ev.id
    },
    async updateEvent (ev) {
        console.log(ev);
    //   await db.collection('calEvent').doc(this.currentlyEditing).update({
    //     details: ev.details
    //   })
    alert('asd');
    this.events.filter(x => {
        if(x.id == ev.id){
            x.name = ev.name;
            x.location = ev.location;
            x.start = ev.start;
            x.end = ev.end;
            x.color = ev.color;
            x.timed = ev.timed;
        }
    })
      this.selectedOpen = false,
      this.currentlyEditing = null
    },
    async deleteEvent (ev) {
    //   await db.collection("calEvent").doc(ev).delete()
      this.selectedOpen = false,
      this.getEvents()
    },
    showEvent ({ nativeEvent, event }) {
      const open = () => {
        this.selectedEvent = event
        this.selectedElement = nativeEvent.target
        setTimeout(() => this.selectedOpen = true, 10)
      }
      if (this.selectedOpen) {
        this.selectedOpen = false
        setTimeout(open, 10)
      } else {
        open()
      }
      nativeEvent.stopPropagation()
    },
    updateRange ({ start, end }) {
      this.start = start
      this.end = end
    },
    nth (d) {
      return d > 3 && d < 21
      ? 'th'
      : ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'][d % 10]
    }
  }
}
</script>
<style scoped>
    .v-menu__content{
        top: 202px !important;
        left: 300px !important;
        min-width: 500px !important;
    }

    .v-menu__content .v-card{
        width: 100% !important;
    }
</style>