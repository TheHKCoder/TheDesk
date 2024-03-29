<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/test', 'TemplateController@test') -> name('test');


// logout pages
Route::get('/', 'AuthController@home') -> name('logout.home');
Route::get('/login', 'AuthController@index') -> name('logout.login');
Route::get('/register', 'AuthController@register') -> name('logout.register');
Route::get('/forgotPassword', 'AuthController@forgotPassword') -> name('logout.forgotPassword');
Route::get('/resetPassword', 'AuthController@resetPassword') -> name('logout.resetPassword');

Route::post('/ajax/login', 'AuthController@ajaxLogin') -> name('ajax.login');
Route::post('/ajax/register', 'AuthController@ajaxRegister') -> name('ajax.register');

// login pages
Route::get('/backend/logout', 'AuthController@logout') -> name('backend.logout');

// chatroom
Route::get('/chatroom/chat/{unique_id}', 'ChatroomController@chat') -> name('login.chatroom.chat');
Route::get('/chatroom/setting/{unique_id}', 'ChatroomController@setting') -> name('login.chatroom.setting');
Route::get('/backend/chatroom/setting/switchType/{unique_id}', 'ChatroomController@backendSwitchType') -> name('backend.chatroom.switchType');
Route::get('/chatroom/setting/channelAddUser/{unique_id}', 'ChatroomController@channelAddUser') -> name('login.chatroom.channelAddUser');
Route::post('/backend/chatroom/channelAddUser/{unique_id}', 'ChatroomController@backendChannelAddUser') -> name('backend.chatroom.channelAddUser');
Route::post('/ajax/chatroom/setting/{mode}', 'ChatroomController@ajaxSetting') -> name('ajax.chatroom.setting');    // update chatroom info
Route::post('/ajax/chatroom/getChatroomList', 'ChatroomController@ajaxGetChatroomList') -> name('ajax.chatroom.getChatroomList');

Route::post('/ajax/newMessage', 'ChatroomController@ajaxNewMessage') -> name('ajax.chatroom.newMessage');
Route::get('/backend/chatroom/messageSeen/{unique_id}', 'ChatroomController@backendMessageSeen') -> name('backend.chatroom.messageSeen');

Route::get('/backend/chatroom/createChatroom/{unique_id}', 'ChatroomController@backendAddToChat') -> name('backend.chatroom.startChat');
Route::post('/chatroom/createChannel', 'ChatroomController@createChannel') -> name('login.chatroom.createChannel');
Route::post('/ajax/createChannel', 'ChatroomController@ajaxCreateChannel') -> name('ajax.createChannel');

// contact list
Route::get('/chatroom/contacts', 'ContactController@index') -> name('login.chatroom.contacts');
Route::post('/ajax/searchContact', 'ContactController@ajaxSearchContact') -> name('ajax.searchContact');
Route::get('/backend/addContact/{unique_id}', 'ContactController@backendAddContact') -> name('backend.chatroom.addContact');
Route::get('/backend/hideContact/{unique_id}', 'ContactController@backendHideContact') -> name('backend.chatroom.hideContact');

// discover user
Route::get('/chatroom/discover', 'UserController@discoverUser') -> name('login.chatroom.discover');
Route::post('/ajax/discover', 'UserController@ajaxDiscover') -> name('ajax.discover');

// request
Route::get('/request/new/{unique_id}', 'NewRequestController@new') -> name('login.request.new');
Route::post('/ajax/newRequest', 'NewRequestController@ajaxNewRequest') -> name('ajax.newRequest');
Route::get('/request/view', 'NewRequestController@view') -> name('login.request.view');
Route::get('/backend/request/response/{unique_id}', 'NewRequestController@backendResponse') -> name('backend.request.response');

// profile management
Route::get('/home', 'UserController@index') -> name('login.home');
Route::get('/login/account/profile', 'UserController@profile') -> name('login.account.profile');
Route::post('/ajax/updateProfile', 'UserController@ajaxUpdateProfile') -> name('ajax.updateProfile');
Route::get('/login/account/editPassword', 'UserController@editPassword') -> name('login.account.editPassword');
Route::post('/ajax/editPassword', 'UserController@ajaxEditPassword') -> name('ajax.editPassword');

// business admin 
Route::get('/businessAdmin/user', 'BusinessAdminController@viewUser') -> name('login.businessAdmin.viewUser');
Route::get('/businessAdmin/removeBusinessPlanUser/{unique_id}', 'BusinessAdminController@removeBusinessPlanUser') -> name('login.businessAdmin.removeBusinessPlanUser');
Route::get('/businessAdmin/addUser', 'BusinessAdminController@addUser') -> name('login.businessAdmin.addUser');
Route::post('/ajax/addBusinessUser', 'BusinessAdminController@ajaxAddBusinessUser') -> name('ajax.addBusinessUser');

// admin
Route::get('/admin/businessPlan', 'AdminController@viewBusinessPlan') -> name('login.admin.viewBusinessPlan');
Route::post('/ajax/searchBusinessPlan', 'AdminController@ajaxSearchBusinessPlan') -> name('ajax.searchBusinessPlan');
Route::get('/admin/businessPlanDetails/{unique_id}', 'AdminController@viewBusinessPlanDetails') -> name('login.admin.viewBusinessPlanDetails');
Route::get('/admin/addAdminUser', 'AdminController@addAdminUser') -> name('login.admin.addAdminUser');
Route::post('/ajax/addAdminUser', 'AdminController@ajaxAddAdminUser') -> name('ajax.addAdminUser');

Route::get('/admin/createBusinessPlan', 'AdminController@createBusinessPlan') -> name('login.admin.createBusinessPlan');
Route::post('/ajax/createBusinessPlan', 'AdminController@ajaxCreateBusinessPlan') -> name('ajax.createBusinessPlan');