<?php

use App\Http\Controllers\ClientsController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\InfluencerController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MainMenuController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MenuPermissionController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PlatformSocialController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\ContentStyleController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SubtypeController;
use App\Http\Controllers\PastProjectController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\PresentationController;
use App\Http\Controllers\InfluencerAddressController;
use App\Http\Controllers\ProjectTimelineController;
use App\Http\Controllers\SocialInfluencerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ExcelController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

//////////////////////////////////////////web no route group/////////////////////////////////////////////////////
//Login Admin
// Route::post('/login', [LoginController::class, 'login']);

//use middleware checkjwt
// Route::post('/check_login', [LoginController::class, 'checkLogin']);

//user
// Route::post('/create_admin', [UserController::class, 'createUserAdmin']);
// Route::post('/forgot_password_user', [UserController::class, 'ForgotPasswordUser']);

// Pdf
Route::get('/get_pdf', [PdfController::class, 'generatePdf']);

// Presentatiion
Route::get('/get_ppx/{id}', [PresentationController::class, 'generatePresentation']);

// Excel
Route::post('/get_excel', [ExcelController::class, 'ExportServiceCenterByComp']);

// Upload File
Route::post('/upload_file', [Controller::class, 'uploadFile']);
Route::post('/download_file', [Controller::class, 'downloadFile']);

// Line Notification
Route::get('/updateproject/{id}', [ProjectController::class, 'UpdateProjectStatus']);

// Influencer Social
Route::post('/get_Tiktok_info', [SocialInfluencerController::class, 'getTiktokInfo']);
Route::post('/get_Youtube_info', [SocialInfluencerController::class, 'getYoutubeInfo']);
Route::post('/get_Instagram_info', [SocialInfluencerController::class, 'getInstagramInfo']);

Route::resource('presentation', PresentationController::class);
Route::post('/presentation_page', [PresentationController::class, 'getPage']);
Route::get('/get_presentation', [PresentationController::class, 'getList']);
Route::get('/influ_slide', [PresentationController::class, 'createInfluSide']);

// Influencer Employee management
Route::resource('influencer', InfluencerController::class);
Route::post('/influencer_page', [InfluencerController::class, 'getPage']);
Route::get('/get_influencer', [InfluencerController::class, 'getList']);
Route::post('/fix_influencer', [InfluencerController::class, 'fixdataInfluencer']);
Route::get('/search_influencer', [InfluencerController::class, 'searchData']);
Route::post('/get_influtimeline', [InfluencerController::class, 'getInfluencerTimeline']);

// Influencer Self management
// Route::post('/line_influencer', [InfluencerController::class, 'Line_Influencer']);
Route::post('/selfassign', [InfluencerController::class, 'selfassign']);
Route::post('/selfupdate', [InfluencerController::class, 'selfupdate']);

// Influencer Login
Route::post('/line_influencer', [LoginController::class, 'influencerlogin']);

// PlatformSocial
Route::resource('platformSocial', PlatformSocialController::class);
Route::post('/platformSocial_page', [PlatformSocialController::class, 'getPage']);
Route::get('/get_platformSocial', [PlatformSocialController::class, 'getList']);

// subtypeController
Route::resource('subtype', SubtypeController::class);
Route::post('/subtype_page', [SubtypeController::class, 'getPage']);
Route::get('/get_subtype', [SubtypeController::class, 'getList']);

// Career
Route::resource('career', CareerController::class);
Route::post('/career_page', [CareerController::class, 'getPage']);
Route::get('/get_career', [CareerController::class, 'getList']);

// ContentStyle
Route::resource('contentstyle', ContentStyleController::class);
Route::post('/contentstyle_page', [ContentStyleController::class, 'getPage']);
Route::get('/get_contentstyle', [ContentStyleController::class, 'getList']);

// Customer
Route::resource('customer', CustomerController::class);
Route::post('/customer_page', [CustomerController::class, 'getPage']);
Route::get('/get_customer', [CustomerController::class, 'getList']);
Route::get('/search_customer', [CustomerController::class, 'searchData']);

// customer Login
Route::post('/google_customer', [LoginController::class, 'customerlogin']);

// Employee
Route::resource('employee', EmployeeController::class);
Route::post('/employee_page', [EmployeeController::class, 'getPage']);
Route::get('/get_employee', [EmployeeController::class, 'getList']);
Route::post('/add_emp_credential', [EmployeeController::class, 'addCredential']);
Route::get('/search_employee', [EmployeeController::class, 'searchData']);

// Employee Login
Route::post('/google_employee', [LoginController::class, 'employeelogin']);
Route::post('/selfassign_employee', [EmployeeController::class, 'selfassign']);

