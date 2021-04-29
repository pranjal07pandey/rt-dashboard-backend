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
    <input type="hidden" name="docket_data[draft_id]" :value="docket_draft.id">

    <div class="docketFieldTempelete row" style="padding:15px;display: block">
      <div v-for="(template, key) in templateData" :key="key">
        <div class="col-lg-12 templateFieldBg" v-if="template.docket_field_category_id == 1">
          <label>{{template.label}}</label>
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <input type="text" class="form-control" :value="template.default_value" v-once :name="'docket_data[docket_field_values]['+key+'][value]'" :required="(template.required == 1) ? true : false " :placeholder="template.label">
        </div>
        <div class="col-lg-12 templateFieldBg" v-if="template.docket_field_category_id == 2">
          <label>{{template.label}}</label>
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <textarea :class="'form-control longText_'+key" :value="template.default_value" v-once :name="'docket_data[docket_field_values]['+key+'][value]'" :required="(template.required == 1) ? true : false " :placeholder="template.label"></textarea>
        </div>
        <div class="col-lg-12 templateFieldBg" v-if="template.docket_field_category_id == 3">
          <label>{{template.label}}</label>
           <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <input type="number" class="form-control" :value="template.default_value" v-once :name="'docket_data[docket_field_values]['+key+'][value]'" :required="(template.required == 1) ? true : false" :min="template.config.min" :max="template.config.max" :placeholder="template.label">
        </div>
        <div class="col-lg-12 templateFieldBg" v-if="template.docket_field_category_id == 4">
          <label>{{template.label}}</label>
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <input type="text" class="form-control" :value="template.default_value" v-once :name="'docket_data[docket_field_values]['+key+'][value]'" :required="(template.required == 1) ? true : false " :placeholder="template.label">
        </div>
        <div class="col-lg-12 templateFieldBg" v-if="template.docket_field_category_id == 5">
          <label>{{template.label}}</label>
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][value]'" class="imageCountAppend">
          <input type="file" class="form-control" accept="image/*" multiple v-on:change="imageUpload($event,template.docket_field_category_id,key)">
        </div>
        <div class="col-lg-12 templateFieldBg" v-if="template.docket_field_category_id == 6">
          <label>{{template.label}}</label>
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <input :type="template.label" class="form-control" :name="'docket_data[docket_field_values]['+key+'][value]'" :required="(template.required == 1)?true : false" :placeholder="template.label">
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
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][value]'" value="1 Image Attached">
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
          <vue-tags-input :class="'form-control emailField_'+key" @tags-changed="allTags => tagsChanged(allTags,key)"
                v-model="emailModelValue[key]" :placeholder="template.label" :requiredValue="template.required" :keyId="key"
                :tags="emailTags[key]" v-on:blur="tagsBlur($event,key)"
                :docketFieldCategoryId="template.docket_field_category_id" :formFieldId="template.id" style="height:auto" />
        </div>
         <div class="col-lg-12 templateFieldBg" v-if="template.docket_field_category_id == 28">
          <label>{{template.label}}</label>
          <span style="display:none">{{ folderLoop(template.folderList,template.default_value) }}</span>
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][category_id]'" :value="template.docket_field_category_id">
          <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][form_field_id]'" :value="template.id">
          <select :name="'docket_data[docket_field_values]['+key+'][folder_value][folders][][id]'" :class="'form-control folderField_'+key">
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
                  <th>Remove</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in rowCounter['counter_'+template.id]" :key="item" :class="'tableRow row_count_'+item">
                  <td v-for="grid in template.modularGrid" :key="grid.id">
                    <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][grid_value]['+item+']['+grid.id+'][category_id]'" :value="grid.docket_field_category_id">

                    <input type="hidden" :name="'docket_data[docket_field_values]['+key+'][grid_value]['+item+']['+grid.id+'][form_field_id]'" :value="grid.id">

                    <input type="text" v-if="grid.docket_field_category_id == 1" :name="'docket_data[docket_field_values]['+key+'][grid_value]['+item+']['+grid.id+'][value]'" 
                          class="form-control" v-model="tableInput[template.id][item - 1]['input_' +grid.id]" :placeholder="grid.label" 
                          :required="(grid.required == 1)?true : false">
                    
                    <textarea :class="'form-control longTextGrid_'+key+'_'+item" v-if="grid.docket_field_category_id == 2" :placeholder="grid.label" style="width: 120px;height: 37px;"
                          :name="'docket_data[docket_field_values]['+key+'][grid_value]['+item+']['+grid.id+'][value]'" :required="(grid.required == 1) ? true : false" 
                          v-model="tableInput[template.id][item - 1]['input_' +grid.id]" ></textarea>
                    
                    <input type="number" v-if="grid.docket_field_category_id == 3" :name="'docket_data[docket_field_values]['+key+'][grid_value]['+item+']['+grid.id+'][value]'"
                         :class="'form-control numberFormula'" :data="template.id+'_'+item+'_'+grid.id" :placeholder="grid.label" 
                          v-model="numberFormula[template.id+'_'+item+'_'+grid.id]" :readonly="formulaCalculation(grid,item,template.id)"  :formula="grid.formula"
                          :required="(grid.required == 1)?true : false" @input="calculateTotal(template.id,template.modularGrid)">
                          
                    <input type="text" v-if="grid.docket_field_category_id == 4" :name="'docket_data[docket_field_values]['+key+'][grid_value]['+item+']['+grid.id+'][value]'" 
                          class="form-control" v-model="tableInput[template.id][item - 1]['input_' +grid.id]" :placeholder="grid.label" 
                          :required="(grid.required == 1)?true : false">
                    
                    <input type="hidden" v-if="grid.docket_field_category_id == 5" :name="'docket_data[docket_field_values]['+key+'][grid_value]['+item+']['+grid.id+'][value]'" 
                            class="image_value">
                    
                    <input type="file" v-if="grid.docket_field_category_id == 5" multiple v-on:change="fileUpload($event,key,item,grid.id)" class="form-control" accept="image/*">
                    
                    <input type="hidden" v-if="grid.docket_field_category_id == 6" :name="'docket_data[docket_field_values]['+key+'][grid_value]['+item+']['+grid.id+'][value]'" 
                            class="formatedDate">
                    
                    <input type="date" v-on:change="dateFormat($event)" v-if="grid.docket_field_category_id == 6" :class="'form-control date_value_'+key+'_'+(item - 1)+'_'+grid.docket_field_category_id" 
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
                    
                    <!-- <input type="hidden" v-if="grid.docket_field_category_id == 21"  :name="'docket_data[docket_field_values]['+key+'][grid_value]['+item+']['+grid.id+'][value]'" value=""> -->
                    <input type="text" v-if="grid.docket_field_category_id == 21" class="form-control" :name="'docket_data[docket_field_values]['+key+'][grid_value]['+item+']['+grid.id+'][value]'" 
                            v-model="tableInput[template.id][item - 1]['input_' +grid.id]" :required="(grid.required == 1)?true : false" :placeholder="grid.label" >
                    
                    <!-- <input type="email" v-if="grid.docket_field_category_id == 29" class="form-control" v-model="tableInput[template.id][item - 1]['input_' +grid.id]" :placeholder="grid.label" 
                            :required="(grid.required == 1)?true : false" data-role="tagsinput"> -->
                            
                    <vue-tags-input v-if="grid.docket_field_category_id == 29" :class="'form-control emailField_'+key+'_'+item+'_'+grid.id"  @tags-changed="allTags => tagsChanged(allTags,'grid_'+key+'_'+item+'_'+grid.id)"
                          v-model="emailModelValue['grid_'+key+'_'+item+'_'+grid.id]" :placeholder="grid.label" :docketFieldCategoryId="grid.docket_field_category_id" :tags="emailTags['grid_'+key+'_'+item+'_'+grid.id]"
                          :keyId="key" :itemId="item" :gridId="grid.id" :requiredValue="template.required" v-on:blur="tagsBlur($event,'grid_'+key+'_'+item+'_'+grid.id)" :formFieldId="grid.id" style="height:auto" />

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
          <label>Draft Name</label>
          <input type="text" name="docket_data[draft_name]" class="form-control draftName" placeholder="Draft Name" required>
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
    <explanation v-show="isExplanationVisible" :explanations="explanationSubDocket" :explanationKey="explanationKey" :explanationEditValue="explanationEditValue" :explanationEditKey="explanationEditKey" @close="closeModal" 
                @explanationData="explanationValue"></explanation>
    <manualTimer v-show="isManualTimerVisible" @close="closeModal" :manualTimerFields="manualTimerSubField" :manualTimerKey="manualTimerKey"
                :manualTimerItem="manualTimerItem" :manualTimerGridId="manualTimerGridId" @manualTimerData="manualTimerValue"></manualTimer>
  </div>
