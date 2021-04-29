<template>
    <form ref="explanation" @submit.prevent="submit" class="customExplanationForm">
        <transition name="modal-fade">
            <div class="modal-backdrop">
                <div class="modal" role="dialog" aria-labelledby="modalTitle" aria-describedby="modalDescription" >
                    <header class="modal-header" id="modalTitle" >
                    <h3 name="header">Explanation</h3>
                        <button type="button" class="btn-close" @click="close" aria-label="Close modal">x</button>
                    </header>
                    <section class="modal-body" id="modalDescription">
                        <div class="form-group" v-for="(explanation, key) in explanations" :key="key">
                          <label>{{ explanation.label }}</label>
                          <input type="hidden" :name="'explanationData['+key+'][category_id]'" :value="explanation.docket_field_category_id">
                          <input type="hidden" :name="'explanationData['+key+'][form_field_id]'" :value="explanation.id">
                          <!-- {{explanationKey}}_{{key}}_{{explanation.id}} -->
                          <input type="text" :name="'explanationData['+key+'][value]'" v-if="explanation.docket_field_category_id == 1" v-model="shortText[explanationKey+'_'+key+'_'+explanation.id]" class="form-control" :placeholder="explanation.label" :required="(explanation.required == 1)?'required':''">
                          <textarea v-if="explanation.docket_field_category_id == 2" :name="'explanationData['+key+'][value]'" v-model="longText[explanationKey+'_'+key+'_'+explanation.id]" :placeholder="explanation.label" class="form-control" :required="(explanation.required == 1)?'required':''"></textarea>
                          <input type="file" accept="image/*" name="explanation_file" class="showFile" v-on:change="onFileChange" multiple v-if="explanation.docket_field_category_id == 5" :required="(explanation.required == 1)?'required':''" :key-data="key">
                        </div>
                        <div class="explanationImageAppend row"></div>
                    </section>
                    <footer class="modal-footer">
                        <button type="button" class="btn btn-primary" id="explanationSave" @click="submit">Save</button>
                    </footer>
                </div>
            </div>
        </transition>
    </form>
</template>


<script>
    export default {
        name: 'explanation',
        data(){
          return{
            // shortText:'',
            // longText:'',
            shortText:[],
            longText:[],
            image:'',
            explanation_key:'',
            explanationData:[],
          }
        },
        props:['explanations','explanationKey','explanationEditValue','explanationEditKey'],
        watch:{
          explanationEditValue(){
            if(this.explanationEditValue){
              for (let index = 0; index < this.explanationEditValue.length; index++) {
                if(this.explanationEditValue[index]){
                  if(this.explanationEditValue[index].category_id == 1){
                    this.shortText[this.explanationEditKey+'_'+index+'_'+this.explanationEditValue[index].form_field_id] = this.explanationEditValue[index].value;
                  }
                  if(this.explanationEditValue[index].category_id == 2){
                    this.longText[this.explanationEditKey+'_'+index+'_'+this.explanationEditValue[index].form_field_id] = this.explanationEditValue[index].value;
                  }
                  if(this.explanationEditValue[index].category_id == 5){
                    if(this.explanationEditValue[index].image_value){
                      for (let index1 = 0; index1 < this.explanationEditValue[index].image_value.length; index1++) {
                        setTimeout(() => {
                          $('.explanationImageAppend').append(`
                            <div class="explanationImage col-lg-3">
                              <input type="hidden" name="explanationData[${index}][image_value][]" value="${this.explanationEditValue[index].image_value[index1]}">
                              <img src="${this.explanationEditValue[index].image_value[index1]}" style="width:100px">
                              <i class="material-icons" onClick="removeExplaination(this)" style="position: absolute;cursor: pointer;right: 0;top: 0;">close</i>
                            </div>
                          `);
                        }, 5000);
                      }
                    }
                  }
                }
              }
            }
          }
        },
        methods: {
          close() {
            // $('.customExplanationForm').find("input,textarea,select").val('').end();
            // this.explanation_values = []
            this.explanation_key = '';
            this.$emit('close');
          },
          onFileChange(event){
            var files = event.target.files || event.dataTransfer.files;
            if (!files.length){
              return;
            }else{
              let formData    =   new FormData();
              for (let index = 0; index < files.length; index++) {
                console.log(index);
                formData.append('explanation_file[]',(event.srcElement || event.target).files[index]);
              }
              var key = $(event.target).attr('key-data');
               axios.post(`/api/web/files/upload`, formData).then(res =>{
                // this.image = res.data;
                for (let index = 0; index < res.data.length; index++) {
                  $(event.target).closest('div').find('.explanationImageAppend').append(`
                    <input type="hidden" :name="'explanationData[${key}][value][]'" value="${res.data[index]}">
                  `);

                  $('.explanationImageAppend').append(`
                    <div class="explanationImage col-lg-3">
                      <input type="hidden" name="explanationData[${key}][image_value][]" value="${res.data[index]}">
                      <img src="${res.data[index]}" style="width:100px">
                      <i class="material-icons" onClick="removeExplaination(this)" style="cursor:pointer;">close</i>
                    </div>
                  `);
                }
                this.explanation_key = this.explanationKey;
              }).catch(error => {
                  console.error("There was an error!", error);
              });
            }
          },
          submit(){
            var i = 0;
            this.explanationData = [];
            for (let index = 0; index < this.explanations.length; index++) {
              const element = this.explanations[index];
              this.explanationData[index] = [];
              this.explanationData[index].category_id = this.explanations[index].docket_field_category_id;
              this.explanationData[index].form_field_id = this.explanations[index].id;
              if(this.explanations[index].docket_field_category_id == 1){
                this.explanationData[index].value = this.shortText[this.explanationKey+'_'+index+'_'+this.explanations[index].id];
              }else if(this.explanations[index].docket_field_category_id == 2){
                this.explanationData[index].value = this.longText[this.explanationKey+'_'+index+'_'+this.explanations[index].id];
              }else{
                var image_value = [];
                $(`input[name="explanationData[${index}][image_value][]"]`).each(function(){
                  image_value.push($(this).val());
                });
                console.log(image_value);
                this.explanationData[index].value = image_value;    
              }
            }
            this.explanationData['explanation_key'] = this.explanationKey;
            this.$emit('explanationData', this.explanationData);
            this.close();
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