// Department
Route::resource('department', DepartmentController::class);
Route::post('/department_page', [DepartmentController::class, 'getPage']);
Route::get('/get_department', [DepartmentController::class, 'getList']);

// Position
Route::resource('position', PositionController::class);
Route::post('/position_page', [PositionController::class, 'getPage']);
Route::get('/get_position', [PositionController::class, 'getList']);

// Address
Route::get('/provinces', [ AddressController::class , 'getProvinces' ]);
Route::get('/amphoes', [AddressController::class , 'getAmphoes' ]);
Route::get('/tambons', [ AddressController::class , 'getTambons' ]);
Route::get('/zipcodes', [AddressController::class, 'getZipcodes'] );

// Influencer Address
Route::get('/influ_provinces', [ InfluencerAddressController::class , 'getProvinces' ]);
Route::get('/influ_amphoes', [InfluencerAddressController::class , 'getAmphoes' ]);
Route::get('/influ_tambons', [ InfluencerAddressController::class , 'getTambons' ]);
Route::get('/influ_zipcodes', [InfluencerAddressController::class, 'getZipcodes'] );

// Project
Route::resource('project', ProjectController::class);
Route::post('/project_page', [ProjectController::class, 'getPage']);
Route::get('/get_project', [ProjectController::class, 'getList']);
Route::post('/add_project_influencer', [ProjectController::class, 'addInfluencer']);

// Product
// Route::resource('product', ProductController::class);
// Route::post('/product_page', [ProductController::class, 'getPage']);
// Route::get('/get_product', [ProductController::class, 'getList']);
Route::get('/get_product/{id}', [ProductController::class, 'getProductbyProject']);
Route::post('/product_item', [ProductController::class, 'addProductItem']);

// Past Project
Route::resource('pastproject', PastProjectController::class);
Route::post('/pastproject_page', [PastProjectController::class, 'getPage']);
Route::get('/get_pastproject', [PastProjectController::class, 'getList']);

// Project Timeline
Route::resource('project_timeline', ProjectTimelineController::class);
Route::post('/project_timeline_page', [ProjectTimelineController::class, 'getPage']);
Route::get('/get_project_timeline', [ProjectTimelineController::class, 'getList']);
Route::post('/update_draft_status', [ProjectTimelineController::class, 'updateStatus']);

Route::post('/item_bymonth', [ProjectTimelineController::class, 'getProductTimelineByMonth']);
Route::post('/update_timeline', [ProjectTimelineController::class, 'updateTimeline']);


// Client
// Route::resource('client', ClientsController::class);
// Route::post('/client_page', [ClientsController::class, 'getPage']);
// Route::get('/get_client', [ClientsController::class, 'getList']);
// Route::post('/update_client', [ClientsController::class, 'updateData']);

// // province
// Route::resource('province', ProvinceController::class);
// Route::post('/province_page', [ProvinceController::class, 'getPage']);
// Route::get('/get_province', [ProvinceController::class, 'getList']);

// // Permission
// Route::resource('permission', PermissionController::class);
// Route::post('/permission_page', [PermissionController::class, 'getPage']);
// Route::get('/get_permission', [PermissionController::class, 'getList']);

// //Main Menu
// Route::resource('main_menu', MainMenuController::class);
// Route::get('/get_main_menu', [MainMenuController::class, 'getList']);

// //Menu
// Route::resource('menu', MenuController::class);
// Route::get('/get_menu', [MenuController::class, 'getList']);

// //Menu Permission
// Route::resource('menu_permission', MenuPermissionController::class);
// Route::get('/get_menu_permission', [MenuPermissionController::class, 'getList']);
// Route::post('checkAll', [MenuPermissionController::class, 'checkAll']);

//controller
// current use this!!!
Route::post('upload_images', [Controller::class, 'uploadImages']);

// Route::post('upload_file', [Controller::class, 'uploadFile']);
// Route::post('upload_signature', [Controller::class, 'uploadSignature']);

//user
// Route::resource('user', UserController::class);
// Route::get('/get_user', [UserController::class, 'getList']);
// Route::post('/user_page', [UserController::class, 'getPage']);
// Route::get('/user_profile', [UserController::class, 'getProfileUser']);
// Route::post('/update_user', [UserController::class, 'update']);


Route::resource('user', UserController::class);
Route::put('/update_password_user/{id}', [UserController::class, 'updatePasswordUser']);
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

Route::group(['middleware' => 'checkjwt'], function () {


    Route::put('/reset_password_user/{id}', [UserController::class, 'ResetPasswordUser']);
    Route::post('/update_profile_user', [UserController::class, 'updateProfileUser']);
    Route::get('/get_profile_user', [UserController::class, 'getProfileUser']);
});

//upload

// Route::post('/upload_file', [UploadController::class, 'uploadFile']);
