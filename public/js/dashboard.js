/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 1);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/assets/js/dashboard.js":
/*!******************************************!*\
  !*** ./resources/assets/js/dashboard.js ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

//employee management
__webpack_require__(/*! ../../views/dashboard/company/employeeManagement/index */ "./resources/views/dashboard/company/employeeManagement/index.js");

__webpack_require__(/*! ../../views/dashboard/company/employeeManagement/modal-popup/activate-employee */ "./resources/views/dashboard/company/employeeManagement/modal-popup/activate-employee.js"); //messages/reminders


__webpack_require__(/*! ../../views/dashboard/company/message-reminders/index */ "./resources/views/dashboard/company/message-reminders/index.js");

__webpack_require__(/*! ../../views/dashboard/company/message-reminders/partials/message-user-list */ "./resources/views/dashboard/company/message-reminders/partials/message-user-list.js");

__webpack_require__(/*! ../../views/dashboard/company/message-reminders/partials/chatView */ "./resources/views/dashboard/company/message-reminders/partials/chatView.js");

__webpack_require__(/*! ../../views/dashboard/company/message-reminders/modal-popup/new-group */ "./resources/views/dashboard/company/message-reminders/modal-popup/new-group.js");

__webpack_require__(/*! ../../views/dashboard/company/message-reminders/modal-popup/send-message */ "./resources/views/dashboard/company/message-reminders/modal-popup/send-message.js"); //client management


__webpack_require__(/*! ../../views/dashboard/company/clientManagement/modal-popup/delete-request/delete-request */ "./resources/views/dashboard/company/clientManagement/modal-popup/delete-request/delete-request.js");

__webpack_require__(/*! ../../views/dashboard/company/clientManagement/modal-popup/find-client/find-client */ "./resources/views/dashboard/company/clientManagement/modal-popup/find-client/find-client.js");

__webpack_require__(/*! ../../views/dashboard/company/clientManagement/modal-popup/update-email-client/update-email-client */ "./resources/views/dashboard/company/clientManagement/modal-popup/update-email-client/update-email-client.js");

__webpack_require__(/*! ../../views/dashboard/company/clientManagement/modal-popup/delete-email-client/delete-email-client */ "./resources/views/dashboard/company/clientManagement/modal-popup/delete-email-client/delete-email-client.js"); //docket management


__webpack_require__(/*! ../../views/dashboard/company/docketManager/partials/table-view/table-header/table-header-menu */ "./resources/views/dashboard/company/docketManager/partials/table-view/table-header/table-header-menu.js");

__webpack_require__(/*! ../../views/dashboard/company/docketManager/modal-popup/cancel-docket/cancel-docket */ "./resources/views/dashboard/company/docketManager/modal-popup/cancel-docket/cancel-docket.js");

__webpack_require__(/*! ../../views/dashboard/company/docketManager/modal-popup/docket-label/docket-label */ "./resources/views/dashboard/company/docketManager/modal-popup/docket-label/docket-label.js");

__webpack_require__(/*! ../../views/dashboard/company/docketManager/modal-popup/docket-label/delete-docket-label */ "./resources/views/dashboard/company/docketManager/modal-popup/docket-label/delete-docket-label.js");

__webpack_require__(/*! ../../views/dashboard/company/docketManager/modal-popup/delete-docket/delete-docket */ "./resources/views/dashboard/company/docketManager/modal-popup/delete-docket/delete-docket.js");

__webpack_require__(/*! ../../views/dashboard/company/invoiceManager/modal-popup/invoice-label/invoice-label */ "./resources/views/dashboard/company/invoiceManager/modal-popup/invoice-label/invoice-label.js");

__webpack_require__(/*! ../../views/dashboard/company/invoiceManager/modal-popup/invoice-label/delete-invoice-label */ "./resources/views/dashboard/company/invoiceManager/modal-popup/invoice-label/delete-invoice-label.js"); //docket-template


__webpack_require__(/*! ../../views/dashboard/company/docketManager/docket-template/modal-popup/assign-folder/assign-folder */ "./resources/views/dashboard/company/docketManager/docket-template/modal-popup/assign-folder/assign-folder.js");

__webpack_require__(/*! ../../views/dashboard/company/docketManager/docket-template/modal-popup/unassign-folder/unassign-folder */ "./resources/views/dashboard/company/docketManager/docket-template/modal-popup/unassign-folder/unassign-folder.js"); //invoice management


__webpack_require__(/*! ../../views/dashboard/company/invoiceManager/partials/table-view/table-header/table-header-menu */ "./resources/views/dashboard/company/invoiceManager/partials/table-view/table-header/table-header-menu.js");

__webpack_require__(/*! ../../views/dashboard/company/invoiceManager/create/partials/template/template */ "./resources/views/dashboard/company/invoiceManager/create/partials/template/template.js"); //folder management


__webpack_require__(/*! ../../views/dashboard/company/folder-management/index */ "./resources/views/dashboard/company/folder-management/index.js");

__webpack_require__(/*! ../../views/dashboard/company/folder-management/popup-modal/new-folder/new-folder */ "./resources/views/dashboard/company/folder-management/popup-modal/new-folder/new-folder.js");

__webpack_require__(/*! ../../views/dashboard/company/folder-management/popup-modal/move-folder-item/move-folder-item */ "./resources/views/dashboard/company/folder-management/popup-modal/move-folder-item/move-folder-item.js");

__webpack_require__(/*! ../../views/dashboard/company/folder-management/popup-modal/search-folder/search-folder */ "./resources/views/dashboard/company/folder-management/popup-modal/search-folder/search-folder.js");

__webpack_require__(/*! ../../views/dashboard/company/folder-management/popup-modal/edit-folder/edit-folder */ "./resources/views/dashboard/company/folder-management/popup-modal/edit-folder/edit-folder.js");

__webpack_require__(/*! ../../views/dashboard/company/folder-management/popup-modal/remove-folder/remove-folder */ "./resources/views/dashboard/company/folder-management/popup-modal/remove-folder/remove-folder.js");

__webpack_require__(/*! ../../views/dashboard/company/folder-management/popup-modal/assign-template/assign-template */ "./resources/views/dashboard/company/folder-management/popup-modal/assign-template/assign-template.js");

__webpack_require__(/*! ../../views/dashboard/company/folder-management/popup-modal/folder-filter/folder-filter */ "./resources/views/dashboard/company/folder-management/popup-modal/folder-filter/folder-filter.js");

__webpack_require__(/*! ../../views/dashboard/company/folder-management/popup-modal/recover-folder-item/recover-folder-item */ "./resources/views/dashboard/company/folder-management/popup-modal/recover-folder-item/recover-folder-item.js");

__webpack_require__(/*! ../../views/dashboard/company/folder-management/popup-modal/shareable-folder/shareable-folder */ "./resources/views/dashboard/company/folder-management/popup-modal/shareable-folder/shareable-folder.js");

__webpack_require__(/*! ../../views/shareable-folder/shareable-folder */ "./resources/views/shareable-folder/shareable-folder.js");

__webpack_require__(/*! ../../views/dashboard/company/docketManager/modal-popup/advanced-filter/advanced-filter */ "./resources/views/dashboard/company/docketManager/modal-popup/advanced-filter/advanced-filter.js");

/***/ }),

/***/ "./resources/views/dashboard/company/clientManagement/modal-popup/delete-email-client/delete-email-client.js":
/*!*******************************************************************************************************************!*\
  !*** ./resources/views/dashboard/company/clientManagement/modal-popup/delete-email-client/delete-email-client.js ***!
  \*******************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  $('#deleteEmailClientModal').on('show.bs.modal', function (e) {
    var id = $(e.relatedTarget).data('id');
    $("#deleteEmailClientModal #deleteEmailClientid").val(id);
  });
});

/***/ }),

/***/ "./resources/views/dashboard/company/clientManagement/modal-popup/delete-request/delete-request.js":
/*!*********************************************************************************************************!*\
  !*** ./resources/views/dashboard/company/clientManagement/modal-popup/delete-request/delete-request.js ***!
  \*********************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  $('#deleteRequestModal').on('show.bs.modal', function (e) {
    var id = $(e.relatedTarget).data('id');
    $("#deleteRequestModal #clientid").val(id);
  });
});

/***/ }),

/***/ "./resources/views/dashboard/company/clientManagement/modal-popup/find-client/find-client.js":
/*!***************************************************************************************************!*\
  !*** ./resources/views/dashboard/company/clientManagement/modal-popup/find-client/find-client.js ***!
  \***************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  $('#findClientModal .search-box .form-group input[type="text"]').on("keyup input", function () {
    var inputVal = $(this).val().trim();
    var resultDropdown = $(this).siblings(".searchResult");

    if (inputVal.length >= 3) {
      $.get(base_url + '/dashboard/company/clientManagement/clients/search/' + inputVal).done(function (data) {
        resultDropdown.html(data);
      });
    } else {
      resultDropdown.empty();
    }
  });
  $(document).on('click', '#findClientModal .clientRequested', function () {
    var requestedId = $(this).attr('userId');
    var id = $(this).attr('id');
    $(this).html('<span class="spinner" style="padding: 0 37px 0px 37px;font-size: 14px;"><i class="fa fa-spinner fa-spin"></i></span>');
    $.ajax({
      type: "post",
      data: {
        'id': requestedId
      },
      url: base_url + "/dashboard/company/clientManagement/clients/request",
      success: function success(response) {
        if (response.status == true) {
          if (id == response.id) {
            $("#" + id).removeClass("pull-right btn btn-success  btn-sm btn-raised clientRequested");
            $("#" + id).addClass("pull-right btn btn-secondary btn-sm btn-raised changeText");
            $(".changeText").html('<i class="fa fa-check"></i> ' + 'REQUESTED');
          }
        } else {
          alert(response.message);
        }
      }
    });
  });
});

/***/ }),

/***/ "./resources/views/dashboard/company/clientManagement/modal-popup/update-email-client/update-email-client.js":
/*!*******************************************************************************************************************!*\
  !*** ./resources/views/dashboard/company/clientManagement/modal-popup/update-email-client/update-email-client.js ***!
  \*******************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  $('#updateEmailClientModal').on('show.bs.modal', function (e) {
    var id = $(e.relatedTarget).data('id');
    var email = $(e.relatedTarget).data('email');
    var fullname = $(e.relatedTarget).data('fullname');
    var companyname = $(e.relatedTarget).data('companyname');
    var companyaddress = $(e.relatedTarget).data('companyaddress');
    $("#updateEmailClientModal #id").val(id);
    $("#updateEmailClientModal #email").val(email);
    $("#updateEmailClientModal #fullname").val(fullname);
    $("#updateEmailClientModal #companyname").val(companyname);
    $("#updateEmailClientModal #companyaddress").val(companyaddress);
  });
});

/***/ }),

/***/ "./resources/views/dashboard/company/docketManager/docket-template/modal-popup/assign-folder/assign-folder.js":
/*!********************************************************************************************************************!*\
  !*** ./resources/views/dashboard/company/docketManager/docket-template/modal-popup/assign-folder/assign-folder.js ***!
  \********************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  $('#assignFolderModal').on('show.bs.modal', function (e) {
    var id = $(e.relatedTarget).data('id');
    var title = $(e.relatedTarget).data('name');
    $(".assignFolderName").text(title);
    $('#templateId').val(id);
  });
  $(document).on('click', '#assignFolderModal .submit', function () {
    var templateId = $('#templateId').val();
    var type = 1;
    var folderId = $('#assignFolderId').val();
    var name = $('.assignFolderName').text();
    var assignTempalteErrorMessage = ".assignTempalteErrorMessage";
    $(assignTempalteErrorMessage).css('display', 'none');
    $.ajax({
      type: "Post",
      url: base_url + '/dashboard/company/folder/assignTemplateFolder',
      data: {
        'folderId': folderId,
        'type': type,
        'templateId': templateId,
        'name': name
      },
      success: function success(response) {
        if (response.status == true) {
          $(response.buttonConfig[1]).replaceWith(response.buttonConfig[0]);
          $(response.buttonConfig[2]).replaceWith(response.buttonConfig[3]);
          $('#assignFolderModal').modal('hide');
        } else if (response.status == false) {
          $(assignTempalteErrorMessage).css('display', 'block');
          $(assignTempalteErrorMessage).html('<i class="fa fa-exclamation-circle"></i> ' + response.message);
        }
      }
    });
  });
});

/***/ }),

