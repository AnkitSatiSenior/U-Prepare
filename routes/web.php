<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\NavbarItemController;
use App\Http\Controllers\BoqentryDataController;
use App\Http\Controllers\ContractorController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PackageProjectAssignmentController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\EpcEntryDataController;
use App\Http\Controllers\Admin\UserSafeguardSubpackageController;
use App\Http\Controllers\SafeguardEntryController;
use App\Http\Controllers\Admin\PackageProjectController;
use App\Http\Controllers\Admin\DesignationController;
use App\Http\Controllers\Admin\ContractionPhaseController;
use App\Http\Controllers\AlreadyDefineEpcController;
use App\Http\Controllers\FinancialProgressUpdateController;
use App\Http\Controllers\Admin\SafeguardComplianceController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\PhysicalBoqProgressController;
use App\Http\Controllers\SocialSafeguardEntryController;
use App\Http\Controllers\Admin\ProjectsCategoryController;
use App\Http\Controllers\Admin\PhysicalEpcProgressController;
use App\Http\Controllers\Admin\SubPackageProjectTestTypeController;
use App\Http\Controllers\Admin\WorkServiceController;
use App\Http\Controllers\MediaFileController;
use App\Http\Controllers\Admin\RoleRouteController;
use App\Http\Controllers\Admin\SubPackageProjectTestController;
use App\Http\Controllers\Admin\ProcurementDetailController;
use App\Http\Controllers\Admin\PackageComponentController;
use App\Http\Controllers\Admin\SubDepartmentController;
use App\Http\Controllers\Admin\ProcurementWorkProgramController;
use App\Http\Controllers\Admin\TypeOfProcurementController;
use App\Http\Controllers\Admin\AlreadyDefineSafeguardEntryController;
use App\Http\Controllers\Admin\SlideController;
use App\Http\Controllers\Admin\ContractSecurityTypeController;
use App\Http\Controllers\Admin\ContractSecurityFormController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminNewsController;
use App\Http\Controllers\AdminTenderController;
use App\Http\Controllers\Admin\AlreadyDefinedWorkProgressController;
use App\Http\Controllers\Admin\WorkProgressDataController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SubPackageProjectTestReportController;
use App\Http\Controllers\Admin\ContractSecurityController;
use App\Http\Controllers\Admin\GrievanceController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\Admin\FeedbackController as AdminFeedback;
use App\Http\Controllers\RoleDashboardController;
use App\Http\Controllers\SafeguardGlobalFunctionController;
use App\Http\Controllers\PackageProjectDocumentController;
use App\Http\Controllers\GrievancePublicController;
use App\Http\Controllers\Admin\GrievanceComplaintNatureController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProjectAccessSummaryController;
use App\Http\Controllers\PermissionGroupController;
use App\Http\Controllers\Admin\ActivityLogController;
use Symfony\Component\Process\Process;
use App\Http\Controllers\Admin\ProjectSubprojectLinkController;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Artisan;

Route::get('/link-storage', function () {
    Artisan::call('storage:link');
    $output = Artisan::output();
    return back()->with('success', "Storage linked successfully! \n$output");
});
Route::get('/media-files/by-ids', [MediaFileController::class, 'getByIds'])->name('media-files.by-ids');

Route::get('/en/grievance', [GrievancePublicController::class, 'create'])->name('grievances.create');
Route::post('/grievance', [GrievancePublicController::class, 'store'])->name('grievances.store');
Route::get('/en/grievance/status/{grievance_no}', [GrievancePublicController::class, 'status'])->name('grievances.status');
Route::get('/en/grievance/status', [GrievancePublicController::class, 'status2'])->name('grievances.status.with');
Route::post('/activity-log-location', [\App\Http\Controllers\Admin\ActivityLogController::class, 'updateLocation'])->name('activity_logs.update_location');