</template>
<script>
  import Multiselect from 'vue-multiselect';
  import modal from './email_modal';
  import sketchpad from './sketchpad';
  import signature from './signature';
  import explanation from './explanation';
  import manualTimer from './manualTimer';
  import axios from 'axios';
  import Vue from 'vue';
  import VueTagsInput from '@johmun/vue-tags-input';

  export default {
    components: {
       Multiselect,modal,sketchpad,signature,explanation,manualTimer,VueTagsInput
    },
    props:['docket_templete','custom_email_client','record_time_user','docket_draft','from_assign'],
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
            emailTags:[],
            docket_field_values: [
                  {'category_id':0, 'form_field_id': 0, 'value':''}
              ],
            explanationEditValue:[],
            explanationEditKey:'',
            itemRemoved:[],
            loopOnce:0,
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

      if(this.docket_draft.value.rt_user_receivers.length){
        this.recipient_type = 'record_time';
        this.selectedRecipients = [... this.docket_draft.value.rt_user_receivers];
      }
      if(this.docket_draft.value.email_user_receivers.length){
        this.recipient_type = 'custom_email_client';
        this.selectedRecipients = [... this.docket_draft.value.email_user_receivers];
      }
      for (let index = 0; index < this.docket_draft.value.rt_user_approvers.length; index++) {
        this.authorizeApprovalRecipient.push(this.docket_draft.value.rt_user_approvers[index]);
      }
      for (let index = 0; index < this.docket_draft.value.email_user_approvers.length; index++) {
        this.authorizeApprovalRecipient.push(JSON.stringify(this.docket_draft.value.email_user_approvers[index]));
      }
      this.docket_template_id = this.docket_draft.value.template.id;
      this.selectedOption(null,this.docket_template_id);
      this.activate();

      const docket_field_values = this.docket_draft.value.docket_data.docket_field_values;

      if(this.docket_draft.value.docket_data.draft_name){
        $('.spinnerCheckgrid').css('display','block')
        this.draftStatusValue = 1;
      }
      
      for (let index = 0; index < docket_field_values.length; index++) {
        if(docket_field_values[index]['category_id'] == 29){
          this.emailTags[index] = [];
          for (let index1 = 0; index1 < docket_field_values[index]['email_list_value']['email_list'].length; index1++) {
            this.emailTags[index].push({text: docket_field_values[index]['email_list_value']['email_list'][index1]['email']})
          }
        }
        if(docket_field_values[index]['category_id'] == 22){
          const gridValue = Object.keys(docket_field_values[index]['grid_value']).map((key) => docket_field_values[index]['grid_value'][key]);
          const templateId = docket_field_values[index]['form_field_id'];
          for (let index1 = 0; index1 < gridValue.length; index1++) {
            const gridValueRow = Object.keys(gridValue[index1]).map((key) => gridValue[index1][key]);
            gridValueRow.forEach((element,index3) => {
              if(element['category_id'] == 29){
                this.emailTags['grid_'+index+'_'+(index1+1)+'_'+element['form_field_id']] = [];
                for (let index5 = 0; index5 < element['email_list_value']['email_list'].length; index5++) {
                  this.emailTags['grid_'+index+'_'+(index1+1)+'_'+element['form_field_id']].push({text: element['email_list_value']['email_list'][index5]['email']})
                }
              }
            });
          }
        }
      }
    },
    methods:{
      activate() {
        setTimeout(() => {
          if(this.docket_draft.value.docket_data.draft_name){
            $('.checkboxDraft').prop('checked',true);
            $('input[name="docket_data[draft_name]"]').val(this.docket_draft.value.docket_data.draft_name);
          }
          
          const docket_field_values = this.docket_draft.value.docket_data.docket_field_values;
          for (let index = 0; index < docket_field_values.length; index++) {
            if(docket_field_values[index]['category_id'] == 2){
              $('.longText_'+index).val(docket_field_values[index]['value']);
            }else if(docket_field_values[index]['category_id'] == 5){
              if(docket_field_values[index]['image_value']){
                $('input[name="docket_data[docket_field_values]['+index+'][value]"]').val(docket_field_values[index]['value']);
                for (let index1 = 0; index1 < docket_field_values[index]['image_value'].length; index1++) {
                  $('input[name="docket_data[docket_field_values]['+index+'][value]"]').closest('.templateFieldBg').append(`
                    <input type="hidden" name="docket_data[docket_field_values][${index}][image_value][]" value="${docket_field_values[index]['image_value'][index1]}" class="imageValue">
                  `);
                }
              }
            }else if(docket_field_values[index]['category_id'] == 7){
              $('input[name="docket_data[docket_field_values]['+index+'][unit_rate_value][per_unit_rate]"]').val(docket_field_values[index]['unit_rate_value']['per_unit_rate']);
              $('input[name="docket_data[docket_field_values]['+index+'][unit_rate_value][total_unit]"]').val(docket_field_values[index]['unit_rate_value']['total_unit']);
              $('input[name="docket_data[docket_field_values]['+index+'][unit_rate_value][total]"]').val(docket_field_values[index]['value']);
              $('input[name="docket_data[docket_field_values]['+index+'][value]"]').val(docket_field_values[index]['value']);
              $('input[name="docket_data[docket_field_values]['+index+'][value]"]').closest('.templateFieldBg').find('.unitRateTotal').html("Total: $"+docket_field_values[index]['value']);
            }else if(docket_field_values[index]['category_id'] == 9){
              $(`.signature_key_${index}`).closest('div').append(`
              <input type="hidden" name="docket_data[docket_field_values][${index}][value]" value="1 Image Attached">`);
              for (let index1 = 0; index1 < docket_field_values[index]['signature_value'].length; index1++) {
                $('.signaturePadAppend').append(`
                    <div class="appendedImage signaturetemplate${docket_field_values[index]['form_field_id']} style="display:inline">
                        <div>
                          <input type="text" class="input-form"  placeholder="Signature name" value="${docket_field_values[index]['signature_value'][index1]['name']}">
                          <input type="hidden" value="${docket_field_values[index]['signature_value'][index1]['image']}">
                          <input type="hidden" value="${index1}">
                          <img src='${docket_field_values[index]['signature_value'][index1]['image']}' class="signaturePadImg" />
                          <i class="material-icons" onClick="removeSignatureImage(this,${index1})" style="cursor:pointer;">close</i>
                        </div>
                    </div>
                `);
                $('.signature_key_'+index).closest('.templateFieldBg').append(`<div class="signature_key_append_${index} unique_signature_${index1}">
                  <input type="hidden" name="docket_data[docket_field_values][${index}][signature_value][${index1}][image]" value="${docket_field_values[index]['signature_value'][index1]['image']}">
                  <input type="hidden" name="docket_data[docket_field_values][${index}][signature_value][${index1}][name]" value="${docket_field_values[index]['signature_value'][index1]['name']}">
                </div>`);
              }
            }else if(docket_field_values[index]['category_id'] == 14){
              for (let index5 = 0; index5 < docket_field_values[index]['image_value'].length; index5++) {
                $('.sketchPad_key_'+index).closest('.templateFieldBg').append(`<div class="sketch_pad_append_${index}">
                  <input type="hidden" name="docket_data[docket_field_values][${index}][image_value][]" value="${docket_field_values[index]['image_value'][index5]}">
                </div>`);
                $('.sketchPadAppend').append(`
                    <div class="appendedSketchPadImage col-md-4 sketchpad_${index}_undefined_undefined style="display:inline">
                        <div>
                            <input type="hidden" name="sketchpad[]" value="${docket_field_values[index]['image_value'][index5]}">
                            <img src='${docket_field_values[index]['image_value'][index5]}' class="sketchPadImg" />
                            <i class="material-icons" onClick="removeSketchpadImage(this)" style="cursor:pointer;">close</i>
                        </div>
                    </div>
                `);
              }
              $('input[name="docket_data[docket_field_values]['+index+'][value]"]').val(`${docket_field_values[index]['image_value'].length} Image Attached`);
              // $('.sketchPad_key_'+index).closest('.templateFieldBg').append(`<div class="sketch_pad_append_${index}">
              //   <input type="hidden" name="docket_data[docket_field_values][${index}][image_value][]" value="${docket_field_values[index]['image_value'][0]}">
              // </div>`);
              // $('.sketchPad_key_'+index).closest('.templateFieldBg').find('.sketchPadPreview').attr('src',''+docket_field_values[index]['image_value'][0]+'').show();
            }else if(docket_field_values[index]['category_id'] == 18){
              if(docket_field_values[index]['yes_no_value']['explanation']){
                this.explanationEditValue = docket_field_values[index]['yes_no_value']['explanation'];
                // this.explanationEditValue['key'] = index;
                this.explanationEditKey = index;
                $('input[name="docket_data[docket_field_values]['+index+'][yes_no_value][selected_type]"]').val(docket_field_values[index]['yes_no_value']['selected_type']);
                $('input[name="docket_data[docket_field_values]['+index+'][yes_no_value][selected_id]"]').closest('div').find('input[id="'+docket_field_values[index]['yes_no_value']['selected_id']+'"]').prop('checked',true);
                for (let index1 = 0; index1 < docket_field_values[index]['yes_no_value']['explanation'].length; index1++) {
                  if(docket_field_values[index]['yes_no_value']['explanation'][index1]){
                    if(docket_field_values[index]['yes_no_value']['explanation'][index1]['category_id'] == 5){
                      var imageValue = '';
                      if(docket_field_values[index]['yes_no_value']['explanation'][index1]['image_value']){
                        for (let index2 = 0; index2 < docket_field_values[index]['yes_no_value']['explanation'][index1]['image_value'].length; index2++) {
                          imageValue += `<input type="hidden" name="docket_data[docket_field_values][${index}][yes_no_value][explanation][${index1}][image_value][]" value="${docket_field_values[index]['yes_no_value']['explanation'][index1]['image_value'][index2]}">`;
                        }
                      }
                      $('.explanation_key_'+index).append(`
                      <div class="explanation_key_append_${index}">
                        <input type="hidden" name="docket_data[docket_field_values][${index}][yes_no_value][explanation][${index1}][category_id]" value="${docket_field_values[index]['yes_no_value']['explanation'][index1]['category_id']}">
                        <input type="hidden" name="docket_data[docket_field_values][${index}][yes_no_value][explanation][${index1}][form_field_id]" value="${docket_field_values[index]['yes_no_value']['explanation'][index1]['form_field_id']}">
                        ${imageValue}
                        <input type="hidden" name="docket_data[docket_field_values][${index}][yes_no_value][explanation][${index1}][value]" value="${docket_field_values[index]['yes_no_value']['explanation'][index1]['value']}">
                      </div>`);
                    }else{
                      $('.explanation_key_'+index).append(` 
                      <div class="explanation_key_append_${index}">
                        <input type="hidden" name="docket_data[docket_field_values][${index}][yes_no_value][explanation][${index1}][category_id]" value="${docket_field_values[index]['yes_no_value']['explanation'][index1]['category_id']}">
                        <input type="hidden" name="docket_data[docket_field_values][${index}][yes_no_value][explanation][${index1}][form_field_id]" value="${docket_field_values[index]['yes_no_value']['explanation'][index1]['form_field_id']}">
                        <input type="hidden" name="docket_data[docket_field_values][${index}][yes_no_value][explanation][${index1}][value]" value="${docket_field_values[index]['yes_no_value']['explanation'][index1]['value']}">
                      </div>`);
                    }
                  }
                }
              }
            }else if(docket_field_values[index]['category_id'] == 24){
              $('input[name="docket_data[docket_field_values]['+index+'][unit_rate_value][per_unit_rate]"]').val(docket_field_values[index]['unit_rate_value']['per_unit_rate']);
              $('input[name="docket_data[docket_field_values]['+index+'][unit_rate_value][per_unit_rate]"]').closest('div').find('.tallyableUnitRateValue_1').val(docket_field_values[index]['unit_rate_value']['per_unit_rate']);
              $('input[name="docket_data[docket_field_values]['+index+'][unit_rate_value][total_unit]"]').val(docket_field_values[index]['unit_rate_value']['total_unit']);
              $('input[name="docket_data[docket_field_values]['+index+'][unit_rate_value][total_unit]"]').closest('div').find('.tallyableUnitRateValue_2').val(docket_field_values[index]['unit_rate_value']['total_unit']);
              $('input[name="docket_data[docket_field_values]['+index+'][unit_rate_value][total]"]').val(docket_field_values[index]['value']);
              $('input[name="docket_data[docket_field_values]['+index+'][value]"]').val(docket_field_values[index]['value']);
              $('input[name="docket_data[docket_field_values]['+index+'][value]"]').closest('.templateFieldBg').find('.tallyableUnitRateTotal').html("Total: $"+docket_field_values[index]['value']);
            }else if(docket_field_values[index]['category_id'] == 20){
              $('.manual_timer_'+index).append(`
                <div class="manual_timer_append_${index}">
                  <input type="hidden" name="docket_data[docket_field_values][${index}][manual_timer_value][explanation]" value="${docket_field_values[index]['manual_timer_value']['explanation']}">
                  <input type="hidden" name="docket_data[docket_field_values][${index}][manual_timer_value][from]" value="${docket_field_values[index]['manual_timer_value']['from']}">
                  <input type="hidden" name="docket_data[docket_field_values][${index}][manual_timer_value][to]" value="${docket_field_values[index]['manual_timer_value']['to']}">
                  <input type="hidden" name="docket_data[docket_field_values][${index}][manual_timer_value][totalDuration]" value="${docket_field_values[index]['manual_timer_value']['totalDuration']}">
                  <input type="hidden" name="docket_data[docket_field_values][${index}][manual_timer_value][breakDuration]" value="${docket_field_values[index]['manual_timer_value']['breakDuration']}">
                  <input type="hidden" name="docket_data[docket_field_values][${index}][value]" value="${docket_field_values[index]['value']}">
                </div>
              `);
            }else if(docket_field_values[index]['category_id'] == 28){
              $('.folderField_'+index+ '> option').each(function(){
                if($(this).val() == docket_field_values[index]['value']){
                  $(this).prop('selected','selected');
                }
              });
            }else if(docket_field_values[index]['category_id'] == 29){
              for (let index1 = 0; index1 < docket_field_values[index]['email_list_value']['email_list'].length; index1++) {
                $('.emailField_'+index).closest('.templateFieldBg').append(`
                  <div class="email_field_append">
                    <input type="hidden" name="docket_data[docket_field_values][${index}][category_id]" value="${docket_field_values[index]['category_id']}">
                    <input type="hidden" name="docket_data[docket_field_values][${index}][form_field_id]" value="${docket_field_values[index]['form_field_id']}">
                    <input type="hidden" name="docket_data[docket_field_values][${index}][email_list_value][email_list][${index1}][email]" value="${docket_field_values[index]['email_list_value']['email_list'][index1].email}">
                    <input type="hidden" name="docket_data[docket_field_values][${index}][email_list_value][email_list][${index1}][send_copy]" value="${docket_field_values[index]['email_list_value']['email_list'][index1].send_copy}">
                  </div>
                `);
              }
            }else if(docket_field_values[index]['category_id'] == 22){
              const gridValue = Object.keys(docket_field_values[index]['grid_value']).map((key) => docket_field_values[index]['grid_value'][key]);
              const templateId = docket_field_values[index]['form_field_id'];
              for (let index1 = 0; index1 < gridValue.length; index1++) {
                const gridValueRow = Object.keys(gridValue[index1]).map((key) => gridValue[index1][key]);
                this.tableInput[templateId][index1] = [];
                gridValueRow.forEach((element,index3) => {
                  if(element['category_id'] == 1 || element['category_id'] == 4 || element['category_id'] == 6 || element['category_id'] == 3 || element['category_id'] == 21){
                    if(element['category_id'] == 3){
                      this.numberFormula[templateId+'_'+(index1 + 1)+'_'+element['form_field_id']] = element['value'];
                      this.calculateTotal(templateId,gridValueRow,index1);
                    }else{
                      this.tableInput[templateId][index1]['input_' +element['form_field_id']] = element['value'];
                    }
                    $('input[name="docket_data[docket_field_values]['+index+'][grid_value]['+(index1 + 1)+']['+element['form_field_id']+'][value]"]').val(element['value']);
                    if(element['category_id'] == 6){
                      this.tableInput[templateId][index1]['input_' +element['form_field_id']] = moment(element['value']).format('YYYY-MM-DD');
                      $(`.date_value_${index}_${index1}_${element['category_id']}`).val(moment(element['value']).format('YYYY-MM-DD'));
                    }
                  }else if(element['category_id'] == 2){
                    this.tableInput[templateId][index1]['input_' +element['form_field_id']] = element['value'];
                    $('.longTextGrid_'+index+'_'+(index1 + 1)).val(element['value']);
                  }else if(element['category_id'] == 5){
                    this.tableInput[templateId][index1]['input_' +element['form_field_id']] = element['value'];
                    $(`input[name="docket_data[docket_field_values][${index}][grid_value][${(index1 + 1)}][${element['form_field_id']}][value]"]`).val(element['value']);
                    for (let index5 = 0; index5 < element['image_value'].length; index5++) {
                      $(`input[name="docket_data[docket_field_values][${index}][grid_value][${(index1 + 1)}][${element['form_field_id']}][value]"]`).closest('td').append(`
                        <div class="image_url_${(index1 + 1)}_${element['form_field_id']}">
                          <input type="hidden" name="docket_data[docket_field_values][${index}][grid_value][${(index1 + 1)}][${element['form_field_id']}][image_value][]" value="${element['image_value'][index5]}">
                        </div>
                      `);
                    }
                  }else if(element['category_id'] == 8){
                    if(element['value'] == 1){
                      this.tableInput[templateId][index1]['input_' +element['form_field_id']] = element['value'];
                      $('input[name="docket_data[docket_field_values]['+index+'][grid_value]['+(index1 + 1)+']['+element['form_field_id']+'][value]"]').prop('checked',true);
                    }
                  }else if(element['category_id'] == 9){
                    $(`.signature_key_${index}_${(index1 + 1)}`).closest('td').append(`
                    <input type="hidden" name="docket_data[docket_field_values][${index}][grid_value][${(index1 + 1)}][${element['form_field_id']}][value]" value="${element['signature_value'].length} Image Attached">`);
                    for (let index5 = 0; index5 < element['signature_value'].length; index5++) {
                      $('.signaturePadAppend').append(`
                        <div class="appendedImage signaturegrid${(index1 + 1)}_${index}_${element['form_field_id']} style="display:inline">
                          <div>
                            <input type="text" class="input-form"  placeholder="Signature name" value="${element['signature_value'][index5]['name']}">
                            <input type="hidden" value="${element['signature_value'][index5]['image']}">
                            <input type="hidden" value="${element['form_field_id']}${(index1 + 1)}${index5}">
                            <img src='${element['signature_value'][index5]['image']}' class="signaturePadImg" />
                            <i class="material-icons" onClick="removeSignatureImage(this,${element['form_field_id']}${(index1 + 1)}${index5})" style="cursor:pointer;">close</i>
                          </div>
                        </div>
                      `);
                      $(`.signature_key_${index}_${(index1 + 1)}`).closest('td').append(`<div class="signature_key_append_${index}_${(index1 + 1)} unique_signature_${element['form_field_id']}${(index1 + 1)}${index5}">
                        <input type="hidden" name="docket_data[docket_field_values][${index}][grid_value][${(index1 + 1)}][${element['form_field_id']}][signature_value][${index5}][image]" value="${element['signature_value'][index5]['image']}">
                        <input type="hidden" name="docket_data[docket_field_values][${index}][grid_value][${(index1 + 1)}][${element['form_field_id']}][signature_value][${index5}][name]" value="${element['signature_value'][index5]['name']}">
                      </div>`);
                    }
                  }else if(element['category_id'] == 14){
                    for (let index5 = 0; index5 < element['image_value'].length; index5++) {
                      $(`.tableSketchPadPreview_${element['form_field_id']}_${(index1 + 1)}`).closest('td').append(`
                      <div class="sketch_pad_append_${index}_${(index1 + 1)}_${element['form_field_id']}">
                        <input type="hidden" name="docket_data[docket_field_values][${index}][grid_value][${(index1 + 1)}][${element['form_field_id']}][image_value][]" value="${element['image_value'][index5]}">
                        <input type="hidden" name="docket_data[docket_field_values][${index}][grid_value][${(index1 + 1)}][${element['form_field_id']}][value]" value="${element['value']}">
                      </div>`);

                      $('.sketchPadAppend').append(`
                        <div class="appendedSketchPadImage col-md-4 sketchpad_${index}_${element['form_field_id']}_${(index1 + 1)} style="display:inline">
                            <div>
                                <input type="hidden" name="sketchpad[]" value="${element['image_value'][index5]}">
                                <img src='${element['image_value'][index5]}' class="sketchPadImg" />
                                <i class="material-icons" onClick="removeSketchpadImage(this)" style="cursor:pointer;">close</i>
                            </div>
                        </div>
                      `);
                    }
                    // $(`.tableSketchPadPreview_${element['form_field_id']}_${(index1 + 1)}`).attr('src',element['image_value'][0]).show();
                   
                  }else if(element['category_id'] == 20){
                    $(`.manual_timer_${index}_${(index1 + 1)}_${element['form_field_id']}`).closest('td').append(`
                    <div class="manual_timer_append_${index}_${(index1 + 1)}_${element['form_field_id']}">
                      <input type="hidden" name="docket_data[docket_field_values][${index}][grid_value][${(index1 + 1)}][${element['form_field_id']}][manual_timer_value][explanation]" value="${element['manual_timer_value']['explanation']}">
                      <input type="hidden" name="docket_data[docket_field_values][${index}][grid_value][${(index1 + 1)}][${element['form_field_id']}][manual_timer_value][from]" value="${element['manual_timer_value']['from']}">
                      <input type="hidden" name="docket_data[docket_field_values][${index}][grid_value][${(index1 + 1)}][${element['form_field_id']}][manual_timer_value][to]" value="${element['manual_timer_value']['to']}">
                      <input type="hidden" name="docket_data[docket_field_values][${index}][grid_value][${(index1 + 1)}][${element['form_field_id']}][manual_timer_value][totalDuration]" value="${element['manual_timer_value']['totalDuration']}">
                      <input type="hidden" name="docket_data[docket_field_values][${index}][grid_value][${(index1 + 1)}][${element['form_field_id']}][manual_timer_value][breakDuration]" value="${element['manual_timer_value']['breakDuration']}">
                      <input type="hidden" name="docket_data[docket_field_values][${index}][grid_value][${(index1 + 1)}][${element['form_field_id']}][value]" value="${element['value']}">
                    </div>`);
                  }else if(element['category_id'] == 26){
                    this.tableInput[templateId][index1]['input_' +element['form_field_id']] = moment(element['value'], ["h:mm A"]).format("HH:mm");
                    $('input[name="docket_data[docket_field_values]['+index+'][grid_value]['+(index1 + 1)+']['+element['form_field_id']+'][value]"]').val(element['value']);
                    $('input[name="docket_data[docket_field_values]['+index+'][grid_value]['+(index1 + 1)+']['+element['form_field_id']+'][value]').closest('td').find('input[type="time"]').val(moment(element['value'], ["h:mm A"]).format("HH:mm"));
                  }else if(element['category_id'] == 29){
                    for (let index5 = 0; index5 < element['email_list_value']['email_list'].length; index5++) {
                      $(`.emailField_${index}_${(index1 + 1)}_${element['form_field_id']}`).closest('td').append(`
                      <div class="email_field_append">
                        <input type="hidden" name="docket_data[docket_field_values][${index}][grid_value][${(index1 + 1)}][${element['form_field_id']}][category_id]" value="${element['category_id']}">
                        <input type="hidden" name="docket_data[docket_field_values][${index}][grid_value][${(index1 + 1)}][${element['form_field_id']}][form_field_id]" value="${element['form_field_id']}">
                        <input type="hidden" name="docket_data[docket_field_values][${index}][grid_value][${(index1 + 1)}][${element['form_field_id']}][email_list_value][email_list][${index5}][email]" value="${element['email_list_value']['email_list'][index5]['email']}">
                        <input type="hidden" name="docket_data[docket_field_values][${index}][grid_value][${(index1 + 1)}][${element['form_field_id']}][email_list_value][email_list][${index5}][send_copy]" value="${element['email_list_value']['email_list'][index5]['send_copy']}">
                      </div>`);
                    }
                  }
                });
              }
            }else{
              if(docket_field_values[index]['category_id'] == 8){
                if(docket_field_values[index]['value'] == 1){
                  $('input[name="docket_data[docket_field_values]['+index+'][value]"]').prop('checked',true);
                }
              }
              if(docket_field_values[index]['category_id'] == 1 || docket_field_values[index]['category_id'] == 2 || docket_field_values[index]['category_id'] == 4 || 
                docket_field_values[index]['category_id'] == 3 || docket_field_values[index]['category_id'] == 6 || docket_field_values[index]['category_id'] == 16 ||
                docket_field_values[index]['category_id'] == 12 || docket_field_values[index]['category_id'] == 25){
                $('input[name="docket_data[docket_field_values]['+index+'][value]"]').val(docket_field_values[index]['value']);
              }
              if(docket_field_values[index]['category_id'] == 26){
                $('input[name="docket_data[docket_field_values]['+index+'][value]"]').val(docket_field_values[index]['value']);
                $('input[name="docket_data[docket_field_values]['+index+'][value]"]').closest('.templateFieldBg').find('input[type="time"]').val(moment(docket_field_values[index]['value'], ["h:mm A"]).format("HH:mm"));
              }
            }
          }
          if(this.from_assign){
            $('input').attr('required', false);
          }
          $(".spinnerCheckgrid").css('display','none');
        }, 5000);
      },
      selectedOption(event,value = null){
        console.log('1');
          axios.post(`/api/web/docket/fields/${(event) ? event.target.value : value}`).then(res =>{
            const responseData = res.data.template.docket_field;
            const girdTable = responseData.filter(element => element.docket_field_category_id == 22);
            this.girdTableFormula = girdTable;
            this.tableInput = [];
            this.rowCounter = {};
            girdTable.forEach(element => {
              const tableData = element.modularGrid;
              let defaultObject = {};
              tableData.forEach((element,index) => {
                defaultObject['input_' + element.id] = element.default_value;
                defaultObject['formula_'+ element.id] = element.formula;
              });
              this.defaultTableObject[element.id] = defaultObject;
              this.tableInput[element.id]= [];
              this.tableInput[element.id].push({ ... this.defaultTableObject[element.id] });
              this.rowCounter['counter_'+element.id] = 1;
              this.gridSumable[element.id] = 0;
            });

            const docket_field_values = this.docket_draft.value.docket_data.docket_field_values.filter(element => {
              if(element.category_id == 22){
                return element;
              }
            });

            docket_field_values.forEach(element => {
              const gridValueRow = Object.keys(element.grid_value).map((key) => element.grid_value[key]);
              for (let index = 1; index < gridValueRow.length; index++) {
                this.cloneRow(element.form_field_id);
              }
            });

            this.templateDB = res.data.template;
            this.templateData = res.data.template.docket_field;

            console.log(this.templateData);
          }).catch(error => {
              console.error("There was an error!", error);
          });
      },
      tagsBlur(event,keyy){
        console.log('2');
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
        console.log('3');
        var formatedDate = moment(event.target.value).format('D-MMM-YYYY');
        $(event.target).closest('td').find('.formatedDate').val(formatedDate);
      },
      tConvert (event,item=null,gridId=null,templateId = null,grid = null) {
        console.log('4');
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
        console.log('5');
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
        console.log('6');
        this.authorizeApprovalRecipient = [];
        this.selectedRecipients = [];
        this.recipient_type = type;
      },
      showModal() {
        console.log('7');
        this.isModalVisible = true;
      },
      closeModal() {
        console.log('8');
        this.isModalVisible = false;
        this.isSketchModalVisible = false;
        this.isSignatureVisible = false;
        this.isExplanationVisible = false;
        this.isManualTimerVisible = false;
      },
      signatureImageUrl(value){
        console.log('9');
        var item = 0;
        if(value.signatureItem){
          item = $(`.signature_key_append_${value.signatureKey}_${value.signatureItem}`).length;
          $(`.signature_key_${value.signatureKey}`).closest('td').append(`
          <input type="hidden" name="docket_data[docket_field_values][${value.signatureKey}][grid_value][${value.signatureItem}][${value.signatureGridId}][value]" value="${(value.data.length + item)} Image Attached">`);
        }else{
          item = $(`.signature_key_append_${value.signatureKey}`).length;
          $(`.signature_key_${value.signatureKey}`).closest('div').append(`
          <input type="hidden" name="docket_data[docket_field_values][${value.signatureKey}][value]" value="${(value.data.length + item)} Image Attached">`);
        }
        for (let index = 0; index < value.data.length; index++) {
          if(value.signatureItem){
            $(`.signature_key_${value.signatureKey}_${value.signatureItem}`).closest('td').append(`
            <div class="signature_key_append_${value.signatureKey}_${value.signatureItem} unique_signature_${value.data[index].signature_unique_count}">
              <input type="hidden" name="docket_data[docket_field_values][${value.signatureKey}][grid_value][${value.signatureItem}][${value.signatureGridId}][signature_value][${(index + item)}][image]" value="${value.data[index].url}">
              <input type="hidden" name="docket_data[docket_field_values][${value.signatureKey}][grid_value][${value.signatureItem}][${value.signatureGridId}][signature_value][${(index + item)}][name]" value="${value.data[index].name}">
            </div>`);
          }else{
            $(`.signature_key_${value.signatureKey}`).closest('div').append(`
            <div class="signature_key_append_${value.signatureKey} unique_signature_${value.data[index].signature_unique_count}">
              <input type="hidden" name="docket_data[docket_field_values][${value.signatureKey}][signature_value][${(index + item)}][image]" value="${value.data[index].url}">
              <input type="hidden" name="docket_data[docket_field_values][${value.signatureKey}][signature_value][${(index + item)}][name]" value="${value.data[index].name}">
            </div>`);
          }
        }
      },
      sketchPadImageUrl(value){
        console.log('10');
        console.log(value);
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
        console.log('11');
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
        // console.log('12');
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
        console.log('13');
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
        console.log('14');
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
        console.log('15');
        $('.appendedImage').hide();
        $('.signature'+id).show();
        this.isSignatureVisible = true;
        this.signatureTempleteId = id;
        this.signatureKey = key;
        this.signatureItem = item;
        this.signatureGridId = gridId;
      },
      manualTimerValue(value){
        console.log('16');
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
        console.log('16');
        this.manualTimerSubField = manualTimerSubField;
        this.isManualTimerVisible = true;
        this.manualTimerKey = key;
        this.manualTimerItem = item;
        this.manualTimerGridId = gridId;
      },
      cloneRow(templateId){
        console.log('17');
        this.tableInput[templateId] = [...this.tableInput[templateId] , { ... this.defaultTableObject[templateId] }];
        this.rowCounter['counter_'+templateId] = this.rowCounter['counter_'+templateId] + 1;
        const newCounterArray = {... this.rowCounter};
        this.rowCounter = {};
        this.rowCounter = {...newCounterArray};
      },
      newEmailClientAdded(value){
        console.log('18');
        var data = { "id": value.id, "name": value.email }
        this.custom_email_client.push(data);
        this.selectedRecipients.push(data);
      },
      explanationValue(value){
        console.log('19');
        console.log(value);
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
        console.log('20');
        this.record_time_user.push(value);
      },
      submit(){
        console.log('21');
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
        if(this.authorizeApprovalRecipient.length < 1){
          if(this.recipient_type == 'record_time'){
            formData.append('rt_user_approvers[]',[]);
          }else{
            formData.append('email_user_approvers[]',[]);
          }
        }

        formData.append('docket_data[email_subject]','Email Subject Here');

        formData.append('template',JSON.stringify(this.templateDB));
        var currentdate = new Date();
        var formatedDate = moment(currentdate).format('D-MMM-YYYY h:m a');
        formData.append('docket_data[draft_date]',formatedDate);

        axios.post(`/api/web/docket/sent`, formData).then(res =>{
          console.log(res);
            if(res.status){
              alert(res.data.message);
              window.location.reload();
            }
        }).catch(error => {
            console.error("There was an error!", error);
        });
      },
      explanation(event,subDocket,key){
        console.log('21');
        $('.explanation_key_append_'+key).remove();
        $('.explanation_key_'+key).append(`
          <div class="explanation_key_append_${key}">
            <input type="hidden" name="docket_data[docket_field_values][${key}][yes_no_value][explanation][]" value="">
          </div>`);
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
          // this.numberFormula[template_id+'_'+index+'_'+gridNumber.id] = eval(value);

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
        console.log('23');
        var fieldType1 = $(event.target).closest('.tallyableUnitRateChange').find('.tallyableUnitRateValue_1').val();
        var fieldType2 = $(event.target).closest('.tallyableUnitRateChange').find('.tallyableUnitRateValue_2').val();
        const type1 = (fieldType1 == undefined) ? 0 : fieldType1;
        const type2 = (fieldType2 == undefined) ? 0 : fieldType2;
        const value = Number(type1) * Number(type2);
        $(event.target).closest('.templateFieldBg').find('.tallyableUnitRateTotal').html("Total: $"+value);
        $(event.target).closest('.templateFieldBg').find('.tallyableTotal').val(value);
        
        $(event.target).closest('.tallyableUnitRateChange').find('.tallyableUnitRateField_1').val(Number(type1));
        $(event.target).closest('.tallyableUnitRateChange').find('.tallyableUnitRateField_2').val(Number(type2));
      },
      unitRateChange(event){
        console.log('23');
        var fieldType1 = $(event.target).closest('.unitRateChange').find('.unitRateField_1').val();
        var fieldType2 = $(event.target).closest('.unitRateChange').find('.unitRateField_2').val();
        const type1 = (fieldType1 == undefined) ? 0 : fieldType1;
        const type2 = (fieldType2 == undefined) ? 0 : fieldType2;
        const value = Number(type1) * Number(type2);
        $(event.target).closest('.templateFieldBg').find('.unitRateTotal').html("Total: $"+value);
        $(event.target).closest('.templateFieldBg').find('.unit_rate_total_value_field').val(value);
      },
      draftStatus(event){
        console.log('24');
        if(event.target.checked){
          this.draftStatusValue = 1;
          $('.draftbox').show();
        }else{
          this.draftStatusValue = 0;
          $('.draftbox').hide();
        }
        
      },
      calculateTotal(templateId,grid,item = null){
        if(item){
          var gridFilteredData = grid.filter(x=>x.category_id == 3);
          this.gridSumable = [];
          for (let index2 = 0; index2 < gridFilteredData.length; index2++) {
            var total = 0;
            for (let index1 = 1; index1 <= this.rowCounter['counter_'+templateId]; index1++) {
              if(this.numberFormula[templateId+'_'+index1+'_'+gridFilteredData[index2].form_field_id]){
                total = total + Number(this.numberFormula[templateId+'_'+index1+'_'+gridFilteredData[index2].form_field_id]);
              }
            }
            this.gridSumable[templateId+'_'+gridFilteredData[index2].form_field_id] = total;
          }
        }else{
          var gridFilteredData = grid.filter(x=>x.docket_field_category_id == 3);
          this.gridSumable = [];
          for (let index2 = 0; index2 < gridFilteredData.length; index2++) {
            var total = 0;
            for (let index1 = 1; index1 <= this.rowCounter['counter_'+templateId]; index1++) {
              if(this.numberFormula[templateId+'_'+index1+'_'+gridFilteredData[index2].id]){
                total = total + Number(this.numberFormula[templateId+'_'+index1+'_'+gridFilteredData[index2].id]);
              }
            }
            this.gridSumable[templateId+'_'+gridFilteredData[index2].id] = total;
          }
        }
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