/***/ "./resources/views/dashboard/company/docketManager/docket-template/modal-popup/unassign-folder/unassign-folder.js":
/*!************************************************************************************************************************!*\
  !*** ./resources/views/dashboard/company/docketManager/docket-template/modal-popup/unassign-folder/unassign-folder.js ***!
  \************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  $('#unassignFolderModal').on('show.bs.modal', function (e) {
    var folderId = $(e.relatedTarget).data('folderid');
    var templateId = $(e.relatedTarget).data('id');
    var templateName = $(e.relatedTarget).data('name');
    $("#unassignFolderModal #unassignFolderId").val(folderId);
    $("#unassignFolderModal #unassignTemplateId").val(templateId);
    $("#unassignFolderModal #unassignTemplateName").val(templateName);
  });
  $(document).on('click', '#unassignFolderModal .submit', function () {
    var folderId = $("#unassignFolderId").val();
    var templateId = $("#unassignTemplateId").val();
    var templateName = $("#unassignTemplateName").val();
    var assignTempalteErrorMessage = ".unassignTempalteErrorMessage";
    $(assignTempalteErrorMessage).css('display', 'none');
    $.ajax({
      type: "Post",
      url: base_url + '/dashboard/company/folder/unassignTemplateFolder',
      data: {
        'folderId': folderId,
        'templateId': templateId,
        'templateName': templateName
      },
      success: function success(response) {
        if (response.status == true) {
          $(response.buttonConfig[1]).replaceWith(response.buttonConfig[0]);
          $(response.buttonConfig[2]).replaceWith(response.buttonConfig[3]);
          $('#unassignFolderModal').modal('hide');
        } else if (response.status == false) {
          $(assignTempalteErrorMessage).css('display', 'block');
          $(assignTempalteErrorMessage).html('<i class="fa fa-exclamation-circle"></i> ' + response.message);
        }
      }
    });
  });
});

/***/ }),

/***/ "./resources/views/dashboard/company/docketManager/modal-popup/advanced-filter/advanced-filter.js":
/*!********************************************************************************************************!*\
  !*** ./resources/views/dashboard/company/docketManager/modal-popup/advanced-filter/advanced-filter.js ***!
  \********************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).on('change', '.selectDocketTemplate', function () {
  console.log($(this).find(":selected").val());
  var docketTemplateId = $(this).find(":selected").val();
  $.ajax({
    type: "post",
    url: base_url + '/dashboard/company/docketBookManager/dockets/docketfieldName',
    data: {
      docketTemplateId: docketTemplateId
    },
    success: function success(response) {
      $('.docketFieldNameSelect').html(response);
    }
  });
});

/***/ }),

/***/ "./resources/views/dashboard/company/docketManager/modal-popup/cancel-docket/cancel-docket.js":
/*!****************************************************************************************************!*\
  !*** ./resources/views/dashboard/company/docketManager/modal-popup/cancel-docket/cancel-docket.js ***!
  \****************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  $('#cancelDocketModal').on('show.bs.modal', function (e) {
    $('#cancelDocketModal .flash-message').css('display', 'none');
    $('#cancelDocketModal .submit').html('Yes');
    $('#cancelDocketModal #cancelid').val($(e.relatedTarget).attr('data-id'));
    $('#cancelDocketModal #canceltype').val($(e.relatedTarget).attr('data-type'));
  });
  $(document).on('click', '#cancelDocketModal .submit', function () {
    var id = $('#cancelDocketModal #cancelid').val();
    var type = $('#cancelDocketModal #canceltype').val();
    $(this).html('<span class="spinner" style="padding: 0 37px 0px 37px;font-size: 14px;"><i class="fa fa-spinner fa-spin"></i></span>');
    $.ajax({
      type: "Post",
      url: base_url + '/dashboard/company/docketBookManager/dockets/cancelDocket',
      data: {
        'type': type,
        'id': id
      },
      success: function success(response) {
        if (response.status == true) {
          var parentTr = $("#docketLabelIdentify" + response.id).parents("tr");
          parentTr.addClass("cancelled");
          parentTr.children('td').eq(4).html("");
          parentTr.children('td').eq(4).append('<span class="label label-danger">Cancelled</span>');
          parentTr.children().last('td').children().eq(1).hide();
          parentTr.children().last('td').children().eq(2).hide();
          parentTr.children().last('td').children().eq(3).hide();
          $('#cancelDocketModal').modal('hide');
        } else if (response.status == false) {
          $('#cancelDocketModal .submit').html('Yes');
          $('#cancelDocketModal .flash-message').fadeIn();
          $('#cancelDocketModal .flash-message .message').html(response.message);
        }
      }
    });
  });
});

/***/ }),

/***/ "./resources/views/dashboard/company/docketManager/modal-popup/delete-docket/delete-docket.js":
/*!****************************************************************************************************!*\
  !*** ./resources/views/dashboard/company/docketManager/modal-popup/delete-docket/delete-docket.js ***!
  \****************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  $('#deleteSentDocket').on('show.bs.modal', function (e) {
    $('#deleteSentDocket .flash-message').css('display', 'none');
    $('#deleteSentDocket .submit').html('Yes');
    $('#deleteSentDocket #deleteDocketIds').val($(e.relatedTarget).attr('data-id'));
    $('#deleteSentDocket #deleteDocketTypes').val($(e.relatedTarget).attr('data-type'));
  });
  $(document).on('click', '#deleteSentDocket .submit', function () {
    var id = $('#deleteSentDocket #deleteDocketIds').val();
    var type = $('#deleteSentDocket #deleteDocketTypes').val();
    $(this).html('<span class="spinner" style="padding: 0 37px 0px 37px;font-size: 14px;"><i class="fa fa-spinner fa-spin"></i></span>');
    $.ajax({
      type: "Post",
      url: base_url + '/dashboard/company/docketBookManager/dockets/submitDeleteDocket',
      data: {
        'type': type,
        'id': id
      },
      success: function success(response) {
        if (response.status == true) {
          $.map($('.selectitem'), function (el) {
            if ($(el).val() == id) {
              console.log($(el).parent().parent('tr').remove());
            }
          });

          if (response.type == "create") {
            $(".rtTree").append(' <li><a href="#" id="' + response.newFolderId + '">' + response.newFolderName + '<span style="    position: absolute;right: 4px;">' + response.totalItem + '</span></a><ul></ul> <div   data-id="' + response.newFolderId + '" data-title="' + response.newFolderName + '" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div></li>');
          } else if (response.type == "update") {
            var test = $('.boxContent .rtTree li #' + response.id + ' span').text('(' + response.data + ')');
          }

          $('#deleteSentDocket').modal('hide');
        } else if (response.status == false) {
          $('#deleteSentDocket .submit').html('Yes');
          $('#deleteSentDocket .flash-message').fadeIn();
          $('#deleteSentDocket .flash-message .message').html(response.message);
        }
      }
    });
  });
});

/***/ }),

/***/ "./resources/views/dashboard/company/docketManager/modal-popup/docket-label/delete-docket-label.js":
/*!*********************************************************************************************************!*\
  !*** ./resources/views/dashboard/company/docketManager/modal-popup/docket-label/delete-docket-label.js ***!
  \*********************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  $('#deleteDocketLabelModal').on('show.bs.modal', function (e) {
    $('#deleteDocketLabelModal .flash-message').css('display', 'none');
    $('#deleteDocketLabelModal .submit').html('Yes');
    $('#deleteDocketLabelModal .docket-label-id').val($(e.relatedTarget).attr('data-id'));
    $('#deleteDocketLabelModal .type').val($(e.relatedTarget).attr('data-type'));
  });
  $(document).on('click', '#deleteDocketLabelModal .submit', function () {
    var id = $('#deleteDocketLabelModal .docket-label-id').val();
    var type = $('#deleteDocketLabelModal .type').val();
    $(this).html('<span class="spinner" style="padding: 0 37px 0px 37px;font-size: 14px;"><i class="fa fa-spinner fa-spin"></i></span>');
    $.ajax({
      type: "Post",
      url: base_url + '/dashboard/company/docketBookManager/dockets/labels/delete',
      data: {
        'type': type,
        'id': id
      },
      success: function success(response) {
        if (response.status == true) {
          $('.docket-label-' + response.id).remove();
          $('#deleteDocketLabelModal').modal('hide');
        } else if (response.status == false) {
          $('#deleteDocketLabelModal .submit').html('Save');
          $('#deleteDocketLabelModal .flash-message').fadeIn();
          $('#deleteDocketLabelModal .flash-message .message').html(response.message);
        }
      }
    });
  });
});

/***/ }),

/***/ "./resources/views/dashboard/company/docketManager/modal-popup/docket-label/docket-label.js":
/*!**************************************************************************************************!*\
  !*** ./resources/views/dashboard/company/docketManager/modal-popup/docket-label/docket-label.js ***!
  \**************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  try {
    var slimSelect = new SlimSelect({
      select: '#docketLabelModal .slim-select',
      addToBody: false,
      placeholder: 'Select Label'
    });
    $('#docketLabelModal').on('show.bs.modal', function (e) {
      $('#docketLabelModal .flash-message').css('display', 'none');
      $('#docketLabelModal .submit').html('Save');
      slimSelect.set([]);
      $('#docketLabelModal .form-group').removeClass('has-error');
      $('#docketLabelModal .docket-company-id').html($(e.relatedTarget).attr('data-formatted-id'));
      $('#docketLabelModal #docket-id').val($(e.relatedTarget).attr('data-id'));
      $('#docketLabelModal #docket-type').val($(e.relatedTarget).attr('data-type'));
    });
    $(document).on('click', '#docketLabelModal .flash-message .close', function () {
      $('#docketLabelModal .flash-message').fadeOut();
    });
    $(document).on('click', '#docketLabelModal .submit', function () {
      if (slimSelect.selected().length == 0) {
        $('#docketLabelModal .flash-message').fadeIn();
        $('#docketLabelModal .flash-message .message').html('Please select Docket Label');
      } else {
        $('#docketLabelModal .flash-message').fadeOut();
        var id = $('#docketLabelModal #docket-id').val();
        var type = $('#docketLabelModal #docket-type').val();
        var value = slimSelect.selected();
        $(this).html('<span class="spinner" style="padding: 0 37px 0px 37px;font-size: 14px;"><i class="fa fa-spinner fa-spin"></i></span>');
        $.ajax({
          type: "Post",
          url: base_url + '/dashboard/company/docketBookManager/dockets/labels/assign',
          data: {
            'type': type,
            'id': id,
            'value': value
          },
          success: function success(response) {
            console.log(response);

            if (response.status == true) {
              $('.docket-label-container #' + response.id + " ul").append(response.html);
              $('#docketLabelModal').modal('hide');
            } else if (response.status == false) {
              $('#docketLabelModal .submit').html('Save');
              $('#docketLabelModal .flash-message').fadeIn();
              $('#docketLabelModal .flash-message .message').html(response.message);
            }
          }
        });
      }
    });
  } catch (e) {}
});

/***/ }),