Route::post('/grievance/status', [GrievancePublicController::class, 'statusSearch'])->name('grievances.status.check');
Route::post('/get-subcategories', [GrievancePublicController::class, 'getSubCats'])->name('grievance.get.scats');
Route::post('/get-districts', [GrievancePublicController::class, 'getDistricts'])->name('grievance.get.districts');
Route::post('/get-blocks', [GrievancePublicController::class, 'getBlocks'])->name('grievance.get.blocks');
Route::post('/get-projects', [GrievancePublicController::class, 'getProjects'])->name('grievance.get.projects');

Route::get('/en/news', [AdminNewsController::class, 'publicIndex'])->name('news.index');
// Hindi news listing
Route::get('/hi/news', [AdminNewsController::class, 'publicIndex'])->name('news.index.hi');
Route::post('/admin/clear-cache', [PageController::class, 'clearCache'])->name('admin.clear.cache');
Route::post('/admin/storage-link', [PageController::class, 'storageLink'])->name('admin.storage.link');
// Show single news item
Route::get('/news/{news}', [AdminNewsController::class, 'show'])->name('news.show');
Route::get('/hi/news/{news}', [AdminNewsController::class, 'show'])->name('news.show.hi');
Route::get('/en/tenders', [AdminTenderController::class, 'publicIndex'])->name('tender.index.public');

// Public form submission
Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
Route::get('/en/{slug}', [PageController::class, 'showPage'])->name('page.show');
Route::get('/hi/{slug}', [PageController::class, 'showPageHi'])->name('page.show.hi');
Route::get('/', [PageController::class, 'showWelcomePage'])->name('welcome.default');
Route::get('/{lang}/{slug}', [PageController::class, 'showLocalizedPage'])
    ->where(['lang' => 'en|hi'])
    ->name('pages.localized');
Route::get('/get-subdepartments/{id}', [ProjectAccessSummaryController::class, 'getSubDepartmentsByDepartment']);
Route::get('/get-phases/{id}', [ProjectAccessSummaryController::class, 'getPhases']);
Route::get('/api/get-users-by-subdepartment/{id}', [ProjectAccessSummaryController::class, 'getUsersBySubDepartment']);
Route::get('/api/get-package-projects-by-user/{id}', [ProjectAccessSummaryController::class, 'getPackageProjectsByUser']);
Route::get('/api/subdepartment-users-projects/{subDepartmentId}', [ProjectAccessSummaryController::class, 'getUsersProjectsAndSubPackagesBySubDepartment']);

Route::get('/api/department-users-projects/{departmentId}', [ProjectAccessSummaryController::class, 'getUsersAndProjectsByDepartment']);

