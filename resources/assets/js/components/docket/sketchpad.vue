<template>
    <form ref="sketchpad" @submit.prevent="submit" class="customSketchpadForm">
        <transition name="modal-fade">
            <div class="modal-backdrop">
                <div class="modal" role="dialog" aria-labelledby="modalTitle" aria-describedby="modalDescription" >
                    <header class="modal-header" id="modalTitle" >
                    <h3 name="header">SketchPad</h3>
                        <button type="button" class="btn-close" @click="close" aria-label="Close modal">x</button>
                    </header>
                    <section class="modal-body" id="modalDescription">
                        <div style="padding-bottom:10px">
                            <div class="sketchPadAppend row" style="padding: 7px;">
                            </div>
                            <div class="wrapper1">
                                <input type="hidden" name="sketchPadKey" :value="sketchPadKey">
                                <label class="control-label" for="title">Signature <b style="color:red;font-size: 13px;">*</b></label>&nbsp;&nbsp;&nbsp;
                                <label>Choose colors: </label> &nbsp;&nbsp;&nbsp;
                                <input type="color" class="sketchPadColor">
                                <div style="float:right;padding:12px">
                                  <button type="button" id="sketchPadClear" >Clear</button> &nbsp;&nbsp;&nbsp;
                                  <button type="button" id="sketchPadSave" :sketchPad_key="sketchPadKey" :sketchPad_id="sketchPadGridId" :sketchPad_item="sketchPadItem">Add</button>
                                </div>
                                <br><br>
                                <canvas id="sketch-pad" class="sketch-pad" width="460" height="200" style="background-color:#ebebeb;" ></canvas>
                                <p style="display: none;color: red;" class="sketchPadRequired"><i>*Signature Required</i> </p>
                            </div>
                            <!-- <div>
                                <button type="button" style="position: absolute;right: 20px;top: 30px;" id="sketchPadClear">Clear</button>
                            </div> -->
                        </div>
                    </section>
                    <footer class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </footer>
                </div>
            </div>
        </transition>
    </form>
</template>


<script>
    export default {
        name: 'sketchpad',
        props:['sketchPadPreviewClass','sketchPadKey','sketchPadItem','sketchPadGridId'],
        methods: {
          close() {
              this.$emit('close');
          },
          submit(){
            let formData    =   new FormData();
            var formDataValueCheck = 0;
            var gridIdVal = undefined;
            var itemVal = undefined;
            if(this.sketchPadGridId){
              gridIdVal = this.sketchPadGridId;
            }
            if(this.sketchPadItem){
              itemVal = this.sketchPadItem;
            }
            $(`.sketchpad_${this.sketchPadKey}_${gridIdVal}_${itemVal}`).each(function(){
              var sketchpadImageUrl = $(this).find('input[name="sketchpad[]"]').val();
              if(sketchpadImageUrl){
                if(!sketchpadImageUrl.includes('http')){
                  formDataValueCheck = 1;
                  formData.append('sketchpad[]',sketchpadImageUrl);
                }
              }
            });
            if(formDataValueCheck == 1){
              axios.post(`/api/web/files/upload`, formData).then(res =>{
                var temp = [];
                var data = res.data;
                $(`.sketchpad_${this.sketchPadKey}_${gridIdVal}_${itemVal}`).each(function(){
                  var sketchpadImageUrl = $(this).find('input[name="sketchpad[]"]').val();
                  if(sketchpadImageUrl){
                    if(sketchpadImageUrl.includes('http')){
                      data.push(sketchpadImageUrl);
                    }
                  }
                });
                temp['data'] = data;
                temp['sketchPadKey'] = this.sketchPadKey;
                temp['sketchPadItem'] = this.sketchPadItem;
                temp['sketchPadGridId'] = this.sketchPadGridId;
                this.$emit('sketchPad_image', temp);
                this.$emit('close');
              }).catch(error => {
                  console.error("There was an error!", error);
              });
            }else{
              var temp = [];
              var data = [];
              $(`.sketchpad_${this.sketchPadKey}_${gridIdVal}_${itemVal}`).each(function(){
                var sketchpadImageUrl = $(this).find('input[name="sketchpad[]"]').val();
                if(sketchpadImageUrl){
                  if(sketchpadImageUrl.includes('http')){
                    data.push(sketchpadImageUrl);
                  }
                }
              });
              temp['data'] = data;
              temp['sketchPadKey'] = this.sketchPadKey;
              temp['sketchPadItem'] = this.sketchPadItem;
              temp['sketchPadGridId'] = this.sketchPadGridId;
              this.$emit('sketchPad_image', temp);
              this.$emit('close');
            }
          },
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
</style>