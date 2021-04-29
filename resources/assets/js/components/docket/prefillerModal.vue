<template>
    <form ref="signature" @submit.prevent="submit" class="customsignatureForm">
        <transition name="modal-fade">
            <div class="modal-backdrop">
                <div class="modal" role="dialog" aria-labelledby="modalTitle" aria-describedby="modalDescription" >
                    <header class="modal-header" id="modalTitle" >
                    <h3 name="header">Prefiller</h3>
                        <button type="button" class="btn-close" @click="close" aria-label="Close modal">x</button>
                    </header>
                    <section class="modal-body" id="modalDescription" >
                        <div style="padding-bottom:10px" class="appendPrefillerModal">
                            <div v-if="prefillerValue.prefiller.length > 0">
                                <div v-for="prefiller in prefillerValue.prefiller" :key="prefiller.id">
                                    <div v-if="prefiller.prefiller.length > 0" v-on:click="expandPrefiller(prefiller,prefillerValue)">
                                        <span>{{ prefiller.value }}</span>
                                        <i  class="fa fa-angle-right" style="float: right;font-size: 25px;margin-right: 15px;"></i>
                                        <hr>
                                    </div>
                                    <div v-else v-on:click="selectPrefiller(prefiller,$event)" class="selectablePrefiller">
                                        <span>{{ prefiller.value }}</span>
                                        <hr>
                                    </div>
                                </div>
                            </div>
                            <div v-else>
                                No prefiller found.
                            </div>
                        </div>
                    </section>
                    <footer class="modal-footer">
                        <button type="button" class="btn btn-primary" v-on:click="oldPrefiller()">Back</button>
                        <button type="button" class="btn btn-primary" v-on:click="submit()">Save</button>
                    </footer>
                </div>
            </div>
        </transition>
    </form>
</template>


<script>
    export default {
        name: 'prefillerModal',
        props:['prefillerData','prefillerOtherData'],
        data(){
            return{
                prefillerValue: this.prefillerData,
                oldPrefillerValue:[],
                oldPrefillerArray:[],
                selectedPrefillerValue:[],
                selectedStatus: 0,
            }
        },
        beforeMount(){
            this.prefillerValue['prefiller'] = [];
        },
         watch:{
          prefillerData(){
            if(this.prefillerData){
                this.prefillerValue = this.prefillerData;
            }
          }
        },
        methods: {
            close() {
                this.$emit('close');
            },
            expandPrefiller(prefillerValue,oldPrefillerValue){;
                this.prefillerValue = prefillerValue;
                this.oldPrefillerValue = oldPrefillerValue;
                this.oldPrefillerArray.push(oldPrefillerValue);
                this.selectedPrefillerValue.push(prefillerValue.value);
            },
            oldPrefiller(){
                this.prefillerValue = this.oldPrefillerArray[this.oldPrefillerArray.length - 1];
                this.oldPrefillerArray.pop();
                this.selectedPrefillerValue.pop();
                this.selectedStatus = 0;
            },
            selectPrefiller(prefillerValue,event){
                $('.selectablePrefiller').removeClass('selected');
                if(this.selectedStatus == 0){
                    this.selectedPrefillerValue.push(prefillerValue.value);
                    this.selectedStatus = 1;
                    $(event.target).addClass('selected');
                }else{
                    this.selectedStatus = 0;
                    this.selectedPrefillerValue.pop();
                }
            },
            submit(){
                if(this.selectedStatus == 0){
                    alert('Select the prefiller');
                }else{
                    var temp = []; 
                    temp['prefillerKeyValue'] = this.prefillerOtherData['prefillerKeyValue'];
                    temp['category_id'] = this.prefillerOtherData['category_id'];
                    temp['selectedPrefillerValue'] = this.selectedPrefillerValue;
                    this.oldPrefillerValue = [];
                    this.oldPrefillerArray = [];
                    this.selectedPrefillerValue = [];
                    this.selectedStatus = 0;
                    this.$emit('selectedPrefillerData', temp);
                    this.$emit('close');
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
  .selected{
      background: grey;
  }
</style>