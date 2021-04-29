<template>
<div>
   <pulse-loader v-bind:style="{position:['absolute'],left:['50%'],bottom:['50%']}"  :loading="loading" ></pulse-loader>
        <table class="table" id="datatable">
            <thead>
            <tr>
                <th>Title</th>
                <th>Color</th>
                <th>Icon</th>
                <th width="120">Action</th>
            </tr>
            <tr v-for="label in labels" v-bind:key="label.id">
                <td>{{label.title}}</td>
                <td>
                    <p v-bind:style="{background:[label.color], height: ['30px'], width: ['40px']}"></p>
                </td>
                <td>
                    <img v-bind:src="label.icon" height="40" width="40">
                </td>
                <td>
                    <a  @click="editLabel(label)"   data-toggle="modal" data-target="#upDateLabel"  class="btn btn-success btn-xs btn-raised"  v-bind:style="{margin:['0px 5px 0px']}"  ><i class="fa fa-eye"></i></a>
                    <a  @click="deleteLabel(label.id)"  class="btn btn-raised btn-danger btn-xs" v-bind:style="{margin:['0px', '5px', '0px']}"><span class="glyphicon glyphicon-trash templet-trash" aria-hidden="true"  /> </a>

                </td>
            </tr>

            </thead>
            <tbody>
            </tbody>
        </table>

    <div class="modal fade" id="upDateLabel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div  class="modal-dialog modal-lg" role="document">

            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close"  v-on:click="closeModal" data-dismiss="modal"   aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" ><i class="fa fa-plus"></i>&nbsp;Update Invoice Label</h4>
                </div>
                <form @submit.prevent="updateLabel">

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden"  v-model="label.id" name="id">
                            <div class="form-group " v-bind:style="{margin:['0']}">
                                <label class="control-label" >Title</label>
                                <input type="text" name="title" class="form-control" v-model="label.title" maxlength="20" >
                            </div>
                            <div class="form-group ">
                                <label class="control-label" >Color</label>
                                <div  class="form__field">
                                    <div class="form__label">
                                        <input type="text" v-model="label.color" name="color"  :class="['form-control']" />
                                    </div>
                                    <div class="form__input" v-bind:style="{position:['absolute'],right:['0'],top:['22px']}">
                                        <swatches v-model="label.color" colors="text-advanced" popover-to="left"></swatches>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group is-empty is-fileinput">
                                <input type="file" class="form-control" name="icon"   v-on:change="onImageChange">
                                <input type="text" readonly=""  class="form-control" v-model="label.icon"  placeholder="Icon">
                                <img height="100" width="100" v-if="imageShow"  class="img-responsive"  v-bind:src="label.icon"  />
                                <img  height="100" width="100"  v-if="label.url" :src="label.url" />

                                <br>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"  >Save</button>
                </div>
                </form>
            </div>

        </div>
    </div>

    <div class="modal fade" id="myModal"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div   class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close"  v-on:click="closeModalStore"  data-dismiss="modal"   aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" ><i class="fa fa-plus"></i>&nbsp;&nbsp;New Invoice Label</h4>
                </div>
                <form @submit.prevent="storeLabel">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group label-floating">
                                <label class="control-label" >Title</label>
                                <input type="text" name="title" class="form-control" v-model="label.title" maxlength="20">
                                <h5 style="color: #757575;"><b>Maximum 20 characters </b></h5>
                            </div>
                            <div class="form-group label-floating">
                                <label class="control-label" >Color</label>
                                <div  class="form__field">
                                    <div class="form__label">
                                        <input type="text" v-model="label.color" name="color"  :class="['form-control']" />
                                    </div>
                                    <div class="form__input" v-bind:style="{position:['absolute'],right:['0'],top:['-7px']}">
                                        <swatches v-model="label.color" colors="text-advanced" popover-to="left"></swatches>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group is-empty is-fileinput">
                                <input type="file" class="form-control" name="icon"   v-on:change="onImageChange">
                                <input type="text" readonly=""  class="form-control" v-model="label.icon"  placeholder="Icon">
                                <img height="100" width="100" v-if="imageShow"  class="img-responsive"  v-bind:src="label.icon"  />
                                <img  height="100" width="100"  v-if="label.url" :src="label.url" />

                                <br>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
                </form>
            </div>

        </div>
    </div>




</div>

</template>


<script>
 import axios from 'axios';
 import Swatches from 'vue-swatches'
 import "vue-swatches/dist/vue-swatches.min.css"



 export default {


     components: { Swatches
     },
        data() {
            return {
                labels: [],
                label:{
                    id:'',
                    title:'',
                    color:'#00aabb',
                    icon:'',
                    files:null,
                    url:null,
                },
                label_id : '',
                edit: false,
                imageShow: true,
                validationErrors: [],
                loading: true,
            }
        },
        created(){
            this.fetchLabels();

        },
        methods: {
            reset(){
                this.label.title = "";
                this.label.files = null;
                this.label.icon = "";
                this.label.color = "#00aabb";
                 this.label.url = null;
            },
            onImageChange(e){
                this.label.files = e.target.files[0];
                this.label.icon = e.target.files[0].name;
                this.label.url = URL.createObjectURL(e.target.files[0]);
                this.imageShow= false;

            },
            oncolorChange(e){
                console.log(e);
            },

            fetchLabels(){

                let config = {
                    header : {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                };
                axios.get(window.location.origin+'/dashboard/company/invoiceLabels',config)
                        .then(res => {
                             this.labels = res.data.data;
                             this.loading = false;
                             console.log(window.location);
                        })
            },

            deleteLabel(id) {
                let config = {
                    header : {
                        'Content-Type' : 'multipart/form-data'
                    },
                };
                if (confirm('Are You Sure?')) {
                    axios.delete(window.location.origin+`/dashboard/company/invoiceLabels/${id}`, config)
                        .then(data => {
                            this.fetchLabels();
                        })
                        .catch(err => console.log(err));
                }
            },
            editLabel(label){
                 this.label = label;
            },

            updateLabel(){
                let formData = new FormData();
                if (this.label.files==null){
                    formData.append('icon', "");
                } else {
                    formData.append('icon',this.label.files, this.label.icon);

                }
                formData.append('id', this.label.id);
                formData.append('color', this.label.color);
                formData.append('title', this.label.title);
                formData.append('_method', "put");


                let config = {
                    header : {
                        'Content-Type' : 'multipart/form-data'
                    },
                };
                axios.post(window.location.origin+`/dashboard/company/invoiceLabels/${this.label.id}`,formData,config)
                    .then(res =>{
                        $('#upDateLabel').modal('hide');
                        this.fetchLabels();
                        $("[data-dismiss=modal]").trigger({ type: "click" });
                        this.reset();
                    })

            },
            storeLabel(){
                let formData = new FormData();
                formData.append('title', this.label.title);
                formData.append('icon',this.label.files, this.label.icon);
                formData.append('color', this.label.color);
                let config = {
                    header : {
                        'Content-Type' : 'multipart/form-data'
                    }
                };
                axios.post(window.location.origin+'/dashboard/company/invoiceLabels',formData,config)
                    .then(res =>{
                        $('#myModal').modal('hide');
                        this.fetchLabels();
                        $("[data-dismiss=modal]").trigger({ type: "click" });
                        this.reset()
                    })
                    .catch(error => {
                            if (error.response.status == 422){
                                this.validationErrors = error.response.data.errors;
                            }
                    })
            },

            closeModal(e){
                this.fetchLabels();
                this.reset()
            },
            closeModalStore(e){
                this.fetchLabels();
                this.reset()
            },
        },






    };
</script>