/***/ "./resources/views/dashboard/company/docketManager/partials/table-view/table-header/table-header-menu.js":
/*!***************************************************************************************************************!*\
  !*** ./resources/views/dashboard/company/docketManager/partials/table-view/table-header/table-header-menu.js ***!
  \***************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  //export dockets
  $(document).on("change", ".rtDataTable th .checkbox", function () {
    if ($(this).is(":checked")) {
      $(".rtDataTable td .checkbox").prop('checked', true);
    } else {
      $(".rtDataTable td .checkbox").prop('checked', false);
    }
  });
  $(document).on('click', '.rtDataTableHeaderMenu #exportcsv', function () {
    if ($('.rtDataTable .selectitem:checked').serialize() == "") {
      alert("Please Select Docket");
    } else {
      var url = base_url + '/dashboard/company/docketBookManager/docket/exportAllDocket' + "?" + $('.rtDataTable .selectitem:checked').serialize();
      window.open(url, "_blank");
    }
  });
  $(document).on('click', '.rtDataTableHeaderMenu #exportpdf', function () {
    if ($('.rtDataTable .selectitem:checked').serialize() == "") {
      alert("Please Select Docket");
    } else {
      var url = base_url + '/dashboard/company/docketBookManager/docket/downloadZip' + "?" + $('.rtDataTable .selectitem:checked').serialize();
      window.open(url, "_blank");
    }
  }); //--per page item selection--//

  $(document).on('change', '.rtDataTableHeaderMenu .selectPaginate', function () {
    doStuff();
  }); //searching

  var timer = null;
  $('.rtDataTableHeaderMenu #searchInput').keydown(function () {
    clearTimeout(timer);
    timer = setTimeout(doStuff, 1000);
  });

  function doStuff() {
    var url = $('.rtDataTableHeaderMenu').attr('dataCurrentURL') + '?search=';
    $(".rtDataTable").html('<div style="position: absolute;left: 50%;top: 64%;font-weight: bold;text-align:center;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="margin-bottom:10px;"></i></div>');
    var paginate = $('.rtDataTableHeaderMenu .selectPaginate').val();

    if ($('.rtDataTableHeaderMenu #searchInput').val().length > 0) {
      $.ajax({
        type: "GET",
        data: {
          items: paginate
        },
        url: url + $('#searchInput').val(),
        success: function success(response) {
          if (response == "") {} else {
            $(".datatable").html(response).show();
          }
        }
      });
    } else {
      $.ajax({
        type: "GET",
        data: {
          data: "all",
          items: paginate
        },
        url: url,
        success: function success(response) {
          if (response == "") {} else {
            $(".datatable").html(response).show();
          }
        }
      });
    }
  }
});

/***/ }),

/***/ "./resources/views/dashboard/company/employeeManagement/index.js":
/*!***********************************************************************!*\
  !*** ./resources/views/dashboard/company/employeeManagement/index.js ***!
  \***********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  $('#employeeListDatatable').on('change', 'tbody input.receiveDocketCopy', function () {
    $.ajax({
      type: "POST",
      url: base_url + '/dashboard/company/employeeManagement/employees/receiveDocketCopy',
      data: {
        "data": $(this).attr("data"),
        "status": this.checked ? 1 : 0
      },
      success: function success(msg) {
        if (msg != "") {
          alert(msg);
        }
      }
    });
  });
  $('#employeeListDatatable').DataTable({
    "order": [[4, "asc"]]
  });
});

/***/ }),

/***/ "./resources/views/dashboard/company/employeeManagement/modal-popup/activate-employee.js":
/*!***********************************************************************************************!*\
  !*** ./resources/views/dashboard/company/employeeManagement/modal-popup/activate-employee.js ***!
  \***********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  $('#activateEmployeeModal').on('show.bs.modal', function (e) {
    var id = $(e.relatedTarget).data('id');
    $("#activateEmployeeModal #empolyeeid").val(id);
  });
});

/***/ }),

/***/ "./resources/views/dashboard/company/folder-management/index.js":
/*!**********************************************************************!*\
  !*** ./resources/views/dashboard/company/folder-management/index.js ***!
  \**********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  $("ul.rtTree").rtTree({
    clickListItem: base_url + '/dashboard/company/folder',
    viewFolderData: base_url + '/dashboard/company/folder/viewFolderData',
    addFolder: base_url + '/dashboard/company/folder/newFolderCreate',
    createFolderSelect: base_url + '/dashboard/company/folder/createFolderSelect',
    ajaxCompletion: function test(response) {
      var items = [];
      $.each(response.data, function () {
        items.push($(".rtTree a.active").siblings("ul").append('<li><a href="#" id="' + this.id + '" >' + this.name + ' <span style="    position: absolute;right: 4px;">' + this.totalItems + '</span></a><ul></ul>  <div  class="editBtn" id="editBtnId" data-id="' + this.id + '" data-title="' + this.name + '" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div> </li>'));
      });
      window.location.hash = "&folderId=" + $(".rtTree a.active").attr('id');
      $(".viewFolder").html(response).show();
      var timers = null;
      $('#searchFolderInputs').keydown(function () {
        clearTimeout(timers);
        timers = setTimeout(doStuffs, 1000);
      });
      dearch();
    }
  }); //folder edit button action

  $(document).on('click', '.rtTree .editBtn', function () {
    $('.editBtn>div').remove();
    var div = '<div class="folder-div"><ul class="divList" style="margin: 0;"><li><button style="border: none; width: 100%;text-align: left; border-radius: 0 16px 16px 0;padding: 4px 12px;" class="folderEdit" data-editId="' + $(this).data('id') + '" data-editName="' + $(this).data('title') + '">Edit</button></li><li><button style="border: none;    width: 100%;text-align: left; border-radius: 0 16px 16px 0;padding: 4px 12px;" class="folderRemove" data-removeId="' + $(this).data('id') + '"  data-title="' + $(this).data('title') + '">Remove Folder</button></li>        <li><button style="border: none;width: 100%;text-align: left; border-radius: 0 16px 16px 0;padding: 4px 12px;" class="assignTemplate" data-removeId="' + $(this).data('id') + '"  data-title="' + $(this).data('title') + '">Assign Template</button></li> <li><button style="border: none;width: 100%;text-align: left; border-radius: 0 16px 16px 0;padding: 4px 12px;" class="downloadFolderPdf" data-FolderId="' + $(this).data('id') + '"  data-title="' + $(this).data('title') + '">Download Pdf</button></li>   <li><button style="border: none; width: 100%;text-align: left; border-radius: 0 16px 16px 0;padding: 4px 12px;" class="shareableFolder" data-editId="' + $(this).data('id') + '" data-editName="' + $(this).data('title') + '">Share Folder</button></li>       </ul></div>';
    $(this).append(div);
  });
  $(document).on('mouseenter', '.rtTree li a', function () {
    $(this).siblings('.editBtn').addClass('editBtnHover');
  });
  $(document).on('mouseleave', '.rtTree li a', function () {
    $(this).siblings('.editBtn').removeClass('editBtnHover');
  });
  $(document).on('mouseleave', '.rtTree .editBtn', function () {
    if ($(this).siblings("a").hasClass("active")) {} else {
      $(this).siblings("a").css("background", "");
    }
  });
  $(document).on('mouseenter', '.rtTree .editBtn', function () {
    if ($(this).siblings("a").hasClass("active")) {} else {
      $(this).siblings("a").css("background", "#f7f7f7");
    }
  });

  window.onload = function () {
    var hideMe = document.getElementById('folder-div');

    document.onclick = function (e) {
      if ($(e.target).hasClass("folder-div") || $(e.target).hasClass("editBtn")) {} else {
        $('.editBtn>div').remove();
      }
    };
  };

  $(document).on('click', '.folderEdit', function () {
    $('#updateFolderModal').modal('show');
    var editIds = $(this).attr('data-editId');
    var editNames = $(this).attr('data-editName');
    $("#updateFolderModal #editNameFolder").val(editNames);
    $("#updateFolderModal #editIdFolder").val(editIds);
  });
  $(document).click(function (evt) {
    if (evt.target.className != 'editBtn') {
      $.map($(".editBtn"), function (el) {
        if ($(el).children('.folder-div').hasClass('folder-div')) {
          $(el).children('.folder-div').remove();
        } else {}
      });
    }
  });
  $(document).on('click', '.rtTree .folderRemove', function () {
    $('#removeFolderModal').modal('show');
    $('.editBtn>div').remove();
    var deleteId = $(this).attr('data-removeId');
    var folderTitle = $(this).attr('data-title');
    $(".deleteMessage").html("Are you sure you want to remove " + "<i class='material-icons' style='font-size: 16px;color: #eece4a;'> " + "folder" + "</i> <b>" + folderTitle + "</b>?");
    $("#removeFolderid").val(deleteId);
  });
  $("#assignTemplateName").chained("#assignTemplateType");
  $(document).on('click', '.rtTree .assignTemplate', function () {
    $('#assignTemplateModal').modal('show');
    var folderId = $(this).attr('data-removeId');
    var folderTitle = $(this).attr('data-title');
    $("#assignTemplateModal .assignFolderName").text(folderTitle);
    $("#assignTemplateId").val(folderId);
    $('.assignTempalteErrorMessage').css('display', 'none');
  });
  $(document).on('click', '.rtTree .downloadFolderPdf', function () {
    var folderId = $(this).attr('data-FolderId');
    var url = $(this).attr('href');
    console.log(base_url);
    $.ajax({
      type: 'post',
      url: base_url + '/dashboard/company/folder/downloadPdf',
      data: {
        'folderId': folderId
      },
      success: function success(response) {
        window.open(base_url + '/zipFile/' + response.messages, '_blank');
      }
    });
  }); // $(".filterempolyeess").chained(".filtercompanys");
  //--per page item selection--//

  $(document).on('change', '.rtDataTableHeaderMenu .selectPaginateFolder', function () {
    doStuffs();
  });

  function doStuffs() {
    var url = $('.rtDataTableHeaderMenu').attr('dataCurrentURL') + '?search=';
    var folderID = $('#removeItemFolderId').val();
    var paginate = $('.rtDataTableHeaderMenu .selectPaginateFolder').val();
    $(".rtDataTable").html('<div style="position: absolute;left: 50%;top: 64%;font-weight: bold;text-align:center;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="margin-bottom:10px;"></i></div>');

    if ($('.rtDataTableHeaderMenu #searchFolderInputs').val().length > 0) {
      $.ajax({
        type: "GET",
        data: {
          'folderId': folderID,
          items: paginate
        },
        url: url + $('#searchFolderInputs').val(),
        success: function success(response) {
          if (response == "") {} else {
            $(".searchViewItems").html(response).show();
          }
        }
      });
    } else {
      $.ajax({
        type: "GET",
        data: {
          data: "all",
          'folderId': folderID,
          items: paginate
        },
        url: url,
        success: function success(response) {
          if (response == "") {} else {
            $(".searchViewItems").html(response).show();
          }
        }
      });
    }
  }

  $(document).on('click', '#searchFolder', function (e) {
    e.preventDefault();
    $('#searchFolderModel').modal('show');
  });
  $(document).on('click', '.rtDataTable #folderPagination ul li a', function (e) {
    e.preventDefault(); //

    var page = $(this).text(); // if(typeof(url) != "undefined"){
    //     var page = url.split('page=')[1];
    // }

    var paginate = $('.rtDataTableHeaderMenu .selectPaginateFolder').val();
    var search = $('.rtMenuSearch').val();
    var id = $('#removeItemFolderId').val();
    var url = $(this).attr('href');
    $.ajax({
      type: 'post',
      url: base_url + '/dashboard/company/folder/viewFolderData?items=' + paginate + '&page=' + page,
      data: {
        'page': page,
        'folderId': id
      },
      success: function success(response) {
        $(".viewFolder").html(response).show();
        window.location.hash = "&folderId=" + id + "&search=" + search + "&items=" + paginate + '&page=' + page;
        dearch();
      }
    });
  });
  $(document).on('click', '#MyModalFolderFilters', function (e) {
    e.preventDefault();
    $('#MyModalFolderFilter').modal('show');
    $(".dateInput").datepicker({
      dateFormat: 'dd-mm-yy'
    });
  });

  if (typeof location.hash.split('#')[1] !== "undefined") {
    var folderId = location.hash.split('#')[1].split('&')[1].split('=')[1];

    if (typeof location.hash.split('#')[1].split('&')[2] !== "undefined") {
      var searchValue = location.hash.split('#')[1].split('&')[2].split('=')[1].replace("%20", " ");
    } else {
      var searchValue = "";
    }

    if (typeof location.hash.split('#')[1].split('&')[3] !== "undefined") {
      var items = location.hash.split('#')[1].split('&')[3].split('=')[1];
      var page = location.hash.split('#')[1].split('&')[4].split('=')[1];
    } else {
      var items = "";
      var page = "";
    }

    if (items != "" && page != "") {
      var searchValueitems = items;
      var searchValuepage = page;
    } else {
      var searchValueitems = 10;
      var searchValuepage = 1;
    }

    if (searchValue == "") {
      var searchValueSend = "";
    } else {
      var searchValueSend = searchValue;
    }

    console.log(searchValueSend);
    console.log(searchValue);
    $.ajax({
      type: "get",
      url: base_url + '/dashboard/company/folder/viewFolderReload?search=' + searchValueSend + '&' + 'folderId=' + folderId,
      data: {
        'page': searchValuepage,
        'items': searchValueitems
      },
      success: function success(response) {
        $.ajax({
          type: 'post',
          url: base_url + '/dashboard/company/folder/searchFolderById',
          data: {
            'id': folderId
          },
          success: function success(response) {
            $(".boxContent").html(response.detail).show();
            $("ul.rtTree").rtTree({
              clickListItem: base_url + '/dashboard/company/folder',
              viewFolderData: base_url + '/dashboard/company/folder/viewFolderData',
              addFolder: base_url + '/dashboard/company/folder/newFolderCreate',
              createFolderSelect: base_url + '/dashboard/company/folder/createFolderSelect',
              ajaxCompletion: function test(response) {
                var items = [];
                $.each(response.data, function () {
                  items.push($(".rtTree a.active").siblings("ul").append('<li><a href="#" id="' + this.id + '">' + this.name + ' <span style="    position: absolute;right: 4px;">' + this.totalItems + '</span></a><ul></ul>  <div  class="editBtn" id="editBtnId" data-id="' + this.id + '" data-title="' + this.name + '" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div> </li>'));
                });
                window.location.hash = "&folderId=" + $(".rtTree a.active").attr('id');
                $(".viewFolder").html(response).show();
              }
            });

            if ($('.boxContent .rtTree .active').attr('id') == folderId) {
              $('.boxContent .rtTree li ul').css('display', 'none'); // jQuery.each( $('.boxContent .rtTree .active').parents('ul') , function( i, value) {
              //     $(value).css('display', '');
              // })

              if ($('.boxContent .rtTree .active').parent('li').first().parent('ul').attr('class') == 'rtTree') {
                $('.boxContent .rtTree .active').parent('li').first().children('ul').css('display', '');
              } else {
                jQuery.each($('.boxContent .rtTree .active').parents('ul'), function (i, value) {
                  $(value).css('display', '');
                });
                $('.boxContent .rtTree .active').parent('li').children('ul').css('display', ''); // $('.boxContent .rtTree .active').parents('ul').length
              } // console.log($('.boxContent .rtTree .active').parent('li').first().parent('ul').attr('class')=='rtTree')
              // if($('.boxContent .rtTree .active').parent('li').first().parent('ul').attr('class')=='rtTree'){
              //
              //     console.log(('.boxContent .rtTree li ul').css('display','none'));
              //
              //    // $('.boxContent .rtTree ').children().first('ul').css('display','none');
              // }else{
              //     console.log($('.boxContent .rtTree .active').parent('li').first().parent('ul').css('display','none'));
              // }

            }
          }
        });
        $(".viewFolder").html(response).show(); // $(".searchViewItems").html(response).show();

        $('.rtMenuSearch').val(searchValue);
        dearch();
      }
    });
  }

  $(document).on('click', '.rtDataTable #reloadFolderPagination ul li a', function (e) {
    e.preventDefault();
    var url = $(this).attr('href');
    var page = url.split('page=')[1];
    var paginate = $('.rtDataTableHeaderMenu .selectPaginateFolder').val();
    console.log(location.hash.split('#')[1]);
    var folderId = location.hash.split('#')[1].split('&')[1].split('=')[1];

    if (typeof location.hash.split('#')[1].split('&')[2] !== "undefined") {
      var searchValue = location.hash.split('#')[1].split('&')[2].split('=')[1].replace("%20", " ");
    } else {
      var searchValue = "";
    }

    if (searchValue == "") {
      var searchValueSend = "";
    } else {
      var searchValueSend = searchValue;
    }

    $.ajax({
      type: 'get',
      url: base_url + '/dashboard/company/folder/viewFolderReload?items=' + paginate + '&page=' + page,
      data: {
        folderId: folderId,
        search: searchValueSend
      },
      success: function success(response) {
        $(".viewFolder").html(response).show();
        $('.rtMenuSearch').val(searchValue);
        window.location.hash = "&folderId=" + folderId + "&search=" + searchValueSend + "&items=" + paginate + '&page=' + page;
        dearch();
      }
    });
  });
  $(document).on('click', '.rtDataTable #searchFolderPagination ul li a', function (e) {
    e.preventDefault();
    var url = $(this).attr('href');
    var page = url.split('page=')[1];
    var paginate = $('.rtDataTableHeaderMenu .selectPaginateFolder').val();
    var id = $('#removeItemFolderId').val();
    var search = $('.rtMenuSearch').val();
    $.ajax({
      type: 'get',
      url: base_url + '/dashboard/company/folder/searchFolderItems?search=' + search + '&' + 'folderId=' + id,
      data: {
        'page': page,
        'items': paginate
      },
      success: function success(response) {
        $(".searchViewItems").html(response).show();
        $('.rtMenuSearch').val(search);
        window.location.hash = "&folderId=" + id + "&search=" + search + "&items=" + paginate + '&page=' + page;
        dearch();
      }
    });
  });

  function dearch() {
    $(document).ready(function () {
      var timers = null;
      $('#searchFolderInputs').keydown(function () {
        clearTimeout(timers);
        timers = setTimeout(doStuffs, 1000);
      });

      function doStuffs() {
        $(".datatable").html('<div style="position: absolute;left: 50%;top: 64%;font-weight: bold;text-align:center;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="margin-bottom:10px;"></i></div>');

        if ($('#searchFolderInputs').val().length > 0) {
          $.ajax({
            type: "get",
            url: base_url + '/dashboard/company/folder/searchFolderItems?search=' + $('#searchFolderInputs').val() + '&' + 'folderId=' + $('#removeItemFolderId').val(),
            success: function success(response) {
              if (response == "") {} else {
                window.location.hash = '&' + location.hash.split('#')[1].split('&')[1] + "&search=" + $('#searchFolderInputs').val();
                $(".searchViewItems").html(response).show();
              }
            }
          });
        } else {
          $.ajax({
            type: "get",
            data: {
              data: "all",
              'folderId': $('#removeItemFolderId').val()
            },
            url: base_url + '/dashboard/company/folder/searchFolderItems?search=',
            success: function success(response) {
              if (response == "") {} else {
                window.location.hash = '&' + location.hash.split('#')[1].split('&')[1] + "&search=";
                $(".searchViewItems").html(response).show();
              }
            }
          });
        }
      }
    });
  }
});

/***/ }),