Route::get('/api/get-subpackage/{projectID}', [ProjectAccessSummaryController::class, 'getSubpackge']);

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'role.routes'])->group(function () {
    Route::prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/summary/dynamic-report', [ProjectAccessSummaryController::class, 'dynamicComplianceReport'])->name('package-safeguard.dynamic-report');

            Route::get('safeguards/list', [ProjectAccessSummaryController::class, 'getSafeguardsWithPhases'])->name('safeguards.list');
            Route::get('sub-packages', [ProjectAccessSummaryController::class, 'index'])->name('projects.subpackage-index');
            Route::get('/project-summary/{packageId}/{subId}', [ProjectAccessSummaryController::class, 'summary'])->name('summary');

            Route::get('/permission-groups', [PermissionGroupController::class, 'index'])->name('permission.groups.index');

            Route::post('/permission-groups/store', [PermissionGroupController::class, 'store'])->name('permission.groups.store');

            Route::post('/permission-groups/{id}/update', [PermissionGroupController::class, 'update'])->name('permission.groups.update');

            Route::delete('/permission-groups/{id}/delete', [PermissionGroupController::class, 'destroy'])->name('permission.groups.delete');

            Route::post('/permission-groups/{id}/assign-routes', [PermissionGroupController::class, 'assignRoutes'])->name('permission.groups.routes.assign');

            Route::delete('/permission-group-route/{routeId}/remove', [PermissionGroupController::class, 'removeRoute'])->name('permission.group.route.remove');
            Route::get('/permission-groups/{id}/routes', [PermissionGroupController::class, 'manageRoutes'])->name('permission.groups.routes');

            Route::post('/permission-groups/{id}/routes/save', [PermissionGroupController::class, 'saveRoutes'])->name('permission.groups.routes.save');

            Route::prefix('already-define-safeguards')
                ->name('already-define-safeguards.')
                ->group(function () {
                    // Index Page (List + Import Form)
                    Route::get('/', [AlreadyDefineSafeguardEntryController::class, 'index'])->name('index');

                    // Import Excel
                    Route::post('/import', [AlreadyDefineSafeguardEntryController::class, 'import'])->name('import');

                    // Edit form
                    Route::get('/{id}/edit', [AlreadyDefineSafeguardEntryController::class, 'edit'])->name('edit');

                    // Update entry
                    Route::put('/{id}', [AlreadyDefineSafeguardEntryController::class, 'update'])->name('update');

                    // Delete entry
                    Route::delete('/{id}', [AlreadyDefineSafeguardEntryController::class, 'destroy'])->name('destroy');
                });

            Route::post('/social-safeguard/update/{id}', [SocialSafeguardEntryController::class, 'update'])->name('social.update');

            Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity_logs.index');
            Route::delete('activity-logs/{activityLog}', [ActivityLogController::class, 'destroy'])->name('activity_logs.destroy');

            Route::get('contracts/{contract}/edit-sub-packages', [App\Http\Controllers\Admin\ContractController::class, 'editsubpackge'])->name('contracts.edit-sub-packages');
            Route::post('/sub-projects/update-multiple', [ContractController::class, 'updateMultiple'])->name('sub-projects.update-multiple');

            Route::post('/work-progress/upload-images', [WorkProgressDataController::class, 'uploadImagesToLastProgress'])->name('work_progress_data.uploadImagesToLastProgress');
            Route::get('work-progress/gallery/{projectId}', [WorkProgressDataController::class, 'showProjectImages'])->name('work_progress.gallery');

            Route::post('physical_epc_progress/upload-images', [PhysicalEpcProgressController::class, 'uploadImagesToLastProgress'])->name('physical_epc_progress.upload_images');
            Route::post('physical-boq-progress/upload-images', [PhysicalBoqProgressController::class, 'uploadImagesToLastProgressBoq'])->name('physical_boq_progress.upload_images.boq');

            Route::apiResource('project-subproject-links', ProjectSubprojectLinkController::class);

            Route::delete('/media/delete/{id}', [MediaFileController::class, 'deleteMedia'])->name('media.delete');

            Route::get('contracts/{contract}/subprojects/{subProject}', [ReportController::class, 'showSubProject'])->name('contracts.subprojects.show');

            Route::delete('media/destroy/{id}', [MediaFileController::class, 'destroy'])->name('media.destroy');

            Route::post('/chat/create', [ChatController::class, 'create'])->name('chat.create');

            Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
            Route::get('/chat/{id}', [ChatController::class, 'show'])->name('chat.show');
            Route::resource('already_defined_work_progress', AlreadyDefinedWorkProgressController::class);

            // Work Progress Data (CRUD)
            Route::resource('work_progress_data', WorkProgressDataController::class);

            // Quick update (AJAX/inline)
            Route::post('work_progress_data/{workProgressData}/quick-update', [WorkProgressDataController::class, 'quickUpdate'])->name('work_progress_data.quick-update');
            Route::post('grievances/{grievance}/status', [GrievanceController::class, 'updateStatus'])->name('grievances.updateStatus');
            Route::get('/social-safeguard/dynamic-report', [SocialSafeguardEntryController::class, 'dynamicComplianceReport'])->name('social-safeguard.dynamic-report');
            Route::get('/reports/sub-package-projects', [ReportController::class, 'subPackageProjectsSummaryReport'])->name('reports.sub-package-projects');

            Route::resource('grievance-complaint-nature', GrievanceComplaintNatureController::class);

            Route::resource('safeguard-global', SafeguardGlobalFunctionController::class);

            Route::resource('grievance_details', App\Http\Controllers\Admin\GrievanceComplaintDetailController::class);

            Route::get('/social-safeguard-summary', [ReportController::class, 'socialSafeguardSummaryReport'])->name('reports.social-safeguard-summary');
            Route::get('{project_id}/{compliance_id}/{phase_id}/report-summary', [SocialSafeguardEntryController::class, 'reportSummary'])->name('report.summary');
            Route::get('/safeguard/{project_id}/{compliance_id}/{phase_id?}/report-summary', [SocialSafeguardEntryController::class, 'indexReport'])->name('report.indexReport');

            Route::resource('role_dashboards', RoleDashboardController::class);

            Route::get('/packages-summary', [ReportController::class, 'packagesSummaryReport'])->name('reports.packages-summary');
            Route::get('/sub-projects/{subProjectId}/documents', [PackageProjectDocumentController::class, 'subProjectDocuments'])->name('sub-projects.documents');
            Route::get('/package-projects/{id}/documents', [PackageProjectDocumentController::class, 'index'])->name('package-projects.documents');
            Route::get('package-project-assignments/report', [PackageProjectAssignmentController::class, 'assignmentTree'])->name('package-project-assignments.report');

            Route::get('user-safeguard-subpackage/tree', [UserSafeguardSubpackageController::class, 'assignmentTree'])->name('user-safeguard-subpackage.tree');

            Route::get('/social-safeguard/gallery', [SocialSafeguardEntryController::class, 'gallery'])->name('social_safeguard.gallery');

            // Test Reports
            Route::delete('user-safeguard-subpackage/bulk-destroy', [UserSafeguardSubpackageController::class, 'bulkDestroy'])->name('user-safeguard-subpackage.bulk-destroy');
            Route::resource('user-safeguard-subpackage', UserSafeguardSubpackageController::class);

            Route::delete('/media/social/{id}', [SocialSafeguardEntryController::class, 'destroyMedia'])->name('social.media.destroy');

            Route::get('/contracts/{contract}/history', [ContractController::class, 'history'])->name('contracts.history');

            Route::get('sub_package_project_test_reports/{testId}', [SubPackageProjectTestReportController::class, 'index'])->name('sub_package_project_test_reports.index');

            Route::post('sub_package_project_test_reports', [SubPackageProjectTestReportController::class, 'store'])->name('sub_package_project_test_reports.store');

            Route::get('sub_package_project_test_reports/{report}/edit', [SubPackageProjectTestReportController::class, 'edit'])->name('sub_package_project_test_reports.edit');

            Route::post('sub_package_project_test_reports/{report}', [SubPackageProjectTestReportController::class, 'update'])->name('sub_package_project_test_reports.update');

            Route::delete('sub_package_project_test_reports/{report}', [SubPackageProjectTestReportController::class, 'destroy'])->name('sub_package_project_test_reports.destroy');

            Route::get('sub_package_project_test_reports/restore/{id}', [SubPackageProjectTestReportController::class, 'restore'])->name('sub_package_project_test_reports.restore');

            Route::resource('sub_package_project_test_types', SubPackageProjectTestTypeController::class)->except(['create', 'edit', 'show']);
            Route::get('sub_package_project_tests/{subPackageProject}', [SubPackageProjectTestController::class, 'index'])->name('sub_package_project_tests.index');
            Route::post('sub_package_project_tests', [SubPackageProjectTestController::class, 'store'])->name('sub_package_project_tests.store');
            Route::get('sub_package_project_tests/{subPackageProjectTest}/edit', [SubPackageProjectTestController::class, 'edit'])->name('sub_package_project_tests.edit');
            Route::put('sub_package_project_tests/{subPackageProjectTest}', [SubPackageProjectTestController::class, 'update'])->name('sub_package_project_tests.update');
            Route::delete('sub_package_project_tests/{subPackageProjectTest}', [SubPackageProjectTestController::class, 'destroy'])->name('sub_package_project_tests.destroy');
            Route::post('sub_package_project_tests/{id}/restore', [SubPackageProjectTestController::class, 'restore'])->name('sub_package_project_tests.restore');

            Route::prefix('contracts/{contract}')
                ->name('contracts.')
                ->group(function () {
                    Route::resource('securities', ContractSecurityController::class);
                });
            Route::get('contract-register', [ReportController::class, 'contractRegisterReport'])->name('contract-register');
            Route::resource('contract-security-types', ContractSecurityTypeController::class);
            Route::resource('contract-security-forms', ContractSecurityFormController::class);

            Route::get('reports/subprojects', [ReportController::class, 'subProjectsReport'])->name('reports.subprojects');
            Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
            Route::get('reports/{id}', [ReportController::class, 'show'])->name('reports.show');

            Route::post('/social-safeguard-entries/save', [SocialSafeguardEntryController::class, 'save'])->name('social_safeguard_entries.save');

            Route::resource('news', AdminNewsController::class);
            Route::resource('tenders', AdminTenderController::class);

            // Admin routes (with middleware protection)
            Route::resource('feedback', AdminFeedback::class)->only(['index', 'show', 'destroy']);

            Route::resource('slides', SlideController::class);

            Route::resource('leaders', \App\Http\Controllers\Admin\LeaderController::class);
            Route::resource('videos', \App\Http\Controllers\Admin\VideoController::class);

            Route::get('update-progress', [FinancialProgressUpdateController::class, 'index2'])->name('financial-progress-updates.index2');
            Route::resource('grievances', GrievanceController::class);
            Route::get('social-safeguard-entries/{project_id}/{compliance_id}/report-details', [SocialSafeguardEntryController::class, 'reportDetails'])->name('social_safeguard_entries.report_details');

            // permanent delete
            Route::delete('social-safeguard-entries/{id}', [SocialSafeguardEntryController::class, 'destroy'])->name('social_safeguard_entries.destroy');
            Route::get('social-safeguard-entries/{project_id}/{compliance_id}/report', [SocialSafeguardEntryController::class, 'report'])->name('social_safeguard_entries.report');
            // Show by grievance_no instead of ID
            Route::get('/grievances/{grievance_no}', [GrievanceController::class, 'show'])->name('grievances.show');

            // Logs
            Route::post('/grievances/{grievance_id}/logs', [GrievanceController::class, 'storeLog'])->name('grievances.logs.store');
            Route::put('/grievance-logs/{id}', [GrievanceController::class, 'updateLog'])->name('grievances.logs.update');
            Route::delete('/grievance-logs/{id}', [GrievanceController::class, 'destroyLog'])->name('grievances.logs.destroy');

            // Assignments
            Route::post('/grievances/{grievance_id}/assignments', [GrievanceController::class, 'storeAssignment'])->name('grievances.assignments.store');
            Route::put('/grievance-assignments/{id}', [GrievanceController::class, 'updateAssignment'])->name('grievances.assignments.update');
            Route::delete('/grievance-assignments/{id}', [GrievanceController::class, 'destroyAssignment'])->name('grievances.assignments.destroy');
            Route::resource('sub-departments', SubDepartmentController::class);

            Route::resource('package-project-assignments', PackageProjectAssignmentController::class);

            Route::resource('role_routes', RoleRouteController::class);

            Route::resource('type-of-procurements', TypeOfProcurementController::class);

            // Route for restoring soft deleted items
            Route::post('type-of-procurements/{id}/restore', [TypeOfProcurementController::class, 'restore'])->name('type-of-procurements.restore');

            Route::resource('package-components', PackageComponentController::class);

            // routes/web.php
            Route::get('/pages', [PageController::class, 'listPages'])->name('pages.list');
            Route::get('/pages/create', [PageController::class, 'showCreateForm'])->name('pages.create.form');
            Route::post('/pages/create', [PageController::class, 'createPage'])->name('pages.create');
            Route::get('/pages/edit/{id}', [PageController::class, 'showEditForm'])->name('pages.edit.form');
            Route::put('/pages/edit/{id}', [PageController::class, 'updatePage'])->name('pages.update');
            Route::post('/pages/delete/{id}', [PageController::class, 'deletePage'])->name('pages.delete');

            Route::resource('media', MediaFileController::class)->except(['create', 'edit', 'destroy']);
            Route::post('/media-files/upload', [MediaFileController::class, 'upload'])->name('media_files.upload');

            Route::post('/financial-progress-upload', [FinancialProgressUpdateController::class, 'uploadMedia'])->name('financial-progress.upload');

            Route::resource('navbar-items', NavbarItemController::class);
            Route::post('navbar-items/update-order', [NavbarItemController::class, 'updateOrder'])->name('navbar-items.update-order');

            Route::resource('financial-progress-updates', FinancialProgressUpdateController::class);

            Route::get('social-safeguard-entries/{project_id}/{compliance_id}/{phase_id?}', [SocialSafeguardEntryController::class, 'index'])->name('social_safeguard_entries.index');
            Route::get('/social-safeguard-entries-all', [SocialSafeguardEntryController::class, 'subPackageProjectOverview'])->name('social_safeguard_entries.overview');

            Route::post('/social-safeguard-entries/store-or-update', [SocialSafeguardEntryController::class, 'storeOrUpdateFromIndex'])->name('social_safeguard_entries.storeOrUpdateFromIndex');

            Route::get('/package-projects/by-department/{department}', [\App\Http\Controllers\Admin\PackageProjectAssignmentController::class, 'getProjectsByDepartment'])->name('package-projects.by-department');

            // alias for save()
            Route::post('/social-safeguard-entries/save', [SocialSafeguardEntryController::class, 'save'])->name('social_safeguard_entries.save');
            Route::get('physical_boq_progress', [PhysicalBoqProgressController::class, 'index'])->name('physical_boq_progress.index');
            Route::get('physical_boq_progress_get', [PhysicalBoqProgressController::class, 'physicalProgress'])->name('boqentry.physical-progress');
            Route::post('physical_boq_update', [PhysicalBoqProgressController::class, 'saveProgress'])->name('boqentry.save-physical-progress');
            Route::get('physical_boq_progress/create', [PhysicalBoqProgressController::class, 'create'])->name('physical_boq_progress.create');

            Route::post('physical_boq_progress', [PhysicalBoqProgressController::class, 'store'])->name('physical_boq_progress.store');

            Route::get('physical_boq_progress/{physicalBoqProgress}/edit', [PhysicalBoqProgressController::class, 'edit'])->name('physical_boq_progress.edit');

            Route::put('physical_boq_progress/{physicalBoqProgress}', [PhysicalBoqProgressController::class, 'update'])->name('physical_boq_progress.update');

            Route::delete('physical_boq_progress/{physicalBoqProgress}', [PhysicalBoqProgressController::class, 'destroy'])->name('physical_boq_progress.destroy');

            // Bulk delete route
            Route::delete('physical_boq_progress/bulk-delete', [PhysicalBoqProgressController::class, 'bulkDestroy'])->name('physical_boq_progress.bulk-delete');

            Route::delete('physical-epc-progress/bulk-destroy', [PhysicalEpcProgressController::class, 'bulkDestroy'])->name('physical_epc_progress.bulkDestroy');

            Route::resource('physical_epc_progress', PhysicalEpcProgressController::class);

            Route::resource('already_define_epc', AlreadyDefineEpcController::class);
            Route::post('epcentry_data/store-from-defined', [EpcEntryDataController::class, 'storeFromDefined'])->name('epcentry_data.storeFromDefined');

            Route::resource('work_services', WorkServiceController::class);
            Route::get('/procurement-work-programs/{package_project_id}/edit-by-package/{procurement_details_id}', [ProcurementWorkProgramController::class, 'editByPackage'])->name('procurement-work-programs.edit-by-package');
            Route::post('/procurement-work-programs/{package_project_id}/{procurement_details_id}/upload-documents', [ProcurementWorkProgramController::class, 'uploadDocumentsAndUpdate'])->name('procurement-work-programs.upload-documents');
            Route::resource('procurement-work-programs', ProcurementWorkProgramController::class);
            Route::get('procurement-work-programs/{package_project_id}/{procurement_details_id}', [ProcurementWorkProgramController::class, 'show'])->name('procurement-work-programs.show.pack');
            Route::post('/procurement-work-programs/store-single', [ProcurementWorkProgramController::class, 'storeSingle'])->name('procurement-work-programs.store-single');
            Route::put('/procurement-work-programs/update-single/{id}', [ProcurementWorkProgramController::class, 'updateSingle'])->name('procurement-work-programs.update-single');
            Route::resource('contractors', ContractorController::class);
            Route::resource('contracts', ContractController::class);
            Route::prefix('package-projects/{packageProject}/procurement-details')->group(function () {
                Route::get('create', [ProcurementDetailController::class, 'create'])->name('procurement-details.create');
                Route::post('/', [ProcurementDetailController::class, 'store'])->name('procurement-details.store');
            });

            Route::get('media-gallery', [MediaFileController::class, 'gallery'])->name('media.gallery');
            Route::get('media-files', [MediaFileController::class, 'index'])->name('media.index');
            Route::get('/physical-epc-report', [PhysicalEpcProgressController::class, 'index3'])->name('physicalprogress.index3');
            Route::get('/safeguard-entries-all', [SafeguardEntryController::class, 'index2'])->name('safeguard_entries.index2');
            Route::resource('procurement-details', ProcurementDetailController::class)->except(['create', 'store']);
            Route::post('safeguard_entries/import', [SafeguardEntryController::class, 'import'])->name('safeguard_entries.import');
            Route::post('safeguard_entries/bulk-delete-entry', [SafeguardEntryController::class, 'bulkDelete'])->name('safeguard_entries.bulkDelete.entry');
            Route::resource('safeguard_entries', SafeguardEntryController::class);
            Route::prefix('boqentry')
                ->name('boqentry.')
                ->group(function () {
                    Route::get('/', [BoqentryDataController::class, 'index'])->name('index');
                    Route::post('/upload', [BoqentryDataController::class, 'uploadExcel'])->name('upload');
                    Route::get('/create', [BoqentryDataController::class, 'create'])->name('create');
                    Route::post('/', [BoqentryDataController::class, 'store'])->name('store');
                    Route::get('/{id}/edit', [BoqentryDataController::class, 'edit'])->name('edit');
                    Route::put('/{id}', [BoqentryDataController::class, 'update'])->name('update');
                    Route::delete('/bulk-delete', [BoqentryDataController::class, 'bulkDestroy'])->name('bulk-delete');
                    Route::delete('/{id}', [BoqentryDataController::class, 'destroy'])->name('destroy');
                });
            Route::resource('contraction-phases', ContractionPhaseController::class);
            Route::resource('safeguard-compliances', SafeguardComplianceController::class);
            Route::prefix('epcentry_data')
                ->name('epcentry_data.')
                ->group(function () {
                    Route::get('/', [EpcEntryDataController::class, 'index'])->name('index');
                    Route::get('/create', [EpcEntryDataController::class, 'create'])->name('create');
                    Route::post('/', [EpcEntryDataController::class, 'store'])->name('store');
                    Route::get('/{id}/edit', [EpcEntryDataController::class, 'edit'])->name('edit');
                    Route::put('/{id}', [EpcEntryDataController::class, 'update'])->name('update');
                    Route::delete('/bulk-destroy', [EpcEntryDataController::class, 'bulkDestroy'])->name('bulkDestroy');
                    Route::delete('/{id}', [EpcEntryDataController::class, 'destroy'])->name('destroy');
                });
            Route::resource('users', UserController::class);
            Route::resource('roles', RoleController::class);
            Route::resource('departments', DepartmentController::class);
            Route::resource('designations', DesignationController::class);
            Route::resource('project', ProjectController::class);
            Route::resource('projects-category', ProjectsCategoryController::class);
            Route::resource('package-projects', PackageProjectController::class);
        });
    Route::get('admin/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Dashboard main page
});
