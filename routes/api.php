<?php

use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\ProjectController as SuperAdminProjectController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\notification\FcmTokenController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\Mobile\LoginController;
use App\Http\Controllers\PhaseController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TwoFactorAuthController;
use App\Http\Controllers\EntrepreneurController;
use App\Http\Controllers\GanttChartController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\Mobile\Entrepreneur\DashboardController;
use App\Http\Controllers\Mobile\Entrepreneur\ProjectController as EntrepreneurProjectController;
use App\Http\Controllers\Mobile\Tenant\ProjectController as TenantProjectController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProjectMessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetTypeController;
use App\Http\Controllers\InvitationLogController;
use App\Http\Controllers\Mobile\Investor\DashboardController as InvestorDashboardController;
use App\Http\Controllers\Mobile\Investor\ProjectController as InvestorProjectController;
use App\Http\Controllers\Mobile\Tenant\DashboardController as TenantDashboardController;
use App\Http\Controllers\ProjectDocumentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectInvitationController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\CardDetailController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TestController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Add these routes in routes/api.php

Route::middleware('auth:api')->group(function () {
    Route::post('fcm-token', [FcmTokenController::class, 'saveFcmToken']);
    Route::post('send-notification', [FcmTokenController::class, 'sendNotification']);
});

Route::post('/signup/{role}', [SignupController::class, 'processSignup'])
    ->whereIn('role', ['investor', 'tenant', 'entrepreneur', 'admin'])
    ->name('signup.submit');
Route::post('signup/verify-otp', [SignupController::class, 'verifyOtp']);
Route::post('/send-otp', [SignupController::class, 'sendOtp']);


Route::post('register', [AuthController::class, 'register']);
Route::post("ForgotPassword", [AuthController::class, 'ForgotPassword']);
Route::post('2fa/verify', [TwoFactorAuthController::class, 'verify2FA']);

Route::post('login', [AuthController::class, 'login']);
Route::post('resend-otp', [AuthController::class, 'resendOtp']);
Route::post('verify-otp', [AuthController::class, 'verifyOtp']);

Route::get('gantt-data', [GanttChartController::class, 'getAllProjectsGanttData']);
Route::post('send-2fa-qr', [AuthController::class, 'send2FAQrToEmail']);