/***/ "./resources/views/dashboard/company/folder-management/popup-modal/assign-template/assign-template.js":
/*!************************************************************************************************************!*\
  !*** ./resources/views/dashboard/company/folder-management/popup-modal/assign-template/assign-template.js ***!
  \************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).on('click', '#assignTemplateModal .submit', function () {
  var assignTempalteErrorMessage = ".assignTempalteErrorMessage";
  $(assignTempalteErrorMessage).css('display', 'none');
  var id = $('#assignTemplateId').val();
  var type = $('#assignTemplateType').val();
  var name = $('#assignTemplateName').val();
  $.ajax({
    type: "Post",
    url: base_url + '/dashboard/company/folder/assignTemplateFolder',
    data: {
      'folderId': id,
      'type': type,
      'templateId': name
    },
    success: function success(response) {
      if (response.status == true) {
        $('#assignTemplateModal').modal('hide');
      } else if (response.status == false) {
        $(assignTempalteErrorMessage).css('display', 'block');
        $(assignTempalteErrorMessage).html('<i class="fa fa-exclamation-circle"></i> ' + response.message);
      }
    }
  });
});

/***/ }),

/***/ "./resources/views/dashboard/company/folder-management/popup-modal/edit-folder/edit-folder.js":
/*!****************************************************************************************************!*\
  !*** ./resources/views/dashboard/company/folder-management/popup-modal/edit-folder/edit-folder.js ***!
  \****************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).on('click', '#updateFolderModal .submit', function () {
  var folderId = $('#editIdFolder').val();
  var folderName = $('#editNameFolder').val();
  $.ajax({
    type: "Post",
    url: base_url + '/dashboard/company/folder/updateFolder',
    data: {
      'id': folderId,
      'title': folderName
    },
    success: function success(response) {
      if (response.status == true) {
        $(".rtTree li a[id=" + response.id + "]").html(response.title + '<span style="    position: absolute;right: 4px;">' + response.totalItems + '</span>');
        $(".rtTree li .editBtn").data('title', response.title);
      } else {
        alert("Invalid action.");
      }

      if ($(".rtTree li a[id=" + response.id + "]").hasClass('active')) {
        $('.rtTabHeader ul li h4').html(response.title + '<span style="position: absolute;right: 4px;">' + response.totalItems + '</span>');
      }

      $('#updateFolderModal').modal('hide');
    }
  });
});

/***/ }),

/***/ "./resources/views/dashboard/company/folder-management/popup-modal/folder-filter/folder-filter.js":
/*!********************************************************************************************************!*\
  !*** ./resources/views/dashboard/company/folder-management/popup-modal/folder-filter/folder-filter.js ***!
  \********************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$('#MyModalFolderFilter').on('show.bs.modal', function (e) {
  var type = $("#folderSelect").find(":checked").val();
  $(".spinnerCheck").css('display', 'block');
  $.ajax({
    type: "Post",
    url: base_url + '/dashboard/company/folder/showFolderAdvanceFilter',
    data: {
      'type': type
    },
    success: function success(response) {
      $("#folderContentFilter").html(response).show();
      $(".filterempolyeess").chained(".filtercompanys");
      $(".dateInput").datepicker({
        dateFormat: 'dd-mm-yy'
      });
      $(".spinnerCheck").css('display', 'none');
    }
  });
});
$(document).on('change', '#folderSelect', function () {
  var type = $(this).find(":checked").val();
  $(".spinnerCheck").css('display', 'block');
  $.ajax({
    type: "Post",
    url: base_url + '/dashboard/company/folder/showFolderAdvanceFilter',
    data: {
      'type': type
    },
    success: function success(response) {
      $("#folderContentFilter").html(response).show();
      $(".filterempolyeess").chained(".filtercompanys");
      $(".dateInput").datepicker({
        dateFormat: 'dd-mm-yy'
      });
      $(".spinnerCheck").css('display', 'none');
    }
  });
});
$(document).on('click', '.submitData', function () {
  var type = $('.filterType').val();
  var company = $('#filtercompany').val();
  var employee = $('#filterempolyees').val();
  var itemName = $('#itemName').val();
  var itemId = $('#itemId').val();
  var itemDateCat = $('#itemDateCat').val();
  var itemDateFrom = $('.itemDateFrom').val();
  var itemDateto = $('.itemDateto').val();
  var invoiceable = $(".invoiceableFilter").attr("checked") ? 1 : null;
  var emailFilter = $('#emailFilter').val();
  var folder_id = $('#removeItemFolderId').val();
  var data = $('.folderFilter .docketFieldNameSelect div div input');
  var docketFieldValue = [];
  $(data).each(function (index) {
    if ($(this).val() == "") {
      docketFieldValue[index] = $(this).attr('name').split("[")[1].split(']')[0] + "-" + "null";
    } else {
      docketFieldValue[index] = $(this).attr('name').split("[")[1].split(']')[0] + "-" + $(this).val();
    }
  });
  $.ajax({
    type: "Post",
    url: base_url + '/dashboard/company/folder/advanceSearch/AdvanceFilter',
    data: {
      'type': type,
      'company': company,
      'employee': employee,
      'TemplateId': itemName,
      'id': itemId,
      'date': itemDateCat,
      'from': itemDateFrom,
      'to': itemDateto,
      'invoiceable': invoiceable,
      'email': emailFilter,
      'folder_id': folder_id,
      'docketFieldValue': docketFieldValue
    },
    success: function success(response) {
      $("#folderAdvanceFilterView").html(response).show();
      $("#folderAdvanceFilterFooterView").css('display', 'none');
      $('#MyModalFolderFilter').modal('hide');
    }
  });
});

/***/ }),

