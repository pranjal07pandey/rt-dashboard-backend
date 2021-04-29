<template>
  <div style="padding: 20px;background: #fff;min-height: 450px;">
  <form id="docketForm" class="docket" ref="docketForm" @submit.prevent="submit">
    <h4>Docket Templete</h4>
    <div class="form-group">
      <select v-model="docket_template_id" class="form-control" @change="selectedOption($event)">
        <option value="" disabled>Select docket templete</option>
        <option v-for="templete in docket_templete" :key="templete.id" :value="templete.id">{{ templete.title }}</option>
      </select>
      <span class="error docketTemplateSelect"></span>
    </div>
    <h2>Recipients</h2>
    <div class="form-group">
      <div class="row form-group">
        <div class="col-lg-2">
          <input type="radio" v-on:change="recipientType('record_time')" v-model="recipient_type" value="record_time"> <strong>Record Time Users</strong>
        </div>
        <div class="col-lg-2">
          <input type="radio" v-on:change="recipientType('custom_email_client')" v-model="recipient_type" value="custom_email_client"> <strong>Custom Email Clients</strong>
        </div>
        <div class="col-lg-2 float-right" v-if="recipient_type == 'custom_email_client'">
          <a class="highlight" @click="showModal">Send to a New Emails</a>
        </div>
      </div>
      <multiselect v-if="recipient_type == 'record_time'" v-model="selectedRecipients" :options="record_time_user" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Pick recipients from record time user" label="name" track-by="user_id">
      </multiselect>
      <multiselect v-if="recipient_type == 'custom_email_client'" v-model="selectedRecipients" :options="custom_email_client"  :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Pick recipients from custom email client" label="name" track-by="id">
      </multiselect>
      <span class="error selectedRecipientField"></span>
    </div>
    <div class="form-group row">
      <h4>Who will approve your docket?</h4>
      <input type="hidden" :name="(recipient_type == 'record_time') ? 'email_user_receivers[]' : 'rt_user_receivers[]'" >
      <input type="hidden" :name="(recipient_type == 'record_time') ? 'email_user_approvers[]' : 'rt_user_approvers[]'" >
      <div class="col-lg-12" v-for="recipient in selectedRecipients" :key="recipient.id" style="border-bottom: 1px solid;padding:5px">
        <input type="hidden" :name="(recipient_type == 'record_time') ? 'rt_user_receivers[]' : 'email_user_receivers[]'" :value="JSON.stringify(recipient)">
        <img :src="recipient.image" v-if="recipient.image" style="width:50px" /> 
        <img src="/assets/dashboard/images/logoAvatar.png" v-else style="width:50px" />&nbsp;&nbsp;&nbsp;
        <label>{{recipient.name}}</label>
        <input type="checkbox" :name="(recipient_type == 'record_time') ? 'rt_user_approvers[]' : 'email_user_approvers[]'" v-model="authorizeApprovalRecipient" :value="JSON.stringify(recipient)" style="float:right;margin-right:20px">
      </div>
    </div>
    <input type="hidden" name="docket_data[is_email]" :value="(recipient_type == 'record_time') ? false : true">
    <input type="hidden" name="docket_data[isSent]" value="false">
    <input type="hidden" name="docket_data[isValid]" value="true">
    <input type="hidden" name="docket_data[isWeb]" value="true">
    <input type="hidden" name="docket_data[isAdmin]" value="false">

    <div class="docketFieldTempelete row" style="padding:15px;display: block">
      <div v-for="(template, key) in templateData" :key="key">
        <div class="col-lg-12 templateFieldBg" v-if="template.docket_field_category_id == 1">
          <label>{{template.label}}</label>
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <input type="text" class="form-control" :value="template.default_value" v-once :name="'docket_data[docket_field_values]['+key+'][value]'" 
              :required="(template.required == 1) ? true : false " :placeholder="template.label">
            <a v-if="template.prefiller_data.hasExtraPrefiller" v-on:click="openPrefillerModal(template.id,key,template.docket_field_category_id)">Select</a>
        </div>
        <div class="col-lg-12 templateFieldBg" v-if="template.docket_field_category_id == 2">
          <label>{{template.label}}</label>
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <textarea class="form-control" :value="template.default_value" v-once :name="'docket_data[docket_field_values]['+key+'][value]'" :required="(template.required == 1) ? true : false " :placeholder="template.label"></textarea>
          <a v-on:click="openPrefillerModal(template.id,key,template.docket_field_category_id)">Select</a>
        </div>
        <div class="col-lg-12 templateFieldBg" v-if="template.docket_field_category_id == 3">
          <label>{{template.label}}</label>
           <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <input type="number" class="form-control" :value="template.default_value" v-once :name="'docket_data[docket_field_values]['+key+'][value]'" :required="(template.required == 1) ? true : false" 
          :min="template.config.min" :max="template.config.max" :placeholder="template.label">
          <a v-on:click="openPrefillerModal(template.id,key,template.docket_field_category_id)">Select</a>
        </div>
        <div class="col-lg-12 templateFieldBg" v-if="template.docket_field_category_id == 4">
          <label>{{template.label}}</label>
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <input type="text" class="form-control" :value="template.default_value" v-once :name="'docket_data[docket_field_values]['+key+'][value]'" :required="(template.required == 1) ? true : false " :placeholder="template.label">
          <a v-on:click="openPrefillerModal(template.id,key,template.docket_field_category_id)">Select</a>
        </div>
        <div class="col-lg-12 templateFieldBg" v-if="template.docket_field_category_id == 5">
          <label>{{template.label}}</label>
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][value]'" class="imageCountAppend">
          <input type="file" class="form-control" accept="image/*" multiple v-on:change="imageUpload($event,template.docket_field_category_id,key)"  :required="(template.required == 1) ? true : false ">
        </div>
        <div class="col-lg-12 templateFieldBg" v-if="template.docket_field_category_id == 6">
          <label>{{template.label}}</label>
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <input type="date" class="form-control" :name="'docket_data[docket_field_values]['+key+'][value]'" :required="(template.required == 1)?true : false" :placeholder="template.label">
        </div>
        <div class="col-lg-12 templateFieldBg" v-if="template.docket_field_category_id == 7">
          <label>{{template.label}}</label>
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <div class="unitRateChange">
            <div class="col-lg-6" v-for="(subfield, key1) in template.subField" :key="key1">
              <label>{{ subfield.label }}</label>
              <input type="text" class="form-control unitRateField_1" v-on:keyup="unitRateChange($event)" :name="'docket_data[docket_field_values]['+key+'][unit_rate_value][per_unit_rate]'" v-if="subfield.type == 1" :required="(template.required == 1)?true : false" :placeholder="subfield.label">
              <input type="text" class="form-control unitRateField_2" v-on:keyup="unitRateChange($event)" :name="'docket_data[docket_field_values]['+key+'][unit_rate_value][total_unit]'" v-if="subfield.type == 2" :required="(template.required == 1)?true : false" :placeholder="subfield.label">
            </div>
            <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][unit_rate_value][total]'" class="unit_rate_total_value_field">
            <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][value]'" class="unit_rate_total_value_field">
          </div>
          <span class="unitRateTotal"></span>
        </div>
        <div class="col-lg-12 templateFieldBg" v-if="template.docket_field_category_id == 9">
          <label>{{template.label}}</label>
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <a @click="signatureModal('template'+template.id,key)" :class="'btn btn-primary signature_key_'+key+''">Open Signature Pad</a>
        </div>
        <div class="col-lg-12 templateFieldBg" v-if="template.docket_field_category_id == 10">
          <label>{{template.label}}</label>
          <input type="text" class="form-control" :required="(template.required == 1)?true : false" :placeholder="template.label">
        </div>
        <div class="col-lg-12 templateFieldBg" v-if="template.docket_field_category_id == 12">
          <label>{{template.label}}</label>
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <input type="text" class="form-control" :value="template.default_value" v-once :name="'docket_data[docket_field_values]['+key+'][value]'" :required="(template.required == 1)?true : false" :placeholder="template.label">
        </div>
        <div class="col-lg-12 templateFieldBg" v-if="template.docket_field_category_id == 14">
          <label>{{template.label}}</label>
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <!-- <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][value]'" value="1 Image Attached"> -->
          <a @click="sketchModal('sketchPadPreview',key)" :class="'btn btn-primary sketchPad_key_'+key+''">Open SketchPad</a><br>
          <img class="sketchPadPreview" style="width:300px;display:none;">
        </div>
        <div class="col-lg-12 templateFieldBg" v-if="template.docket_field_category_id == 15">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][value]'" >
          <label>{{template.label}}</label><br>
          <a v-for="document in template.documentSubField" :key="document.id" :href="document.url" style="display:block;padding:5px" target="_blank">
            <span class="material-icons"> picture_as_pdf </span>&nbsp;&nbsp;{{ document.name }}
          </a>
        </div>
        <div class="col-lg-12 templateFieldBg" v-if="template.docket_field_category_id == 16">
          <label>{{template.label}}</label>
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <input type="text" class="form-control" :value="template.default_value" v-once :name="'docket_data[docket_field_values]['+key+'][value]'" :required="(template.required == 1)?true : false" :placeholder="template.label">
        </div>
        <div :class="'col-lg-12 templateFieldBg checkbox_label explanation_key_'+key+''" v-if="template.docket_field_category_id == 18">
          <label>{{template.label}}</label><br>
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][yes_no_value][selected_type]'" class="typeAppend">
          <div style="display:flex;margin-top:10px">
            <div v-for="templateSubField in template.subField" :key="templateSubField.id">
              <input type="radio" :name="'docket_data[docket_field_values]['+key+'][yes_no_value][selected_id]'" :value="templateSubField.id" :selected_type="templateSubField.type" 
              :id="templateSubField.id" :explanation="templateSubField.explanation" v-on:change="explanation($event,templateSubField.subDocket,key)"> &nbsp;
              <label :for="templateSubField.id"> {{templateSubField.label }} {{(templateSubField.explanation == 1) ? '(explanation)' : ''}}  </label>
            </div>
          </div>
        </div>
       
        <div class="col-lg-12 templateFieldBg" v-if="template.docket_field_category_id == 26">
          <label>{{template.label}}</label>
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][value]'" class="timeAppend">
          <input type="time" class="form-control" :value="template.default_value" v-once v-on:change="tConvert($event)" :placeholder="template.label" :required="(template.required == 1)?true : false">
        </div>
        <div class="col-lg-12 templateFieldBg" v-if="template.docket_field_category_id == 8">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <label>{{template.label}}</label><br>
          <input type="checkbox" value="1" :placeholder="template.label" :name="'docket_data[docket_field_values]['+key+'][value]'" :required="(template.required == 1)?true : false">
        </div>
        <div class="col-lg-12 templateFieldBg" v-if="template.docket_field_category_id == 24">
          <label>{{template.label}}</label>s
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <div class="row tallyableUnitRateChange">
            <div class="col-lg-6" v-for="subfield in template.subField" :key="subfield.id">
              <label>{{ subfield.label }}</label>
              <input type="hidden" v-if="subfield.type == 1" class="tallyableUnitRateField_1" :name="'docket_data[docket_field_values]['+key+'][unit_rate_value][per_unit_rate]'">
              <input type="hidden" v-if="subfield.type == 2" class="tallyableUnitRateField_2" :name="'docket_data[docket_field_values]['+key+'][unit_rate_value][total_unit]'">
              <input type="number" :class="'form-control tallyableUnitRateValue_'+subfield.type+''" v-on:keyup="tallyableUnitRateChange($event,template.id)" :placeholder="subfield.label">
              <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][unit_rate_value][total]'" class="tallyableTotal">
            </div>
          </div>
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][value]'" class="tallyableTotal">
          <span class="tallyableUnitRateTotal"></span>
        </div>
        <div class="col-lg-12 templateFieldBg" v-if="template.docket_field_category_id == 25">
           <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <label>{{template.label}}</label>
          <input type="text" class="form-control" :name="'docket_data[docket_field_values]['+key+'][value]'" :value="template.default_value" v-once :placeholder="template.label" :required="(template.required == 1)?true : false">
        </div>
        <div class="col-lg-12 templateFieldBg" v-if="template.docket_field_category_id == 29">
          <label>{{template.label}}</label>
           <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <!-- <input type="email" class="form-control" :value="template.default_value" :name="'docket_data[docket_field_values]['+key+'][value]'" :placeholder="template.label" :required="(template.required == 1)?true : false"> -->
          <vue-tags-input :class="'form-control'" @tags-changed="allTags => tagsChanged(allTags,key)" :tags="emailTags[key]"
                v-model="emailModelValue[key]" :placeholder="template.label" :required="(template.required == 1)?true : false" :requiredValue="template.required" :keyId="key"
                :docketFieldCategoryId="template.docket_field_category_id" v-on:blur="tagsBlur($event,key)" :formFieldId="template.id" style="height:auto" />
        </div>
         <div class="col-lg-12 templateFieldBg" v-if="template.docket_field_category_id == 28">
          <label>{{template.label}}</label>
          <span style="display:none">{{ folderLoop(template.folderList,template.default_value) }}</span>
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <select :name="'docket_data[docket_field_values]['+key+'][folder_value][folders][][id]'" class="form-control">
            <option :value="folderPath.id" v-for="folderPath in folderPathArray" :key="folderPath.key" :selected="(template.default_value == folderPath.id) ? true : false">
              {{  folderPath.name }}
            </option>
          </select>
        </div>
        <div :class="'col-lg-12 templateFieldBg manual_timer_'+key+''" v-if="template.docket_field_category_id == 20">
          <label>{{template.label}}</label><br>
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <a @click="manualTimer(template.subField,key)" class="btn btn-primary">Manual Timer</a>
        </div>
        <div class="col-lg-12 templateFieldBg" v-if="template.docket_field_category_id == 27">
           <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <span v-html="template.label"></span>
        </div>
        <div class="col-lg-12 templateFieldBg" v-if="template.docket_field_category_id == 13">
          <label>{{template.label}}</label><br>
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <span v-for="subfield in template.subField" :key="subfield.id"> 
            <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][value]'" :value="subfield.value">
            {{ subfield.value }}
          </span>
        </div>

         <div class="col-lg-12 templateFieldBg" v-if="template.docket_field_category_id == 22">
          <label>{{template.label}}</label>
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <a class="float-right btn btn-info" @click="cloneRow(template.id)">Add</a>
          <div style="overflow: auto;width:100%">
            <table class="table table-striped" style="text-align:center">
              <thead>
                <tr>
                  <th v-for="grid in template.modularGrid" :key="grid.id">{{ grid.label }}</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in rowCounter['counter_'+template.id]" :key="item" :class="'tableRow row_count_'+item">
                  <td v-for="grid in template.modularGrid" :key="grid.id" >
                    <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][grid_value]['+item+']['+grid.id+'][category_id]'" :value="grid.docket_field_category_id">

                    <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][grid_value]['+item+']['+grid.id+'][form_field_id]'" :value="grid.id">

                    <input type="text" v-if="grid.docket_field_category_id == 1" :name="'docket_data[docket_field_values]['+key+'][grid_value]['+item+']['+grid.id+'][value]'" 
                          class="form-control" v-model="tableInput[template.id][item - 1]['input_' +grid.id]" :placeholder="grid.label"
                          :required="(grid.required == 1)?true:false">
                    
                    <textarea class="form-control" v-if="grid.docket_field_category_id == 2" :placeholder="grid.label" style="width: 120px;height: 37px;"
                          :name="'docket_data[docket_field_values]['+key+'][grid_value]['+item+']['+grid.id+'][value]'" :required="(grid.required == 1) ? true : false" 
                          v-model="tableInput[template.id][item - 1]['input_' +grid.id]" ></textarea>
                    
                    <input type="number" v-if="grid.docket_field_category_id == 3" :name="'docket_data[docket_field_values]['+key+'][grid_value]['+item+']['+grid.id+'][value]'"
                         :class="'form-control numberFormula'" :data="template.id+'_'+item+'_'+grid.id" :placeholder="grid.label" 
                          v-model="numberFormula[template.id+'_'+item+'_'+grid.id]" :readonly="formulaCalculation(grid,item,template.id)"  :formula="grid.formula"
                          :required="(grid.required == 1)?true : false" @input="calculateTotal(template.id,grid.id)" >
                          
                    
                    <input type="text" v-if="grid.docket_field_category_id == 4" :name="'docket_data[docket_field_values]['+key+'][grid_value]['+item+']['+grid.id+'][value]'" 
                          class="form-control" v-model="tableInput[template.id][item - 1]['input_' +grid.id]" :placeholder="grid.label" 
                          :required="(grid.required == 1)?true : false">
                    
                    <input type="hidden" v-if="grid.docket_field_category_id == 5" :name="'docket_data[docket_field_values]['+key+'][grid_value]['+item+']['+grid.id+'][value]'" 
                            class="image_value">
                    
                    <input type="file" v-if="grid.docket_field_category_id == 5" multiple v-on:change="fileUpload($event,key,item,grid.id)" class="form-control" 
                            :required="(grid.required == 1)?true : false" accept="image/*">
                    
                    <input type="hidden" v-if="grid.docket_field_category_id == 6" :name="'docket_data[docket_field_values]['+key+'][grid_value]['+item+']['+grid.id+'][value]'" 
                            class="formatedDate">
                    
                    <input type="date" v-on:change="dateFormat($event)" v-if="grid.docket_field_category_id == 6" class="form-control" 
                          v-model="tableInput[template.id][item - 1]['input_' +grid.id]" :placeholder="grid.label" 
                          :required="(grid.required == 1)?true : false">
                    
                    <input type="checkbox" v-if="grid.docket_field_category_id == 8" v-model="tableInput[template.id][item - 1]['input_' +grid.id]"
                          :name="'docket_data[docket_field_values]['+key+'][grid_value]['+item+']['+grid.id+'][value]'" value="1" :required="(grid.required == 1)?true : false">
                    
                    <a @click="signatureModal('grid'+item+'_'+key+'_'+grid.id,key,item,grid.id)" :class="'btn btn-primary signature_key_'+key+'_'+item" 
                        v-if="grid.docket_field_category_id == 9">Open Signature Pad</a>
                    
                    <a @click="sketchModal('tableSketchPadPreview_'+grid.id+'_'+item,key,item,grid.id)" :class="'btn btn-primary sketchPad_key_'+key+'_'+item+'_'+grid.id" 
                        v-if="grid.docket_field_category_id == 14">Open SketchPad</a>
                    <img :class="'tableSketchPadPreview_'+grid.id+'_'+item " style="width:150px;display:none;" v-if="grid.docket_field_category_id == 14">

                    <a @click="manualTimer(grid.manualTimerSubField,key,item,grid.id)" :class="'btn btn-primary manual_timer_'+key+'_'+item+'_'+grid.id" v-if="grid.docket_field_category_id == 20">Manual Timer</a>
                    
                    <input type="text" v-if="grid.docket_field_category_id == 21" class="form-control" :name="'docket_data[docket_field_values]['+key+'][grid_value]['+item+']['+grid.id+'][value]'"
                            v-model="tableInput[template.id][item - 1]['input_' +grid.id]" :required="(grid.required == 1)?true : false" :placeholder="grid.label">
                    
                    <!-- <input type="email" v-if="grid.docket_field_category_id == 29" class="form-control" v-model="tableInput[template.id][item - 1]['input_' +grid.id]" :placeholder="grid.label" 
                            :required="(grid.required == 1)?true : false" data-role="tagsinput"> -->
                            
                    <vue-tags-input v-if="grid.docket_field_category_id == 29" :class="'form-control'"  @tags-changed="allTags => tagsChanged(allTags,'grid_'+key+'_'+item+'_'+grid.id)" 
                          v-model="emailModelValue['grid_'+key+'_'+item+'_'+grid.id]" :placeholder="grid.label" :required="(grid.required == 1)?true : false" :keyId="key" 
                          :itemId="item" :gridId="grid.id" :requiredValue="template.required" :docketFieldCategoryId="grid.docket_field_category_id" :tags="emailTags['grid_'+template.id+'_'+item+'_'+grid.id]"
                          v-on:blur="tagsBlur($event,'grid_'+key+'_'+item+'_'+grid.id)" :formFieldId="grid.id" style="height:auto" />

                    <input type="time" v-if="grid.docket_field_category_id == 26" class="form-control" v-model="tableInput[template.id][item - 1]['input_' +grid.id]"
                            v-on:change="tConvert($event,item,grid.id,template.id,template.modularGrid)" :placeholder="grid.label" :required="(grid.required == 1)?true : false">
                    <input type="hidden" v-if="grid.docket_field_category_id == 26" :name="'docket_data[docket_field_values]['+key+'][grid_value]['+item+']['+grid.id+'][value]'" :class="'timeAppend_'+item+'_'+grid.id">

                  </td>
                  <td class="deleteButton">
                    <a class="btn btn-warning btn-xs btn-raised" v-on:click="removeRow($event,template.id,template.modularGrid,item)" ><i class="fa fa-trash"></i></a>
                  </td>
                </tr>
                <tr>
                  <td v-for="grid in template.modularGrid" :key="grid.id">
                    <span v-if="grid.docket_field_category_id == 3 && grid.sumable" v-html="'total: '+gridSumable[template.id+'_'+grid.id]"></span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="row ">
      <div class="form-group col-lg-2">
        <input type="checkbox" v-on:change="draftStatus($event)" class="checkboxDraft"><strong>&nbsp;&nbsp; Save to draft</strong>
      </div>
    </div>

    <div class="draftbox" v-if="draftStatusValue == 1">
      <div class="row">
        <div class="form-group col-lg-6">
          <label>Draft Name *</label>
          <input type="text" name="docket_data[draft_name]" class="form-control" placeholder="Draft Name" required>
        </div>
      </div>
    </div>
      
    <input type="submit" :value="(draftStatusValue == 0) ? 'Send' : 'Draft'" class="btn btn-primary">
  </form>

    <modal v-show="isModalVisible" @close="closeModal" @client_email_response="newEmailClientAdded" @saved_client_email_response="newSavedEmailClientAdded" />
    <sketchpad v-show="isSketchModalVisible" :sketchPadPreviewClass="sketchPadPreviewClass" :sketchPadKey="sketchPadKey" 
                :sketchPadItem="sketchPadItem" :sketchPadGridId="sketchPadGridId" @close="closeModal" @sketchPad_image="sketchPadImageUrl">
    </sketchpad>
    <signature v-show="isSignatureVisible" :signatureTempleteId="signatureTempleteId"  :signatureKey="signatureKey" :signatureItem="signatureItem" 
              :signatureGridId="signatureGridId" @close="closeModal" @signature_image="signatureImageUrl"></signature>
    <explanation v-show="isExplanationVisible" :explanations="explanationSubDocket" :explanationKey="explanationKey" @close="closeModal" 
                @explanationData="explanationValue"></explanation>
    <manualTimer v-show="isManualTimerVisible" @close="closeModal" :manualTimerFields="manualTimerSubField" :manualTimerKey="manualTimerKey"
                :manualTimerItem="manualTimerItem" :manualTimerGridId="manualTimerGridId" @manualTimerData="manualTimerValue"></manualTimer>
    <prefillerModal v-show="isPrefillerModalVisible" @close="closeModal" :prefillerData="prefillerData" :prefillerOtherData="prefillerOtherData" @selectedPrefillerData="selectedPrefillerData"></prefillerModal> 
  </div>
