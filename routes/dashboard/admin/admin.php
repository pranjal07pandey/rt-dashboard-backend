<?php

Route::resource('/userManagement', 'UserManagementController');

//------------------------subscription plan---------------------------//
Route::resource('/subscriptionPlan', 'SubscriptionPlanController');
Route::get('/subscriptionPlan/description/{key}', 'SubscriptionPlanController@description');
Route::post('/subscriptionPlan/description/{key}', 'SubscriptionPlanController@descriptionStore');
Route::resource('/addOnsManagement', 'AddOnsManagementController');

Route::post('submitLabel', 'DashboardController@submitLabel');
Route::get('/clientManagement/view', array('as' => 'clientManagement', 'uses' => 'DashboardController@clientManagementView'));
Route::get('deleteEmployee', 'DashboardController@deleteEmployee');
Route::delete('deleteEmployee/{key}', 'DashboardController@deleteEmployeeSubmit');
Route::get('/appSetting', 'DashboardController@appSetting');
Route::post('/appSetting/saveAppInfo', 'DashboardController@saveAppInfo');
Route::post('/appSetting/updateAppInfo', 'DashboardController@updateAppInfo');

//DocumentTheme
Route::get('/documentThemes', 'DocumentThemeController@index')->name('document_theme');
Route::post('/documentTheme/store', 'DocumentThemeController@store');
Route::get('/documentTheme/edit/{id}', 'DocumentThemeController@edit');
Route::post('/documentTheme/update', 'DocumentThemeController@update');
Route::get('/documentTheme/delete/{id}', 'DocumentThemeController@destroy');
Route::get('/documentTheme/restore/{id}', 'DocumentThemeController@restore');

//DefaultTemplate
Route::get('/defaultTemplate', 'DefaultTemplateController@defaultTemplate');
Route::get('/defaultTemplate/category', 'DefaultTemplateController@defaultTemplateCategory');
Route::post('/defaultTemplate/saveDefaultCataegory', 'DefaultTemplateController@saveDefaultCataegory');
Route::post('/defaultTemplate/deleteCategory', 'DefaultTemplateController@deleteCategory');
Route::post('/defaultTemplate/updateDefaultCataegory', 'DefaultTemplateController@updateDefaultCataegory');
Route::post('/defaultTemplate/saveDefaultTemplates', 'DefaultTemplateController@saveDefaultTemplates');
Route::post('/defaultTemplate/deleteDefaultDocket', 'DefaultTemplateController@deleteDefaultDocket');
Route::get('/defaultTemplate/designDefaultDocket/{id}', 'DefaultTemplateController@designDefaultDocket');
Route::post('/defaultTemplate/designDefaultDocket/addDocketField/{key}', 'DefaultTemplateController@addDocketField');
Route::post('/defaultTemplate/designDefaultDocket/deleteDocketField/{key}', 'DefaultTemplateController@deleteDocketField');
Route::post('/defaultTemplate/designDefaultDocket/docketFieldUpdatePosition/{key}', 'DefaultTemplateController@docketFieldUpdatePosition');
Route::post('/defaultTemplate/designDefaultDocket/docketFieldLabelUpdate', 'DefaultTemplateController@docketFieldLabelUpdate');
Route::post('/defaultTemplate/designDefaultDocket/updateTempDocket', 'DefaultTemplateController@updateTempDocket');


//Purchased Themes
Route::get('/purchased/theme', 'ReportController@purchasedTheme')->name('purchase_themes_company');

//------------------------end of subscription plan routes---------------------------//

Route::group(['prefix' => 'reports'], function () {
    Route::get('/', 'ReportController@index')->name('dashboardReport');
    Route::get('/company', 'ReportController@company')->name('report_by_comapny');
    Route::get('/company/excel', 'ReportController@excel')->name('dashboard.reports.company.excel');
    Route::get('/company/view/{id}/employees', 'ReportController@employee');
    Route::get('/company/view/{id}/docket/templates', 'ReportController@docketTemplate');
    Route::get('/invoices', 'ReportController@invoices')->name('stripe_invoices');
    Route::get('/nonActiveCompany', 'ReportController@nonActiveCompany')->name('non_active_company');
    Route::post('/nonActiveCompany/delete', 'ReportController@nonActiveCompanydelete');
});

//Feature Explanation Management

Route::get('/feature/category', 'FeatureController@index')->name('category_list');
Route::post('/feature/saveCategory', 'FeatureController@saveCategoryInfo');
Route::post('/feature/updateCategory', 'FeatureController@updateCategoryInfo');
Route::get('/feature/category/{id}/delete', 'FeatureController@categoryDelete');
Route::get('/feature/category/{id}/restore', 'FeatureController@categoryRestore');
Route::get('/feature/category/post/{id}/view', 'FeatureController@post');
Route::post('/feature/savePost', 'FeatureController@savePost');
Route::post('/feature/updatePost', 'FeatureController@updatePostInfo');
Route::get('/feature/category/post/{id}/delete', 'FeatureController@postDelete');
Route::get('/feature/category/post/{id}/restore', 'FeatureController@postRestore');
Route::post('/feature/savePostScreenshot', 'FeatureController@savePostScreenshot');
Route::get('/feature/category/post/screenshot/{id}/view', 'FeatureController@postScreenshot');
Route::post('/feature/updateScreenshot', 'FeatureController@updateScreenshot');

//Docket Field Categories
Route::get('/docket/category', 'DocketFieldCategoryController@index')->name('docket.field.category');
Route::post('/docket/category/store', 'DocketFieldCategoryController@store')->name('docket.field.category.store');
Route::post('/docket/category/update', 'DocketFieldCategoryController@update')->name('docket.field.category.update');