/***/ "./resources/views/dashboard/company/folder-management/popup-modal/move-folder-item/move-folder-item.js":
/*!**************************************************************************************************************!*\
  !*** ./resources/views/dashboard/company/folder-management/popup-modal/move-folder-item/move-folder-item.js ***!
  \**************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).on('click', '.rtDataTableHeaderMenu #moveFolder', function () {
  if ($('.rtDataTable .selectitem:checked').serialize() == "") {
    alert("Please select the item you want to move.");
  } else {
    $('#moveFolderItemModal').modal('show', function () {});
    $('#moveFolderItemModal .re-mover').val($(this).attr('type'));
    $("#moveFolderItemModal #folderLabel").html("");
    $("#moveFolderItemModal .spinerSubDocket").css("display", "block");
    $.ajax({
      type: "GET",
      url: base_url + '/dashboard/company/folder/getFolderStru',
      success: function success(response) {
        $("#moveFolderItemModal .spinerSubDocket").css("display", "none");
        $("#moveFolderItemModal #folderLabel").html(response);
      }
    });
  }
});
$(document).on('click', '#moveFolderItemModal .submit', function () {
  var typeValue = $('.re-mover').val();

  if (typeValue == 1) {
    //1-alldockets, 2-allinvoice,3 sentdockets, 4 received dockets, 5 emailed dockets, 6 sent invoice, 7 receivedInvoice, 8 emailedInvoice
    if ($('#folder_status').val() == 1) {
      moveItems('dockets', 'all');
    } else if ($('#folder_status').val() == 2) {
      moveItems('invoices', 'all');
    } else if ($('#folder_status').val() == 3) {
      moveItems('dockets', 'sent');
    } else if ($('#folder_status').val() == 4) {
      moveItems('dockets', 'received');
    } else if ($('#folder_status').val() == 5) {
      moveItems('dockets', 'emailed');
    } else if ($('#folder_status').val() == 6) {
      moveItems('invoices', 'sent');
    } else if ($('#folder_status').val() == 7) {
      moveItems('invoices', 'received');
    } else if ($('#folder_status').val() == 8) {
      moveItems('invoices', 'emailed');
    }
  } else if (typeValue == 2) {
    moveItems('folder', 'all');
  }
});

function moveItems(type, section) {
  var docketData = {
    'docketId[]': [],
    'emailDocketId[]': [],
    'folderId': '',
    'invoiceId[]': [],
    'emailInvoiceId[]': []
  };
  var folder_id = $("#moveFolderItemModal #folderFramework").val();
  $('.rtDataTable .forDocket:checked').each(function () {
    docketData['docketId[]'].push($(this).val());
  });
  $('.rtDataTable .forEmailDocket:checked').each(function () {
    docketData['emailDocketId[]'].push($(this).val());
  });
  $('.rtDataTable .forInvoice:checked').each(function () {
    docketData['invoiceId[]'].push($(this).val());
  });
  $('.rtDataTable .forEmailInvoice:checked').each(function () {
    docketData['emailInvoiceId[]'].push($(this).val());
  });
  docketData['folderId'] = folder_id;

  if (type == "folder") {
    var removeItemFolderId = $('#removeItemFolderId').val();
    $.ajax({
      type: "Post",
      url: base_url + '/dashboard/company/folder/saveFolderItems',
      data: docketData,
      success: function success(response) {
        $('.boxContent .rtTree li #' + response.id + ' span').text('(' + response.data + ')');
        $('#moveFolderItemModal').modal('hide');
        $.ajax({
          type: "Post",
          url: base_url + '/dashboard/company/folder/viewFolderData',
          cache: false,
          data: {
            'folderId': removeItemFolderId,
            'items': 10
          },
          success: function success(response) {
            $('.loadspin').css('display', 'none');
            $(".viewFolder").html(response).show();
          }
        });
      }
    });
  } else {
    $.ajax({
      type: "Post",
      url: base_url + '/dashboard/company/folder/saveFolderItems',
      data: docketData,
      success: function success(response) {
        var test = $('.boxContent .rtTree li #' + response.id + ' span').text('(' + response.data + ')');
        $('#moveFolderItemModal').modal('hide');
        var manager = "docketBookManager";

        if (type == "invoices") {
          manager = "invoiceManager";
        }

        $.ajax({
          type: "GET",
          data: {
            data: "all"
          },
          url: base_url + '/dashboard/company/' + manager + '/' + type + '/' + section + '?search=',
          success: function success(response) {
            if (response == "") {} else {
              $(".menuli").addClass("active");
              $(".jstree-anchor").removeClass("jstree-clicked");
              $(".jstree-node").removeClass("jstree-open");
              $(".jstree-node").addClass("jstree-closed");
              $(".datatable").html(response).show();
            }
          }
        });
      }
    });
  }
}

/***/ }),

/***/ "./resources/views/dashboard/company/folder-management/popup-modal/new-folder/new-folder.js":
/*!**************************************************************************************************!*\
  !*** ./resources/views/dashboard/company/folder-management/popup-modal/new-folder/new-folder.js ***!
  \**************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).on('click', '#createNewFolder .submit', function () {
  $('#createNewFolder .dashboardFlashsuccess').css('display', 'none');
  var root_id = $('#createNewFolder #folderSelect option:selected').val();
  var folder_name = $('#createNewFolder #folderNewName').val();
  $.ajax({
    type: "Post",
    url: base_url + '/dashboard/company/folder/newFolderCreate',
    data: {
      'rootId': root_id,
      name: folder_name
    },
    success: function success(response) {
      if (response.status == true) {
        $('.directoryEmpty').css('display', 'none');

        if (root_id == 0) {
          $(".rtTree").append(' <li><a href="#" id="' + response.newFolderId + '">' + response.newFolderName + '<span style="    position: absolute;right: 4px;">' + response.totalItem + '</span></a><ul></ul> <div  class="editBtn" id="editBtnId" data-id="' + response.newFolderId + '" data-title="' + response.newFolderName + '" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div></li>');
        } else {
          var activeLink = ".rtTree a[id='" + root_id + "']";
          $(".rtTree a.active").siblings("ul").append(' <li><a href="#" id="' + response.newFolderId + '">' + response.newFolderName + '<span style="    position: absolute;right: 4px;">' + response.totalItem + '</span></a><ul></ul><div  class="editBtn" id="editBtnId" data-id="' + response.newFolderId + '" data-title="' + response.newFolderName + '" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div></li>');
        }

        $('#createNewFolder').modal('hide');
        $("#folderNewName").val("");
      } else if (response.name) {
        var wrappermessage = ".messagesucess";
        $(wrappermessage).html(response["name"]);
        $('.dashboardFlashsuccess').css('display', 'block');
      } else {
        var wrappermessage = ".messagesucess";
        $(wrappermessage).html(response.message);
        $('.dashboardFlashsuccess').css('display', 'block');
      }
    }
  });
});

/***/ }),

/***/ "./resources/views/dashboard/company/folder-management/popup-modal/recover-folder-item/recover-folder-item.js":
/*!********************************************************************************************************************!*\
  !*** ./resources/views/dashboard/company/folder-management/popup-modal/recover-folder-item/recover-folder-item.js ***!
  \********************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$('#recoverFolderItem').on('show.bs.modal', function (e) {
  var id = $(e.relatedTarget).data('id');
  var type = $(e.relatedTarget).data('type');
  $('#removeFolderId').val(id);
  $('#removeFolderType').val(type);
});
$(document).on('click', '.submitRecoverFolderItem', function () {
  var id = $('#removeFolderId').val();
  var type = $('#removeFolderType').val();
  var folderId = $('#removeItemFolderId').val();
  $.ajax({
    type: "Post",
    url: base_url + '/dashboard/company/folder/recoverFolderItem',
    data: {
      'type': type,
      'id': id,
      'folderId': folderId
    },
    success: function success(response) {
      $.map($('.selectitem'), function (el) {
        if ($(el).val() == id) {
          $(el).parent().parent('tr').remove();
        }
      });
      $('#recoverFolderItem').modal('hide');
      var test = $('.boxContent .rtTree li #' + response.id + ' span').text('(' + response.data + ')');
    }
  });
});

/***/ }),

/***/ "./resources/views/dashboard/company/folder-management/popup-modal/remove-folder/remove-folder.js":
/*!********************************************************************************************************!*\
  !*** ./resources/views/dashboard/company/folder-management/popup-modal/remove-folder/remove-folder.js ***!
  \********************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).on('click', '.rtDataTableHeaderMenu #removeItemsFolder', function () {
  if ($('.rtDataTable .selectitem:checked').serialize() == "") {
    alert("Please select item that you want to remove.");
  } else {
    var docketData = {
      'docketId[]': [],
      'emailDocketId[]': [],
      'removeItemFolderId': '',
      'invoiceId[]': [],
      'emailInvoiceId[]': []
    };
    var folder_id = $('#removeItemFolderId').val();
    $('.rtDataTable .forDocket:checked').each(function () {
      docketData['docketId[]'].push($(this).val());
    });
    $('.rtDataTable .forEmailDocket:checked').each(function () {
      docketData['emailDocketId[]'].push($(this).val());
    });
    $('.rtDataTable .forInvoice:checked').each(function () {
      docketData['invoiceId[]'].push($(this).val());
    });
    $('.rtDataTable .forEmailInvoice:checked').each(function () {
      docketData['emailInvoiceId[]'].push($(this).val());
    });
    docketData['folderId'] = folder_id;
    $('.loadspin').css('display', 'block');
    var id = $('#removeItemFolderId').val();
    $.ajax({
      type: "Post",
      url: base_url + '/dashboard/company/folder/removeItemsFolder',
      data: docketData,
      success: function success(response) {
        if (response.data == 0) {
          $('.boxContent .rtTree li #' + response.id + ' span').text('');
        } else {
          $('.boxContent .rtTree li #' + response.id + ' span').text('(' + response.data + ')');
        }

        $.ajax({
          type: "Post",
          url: base_url + '/dashboard/company/folder/viewFolderData',
          cache: false,
          data: {
            'folderId': id,
            'items': 10
          },
          success: function success(response) {
            $('.loadspin').css('display', 'none');
            $(".viewFolder").html(response).show();
          }
        });
      }
    });
  }
});
$(document).on('click', '#removeFolderModal .submit', function () {
  var folderId = $('#removeFolderid').val();
  $.ajax({
    type: "Post",
    url: base_url + '/dashboard/company/folder/removeFolder',
    data: {
      'id': folderId
    },
    success: function success(response) {
      if (response.status == true) {
        if (response.foldercount == 0) {
          $('.directoryEmpty').css('display', 'block');
        }

        $(".rtTree li a[id=" + folderId + "]").parent('li').remove();
        $('#removeFolderModal').modal('hide');
      }
    }
  });
});

/***/ }),