Route::post('mobile/login', [LoginController::class, 'login']);
Route::post('mobile/verify-otp', [LoginController::class, 'verifyOtp']);
Route::post('mobile/logout', [LoginController::class, 'logout']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('invitation-logs', [InvitationLogController::class, 'index']);
    Route::prefix('projects')->group(function () {
        Route::post('documents', [ProjectDocumentController::class, 'store']);
        Route::get('{id}/documents', [ProjectDocumentController::class, 'index']);
        Route::delete('documents/{id}', [ProjectDocumentController::class, 'destroy']);
    });


    Route::post('2fa/enable', [TwoFactorAuthController::class, 'enable2FA']);
    Route::post('2fa/disable', [TwoFactorAuthController::class, 'disable2FA']);
    Route::get('profile', [AuthController::class, 'profile']);
    Route::post("logout", [AuthController::class, 'logout']);

    Route::post("update-password", [AuthController::class, 'updatepassword']);
    Route::get('get-all-permission', [EntrepreneurController::class, 'getAllPermission']);


    // Protect API routes with 2FA
    Route::middleware('2fa')->group(function () {
        Route::get('dashboard', function () {
            return response()->json(['message' => 'Welcome to the protected dashboard!']);
        });
    });
    Route::get('/get-user-permission', function () {
        $permissions = auth()->user()->getDirectPermissions();
        return response()->json([
            'permissions' => $permissions

        ]);
    });
});
Route::group(['middleware' => ['auth:sanctum', 'role:entrepreneur|super admin|admin|investor|tenant']], function () {
    Route::group(['prefix' => 'superadmin'], function () {
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index']);
        Route::apiResource('/projects', SuperAdminProjectController::class);
        Route::get('project/documents/{project_id}', [SuperAdminProjectController::class, 'documents']);
    });
});
Route::group(['middleware' => ['auth:sanctum', 'role:entrepreneur|super admin|admin']], function () {
    Route::group(['prefix' => 'admin'], function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);
        Route::get('GetPhase/{id}', [AdminDashboardController::class, 'getPhasesByProject']);
        Route::get('project-financial-chart', [AdminDashboardController::class, 'projectFinancialChart']);
        Route::apiResource('/projects', AdminProjectController::class);
        Route::get('project/documents/{project_id}', [AdminProjectController::class, 'documents']);
    });
});
Route::group(['middleware' => ['auth:sanctum', 'role:super admin|admin|entrepreneur|investor|tenant']], function () {

    Route::group(['prefix' => 'mobile'], function () {
        Route::get('/dashboard', [DashboardController::class, 'index']);
    });

    Route::group(['prefix' => 'entrepreneur'], function () {
        Route::apiResource('/projects', EntrepreneurProjectController::class);
        Route::get('project/documents/{project_id}', [EntrepreneurProjectController::class, 'documents']);
    });
    Route::group(['prefix' => 'tenant'], function () {
        Route::apiResource('/projects', TenantProjectController::class);
    });

    Route::group(['prefix' => 'investor'], function () {
        Route::apiResource('/projects', InvestorProjectController::class);
    });
    Route::apiResource('asset-types', AssetTypeController::class);

    Route::apiResource('investor', InvestorController::class);
    Route::apiResource('tenant', TenantController::class);
    Route::apiResource('entrepreneur', EntrepreneurController::class);

    Route::post('upload-profile-photo', [UserController::class, 'uploadProfilePhoto']);

    Route::apiResource('users', UserController::class);
    Route::get('user/roles', [UserController::class, 'userRoles']);
    Route::get('user/dropdowns', [UserController::class, 'userDropdownByRoles']);

    Route::get('get-users', [AuthController::class, 'showAllUsers']);
    Route::get('users-info', [AuthController::class, 'showAllUsersinfo']);
    // Projects
    Route::get('projects', [ProjectController::class, 'index']);
    Route::get('projects/{id}', [ProjectController::class, 'show']);
    Route::post('projects', [ProjectController::class, 'store']);
    Route::post('projects/update/{id}', [ProjectController::class, 'update']);
    Route::delete('projects/{id}', [ProjectController::class, 'destroy']);
    Route::get('projects/{id}/history', [ProjectController::class, 'history']);
    Route::get('deleted/projects', [ProjectController::class, 'deletedProjects']);
    Route::post('projects/restore/{id}', [ProjectController::class, 'restoreProject']);
    Route::get('admin/projects', [ProjectController::class, 'adminProjects']);

    Route::get('get-projects', [ProjectController::class, 'getAllProjects']);


    // project chat
    Route::get('projects/{project}/messages', [ProjectMessageController::class, 'index']);
    Route::post('projects/{project}/messages', [ProjectMessageController::class, 'store']);

    // Phases
    Route::get('phases', [PhaseController::class, 'index']);
    Route::get('phases/{id}', [PhaseController::class, 'show']);
    Route::post('phases', [PhaseController::class, 'store']);
    Route::post('phases/update/{id}', [PhaseController::class, 'update']);
    Route::delete('phases/{id}', [PhaseController::class, 'destroy']);
    Route::post('phase/timeline', [PhaseController::class, 'timeline']);
    Route::post('phase/budget', [PhaseController::class, 'budget']);
    Route::post('phase/timeline/{id}', [PhaseController::class, 'updateTimeline']);
    Route::post('phase/budget/{id}', [PhaseController::class, 'updateBudget']);
    Route::get('phase/categories/{project_id}', [PhaseController::class, 'categories']);

    Route::get('/projects/{project}/phases/summary', [PhaseController::class, 'summary']);

    // Milestones
    Route::get('milestones', [MilestoneController::class, 'index']);
    Route::get('milestones/{id}', [MilestoneController::class, 'show']);
    Route::post('milestones', [MilestoneController::class, 'store']);
    Route::post('milestones/update/{id}', [MilestoneController::class, 'update']);
    Route::delete('milestones/{id}', [MilestoneController::class, 'destroy']);
    Route::get('milestones-stage-colors', [MilestoneController::class, 'getStageColors']);

    // notification
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::post('notifications/send', [NotificationController::class, 'send']);
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    // chat
    Route::post('chat/send', [MessageController::class, 'send']);
    Route::get('chat/with/{userId}', [MessageController::class, 'chatWith']);
    Route::get('chat/conversations', [MessageController::class, 'conversations']);
    Route::get('/project/messages/{project_id}', [MessageController::class, 'messages']);
    Route::get('/project/all-messages', [MessageController::class, 'AllMessages']);

    //plan
    Route::apiResource('/plans', PlanController::class);
    Route::apiResource('card-details', CardDetailController::class)->only(['index', 'store', 'update', 'show']);
    Route::post('active-card-details/{cardDetail}', [CardDetailController::class, 'setActiveCardDetail']);
    Route::post('payment', [PaymentController::class, 'processPayment']);
    Route::get('/user-active-plan', [PlanController::class, 'checkUserCurrentMonthActivePlan']);
    Route::get('/user-expired-plan', [PlanController::class, 'checkUserExpiredPlan']);
    Route::post('update-plan-status/{id}/{status}', [PlanController::class, 'subscribeUnsubscribePlan']);
    Route::apiResource('transactions', TransactionController::class);
});

// Project Invitation Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/project-invitations/pending', [ProjectInvitationController::class, 'pendingInvitations']);
    Route::post('/project-invitations/{projectId}/accept', [ProjectInvitationController::class, 'accept']);
    Route::post('/project-invitations/{projectId}/reject', [ProjectInvitationController::class, 'reject']);
    Route::get('/project-invitations/{projectId}/role', [ProjectInvitationController::class, 'getProjectRole']);

    Route::post('/support/message', [SupportController::class, 'sendSupportMessage']);

    Route::post('expired-user-active-plan', [TestController::class, 'expiredUserExistingPlan']);
});


Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