</template>
<script>
  import Multiselect from 'vue-multiselect';
  import modal from './email_modal';
  import sketchpad from './sketchpad';
  import signature from './signature';
  import explanation from './explanation';
  import manualTimer from './manualTimer';
  import prefillerModal from './prefillerModal';
  import axios from 'axios';
  import Vue from 'vue';
  import VueTagsInput from '@johmun/vue-tags-input';

  export default {
    components: {
       Multiselect,modal,sketchpad,signature,explanation,manualTimer,VueTagsInput,prefillerModal
    },
    props:['docket_templete','custom_email_client','record_time_user','template_id','addition_data'],
    data(){
        return{
            docketTempele: [],
            docket_template_id: '',
            search_key : '',
            index: 0,
            defaultTableObject:[],
            tableInput: [],
            recipient_type : 'record_time',
            isModalVisible: false,
            isSketchModalVisible: false,
            isSignatureVisible: false,
            isExplanationVisible: false,
            isManualTimerVisible: false,
            isPrefillerModalVisible: false,
            selectedRecipients : [],
            authorizeApprovalRecipient: [],
            templateData:[],
            templateDB:'',
            numberFormula:[],
            gridSumable:[],
            sketchPadPreviewClass:'',
            explanationSubDocket: [],
            manualTimerSubField:[],
            rowCounter:{},
            subfieldInput:[],
            girdTableFormula:[],
            signatureTempleteId:"",
            folderPathArray:[],
            pathTemp:'',
            signatureKey:'',
            signatureItem:'',
            signatureGridId:'',
            sketchPadKey:'',
            sketchPadItem:'',
            sketchPadGridId:'',
            explanationKey:'',
            manualTimerKey:'',
            manualTimerItem:'',
            manualTimerGridId:'',
            emailFieldValue:[],
            emailModelValue:[],
            draftStatusValue:0,
            folderLoopCount: 0,
            docket_field_values: [
                  {'category_id':0, 'form_field_id': 0, 'value':''}
              ],
            itemRemoved:[],
            emailTags:[],
            prefillerData:[],
            prefillerOtherData: [],
        }
    },
    computed:{
      
    },
    mounted(){
      this.custom_email_client.map(function (x){
        x.user_id = x.id;
        return x.name = x.email + ' ('+ x.full_name +')';
      });
      this.record_time_user.map(function (x){
        let company_name='', company_abn='';
        if(x.company_name != null){
          company_name = ' (' + x.company_name + ')';
        }
        if(x.company_abn != null){
          company_abn = ' (' + x.company_abn + ')';
        }
        return x.name = x.first_name + x.last_name + company_name + company_abn;
      });
      if(this.template_id){
        this.docket_template_id = this.template_id.toString();
        this.selectedOption(null,this.docket_template_id);
        $('input[name="docket_data[isAdmin]"]').val('true');
        this.draftStatusValue = 1;
        $('.checkboxDraft').prop('checked',true);
      }

      console.log(this.record_time_user);
    },
    methods:{
      selectedOption(event,value = null){
          axios.post(`/api/web/docket/fields/${(event) ? event.target.value : value}`).then(res =>{
            const responseData = res.data.template.docket_field;
            const girdTable = responseData.filter(element => element.docket_field_category_id == 22);
            this.girdTableFormula = girdTable;
            this.tableInput = [];
            this.rowCounter = {};
            girdTable.forEach(element => {
              this.rowCounter['counter_'+element.id] = 1;
              const tableData = element.modularGrid;
              const mailTemplateId = element.id;
              let defaultObject = {};
              
              tableData.forEach((element,index) => {
                if(element.prefiller_data.autoPrefiller == 1){
                  var loopPreFiller = element.prefiller_data.prefiller;
                  for (let index1 = 0; index1 < 1; index1--) {
                  var defaultValue = '';
                    for (let index2 = 0; index2 < loopPreFiller.length; index2++) {
                      defaultValue += loopPreFiller[index2].value + ',';
                    }
                    if(loopPreFiller.length > 0){
                      var docket_field_category_id_filter = '';
                      tableData.filter(x => {
                        if(x.id == loopPreFiller[0].link_grid_field_id){
                          docket_field_category_id_filter = x.docket_field_category_id;
                          return x;
                        }
                      });
                      if(docket_field_category_id_filter == 3){
                        this.numberFormula[mailTemplateId+'_'+this.rowCounter['counter_'+mailTemplateId]+'_'+loopPreFiller[0].link_grid_field_id] = parseInt(defaultValue.slice(0, -1));
                      }else{
                        defaultObject['input_' + loopPreFiller[0].link_grid_field_id] = defaultValue.slice(0, -1);
                        defaultObject['formula_'+ loopPreFiller[0].link_grid_field_id] = element.formula;
                      }
                      loopPreFiller = loopPreFiller[0].prefiller;
                    }
                    
                    if(loopPreFiller.length <= 0){
                      break;
                    }
                  }
                }else{
                  if(element.docket_field_category_id == 3){
                    if(!this.numberFormula.hasOwnProperty(mailTemplateId+'_'+this.rowCounter['counter_'+mailTemplateId]+'_'+element.id)){
                      this.numberFormula[mailTemplateId+'_'+this.rowCounter['counter_'+mailTemplateId]+'_'+element.id] = parseInt(element.default_value);
                    }
                  }
                  if(!defaultObject.hasOwnProperty('input_' + element.id)){
                    defaultObject['input_' + element.id] = element.default_value;
                    defaultObject['formula_'+ element.id] = element.formula;
                  }
                }
              });
              
              this.defaultTableObject[element.id] = defaultObject;
              this.tableInput[element.id]= [];
              this.tableInput[element.id].push({ ... this.defaultTableObject[element.id] });
              // this.rowCounter['counter_'+element.id] = 1;
            });

            responseData.forEach((element,key1) => {
              if(element.docket_field_category_id == 29){
                this.emailTags[key1] = [];
                this.emailFieldValue[key1] = [];
                var emails = element.default_value.split(',');
                emails.forEach(email => {
                  this.emailTags[key1].push({text: email});
                  this.emailFieldValue[key1].push({text: email});
                });
              }
              if(element.docket_field_category_id == 22){
                element.modularGrid.forEach((element1,index) => {
                    if(element1.docket_field_category_id == 29){
                      this.emailTags['grid_'+element.id+'_'+this.rowCounter['counter_'+element.id]+'_'+element1.id] = [];
                      this.emailFieldValue['grid_'+element.id+'_'+this.rowCounter['counter_'+element.id]+'_'+element1.id] = [];
                      var emails = element1.default_value.split(',');
                      emails.forEach(email => {
                        this.emailTags['grid_'+element.id+'_'+this.rowCounter['counter_'+element.id]+'_'+element1.id].push({text: email});
                        this.emailFieldValue['grid_'+element.id+'_'+this.rowCounter['counter_'+element.id]+'_'+element1.id].push({text: email});
                      });
                    }
                  });
                }
            });

            this.templateDB = res.data.template;
            this.templateData = res.data.template.docket_field;
            console.log(this.templateData);

            if(this.template_id){
              setTimeout(() => {
                $('input').attr('required', false);
              }, 5000);
            }

          }).catch(error => {
              console.error("There was an error!", error);
          });
      },
      tagsBlur(event,keyy){
        var tags = this.emailFieldValue[keyy];
        if(tags.length < 1){
          if($(event.target).attr('requiredValue') == 1){
            $(event.target).attr('required','required');
          }
        }else{
          $(event.target).removeAttr('required');
        }
        var key = $(event.target).attr('keyId');
        var item = $(event.target).attr('itemId');
        var gridId = $(event.target).attr('gridId');
        var docketFieldCategoryId = $(event.target).attr('docketFieldCategoryId');
        var formFieldId = $(event.target).attr('formFieldId');
        if(item){
          $(event.target).closest('td').find('.email_field_append').remove();
        }else{
          $(event.target).closest('.templateFieldBg').find('.email_field_append').remove();
        }
        var thisEvent = event.target;
        for (let index = 0; index < tags.length; index++) {
          if(item){
            $(event.target).closest('td').append(`
              <div class="email_field_append">
                <input type="hidden" name="docket_data[docket_field_values][${key}][grid_value][${item}][${gridId}][category_id]" value="${docketFieldCategoryId}">
                <input type="hidden" name="docket_data[docket_field_values][${key}][grid_value][${item}][${gridId}][form_field_id]" value="${formFieldId}">
                <input type="hidden" name="docket_data[docket_field_values][${key}][grid_value][${item}][${gridId}][email_list_value][email_list][${index}][email]" value="${tags[index].text}">
                <input type="hidden" name="docket_data[docket_field_values][${key}][grid_value][${item}][${gridId}][email_list_value][email_list][${index}][send_copy]" value="true">
              </div>`);
          }else{
            $(thisEvent).closest('.templateFieldBg').append(`
            <div class="email_field_append">
              <input type="hidden" name="docket_data[docket_field_values][${key}][category_id]" value="${docketFieldCategoryId}">
              <input type="hidden" name="docket_data[docket_field_values][${key}][form_field_id]" value="${formFieldId}">
              <input type="hidden" name="docket_data[docket_field_values][${key}][email_list_value][email_list][${index}][email]" value="${tags[index].text}">
              <input type="hidden" name="docket_data[docket_field_values][${key}][email_list_value][email_list][${index}][send_copy]" value="true">
            </div>`);
          }
        }
      },
      tagsChanged(tags,keyy=null){
        this.emailFieldValue[keyy] = tags;
      },
      dateFormat(event){
        var formatedDate = moment(event.target.value).format('D-MMM-YYYY');
        $(event.target).closest('td').find('.formatedDate').val(formatedDate);
      },
      tConvert (event,item=null,gridId=null,templateId = null,grid = null) {
        var time = event.target.value;
        // Check correct time format and split into components
        time = time.toString ().match (/^([01]\d|2[0-3])(:)([0-5]\d)(:[0-5]\d)?$/) || [time];

        if (time.length > 1) { // If time format correct
          time = time.slice (1);  // Remove full string match value
          time[5] = +time[0] < 12 ? ' AM' : ' PM'; // Set AM/PM
          time[0] = +time[0] % 12 || 12; // Adjust hours
        }
        if(item){
          $(event.target).closest('td').find('.timeAppend_'+item+'_'+gridId).val(time.join (''));
        }else{
          $(event.target).closest('div').find('.timeAppend').val(time.join (''));
        }

        if(item){
          var gridFilteredData = grid.filter(x=>x.docket_field_category_id == 3);

          for (let index = 0; index < gridFilteredData.length; index++) {
            if(gridFilteredData[index].formula.length > 0){
              var secondDiff='';
              gridFilteredData[index].formula.forEach(element => {
                if(element.type == "function"){
                  var start_pos = element.value.indexOf('(') + 1;
                  var end_pos = element.value.indexOf(',',start_pos);
                  var endTimeCellId = element.value.substring(start_pos,end_pos).replace('cell','');
                  var endTime = this.tableInput[templateId][item - 1]['input_' +endTimeCellId];

                  var start_pos = element.value.indexOf(',') + 1;
                  var end_pos = element.value.indexOf(')',start_pos);
                  var startTimeCellId = element.value.substring(start_pos,end_pos).replace('cell','');
                  var startTime = this.tableInput[templateId][item - 1]['input_' +startTimeCellId];

                  if(endTime && startTime){
                    var timeStart = new Date("01/01/2007 " + startTime).getTime() / 1000;
                    var timeEnd = new Date("01/01/2007 " + endTime).getTime() / 1000;

                    secondDiff = timeEnd - timeStart;   
                  }
                }
              });
              if(secondDiff){

                delete this.numberFormula[templateId+'_'+item+'_'+gridFilteredData[index].id];
                this.$set(this.numberFormula,templateId+'_'+item+'_'+gridFilteredData[index].id,secondDiff);

                var total = 0;
                for (let index1 = 1; index1 <= this.rowCounter['counter_'+templateId]; index1++) {
                  if(this.numberFormula[templateId+'_'+index1+'_'+gridFilteredData[index].id] && !this.itemRemoved.includes(index1)){
                    total = total + Number(this.numberFormula[templateId+'_'+index1+'_'+gridFilteredData[index].id]);
                  }
                }
                
                this.gridSumable[templateId+'_'+gridFilteredData[index].id] = total;
              }
            }
          }
        }
      },
      fileUpload(event,key,item,gridId){
        let formData    =   new FormData();
        var fileLength = (event.srcElement || event.target).files.length;
        for (let index = 0; index < fileLength; index++) {
          formData.append('image_file[]',(event.srcElement || event.target).files[index]);
        }
        axios.post(`/api/web/files/upload`, formData).then(res =>{
          $(`.image_url_${item}_${gridId}`).remove();
          for (let index = 0; index < res.data.length; index++) {
            $(event.target).closest('td').append(`
            <div class="image_url_${item}_${gridId}">
              <input type="hidden" name="docket_data[docket_field_values][${key}][grid_value][${item}][${gridId}][image_value][]" value="${res.data[index]}">
            </div>`);
          }
          
          $(event.target).closest('td').find('.image_value').val(fileLength + ' Image Attached');
        }).catch(error => {
            console.error("There was an error!", error);
        });
      },
      recipientType(type){
        this.authorizeApprovalRecipient = [];
        this.selectedRecipients = [];
        this.recipient_type = type;
      },
      showModal() {
        this.isModalVisible = true;
      },
      closeModal() {
        this.isModalVisible = false;
        this.isSketchModalVisible = false;
        this.isSignatureVisible = false;
        this.isExplanationVisible = false;
        this.isManualTimerVisible = false;
        this.isPrefillerModalVisible = false;
      },
      signatureImageUrl(value){
        if(value.signatureItem){
          $(`.signature_key_append_${value.signatureKey}_${value.signatureItem}`).remove();
          $(`.signature_key_${value.signatureKey}`).closest('td').append(`
          <input type="hidden" name="docket_data[docket_field_values][${value.signatureKey}][grid_value][${value.signatureItem}][${value.signatureGridId}][value]" value="${value.data.length} Image Attached">`);
        }else{
          $('.signature_key_append_'+value.signatureKey).remove();
          $(`.signature_key_${value.signatureKey}`).closest('div').append(`
          <input type="hidden" name="docket_data[docket_field_values][${value.signatureKey}][value]" value="${value.data.length} Image Attached">`);
        }
        for (let index = 0; index < value.data.length; index++) {
          if(value.signatureItem){
            $(`.signature_key_${value.signatureKey}_${value.signatureItem}`).closest('td').append(`
            <div class="signature_key_append_${value.signatureKey}_${value.signatureItem} unique_signature_${value.data[index].signature_unique_count}">
              <input type="hidden" name="docket_data[docket_field_values][${value.signatureKey}][grid_value][${value.signatureItem}][${value.signatureGridId}][signature_value][${index}][image]" value="${value.data[index].url}">
              <input type="hidden" name="docket_data[docket_field_values][${value.signatureKey}][grid_value][${value.signatureItem}][${value.signatureGridId}][signature_value][${index}][name]" value="${value.data[index].name}">
            </div>`);
          }else{
            $(`.signature_key_${value.signatureKey}`).closest('div').append(`
            <div class="signature_key_append_${value.signatureKey} unique_signature_${value.data[index].signature_unique_count}">
              <input type="hidden" name="docket_data[docket_field_values][${value.signatureKey}][signature_value][${index}][image]" value="${value.data[index].url}">
              <input type="hidden" name="docket_data[docket_field_values][${value.signatureKey}][signature_value][${index}][name]" value="${value.data[index].name}">
            </div>`);
          }
        }
      },
      sketchPadImageUrl(value){
        if(value.sketchPadItem){
          $(`.sketch_pad_append_${value.sketchPadKey}_${value.sketchPadItem}_${value.sketchPadGridId}`).remove();
        }else{
          $('.sketch_pad_append_'+value.sketchPadKey).remove();
        }
        
        if(value.sketchPadItem){
          var imageValue = '';
          for (let index1 = 0; index1 < value.data.length; index1++) {
            imageValue += `<input type="hidden" name="docket_data[docket_field_values][${value.sketchPadKey}][grid_value][${value.sketchPadItem}][${value.sketchPadGridId }][image_value][]" value="${value.data[index1]}">`;
          }
          $('.sketchPad_key_'+value.sketchPadKey+'_'+value.sketchPadItem+'_'+value.sketchPadGridId).closest('td').append(`
            <div class="sketch_pad_append_${value.sketchPadKey}_${value.sketchPadItem}_${value.sketchPadGridId}">
              ${imageValue}
              <input type="hidden" name="docket_data[docket_field_values][${value.sketchPadKey}][grid_value][${value.sketchPadItem}][${value.sketchPadGridId }][value]" value="${value.data.length} Image Attached">
            </div>`);
        }else{
          var imageValue = '';
          for (let index1 = 0; index1 < value.data.length; index1++) {
            imageValue += `<input type="hidden" name="docket_data[docket_field_values][${value.sketchPadKey}][image_value][]" value="${value.data[index1]}">`;
          }
          $('.sketchPad_key_'+value.sketchPadKey).closest('div').append(`
            <div class="sketch_pad_append_${value.sketchPadKey}">
              ${imageValue}
              <input type="hidden" name="docket_data[docket_field_values][${value.sketchPadKey}][value]" value="${value.data.length} Image Attached">
            </div>`);
        }
      },
      imageUpload(event,docket_field_category_id,key){
        let file    =    new FormData();
        file.append('docket_field_category_id', docket_field_category_id);
        for (var x = 0; x < event.target.files.length; x++) {
            file.append('file[]', event.target.files[x]); 
        }
        
        axios.post(`/api/web/files/upload`, file).then(res =>{
            $(event.target).closest('div').find('.imageValue').remove();
            for (let index = 0; index < res.data.length; index++) {
              $(event.target).closest('div').append(`<input type="hidden" name="docket_data[docket_field_values][${key}][image_value][]" value="${res.data[index]}" class="imageValue">`);
            }
            $(event.target).closest('div').find('.imageCountAppend').val(res.data.length);
        }).catch(error => {
            console.error("There was an error!", error);
        });
      },
      folderLoop(folderList,deafultValue){
        if(this.folderLoopCount == 0){
          var folderPath = [];
          var path = [];
          this.folderPathArray = [];
          for (let index = 0; index < folderList.length; index++) {
            folderPath = this.folders(folderList[index],null);
            path.push(folderPath);
          }

          var i = 0;
          for (let index = 0; index < path.length; index++) {
            for (let index1 = 0; index1 < path[index].length; index1++) {
              this.folderPathArray[i] =  path[index][index1];
              i++;
            }
          }
          this.folderLoopCount = 1;
        }
      },
      folders(folder,rootDetail){
        var temp = [];
        temp['id'] = folder.id;
        temp['name'] = folder.name;
        if(!rootDetail){
          rootDetail = folder.name;
        }else {
          rootDetail += `/${folder.name}`;
          temp['name'] = rootDetail;
        }
        if(folder.folder.length > 0){
          return [temp , ...this.folders(folder.folder[0],rootDetail)];
        }
        return [temp];
      },
      sketchModal(className,key,item=null,gridId=null){
        var itemVal = undefined;
        var gridVal = undefined;
        if(item){
          itemVal = item;
        }
        if(gridId){
          gridVal = gridId;
        }
        $('.appendedSketchPadImage').hide();
        $(`.sketchpad_${key}_${gridVal}_${itemVal}`).show();
        this.isSketchModalVisible = true;
        this.sketchPadPreviewClass = className;
        this.sketchPadKey =  key;
        this.sketchPadItem = item;
        this.sketchPadGridId = gridId;
      },
      signatureModal(id,key,item = null,gridId = null){
        $('.appendedImage').hide();
        $('.signature'+id).show();
        this.isSignatureVisible = true;
        this.signatureTempleteId = id;
        this.signatureKey = key;
        this.signatureItem = item;
        this.signatureGridId = gridId;
      },
      manualTimerValue(value){
        if(value.manualTimerItem){
            $('.manual_timer_append_'+value.manualTimerKey+'_'+value.manualTimerItem+'_'+value.manualTimerGridId).remove(); 
        }else{
            $('.manual_timer_append_'+value.manualTimerKey).remove();
        }
        
        if(value.manualTimerItem){
            $('.manual_timer_'+value.manualTimerKey+'_'+value.manualTimerItem+'_'+value.manualTimerGridId).closest('td').append(`
              <div class="manual_timer_append_${value.manualTimerKey}_${value.manualTimerItem}_${value.manualTimerGridId}">
                <input type="hidden" name="docket_data[docket_field_values][${value.manualTimerKey}][grid_value][${value.manualTimerItem}][${value.manualTimerGridId}][manual_timer_value][explanation]" value="${value.explanation}">
                <input type="hidden" name="docket_data[docket_field_values][${value.manualTimerKey}][grid_value][${value.manualTimerItem}][${value.manualTimerGridId}][manual_timer_value][from]" value="${value.from}">
                <input type="hidden" name="docket_data[docket_field_values][${value.manualTimerKey}][grid_value][${value.manualTimerItem}][${value.manualTimerGridId}][manual_timer_value][to]" value="${value.to}">
                <input type="hidden" name="docket_data[docket_field_values][${value.manualTimerKey}][grid_value][${value.manualTimerItem}][${value.manualTimerGridId}][manual_timer_value][totalDuration]" value="${value.totalDuration}">
                <input type="hidden" name="docket_data[docket_field_values][${value.manualTimerKey}][grid_value][${value.manualTimerItem}][${value.manualTimerGridId}][manual_timer_value][breakDuration]" value="${value.total_break}">
                <input type="hidden" name="docket_data[docket_field_values][${value.manualTimerKey}][grid_value][${value.manualTimerItem}][${value.manualTimerGridId}][value]" value="${value.totalDuration}">
              </div>`);
        }else{
            $('.manual_timer_'+value.manualTimerKey).append(`
              <div class="manual_timer_append_${value.manualTimerKey}">
                <input type="hidden" name="docket_data[docket_field_values][${value.manualTimerKey}][manual_timer_value][explanation]" value="${value.explanation}">
                <input type="hidden" name="docket_data[docket_field_values][${value.manualTimerKey}][manual_timer_value][from]" value="${value.from}">
                <input type="hidden" name="docket_data[docket_field_values][${value.manualTimerKey}][manual_timer_value][to]" value="${value.to}">
                <input type="hidden" name="docket_data[docket_field_values][${value.manualTimerKey}][manual_timer_value][totalDuration]" value="${value.totalDuration}">
                <input type="hidden" name="docket_data[docket_field_values][${value.manualTimerKey}][manual_timer_value][breakDuration]" value="${value.total_break}">
                <input type="hidden" name="docket_data[docket_field_values][${value.manualTimerKey}][value]" value="${value.totalDuration}">
              </div>`);
          }
      },
      manualTimer(manualTimerSubField,key,item=null,gridId=null){
        this.manualTimerSubField = manualTimerSubField;
        this.isManualTimerVisible = true;
        this.manualTimerKey = key;
        this.manualTimerItem = item;
        this.manualTimerGridId = gridId;
      },
      cloneRow(templateId){
        this.tableInput[templateId] = [...this.tableInput[templateId] , { ... this.defaultTableObject[templateId] }];
        this.rowCounter['counter_'+templateId] = this.rowCounter['counter_'+templateId] + 1;
        const newCounterArray = {... this.rowCounter};
        this.rowCounter = {};
        this.rowCounter = {...newCounterArray};

        const girdTable = this.templateData.filter(element => element.docket_field_category_id == 22);
        girdTable.forEach(element => {
          const tableData = element.modularGrid;
          const mailTemplateId = element.id;
          tableData.forEach((element,index) => {
              if(element.prefiller_data.autoPrefiller == 1){
                var loopPreFiller = element.prefiller_data.prefiller;
                for (let index1 = 0; index1 < 1; index1--) {
                var defaultValue = '';
                for (let index2 = 0; index2 < loopPreFiller.length; index2++) {
                    defaultValue += loopPreFiller[index2].value + ',';
                  }
                  if(loopPreFiller.length > 0){
                    var docket_field_category_id_filter = '';
                    tableData.filter(x => {
                      if(x.id == loopPreFiller[0].link_grid_field_id){
                        docket_field_category_id_filter = x.docket_field_category_id;
                        return x;
                      }
                    });
                    if(docket_field_category_id_filter == 3){
                      this.numberFormula[mailTemplateId+'_'+this.rowCounter['counter_'+mailTemplateId]+'_'+loopPreFiller[0].link_grid_field_id] = parseInt(defaultValue.slice(0, -1));
                    }
                    loopPreFiller = loopPreFiller[0].prefiller;
                  }
                  
                  if(loopPreFiller.length <= 0){
                    break;
                  }
                }
              }else{
                if(element.docket_field_category_id == 3){
                  if(!this.numberFormula.hasOwnProperty(mailTemplateId+'_'+this.rowCounter['counter_'+mailTemplateId]+'_'+element.id)){
                    this.numberFormula[mailTemplateId+'_'+this.rowCounter['counter_'+mailTemplateId]+'_'+element.id] = parseInt(element.default_value);
                  }
                }
              }
              // for email
              if(element.docket_field_category_id == 29){
                this.emailTags['grid_'+mailTemplateId+'_'+this.rowCounter['counter_'+mailTemplateId]+'_'+element.id] = [];
                this.emailFieldValue['grid_'+mailTemplateId+'_'+this.rowCounter['counter_'+mailTemplateId]+'_'+element.id] = [];
                var emails = element.default_value.split(',');
                console.log(emails);
                emails.forEach(email => {
                  this.emailTags['grid_'+mailTemplateId+'_'+this.rowCounter['counter_'+mailTemplateId]+'_'+element.id].push({text: email});
                  this.emailFieldValue['grid_'+mailTemplateId+'_'+this.rowCounter['counter_'+mailTemplateId]+'_'+element.id].push({text: email});
                });
              }
          });
        });
      },
      newEmailClientAdded(value){
        var data = { "id": value.id, "name": value.email }
        this.custom_email_client.push(data);
        this.selectedRecipients.push(data);
      },
      explanationValue(value){
        $('.explanation_key_append_'+value.explanation_key).remove();
        for (let index = 0; index < value.length; index++) {
          if(value[index].category_id == 5){
            var imageValue = '';
            for (let index1 = 0; index1 < value[index].value.length; index1++) {
              imageValue += `<input type="hidden" name="docket_data[docket_field_values][${value.explanation_key}][yes_no_value][explanation][${index}][image_value][]" value="${value[index].value[index1]}">`;
            }
            $('.explanation_key_'+value.explanation_key).append(`
            <div class="explanation_key_append_${value.explanation_key}">
              <input type="hidden" name="docket_data[docket_field_values][${value.explanation_key}][yes_no_value][explanation][${index}][category_id]" value="${value[index].category_id}">
              <input type="hidden" name="docket_data[docket_field_values][${value.explanation_key}][yes_no_value][explanation][${index}][form_field_id]" value="${value[index].form_field_id}">
              ${imageValue}
              <input type="hidden" name="docket_data[docket_field_values][${value.explanation_key}][yes_no_value][explanation][${index}][value]" value="${value[index].value.length} image attached">
            </div>`);
          }else{
            $('.explanation_key_'+value.explanation_key).append(`
            <div class="explanation_key_append_${value.explanation_key}">
              <input type="hidden" name="docket_data[docket_field_values][${value.explanation_key}][yes_no_value][explanation][${index}][category_id]" value="${value[index].category_id}">
              <input type="hidden" name="docket_data[docket_field_values][${value.explanation_key}][yes_no_value][explanation][${index}][form_field_id]" value="${value[index].form_field_id}">
              <input type="hidden" name="docket_data[docket_field_values][${value.explanation_key}][yes_no_value][explanation][${index}][value]" value="${value[index].value}">
            </div>`);
          }
        }
      },
      newSavedEmailClientAdded(value){
        value.emailClient['user_id'] = value.emailClient.id;
        value.emailClient['name'] = value.emailClient.email + ' ('+ value.emailClient.full_name +')';
        this.custom_email_client.push(value.emailClient);
        this.selectedRecipients.push(value.emailClient);
      },
      submit(){
        $('.docketTemplateSelect').html('');
        $('.selectedRecipientField').html('');

        if(!this.templateDB){
          $('.docketTemplateSelect').html('Please select the docket template.')
          return;
        }
        if(this.selectedRecipients < 1){
          $('.selectedRecipientField').html('Please select the recipients');
          return;
        }

        let formData    =    new FormData(this.$refs.docketForm);
        formData.append('addition_data',JSON.stringify(this.addition_data));
        if(this.authorizeApprovalRecipient.length < 1){
          alert('Select one authorized approval recipient.');
          return;
        }

        formData.append('docket_data[email_subject]','Email Subject Here');

        formData.append('template',JSON.stringify(this.templateDB));
        var currentdate = new Date();
        var formatedDate = moment(currentdate).format('D-MMM-YYYY h:m a');
        formData.append('docket_data[draft_date]',formatedDate);

        axios.post(`/api/web/docket/sent`, formData).then(res =>{
            if(res.status == 200){
              alert(res.data.message);
              window.location.reload();
            }
        }).catch(error => {
            console.error("There was an error!", error);
        });
      },
      explanation(event,subDocket,key){
        $('.explanation_key_append_'+key).remove();
        $('.explanation_key_'+key).append(`
          <div class="explanation_key_append_${key}">
            <input type="hidden" name="docket_data[docket_field_values][${key}][yes_no_value][explanation][]" value="">
          </div>`);
          console.log('hit hit');
        if(subDocket.length > 0){
          this.explanationSubDocket = subDocket;
          this.isExplanationVisible = true;
          this.explanationKey = key;
          var selected_type = $(event.target).attr('selected_type');
          $(event.target).closest('.templateFieldBg').find('.typeAppend').val(selected_type);
        }
      },
      formulaCalculation(gridNumber,index,template_id){
        if(gridNumber.formula.length > 0){
          var value='';
          var secondDiff='';
          gridNumber.formula.forEach(element => {
            if(element.type == 'cell'){
              var numberFormula = 0;
              if(this.numberFormula[template_id+'_'+index+'_'+element.value]){
                numberFormula = this.numberFormula[template_id+'_'+index+'_'+element.value];
              }
              value += numberFormula;
            }else if(element.type == "function"){
                var start_pos = element.value.indexOf('(') + 1;
                var end_pos = element.value.indexOf(',',start_pos);
                var endTimeCellId = element.value.substring(start_pos,end_pos).replace('cell','');
                var endTime = this.tableInput[template_id][index - 1]['input_' +endTimeCellId];

                var start_pos = element.value.indexOf(',') + 1;
                var end_pos = element.value.indexOf(')',start_pos);
                var startTimeCellId = element.value.substring(start_pos,end_pos).replace('cell','');
                var startTime = this.tableInput[template_id][index - 1]['input_' +startTimeCellId];

                if(endTime && startTime){
                  var timeStart = new Date("01/01/2007 " + startTime).getTime() / 1000;
                  var timeEnd = new Date("01/01/2007 " + endTime).getTime() / 1000;

                  secondDiff = timeEnd - timeStart;   
                }
            }else{
              value += element.value;
            }
          });

          if(secondDiff){
            // delete this.numberFormula[template_id+'_'+index+'_'+gridNumber.id];
            // this.$set(this.numberFormula,template_id+'_'+index+'_'+gridNumber.id,secondDiff);
            // this.numberFormula[template_id+'_'+index+'_'+gridNumber.id] = secondDiff;
          }else{
            this.numberFormula[template_id+'_'+index+'_'+gridNumber.id] = eval(value);
          }


          var total = 0;
          for (let index1 = 1; index1 <= this.rowCounter['counter_'+template_id]; index1++) {
            if(this.numberFormula[template_id+'_'+index1+'_'+gridNumber.id] && !this.itemRemoved.includes(index1)){
              total = total + Number(this.numberFormula[template_id+'_'+index1+'_'+gridNumber.id]);
            }
          }
          this.gridSumable[template_id+'_'+gridNumber.id] = total;

          return 'readonly';
        }
      },
      tallyableUnitRateChange(event,templeteId){
        var fieldType1 = $(event.target).closest('.tallyableUnitRateChange').find('.tallyableUnitRateValue_1').val();
        var fieldType2 = $(event.target).closest('.tallyableUnitRateChange').find('.tallyableUnitRateValue_2').val();
        const type1 = (fieldType1 == undefined) ? 0 : fieldType1;
        const type2 = (fieldType2 == undefined) ? 0 : fieldType2;
        const value = Number(type1) * Number(type2);
        $(event.target).closest('.templateFieldBg').find('.tallyableUnitRateTotal').html("Total: $"+value);
        $(event.target).closest('.templateFieldBg').find('.tallyableTotal').val(value);
        
        $(event.target).closest('.tallyableUnitRateChange').find('.tallyableUnitRateField_1').val(Number(type1));
        $(event.target).closest('.tallyableUnitRateChange').find('.tallyableUnitRateField_2').val(Number(type2));

        // const type1 = (this.subfieldInput[templeteId+'_1'] == undefined) ? 0 : this.subfieldInput[templeteId+'_1'];
        // const type2 = (this.subfieldInput[templeteId+'_2'] == undefined) ? 0 : this.subfieldInput[templeteId+'_2'];
        // const value = Number(type1) * Number(type2);
        // $(event.target).closest('.templateFieldBg').find('.tallyableUnitRateTotal').html("Total: $"+value);
        // $(event.target).closest('.templateFieldBg').find('.tallyableTotal').val(value);
      },
      unitRateChange(event){
        var fieldType1 = $(event.target).closest('.unitRateChange').find('.unitRateField_1').val();
        var fieldType2 = $(event.target).closest('.unitRateChange').find('.unitRateField_2').val();
        const type1 = (fieldType1 == undefined) ? 0 : fieldType1;
        const type2 = (fieldType2 == undefined) ? 0 : fieldType2;
        const value = Number(type1) * Number(type2);
        $(event.target).closest('.templateFieldBg').find('.unitRateTotal').html("Total: $"+value);
        $(event.target).closest('.templateFieldBg').find('.unit_rate_total_value_field').val(value);
      },
      draftStatus(event){
        if(event.target.checked){
          this.draftStatusValue = 1;
          $('.draftbox').show();
        }else{
          this.draftStatusValue = 0;
          $('.draftbox').hide();
        }
        
      },
      calculateTotal(templateId,gridId){
        var total = 0;
        for (let index = 1; index <= this.rowCounter['counter_'+templateId]; index++) {
          if(this.numberFormula[templateId+'_'+index+'_'+gridId]){
            total = total + Number(this.numberFormula[templateId+'_'+index+'_'+gridId]);
          }
        }
        this.gridSumable[templateId+'_'+gridId] = total;
      },
      removeRow(event,templateId,grid,item){
        if(this.itemRemoved.length + 1 == this.rowCounter['counter_'+templateId]){
          alert('One row must be present.');
          return;
        }
        var gridFilteredData = grid.filter(x=>x.docket_field_category_id == 3);
        this.gridSumable = [];
        this.itemRemoved.push(item);

        for (let index2 = 0; index2 < gridFilteredData.length; index2++) {
          
          var total = 0;
          for (let index1 = 1; index1 <= this.rowCounter['counter_'+templateId]; index1++) {
            if(item != index1){
              if(this.numberFormula[templateId+'_'+index1+'_'+gridFilteredData[index2].id]){
                total = total + Number(this.numberFormula[templateId+'_'+index1+'_'+gridFilteredData[index2].id]);
              }
            }else{
              delete this.gridSumable[templateId+'_'+gridFilteredData[index2].id];
              delete this.numberFormula[templateId+'_'+index1+'_'+gridFilteredData[index2].id];
            }
          }
          this.gridSumable[templateId+'_'+gridFilteredData[index2].id] = total;
        }
        
        $(event.target).closest('tr').remove();
      },
      openPrefillerModal(template_id,key,category_id){
        let formData    =    new FormData();
        formData.append('field_id',template_id);
        axios.post(`/api/web/prefiller`, formData).then(res =>{
            if(res.status == 200){
              this.prefillerData = res.data.data;
              console.log(this.prefillerData);
              this.prefillerOtherData['prefillerKeyValue'] = key;
              this.prefillerOtherData['category_id'] = category_id;
              this.isPrefillerModalVisible = true;
            }
        }).catch(error => {
            console.error("There was an error!", error);
        });
      },
      selectedPrefillerData(value){
        var pervData = '';
        if(value['category_id'] == 1){
        if($('input[name="docket_data[docket_field_values]['+value['prefillerKeyValue']+'][value]"]').val()){
            pervData = $('input[name="docket_data[docket_field_values]['+value['prefillerKeyValue']+'][value]"]').val() + ',';
          }
          $('input[name="docket_data[docket_field_values]['+value['prefillerKeyValue']+'][value]"]').val( pervData + value['selectedPrefillerValue'].join("-"));
        }else if(value['category_id'] == 2){
          if($('textarea[name="docket_data[docket_field_values]['+value['prefillerKeyValue']+'][value]"]').val()){
            pervData = $('textarea[name="docket_data[docket_field_values]['+value['prefillerKeyValue']+'][value]"]').val() + ',';
          }
          $('textarea[name="docket_data[docket_field_values]['+value['prefillerKeyValue']+'][value]"]').val( pervData + value['selectedPrefillerValue'].join("-"));
        }else if(value['category_id'] == 3){
          if($('input[name="docket_data[docket_field_values]['+value['prefillerKeyValue']+'][value]"]').val()){
            pervData = $('input[name="docket_data[docket_field_values]['+value['prefillerKeyValue']+'][value]"]').val() + ',';
          }
          $('input[name="docket_data[docket_field_values]['+value['prefillerKeyValue']+'][value]"]').val( pervData + value['selectedPrefillerValue'].join("-"));
        }
        
        console.log(value);
      }
    }
  }
</script>

<style lang="scss" scoped>
@import 'https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css';
  .docket .form-group{
    margin-bottom: 10px;
    margin: 10px 0 0 0;
  }
  .float-right{
    float: right;
    a{
      cursor: pointer;
    }
  }
  .highlight{
    color: blue;
  }
  .modal-vue .overlay {
  position: fixed;
  z-index: 9998;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, .5);
}

.modal-vue .modal {
  position: relative;
  width: 300px;
  z-index: 9999;
  margin: 0 auto;
  padding: 20px 30px;
  background-color: #fff;
}

.modal-vue .close{
  position: absolute;
  top: 10px;
  right: 10px;
}


</style>