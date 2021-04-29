<template>
    <form ref="manualTimer" @submit.prevent="submit" class="customManualTimerForm">
        <transition name="modal-fade">
            <div class="modal-backdrop">
                <div class="modal" role="dialog" aria-labelledby="modalTitle" aria-describedby="modalDescription" >
                    <header class="modal-header" id="modalTitle" >
                    <h3 name="header">Manual Timer</h3>
                        <button type="button" class="btn-close" @click="close" aria-label="Close modal">x</button>
                    </header>
                    <section class="modal-body" id="modalDescription">
                        <div class="form-group" v-for="manualTimerField in manualTimerFields" :key="manualTimerField.key">
                          <label>{{ manualTimerField.label }} {{ (manualTimerField.type == 3) ? '(in minutes)' : '' }}</label>
                          <input type="datetime-local" v-model="from[manualTimerKey+'_'+manualTimerItem+'_'+manualTimerGridId]" v-if="manualTimerField.type == 1" class="form-control from_date_field" :placeholder="manualTimerField.label" required v-on:change="toDateChange()">
                          <input type="datetime-local" v-model="to[manualTimerKey+'_'+manualTimerItem+'_'+manualTimerGridId]" v-if="manualTimerField.type == 2" class="form-control to_date_field" :placeholder="manualTimerField.label" required v-on:change="toDateChange()">
                          <p style="display: none;color: red;" v-if="manualTimerField.type == 2" class="dateErrorValidation"><i></i> </p>
                          <input type="number" v-model="total_break[manualTimerKey+'_'+manualTimerItem+'_'+manualTimerGridId]" v-if="manualTimerField.type == 3" @input="checkTime()" class="form-control" :placeholder="manualTimerField.label+'in minuntes'" required>
                          <span v-if="manualTimerField.type == 3 && manualTimerField.explanation == 1">
                            <br><label>Break Explanation </label>
                            <input type="text" v-model="explanation[manualTimerKey+'_'+manualTimerItem+'_'+manualTimerGridId]" class="form-control" placeholder="Break Explanation" required>
                          </span>
                        </div>
                        <span class="remaningTime" v-html="remaning[manualTimerKey+'_'+manualTimerItem+'_'+manualTimerGridId]"></span>
                    </section>
                    <footer class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="explanationSave">Save</button>
                    </footer>
                </div>
            </div>
        </transition>
    </form>
</template>


<script>
    export default {
        name: 'manualTimer',
        data(){
          return{
            from:[],
            to:[],
            total_break:[],
            remaning:[],
            explanation:[],
            totalDuration:[],
            manualTimerData:[],
            manualTimerPopupValue:[],
          }
        },
        props:['manualTimerFields','manualTimerKey','manualTimerItem','manualTimerGridId'],
        beforeMount(){
          $('.customManualTimerForm').find("input,textarea,select").val('').end();
          $('.remaningTime').html('');
        },
        methods: {
          close() {
            // $('.customManualTimerForm').find("input,textarea,select").val('').end();
            $('.remaningTime').html('');
            $('.dateErrorValidation').html('');
            this.$emit('close');
          },
          submit(){
            var status = this.toDateChange();
            if(!status){
              console.log(status);
              return;
            }
            this.manualTimerData['from'] = moment(this.from[this.manualTimerKey+'_'+this.manualTimerItem+'_'+this.manualTimerGridId]).format('D-MMM-YYYY h:mm a');
            this.manualTimerData['to'] = moment(this.to[this.manualTimerKey+'_'+this.manualTimerItem+'_'+this.manualTimerGridId]).format('D-MMM-YYYY h:mm a');
            this.manualTimerData['total_break'] = this.total_break[this.manualTimerKey+'_'+this.manualTimerItem+'_'+this.manualTimerGridId];
            this.manualTimerData['explanation'] = this.explanation[this.manualTimerKey+'_'+this.manualTimerItem+'_'+this.manualTimerGridId];
            this.manualTimerData['totalDuration'] = this.totalDuration[this.manualTimerKey+'_'+this.manualTimerItem+'_'+this.manualTimerGridId];
            this.manualTimerData['manualTimerKey'] = this.manualTimerKey;
            this.manualTimerData['manualTimerItem'] = this.manualTimerItem;
            this.manualTimerData['manualTimerGridId'] = this.manualTimerGridId;
            console.log(this.manualTimerData);
            this.$emit('manualTimerData', this.manualTimerData);
            this.close();
          },
          checkTime(){
            var remaningValue = '';
            remaningValue = new Date(this.to[this.manualTimerKey+'_'+this.manualTimerItem+'_'+this.manualTimerGridId]).getTime() - new Date(this.from[this.manualTimerKey+'_'+this.manualTimerItem+'_'+this.manualTimerGridId]).getTime();
            remaningValue = remaningValue - this.total_break[this.manualTimerKey+'_'+this.manualTimerItem+'_'+this.manualTimerGridId] * 60 * 1000;
            var minDiff = remaningValue / 60 / 1000; //in minutes
            var hDiff = remaningValue / 3600 / 1000; //in hours
            var humanReadable = {};
            humanReadable.hours = Math.floor(hDiff);
            humanReadable.minutes = minDiff - 60 * humanReadable.hours;
            // $('.remaningTime').html(`Total: ${humanReadable.hours} hr ${humanReadable.minutes} min`)
            this.remaning[this.manualTimerKey+'_'+this.manualTimerItem+'_'+this.manualTimerGridId] = `Total: ${humanReadable.hours} hr ${humanReadable.minutes} min`;
            this.totalDuration[this.manualTimerKey+'_'+this.manualTimerItem+'_'+this.manualTimerGridId] = this.remaning[this.manualTimerKey+'_'+this.manualTimerItem+'_'+this.manualTimerGridId];
          },
          toDateChange(){
            var d1 = Date.parse(this.from[this.manualTimerKey+'_'+this.manualTimerItem+'_'+this.manualTimerGridId]);
            var d2 = Date.parse(this.to[this.manualTimerKey+'_'+this.manualTimerItem+'_'+this.manualTimerGridId]);
            if (d1 >= d2) {
              var from_date_placeholder = $('.from_date_field').attr('placeholder');
              var to_date_placeholder = $('.to_date_field').attr('placeholder');
              $('.dateErrorValidation').html(`${to_date_placeholder} date must be greater than ${from_date_placeholder} date`).show();
              return false;
            }else{
              $('.dateErrorValidation').html('');
              return true;
            }
          }
        },
    };
</script>

<style lang="scss" scoped>
  .modal-backdrop {
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: rgba(0, 0, 0, 0.3);
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .modal {
    overflow: auto;
    background: #FFFFFF;
    box-shadow: 2px 2px 20px 1px;
    overflow-x: auto;
    display: flex;
    flex-direction: column;
    margin: 100px 500px;
  }

  .modal-header,
  .modal-footer {
    padding: 15px;
    display: flex;
  }

  .modal-header {
    border-bottom: 1px solid #eeeeee;
    color: #4AAE9B;
    justify-content: space-between;
  }

  .modal-footer {
    border-top: 1px solid #eeeeee;
    justify-content: flex-end;
  }

  .modal-body {
    position: relative;
    padding: 20px;
  }

  .btn-close {
    border: none;
    font-size: 20px;
    padding: 20px;
    cursor: pointer;
    font-weight: bold;
    color: #4AAE9B;
    background: transparent;
  }

  .btn-green {
    color: white;
    background: #4AAE9B;
    border: 1px solid #4AAE9B;
    border-radius: 2px;
  }
  .form-group .showFile{
    opacity:1;
    position: inherit;
  }
</style>