<template>
    <form ref="signature" @submit.prevent="submit" class="customsignatureForm">
        <transition name="modal-fade">
            <div class="modal-backdrop">
                <div class="modal" role="dialog" aria-labelledby="modalTitle" aria-describedby="modalDescription" >
                    <header class="modal-header" id="modalTitle" >
                    <h3 name="header">Signature Pad</h3>
                        <button type="button" class="btn-close" @click="close" aria-label="Close modal">x</button>
                    </header>
                    <section class="modal-body" id="modalDescription" >
                        <div style="padding-bottom:10px">
                            <div class="signaturePadAppend" style="padding: 7px;">
                            </div>
                            <div class="wrapper1">
                                <div class="form-group">
                                    <label>Name: </label>
                                    <input type="text" class="form-control signature_name" placeholder="Signature name">
                                    <small class="error" style="color:red"></small>
                                </div>
                                <label class="control-label" for="title">Signature <b style="color:red;font-size: 13px;">*</b> </label>&nbsp;&nbsp;&nbsp;
                                <label>Choose colors: </label> &nbsp;&nbsp;&nbsp;
                                <input type="color" class="signaturePadColor"> &nbsp;&nbsp;&nbsp;
                                <div style="float:right;padding:12px">
                                  <button type="button" id="signaturePadClear" >Clear</button> &nbsp;&nbsp;&nbsp;
                                  <button type="button" id="signaturePadSave" :data-template-id="signatureTempleteId" :signature_key="signatureKey">Add</button>
                                </div>
                                <br><br>
                                <canvas id="signature-pad" class="signature-pad" width="460" height="200" style="background-color:#ebebeb;" ></canvas>
                                <p style="display: none;color: red;" class="signaturePadRequired"><i>*Signature Required</i> </p>
                            </div>
                        </div>
                    </section>
                    <footer class="modal-footer">
                        <button type="submit" class="btn btn-primary" >Save</button>
                    </footer>
                </div>
            </div>
        </transition>
    </form>
</template>


<script>
    export default {
        name: 'signature',
        props:['signatureTempleteId','signatureKey','signatureItem','signatureGridId'],
        methods: {
          close() {
              this.$emit('close');
          },
           submit(){
            var dataTemplateId = $('#signaturePadSave').attr('data-template-id');
            let formData    =   new FormData();
            var formDataValueCheck = 0;
            $('.signature'+dataTemplateId).each(function(){
                if($(this).find('input[name="signature_name[]"]').val()){
                  formDataValueCheck = 1;
                  formData.append('signature_name[]',$(this).find('input[name="signature_name[]"]').val());
                  formData.append('signature_image[]',$(this).find('input[name="signature_image[]"]').val());
                  formData.append('signature_unique_count[]',$(this).find('input[name="signature_unique_count[]"]').val());
                }
            });
            if(formDataValueCheck == 1){
              axios.post(`/api/web/files/upload`, formData).then(res => {
                var temp = [];
                temp['data'] = res.data;
                temp['signatureKey'] = this.signatureKey;
                temp['signatureItem'] = this.signatureItem;
                temp['signatureGridId'] = this.signatureGridId;
                this.$emit('signature_image', temp);
                this.$emit('close');
              }).catch(error => {
                  console.error("There was an error!", error);
              });
            }else{
              alert('No signature to save');
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