/***/ "./resources/views/dashboard/company/folder-management/popup-modal/search-folder/search-folder.js":
/*!********************************************************************************************************!*\
  !*** ./resources/views/dashboard/company/folder-management/popup-modal/search-folder/search-folder.js ***!
  \********************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).on("keyup", '#searchFolderModal #searchFolderName', function (event) {
  if (event.keyCode === 13) {
    event.preventDefault();
    $("#searchFolderModal .submit").trigger("click");
  }
});
$(document).on('click', '#searchFolderModal .submit', function () {
  var inputVal = $('#searchFolderModal #searchFolderName').val().trim();

  if (inputVal.length == 0) {
    alert("Please enter folder name");
  } else {
    $.ajax({
      type: "Post",
      url: base_url + '/dashboard/company/folder/searchFolder',
      data: {
        "name": inputVal
      },
      success: function success(response) {
        if (response.status == true) {
          $(".boxContent").html(response.detail).show();
          $('#searchFolderModal').modal('hide');
          $("ul.rtTree").rtTree({
            clickListItem: base_url + '/dashboard/company/folder',
            viewFolderData: base_url + '/dashboard/company/folder/viewFolderData',
            addFolder: base_url + '/dashboard/company/folder/newFolderCreate',
            createFolderSelect: base_url + '/dashboard/company/folder/createFolderSelect',
            ajaxCompletion: function test(response) {
              var items = [];
              $.each(response.data, function () {
                items.push($(".rtTree a.active").siblings("ul").append('<li><a href="#" id="' + this.id + '">' + this.name + ' <span style="    position: absolute;right: 4px;">' + this.totalItems + '</span></a><ul></ul>  <div  class="editBtn" id="editBtnId" data-id="' + this.id + '" data-title="' + this.name + '" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div> </li>'));
              });
              $(".viewFolder").html(response).show();
            }
          });
        }
      }
    });
  } //             $(document).on('click','#UpdateFolder',function () {
  //                 var folderId = $('#editIdFolder').val();
  //                 var folderName = $('#editNameFolder').val();
  //                 $.ajax({
  //                     type:"Post",
  //                     url:base_url+'/dashboard/company/folder/updateFolder',
  //                     data:{'id':folderId,'title':folderName},
  //                     success: function (response) {
  //                         if (response.status == true){
  //                             $(".rtTree li a[id="+response.id+"]").html(response.title+'<span style="    position: absolute;right: 4px;">'+response.totalItems+'</span>');
  //                         }
  //                         if ( $(".rtTree li a[id="+response.id+"]").hasClass('active')){
  //                             $('.rtTabHeader ul li h4').html(response.title+'<span style="    position: absolute;right: 4px;">'+response.totalItems+'</span>');
  //                         }
  //                         $('#updateFolderData').modal('hide');
  //                     }
  //                 });
  //
  //             });
  //
  //
  //
  //
  //         }
  //
  //     }
  // });

});

/***/ }),

/***/ "./resources/views/dashboard/company/folder-management/popup-modal/shareable-folder/shareable-folder.js":
/*!**************************************************************************************************************!*\
  !*** ./resources/views/dashboard/company/folder-management/popup-modal/shareable-folder/shareable-folder.js ***!
  \**************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).on('click', '.shareableFolder', function (e) {
  $('.loadspin').css('display', 'block');
  $('.divList').remove();
  $('.errorMessageShareable').css('display', 'none');
  $('#shareableFolderModal').modal('show');
  var id = $(this).attr('data-editid');
  $('.shareableFolderId').val(id);
  $.ajax({
    type: "post",
    url: base_url + '/dashboard/company/folder/viewShareableData',
    data: {
      folder_id: id
    },
    success: function success(response) {
      $('.shareableContain').html(response);
      $('.loadspin').css('display', 'none');
    }
  });
});
$(document).on('click', '.submitUserShareable', function () {
  $('.loadspin').css('display', 'block');
  $(this).html('<span class="spinner" style="padding: 0 10px 0px 0px;font-size: 14px;"><i class="fa fa-spinner fa-spin"></i></span> Add');
  $('.errorMessageShareable').css('display', 'none');
  var folderId = $('.shareableFolderId').val();
  var email = $('.shareableEmail').val();
  var password = $('.sharePassword').val();
  $.ajax({
    type: "POST",
    url: base_url + '/dashboard/company/folder/saveShareableUsers',
    data: {
      folder_id: folderId,
      email: email,
      password: password
    },
    success: function success(response) {
      if (response['status'] == false) {
        $('.submitUserShareable').text('Add');
        $('.errorMessageShareable').css('display', 'block');
        $('.errorMessageShareable').text(response['message']);
      } else {
        $('.shareableContain').html(response);
        $('.submitUserShareable').text('Add');
      }

      $('.loadspin').css('display', 'none');
    }
  });
});
$(document).on('change', '.sharefolderSelect', function () {
  $('.loadspin').css('display', 'block');
  $('.errorMessageShareable').css('display', 'none');
  var folderId = $('.shareableFolderId').val();
  var value = $(this).val();
  $.ajax({
    type: "POST",
    url: base_url + '/dashboard/company/folder/updateShareableType',
    data: {
      folder_id: folderId,
      value: value
    },
    success: function success(response) {
      if (response['status'] == false) {
        $('.errorMessageShareable').css('display', 'block');
        $('.errorMessageShareable').text(response['message']);
      } else {}

      $('.loadspin').css('display', 'none');
    }
  });
});
$(document).on('click', '.deleteShareableUsers', function (e) {
  $('#deleteShareableUsersModal').modal('show');
  var id = $(this).attr('data-shareableuserId');
  $('.shareableUserId').val(id);
});
$(document).on('click', '.deleteShareableUser', function () {
  $('.loadspin').css('display', 'block');
  var shareableUserId = $('.shareableUserId').val();
  $.ajax({
    type: "post",
    url: base_url + '/dashboard/company/folder/deleteShareableUser',
    data: {
      id: shareableUserId
    },
    success: function success(response) {
      if (response['status'] == false) {
        $('.errorMessageShareable').css('display', 'block');
        $('.errorMessageShareable').text(response['message']);
      } else {
        $('.shareableContain').html(response);
        $('#deleteShareableUsersModal').modal('hide');
      }

      $('.loadspin').css('display', 'none');
    }
  });
});
$(document).on('click', '.editShareableUsers', function () {
  $('.errorMessageShareableUser').css('display', 'none');
  $('#editShareableUserModal').modal('show');
  var id = $(this).attr('data-shareableuserId');
  var email = $(this).attr('data-shareableuserEmail');
  $('.editshareableEmail').val(email);
  $('.updateShareableUserId').val(id);
});
$(document).on('click', '.updateShareableUser', function () {
  $('.loadspin').css('display', 'block');
  var shareableUsersId = $('.updateShareableUserId').val();
  var password = $('.editshareablePassword').val();
  $.ajax({
    type: "post",
    url: base_url + '/dashboard/company/folder/updateShareableUser',
    data: {
      id: shareableUsersId,
      password: password
    },
    success: function success(response) {
      if (response['status'] == false) {
        $('.errorMessageShareableUser').css('display', 'block');
        $('.errorMessageShareableUser').text(response['message']);
      } else {
        $('.shareableContain').html(response);
        $('#editShareableUserModal').modal('hide');
      }

      $('.loadspin').css('display', 'none');
    }
  });
});
$(document).ready(function () {
  var clipboard = new ClipboardJS('.copyurl');
});

/***/ }),

/***/ "./resources/views/dashboard/company/invoiceManager/create/partials/template/template.js":
/*!***********************************************************************************************!*\
  !*** ./resources/views/dashboard/company/invoiceManager/create/partials/template/template.js ***!
  \***********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  $('#framework').multiselect({
    enableFiltering: true,
    enableCaseInsensitiveFiltering: true,
    buttonWidth: '100%',
    includeSelectAllOption: true,
    nonSelectedText: '',
    filterPlaceholder: 'Search Template',
    onChange: function onChange(element, checked) {
      if (element) {
        lastSelected = element.val();
      } else {
        $("#framework").multiselect('select', lastSelected);
        $("#framework").multiselect('deselect', element.val());
      }
    }
  });
});

/***/ }),

/***/ "./resources/views/dashboard/company/invoiceManager/modal-popup/invoice-label/delete-invoice-label.js":
/*!************************************************************************************************************!*\
  !*** ./resources/views/dashboard/company/invoiceManager/modal-popup/invoice-label/delete-invoice-label.js ***!
  \************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  $('#deleteInvoiceLabelModal').on('show.bs.modal', function (e) {
    $('#deleteInvoiceLabelModal .flash-message').css('display', 'none');
    $('#deleteInvoiceLabelModal .submit').html('Yes');
    $('#deleteInvoiceLabelModal .invoice-label-id').val($(e.relatedTarget).attr('data-id'));
    $('#deleteInvoiceLabelModal .type').val($(e.relatedTarget).attr('data-type'));
  });
  $(document).on('click', '#deleteInvoiceLabelModal .submit', function () {
    var id = $('#deleteInvoiceLabelModal .invoice-label-id').val();
    var type = $('#deleteInvoiceLabelModal .type').val();
    $(this).html('<span class="spinner" style="padding: 0 37px 0px 37px;font-size: 14px;"><i class="fa fa-spinner fa-spin"></i></span>');
    $.ajax({
      type: "Post",
      url: base_url + '/dashboard/company/invoiceManager/invoices/labels/delete',
      data: {
        'type': type,
        'id': id
      },
      success: function success(response) {
        if (response.status == true) {
          $('.invoice-label-' + response.id).remove();
          $('#deleteInvoiceLabelModal').modal('hide');
        } else if (response.status == false) {
          $('#deleteInvoiceLabelModal .submit').html('Save');
          $('#deleteInvoiceLabelModal .flash-message').fadeIn();
          $('#deleteInvoiceLabelModal .flash-message .message').html(response.message);
        }
      }
    });
  });
});

/***/ }),

/***/ "./resources/views/dashboard/company/invoiceManager/modal-popup/invoice-label/invoice-label.js":
/*!*****************************************************************************************************!*\
  !*** ./resources/views/dashboard/company/invoiceManager/modal-popup/invoice-label/invoice-label.js ***!
  \*****************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  try {
    var slimSelect = new SlimSelect({
      select: '#invoiceLabelModal .slim-select',
      addToBody: false,
      placeholder: 'Select Label'
    });
    $('#invoiceLabelModal').on('show.bs.modal', function (e) {
      $('#invoiceLabelModal .flash-message').css('display', 'none');
      $('#invoiceLabelModal .submit').html('Save');
      slimSelect.set([]);
      $('#invoiceLabelModal .form-group').removeClass('has-error');
      $('#invoiceLabelModal .invoice-company-id').html($(e.relatedTarget).attr('data-formatted-id'));
      $('#invoiceLabelModal #invoice-id').val($(e.relatedTarget).attr('data-id'));
      $('#invoiceLabelModal #invoice-type').val($(e.relatedTarget).attr('data-type'));
    });
    $(document).on('click', '#invoiceLabelModal .flash-message .close', function () {
      $('#invoiceLabelModal .flash-message').fadeOut();
    });
    $(document).on('click', '#invoiceLabelModal .submit', function () {
      if (slimSelect.selected().length == 0) {
        $('#invoiceLabelModal .flash-message').fadeIn();
        $('#invoiceLabelModal .flash-message .message').html('Please select Invoice Label');
      } else {
        $('#invoiceLabelModal .flash-message').fadeOut();
        var id = $('#invoiceLabelModal #invoice-id').val();
        var type = $('#invoiceLabelModal #invoice-type').val();
        var value = slimSelect.selected();
        $(this).html('<span class="spinner" style="padding: 0 37px 0px 37px;font-size: 14px;"><i class="fa fa-spinner fa-spin"></i></span>');
        $.ajax({
          type: "Post",
          url: base_url + '/dashboard/company/invoiceManager/invoices/labels/assign',
          data: {
            'type': type,
            'id': id,
            'value': value
          },
          success: function success(response) {
            if (response.status == true) {
              $('.invoice-label-container #' + response.id + " ul").append(response.html);
              $('#invoiceLabelModal').modal('hide');
            } else if (response.status == false) {
              $('#invoiceLabelModal .submit').html('Save');
              $('#invoiceLabelModal .flash-message').fadeIn();
              $('#invoiceLabelModal .flash-message .message').html(response.message);
            }
          }
        });
      }
    });
  } catch (e) {}
});

/***/ }),

/***/ "./resources/views/dashboard/company/invoiceManager/partials/table-view/table-header/table-header-menu.js":
/*!****************************************************************************************************************!*\
  !*** ./resources/views/dashboard/company/invoiceManager/partials/table-view/table-header/table-header-menu.js ***!
  \****************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  $(document).on('click', '.rtDataTableHeaderMenu #exportcsvInvoice', function () {
    if ($('.selectitem:checked').serialize() == "") {
      alert("Please Select Invoice");
    } else {
      var url = base_url + '/dashboard/company/invoiceManager/exportInvoice?' + $('.rtDataTable .selectitem:checked').serialize();
      window.open(url, "_blank");
    }
  });
  $(document).on('click', '.rtDataTableHeaderMenu #exportpdfInvoice', function () {
    if ($('.selectitem:checked').serialize() == "") {
      alert("Please Select Invoice");
    } else {
      var url = base_url + '/dashboard/company/invoiceManager/makePdfInvoice?' + $('.rtDataTable .selectitem:checked').serialize();
      window.open(url, "_blank");
    }
  });
  $(document).on('change', '.selectPaginateInvoice', function () {
    searchInvoice();
  });
  var timer = null;
  $('.rtDataTableHeaderMenu #searchInputInvoice').keydown(function () {
    clearTimeout(timer);
    timer = setTimeout(searchInvoice, 1000);
  });

  function searchInvoice() {
    var url = $('.rtDataTableHeaderMenu').attr('dataCurrentURL') + '?search=';
    $(".rtDataTable").html('<div style="position: absolute;left: 50%;top: 64%;font-weight: bold;text-align:center;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="margin-bottom:10px;"></i></div>');
    var paginate = $('.rtDataTableHeaderMenu .selectPaginate').val();

    if ($('#searchInputInvoice').val().length > 0) {
      $.ajax({
        type: "GET",
        data: {
          items: paginate
        },
        url: url + $('#searchInputInvoice').val(),
        success: function success(response) {
          if (response == "") {} else {
            $(".datatable").html(response).show();
          }
        }
      });
    } else {
      $.ajax({
        type: "GET",
        data: {
          data: "all",
          items: paginate
        },
        url: url,
        success: function success(response) {
          if (response == "") {} else {
            $(".datatable").html(response).show();
          }
        }
      });
    }
  }
});

/***/ }),

/***/ "./resources/views/dashboard/company/message-reminders/index.js":
/*!**********************************************************************!*\
  !*** ./resources/views/dashboard/company/message-reminders/index.js ***!
  \**********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  $(".messageUserList li:first-child a").addClass('active');

  if ($(".messageUserList li:first-child a").hasClass('active')) {
    var id = $(".messageUserList li:first-child a").attr('idatt');
    $.ajax({
      type: "post",
      data: {
        'id': id
      },
      url: base_url + '/dashboard/company/messages/chatView',
      success: function success(response) {
        if (response.status == true) {
          $(".viewChat").html(response.html);
          $('.messages').scrollTop($('.messages')[0].scrollHeight);
          $('#myModalNewMessage').modal('hide');
        } else if (response.status == false) {
          alert(response.message);
        }
      }
    });
  }
});

/***/ }),

/***/ "./resources/views/dashboard/company/message-reminders/modal-popup/new-group.js":
/*!**************************************************************************************!*\
  !*** ./resources/views/dashboard/company/message-reminders/modal-popup/new-group.js ***!
  \**************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  try {
    var slimSelectNewGroupModal = new SlimSelect({
      select: '#newGroupModal .slim-select',
      addToBody: false,
      placeholder: 'Select Employee'
    });
    $('#newGroupModal').on('show.bs.modal', function (e) {
      $('#newGroupModal .flash-message').css('display', 'none');
      $('#newGroupModal .submit').html('Create');
      slimSelectNewGroupModal.set([]);
      $('#newGroupModal .form-group').removeClass('has-error');
    });
    $(document).on('click', '#newGroupModal .flash-message .close', function () {
      $('#newGroupModal .flash-message').fadeOut();
    });
    $(document).on('click', '#newGroupModal .submit', function () {
      var title = $('#newGroupModal #groupChatTitle').val();
      var employeeId = slimSelectNewGroupModal.selected();
      ;

      if (title.length == 0) {
        $('#newGroupModal .flash-message').fadeIn();
        $('#newGroupModal .flash-message .message').html('Please enter group title');
      } else if (employeeId.length == 0) {
        $('#newGroupModal .flash-message').fadeIn();
        $('#newGroupModal .flash-message .message').html('Please select Employee');
      } else {
        $('#newGroupModal .flash-message').fadeOut();
        $(this).html('<span class="spinner" style="padding: 0 37px 0px 37px;font-size: 14px;"><i class="fa fa-spinner fa-spin"></i></span>');
        $.ajax({
          type: "post",
          data: {
            'employeeId': employeeId,
            'title': title,
            'isGroup': 1
          },
          url: base_url + '/dashboard/company/messages/create-group',
          success: function success(response) {
            if (response.status == true) {
              $('#newGroupModal').modal('hide');
              $(".messageUserList li a").removeClass('active');
              $('.single_chat' + response.messageGroupID).remove();
              $('.messageUserList').prepend(response.messageGroupHtml);
              $('.single_chat' + response.messageGroupID).addClass('active');
              $.ajax({
                type: "post",
                data: {
                  'id': response.messageGroupID
                },
                url: base_url + '/dashboard/company/messages/chatView',
                success: function success(response) {
                  if (response.status == true) {
                    $(".viewChat").html(response.html);
                    $('#myModalNewMessage').modal('hide');
                  } else if (response.status == false) {
                    alert(response.message);
                  }
                }
              });
            } else if (response.status == false) {
              $('#newGroupModal .submit').html('Create');
              $('#newGroupModal .flash-message').fadeIn();
              $('#newGroupModal .flash-message .message').html(response.message);
            }
          }
        });
      }
    });
  } catch (e) {}
});

/***/ }),

/***/ "./resources/views/dashboard/company/message-reminders/modal-popup/send-message.js":
/*!*****************************************************************************************!*\
  !*** ./resources/views/dashboard/company/message-reminders/modal-popup/send-message.js ***!
  \*****************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  try {
    $(document).on('click', '#newMessageModal .submit', function () {
      var message = $('#singleMessages').val();
      var employeeId = $('#chatUserId').val();

      if (message.length == 0) {
        $('#newMessageModal .flash-message').fadeIn();
        $('#newMessageModal .flash-message .message').html('Please enter your message');
      } else {
        $('#newMessageModal .flash-message').fadeOut();
        $(this).html('<span class="spinner" style="padding: 0 37px 0px 37px;font-size: 14px;"><i class="fa fa-spinner fa-spin"></i></span>');
      }

      $.ajax({
        type: "post",
        data: {
          'employeeId': employeeId,
          'isGroup': 0,
          'message': message
        },
        url: base_url + '/dashboard/company/messages/create-group',
        success: function success(response) {
          if (response.status == true) {
            console.log(response);
            $('#newMessageModal').modal('hide');
            $(".messageUserList li a").removeClass('active');
            $('.single_chat' + response.messageGroupID).remove();
            $('.messageUserList').prepend(response.messageGroupHtml);
            $('.single_chat' + response.messageGroupID).addClass('active');
            $.ajax({
              type: "post",
              data: {
                'id': response.messageGroupID
              },
              url: base_url + '/dashboard/company/messages/chatView',
              success: function success(response) {
                if (response.status == true) {
                  $(".viewChat").html(response.html);
                  $('#myModalNewMessage').modal('hide');
                  $('.messages').scrollTop($('.messages')[0].scrollHeight);
                } else if (response.status == false) {
                  alert(response.message);
                }
              }
            });
          } else if (response.status == false) {
            $('#newGroupModal .submit').html('Send');
            $('#newGroupModal .flash-message').fadeIn();
            $('#newGroupModal .flash-message .message').html(response.message);
          }
        }
      });
    });
  } catch (e) {}
});

/***/ }),

/***/ "./resources/views/dashboard/company/message-reminders/partials/chatView.js":
/*!**********************************************************************************!*\
  !*** ./resources/views/dashboard/company/message-reminders/partials/chatView.js ***!
  \**********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).on("click", '.message-form-wrapper .submit', function (event) {
  var id = $('.message-form-wrapper #groupId').val();
  var message = $('.message-form-wrapper #chatMessage').val();

  if (message.length == 0) {
    alert('Please write your message first.');
  }

  $.ajax({
    type: "post",
    data: {
      'id': id,
      'message': message
    },
    url: base_url + '/dashboard/company/messages',
    success: function success(response) {
      if (response.status == true) {
        $(".messageList").last().append(response.html);
        var groupLi = $('.single_chat' + response.groupId).parent('li')[0].outerHTML;
        $('.single_chat' + response.groupId).remove();
        $('.messageUserList').prepend(groupLi);
        $('.single_chat' + response.groupId).addClass('active');
        $('.message-form-wrapper #chatMessage').val('');
        $('.messages').scrollTop($('.messages')[0].scrollHeight);
        $('.viewChat .seenUser' + response.senderUserId).hide();
        $('.viewChat .seenUser' + response.senderUserId).last().fadeIn();
      } else if (response.status == false) {
        alert(response.message);
      }
    }
  });
});
$(document).on("keyup", '.message-form-wrapper #chatMessage', function (event) {
  if (event.keyCode === 13) {
    event.preventDefault();
    $(".message-form-wrapper .submit").trigger("click");
  }
});

/***/ }),

/***/ "./resources/views/dashboard/company/message-reminders/partials/message-user-list.js":
/*!*******************************************************************************************!*\
  !*** ./resources/views/dashboard/company/message-reminders/partials/message-user-list.js ***!
  \*******************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  $(document).on('click', '.clickToChat', function () {
    $(".messageUserList li a").removeClass('active');
    var id = $(this).attr('idAtt');
    $.ajax({
      type: "post",
      data: {
        'id': id
      },
      url: base_url + '/dashboard/company/messages/chatView',
      success: function success(response) {
        if (response.status == true) {
          $(".viewChat").html(response.html);
          $('.single_chat' + id).addClass('active');
          $('.messages').scrollTop($('.messages')[0].scrollHeight);
        } else if (response.status == false) {
          alert(response.message);
        }
      }
    });
  });
});

/***/ }),

/***/ "./resources/views/shareable-folder/shareable-folder.js":
/*!**************************************************************!*\
  !*** ./resources/views/shareable-folder/shareable-folder.js ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  $("ul.folderRtTree").folderRtTree({
    clickListItem: base_url + '/folder/list',
    viewFolderData: base_url + '/folder/viewFolderData',
    ajaxCompletion: function test(response) {
      var items = [];
      $.each(response.data, function () {
        items.push($(".folderRtTree a.active").siblings("ul").append('<li><a href="#" id="' + this.id + '" >' + this.name + ' <span style="    position: absolute;right: 4px;">' + this.totalItems + '</span></a><ul></ul>  <div  class="editBtn" id="editBtnId" data-id="' + this.id + '" data-title="' + this.name + '" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div> </li>'));
      });
      window.location.hash = "&folderId=" + $(".folderRtTree a.active").attr('id');
      $(".viewFolder").html(response).show();
      var timers = null;
      $('#searchFolderInputs').keydown(function () {
        clearTimeout(timers);
        timers = setTimeout(doStuffs, 1000);
      });
      dearch();
    }
  }); //    var data = $(".folderRtTree a.active").attr('id');
  // window.location.hash = "&folderId="+data;
  //folder edit button action

  $(document).on('click', '.folderRtTree .editBtn', function () {
    $('.editBtn>div').remove();
    var div = '<div class="folder-div"><ul class="divList" style="margin: 0;"> <li><button style="border: none;width: 100%;text-align: left; border-radius: 0 16px 16px 0;padding: 4px 12px;" class="downloadFolderPdf" data-FolderId="' + $(this).data('id') + '"  data-title="' + $(this).data('title') + '">Download Pdf</button></li></ul></div>';
    $(this).append(div);
  });
  $(document).on('mouseenter', '.folderRtTree li a', function () {
    $(this).siblings('.editBtn').addClass('editBtnHover');
  });
  $(document).on('mouseleave', '.folderRtTree li a', function () {
    $(this).siblings('.editBtn').removeClass('editBtnHover');
  });
  $(document).on('mouseleave', '.folderRtTree .editBtn', function () {
    if ($(this).siblings("a").hasClass("active")) {} else {
      $(this).siblings("a").css("background", "");
    }
  });
  $(document).on('mouseenter', '.folderRtTree .editBtn', function () {
    if ($(this).siblings("a").hasClass("active")) {} else {
      $(this).siblings("a").css("background", "#f7f7f7");
    }
  });

  window.onload = function () {
    var hideMe = document.getElementById('folder-div');

    document.onclick = function (e) {
      if ($(e.target).hasClass("folder-div") || $(e.target).hasClass("editBtn")) {} else {
        $('.editBtn>div').remove();
      }
    };
  };

  $(document).on('click', '.folderEdit', function () {
    $('#updateFolderModal').modal('show');
    var editIds = $(this).attr('data-editId');
    var editNames = $(this).attr('data-editName');
    $("#updateFolderModal #editNameFolder").val(editNames);
    $("#updateFolderModal #editIdFolder").val(editIds);
  });
  $(document).click(function (evt) {
    if (evt.target.className != 'editBtn') {
      $.map($(".editBtn"), function (el) {
        if ($(el).children('.folder-div').hasClass('folder-div')) {
          $(el).children('.folder-div').remove();
        } else {}
      });
    }
  });
  $(document).on('click', '.folderRtTree .folderRemove', function () {
    $('#removeFolderModal').modal('show');
    $('.editBtn>div').remove();
    var deleteId = $(this).attr('data-removeId');
    var folderTitle = $(this).attr('data-title');
    $(".deleteMessage").html("Are you sure you want to remove " + "<i class='material-icons' style='font-size: 16px;color: #eece4a;'> " + "folder" + "</i> <b>" + folderTitle + "</b>?");
    $("#removeFolderid").val(deleteId);
  });
  $("#assignTemplateName").chained("#assignTemplateType");
  $(document).on('click', '.folderRtTree .assignTemplate', function () {
    $('#assignTemplateModal').modal('show');
    var folderId = $(this).attr('data-removeId');
    var folderTitle = $(this).attr('data-title');
    $("#assignTemplateModal .assignFolderName").text(folderTitle);
    $("#assignTemplateId").val(folderId);
    $('.assignTempalteErrorMessage').css('display', 'none');
  });
  $(document).on('click', '.folderRtTree .downloadFolderPdf', function () {
    var folderId = $(this).attr('data-FolderId');
    var url = $(this).attr('href');
    $.ajax({
      type: 'post',
      url: base_url + '/folder/downloadPdf',
      data: {
        'folderId': folderId
      },
      success: function success(response) {
        window.open(base_url + '/zipFile/' + response.messages, '_blank');
      }
    });
  }); // $(".filterempolyeess").chained(".filtercompanys");
  //--per page item selection--//

  $(document).on('change', '.rtDataTableHeaderMenu .selectPaginateFolder', function () {
    doStuffs();
  });

  function doStuffs() {
    var url = $('.rtDataTableHeaderMenu').attr('dataCurrentURL') + '?search=';
    var folderID = $('.mainFolderId').val();
    var paginate = $('.rtDataTableHeaderMenu .selectPaginateFolder').val();
    $(".rtDataTable").html('<div style="position: absolute;left: 50%;top: 64%;font-weight: bold;text-align:center;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="margin-bottom:10px;"></i></div>');

    if ($('.rtDataTableHeaderMenu #searchFolderInputs').val().length > 0) {
      $.ajax({
        type: "GET",
        data: {
          'folderId': folderID,
          items: paginate
        },
        url: url + $('#searchFolderInputs').val(),
        success: function success(response) {
          if (response == "") {} else {
            $(".searchViewItems").html(response).show();
          }
        }
      });
    } else {
      $.ajax({
        type: "GET",
        data: {
          data: "all",
          'folderId': folderID,
          items: paginate
        },
        url: url,
        success: function success(response) {
          if (response == "") {} else {
            $(".searchViewItems").html(response).show();
          }
        }
      });
    }
  }

  $(document).on('click', '#searchFolder', function (e) {
    e.preventDefault();
    $('#searchFolderModel').modal('show');
  });
  $(document).on('click', '.rtDataTable #folderPagination ul li a', function (e) {
    e.preventDefault(); //

    var page = $(this).text(); // if(typeof(url) != "undefined"){
    //     var page = url.split('page=')[1];
    // }
    // const paginate = $('.rtDataTableHeaderMenu .selectPaginateFolder').val();

    var paginate = 10;
    var search = "";
    var id = $('.mainFolderId').val();
    var url = $(this).attr('href');
    $.ajax({
      type: 'post',
      url: base_url + '/folder/viewFolderData?items=' + paginate + '&page=' + page,
      data: {
        'page': page,
        'folderId': id
      },
      success: function success(response) {
        $(".viewFolder").html(response).show();
        window.location.hash = "&folderId=" + id + "&search=" + search + "&items=" + paginate + '&page=' + page;
        dearch();
      }
    });
  });
  $(document).on('click', '#MyModalFolderFilters', function (e) {
    e.preventDefault();
    $('#MyModalFolderFilter').modal('show');
    $(".dateInput").datepicker({
      dateFormat: 'dd-mm-yy'
    });
  });

  if (typeof location.hash.split('#')[1] !== "undefined") {
    var folderId = location.hash.split('#')[1].split('&')[1].split('=')[1];

    if (typeof location.hash.split('#')[1].split('&')[2] !== "undefined") {
      var searchValue = location.hash.split('#')[1].split('&')[2].split('=')[1].replace("%20", " ");
    } else {
      var searchValue = "";
    }

    if (typeof location.hash.split('#')[1].split('&')[3] !== "undefined") {
      var items = location.hash.split('#')[1].split('&')[3].split('=')[1];
      var page = location.hash.split('#')[1].split('&')[4].split('=')[1];
    } else {
      var items = "";
      var page = "";
    }

    if (items != "" && page != "") {
      var searchValueitems = items;
      var searchValuepage = page;
    } else {
      var searchValueitems = 10;
      var searchValuepage = 1;
    }

    if (searchValue == "") {
      var searchValueSend = "";
    } else {
      var searchValueSend = searchValue;
    }

    console.log(searchValueSend);
    console.log(searchValue);
    $.ajax({
      type: "post",
      url: base_url + '/folder/viewFolderReload?search=' + searchValueSend + '&' + 'folderId=' + folderId,
      data: {
        'page': searchValuepage,
        'items': searchValueitems
      },
      success: function success(response) {
        $.ajax({
          type: 'post',
          url: base_url + '/folder/searchFolderById',
          data: {
            'id': folderId
          },
          success: function success(response) {
            $(".boxContent").html(response.detail).show();
            $("ul.folderRtTree").folderRtTree({
              clickListItem: base_url + '/folder/list',
              viewFolderData: base_url + '/folder/viewFolderData',
              ajaxCompletion: function test(response) {
                var items = [];
                $.each(response.data, function () {
                  items.push($(".folderRtTree a.active").siblings("ul").append('<li><a href="#" id="' + this.id + '">' + this.name + ' <span style="    position: absolute;right: 4px;">' + this.totalItems + '</span></a><ul></ul>  <div  class="editBtn" id="editBtnId" data-id="' + this.id + '" data-title="' + this.name + '" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div> </li>'));
                });
                window.location.hash = "&folderId=" + $(".folderRtTree a.active").attr('id');
                $(".viewFolder").html(response).show();
              }
            });

            if ($('.boxContent .folderRtTree .active').attr('id') == folderId) {
              $('.boxContent .folderRtTree li ul').css('display', 'none');

              if ($('.boxContent .folderRtTree .active').parent('li').first().parent('ul').attr('class') == 'rtTree') {
                $('.boxContent .folderRtTree .active').parent('li').first().children('ul').css('display', '');
              } else {
                jQuery.each($('.boxContent .folderRtTree .active').parents('ul'), function (i, value) {
                  $(value).css('display', '');
                });
                $('.boxContent .folderRtTree .active').parent('li').children('ul').css('display', '');
              }
            }
          }
        });
        $(".viewFolder").html(response).show(); // $(".searchViewItems").html(response).show();

        $('.rtMenuSearch').val(searchValue);
      }
    });
    console.log("we");
  }

  $(document).on('click', '.rtDataTable #reloadFolderPagination ul li a', function (e) {
    e.preventDefault();
    var url = $(this).attr('href');
    var page = url.split('page=')[1]; // const paginate = $('.rtDataTableHeaderMenu .selectPaginateFolder').val();

    var paginate = 10;
    console.log(location.hash.split('#')[1]);
    var folderId = location.hash.split('#')[1].split('&')[1].split('=')[1];

    if (typeof location.hash.split('#')[1].split('&')[2] !== "undefined") {
      var searchValue = location.hash.split('#')[1].split('&')[2].split('=')[1].replace("%20", " ");
    } else {
      var searchValue = "";
    }

    if (searchValue == "") {
      var searchValueSend = "";
    } else {
      var searchValueSend = searchValue;
    }

    console.log("data");
    $.ajax({
      type: 'post',
      url: base_url + '/folder/viewFolderReload?items=' + paginate + '&page=' + page,
      data: {
        folderId: folderId,
        search: searchValueSend
      },
      success: function success(response) {
        $(".viewFolder").html(response).show();
        $('.rtMenuSearch').val(searchValue);
        window.location.hash = "&folderId=" + folderId + "&search=" + searchValueSend + "&items=" + paginate + '&page=' + page;
        dearch();
      }
    });
  });
  $(document).on('click', '.rtDataTable #searchFolderPagination ul li a', function (e) {
    e.preventDefault();
    var url = $(this).attr('href');
    var page = url.split('page=')[1];
    var paginate = $('.rtDataTableHeaderMenu .selectPaginateFolder').val();
    var id = $('.mainFolderId').val();
    var search = $('.rtMenuSearch').val();
    $.ajax({
      type: 'get',
      url: base_url + '/dashboard/company/folder/searchFolderItems?search=' + search + '&' + 'folderId=' + id,
      data: {
        'page': page,
        'items': paginate
      },
      success: function success(response) {
        $(".searchViewItems").html(response).show();
        $('.rtMenuSearch').val(search);
        window.location.hash = "&folderId=" + id + "&search=" + search + "&items=" + paginate + '&page=' + page;
        dearch();
      }
    });
  });

  function dearch() {
    $(document).ready(function () {
      var timers = null;
      $('#searchFolderInputs').keydown(function () {
        clearTimeout(timers);
        timers = setTimeout(doStuffs, 1000);
      });

      function doStuffs() {
        $(".datatable").html('<div style="position: absolute;left: 50%;top: 64%;font-weight: bold;text-align:center;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="margin-bottom:10px;"></i></div>');

        if ($('#searchFolderInputs').val().length > 0) {
          $.ajax({
            type: "get",
            url: base_url + '/dashboard/company/folder/searchFolderItems?search=' + $('#searchFolderInputs').val() + '&' + 'folderId=' + $('.mainFolderId').val(),
            success: function success(response) {
              if (response == "") {} else {
                window.location.hash = '&' + location.hash.split('#')[1].split('&')[1] + "&search=" + $('#searchFolderInputs').val();
                $(".searchViewItems").html(response).show();
              }
            }
          });
        } else {
          $.ajax({
            type: "get",
            data: {
              data: "all",
              'folderId': $('.mainFolderId').val()
            },
            url: base_url + '/dashboard/company/folder/searchFolderItems?search=',
            success: function success(response) {
              if (response == "") {} else {
                window.location.hash = '&' + location.hash.split('#')[1].split('&')[1] + "&search=";
                $(".searchViewItems").html(response).show();
              }
            }
          });
        }
      }
    });
  }
});

/***/ }),

/***/ 1:
/*!************************************************!*\
  !*** multi ./resources/assets/js/dashboard.js ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! G:\WebAndApp\RecordTime-Backend\resources\assets\js\dashboard.js */"./resources/assets/js/dashboard.js");


/***/ })

/******/ });