<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\WorkController;
use App\Http\Controllers\Backend\AuthController;
use App\Http\Controllers\Backend\TaskController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\DictController;
use App\Http\Controllers\Backend\HomeController;
use App\Http\Controllers\Backend\PlanController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\FileController;
use App\Http\Controllers\Backend\PartController;
use App\Http\Controllers\Backend\IssueController;
use App\Http\Controllers\Backend\NoticeController;
use App\Http\Controllers\Backend\CommitController;
use App\Http\Controllers\Backend\TorqueController;
use App\Http\Controllers\Backend\UploadController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\UserLogController;
use App\Http\Controllers\Backend\ExamineController;
use App\Http\Controllers\Backend\TrainingController;
use App\Http\Controllers\Backend\AssemblyController;
use App\Http\Controllers\Backend\DictItemController;
use App\Http\Controllers\Backend\TaskCronController;
use App\Http\Controllers\Backend\DocumentController;
use App\Http\Controllers\Backend\TorqueItemController;
use App\Http\Controllers\Backend\CommitItemController;
use App\Http\Controllers\Backend\DepartmentController;
use App\Http\Controllers\Backend\BirthdayCardController;
use App\Http\Controllers\Backend\SystemConfigController;
use App\Http\Controllers\Backend\CommitApproveController;
use App\Http\Controllers\Backend\LocalePackageController;
use App\Http\Controllers\Backend\CommitItemOptionController;
use App\Http\Controllers\Backend\TorqueChangeRecordController;


/**
 * ID正则
 */
define('ID_REGEX', '[0-9]+');

/**
 * slug正则
 */
define('SLUG_REGEX', '[0-9a-zA-Z\-_]+');

/**
 * training正则
 */
define('TRAINING_REGEX', 'safe|skill|multiple');

/**
 * UUID正则
 */
define('UUID_REGEX', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');


Route::get('/captcha', [AuthController::class, 'captcha'])->name('captcha');

Route::middleware("guest")->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'auth'])->name('login');
    Route::group(['prefix' => '/forget'], function () {
        Route::get('/', [AuthController::class, 'forget'])->name('forget');
        Route::post('/send', [AuthController::class, 'sendCode'])->name('forget.send');
        Route::post('/check', [AuthController::class, 'checkAccount'])->name('forget.check');
        Route::post('/reset', [AuthController::class, 'resetPassword'])->name('forget.reset');
    });
});

Route::middleware("auth")->group(function () {
    Route::get('/login/first', [AuthController::class, 'first'])->name('login.first');
    Route::post('/login/first', [AuthController::class, 'finish'])->name('login.first');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware("finish")->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('dashboard');

        Route::post('/check', [HomeController::class, 'checkPassword'])->name('check.password');
        Route::group(['prefix' => '/upload'], function () {
            Route::post('/image', [UploadController::class, 'image'])->name('uploadImage');
            Route::post('/video', [UploadController::class, 'video'])->name('uploadVideo');
            Route::post('/file', [UploadController::class, 'file'])->name('uploadFile');
        });

        //个人资料
        Route::group(['prefix' => '/profile'], function () {
            Route::get('/', [HomeController::class, 'profile'])->name('profile.index');
            Route::post('/', [HomeController::class, 'save'])->name('profile.index');
            Route::post('/account', [HomeController::class, 'account'])->name('profile.account');
            Route::post('/password', [HomeController::class, 'password'])->name('profile.password');
            Route::get('/log', [HomeController::class, 'log'])->name('profile.log');
            Route::get('/unread', [HomeController::class, 'unread'])->name('profile.unread');
            Route::post('/read/{id}', [HomeController::class, 'read'])->name('profile.read');
            Route::delete('/delete/{id}', [HomeController::class, 'delete'])->name('profile.delete');
            Route::post('/approve/{id}', [HomeController::class, 'approve'])->name('profile.approve');
            Route::get('/message', [HomeController::class, 'message'])->name('profile.message');
            Route::get('/message/{id}', [HomeController::class, 'messageDetail'])->name('profile.message_detail')->where('id', UUID_REGEX);
        });

        //角色
        Route::group(['prefix' => '/role'], function () {
            Route::get('/', [RoleController::class, 'index'])->name("role.index");
            Route::get('/list', [RoleController::class, 'list'])->name("role.list");
            Route::post('/', [RoleController::class, 'create'])->name("role.create");
            Route::post('/batch', [RoleController::class, 'batchDelete'])->name("role.batch_delete");
            Route::put('/{id}', [RoleController::class, 'update'])->name("role.update")->where('id', UUID_REGEX);
            Route::patch('/{id}', [RoleController::class, 'valid'])->name("role.valid")->where('id', UUID_REGEX);
        });

        //组织机构
        Route::group(['prefix' => '/department'], function () {
            Route::get('/', [DepartmentController::class, 'index'])->name("department.index");
            Route::get('/list', [DepartmentController::class, 'list'])->name("department.list");
            Route::post('/', [DepartmentController::class, 'create'])->name("department.create");
            Route::post('/import', [DepartmentController::class, 'import'])->name('department.import');
            Route::get('/template', [DepartmentController::class, 'template'])->name('department.template');
            Route::post('/batch', [DepartmentController::class, 'batchDelete'])->name("department.batch_delete");
            Route::put('/{id}', [DepartmentController::class, 'update'])->name("department.update")->where('id', UUID_REGEX);
        });

        //字典
        Route::group(['prefix' => '/dict'], function () {
            Route::get('/', [DictController::class, 'index'])->name("dict.index");
            Route::get('/list', [DictController::class, 'list'])->name("dict.list");
            Route::post('/', [DictController::class, 'create'])->name("dict.create");
            Route::get('/{code}', [DictController::class, 'option'])->name("dict.option");
            Route::post('/export', [DictController::class, 'export'])->name("dict.export");
            Route::put('/{id}', [DictController::class, 'update'])->name("dict.update")->where('id', ID_REGEX);
            Route::group(['prefix' => '/item'], function () {
                Route::get('/{code}', [DictItemController::class, 'list'])->name("dict_item.list")->where('code', SLUG_REGEX);
                Route::post('/{code}', [DictItemController::class, 'create'])->name("dict_item.create")->where('code', SLUG_REGEX);
                Route::put('/{code}/{id}', [DictItemController::class, 'update'])->name("dict_item.update")->where(['code' => SLUG_REGEX, 'id' => ID_REGEX]);
                Route::post('/{code}/delete', [DictItemController::class, 'batchDelete'])->name("dict_item.batch_delete")->where(['code' => SLUG_REGEX]);
            });
        });

        //系统参数
        Route::group(['prefix' => '/system'], function () {
            Route::get('/', [SystemConfigController::class, 'index'])->name('system_config.index');
            Route::post('/', [SystemConfigController::class, 'index'])->name('system_config.index');
        });

        //登录日志
        Route::group(['prefix' => '/log'], function () {
            Route::get('/', [UserLogController::class, 'index'])->name('user_log.index');
            Route::get('/list', [UserLogController::class, 'list'])->name('user_log.list');
        });

        //语言包
        Route::group(['prefix' => '/language'], function () {
            Route::get('/', [LocalePackageController::class, 'index'])->name("locale_package.index");
            Route::get('/list', [LocalePackageController::class, 'list'])->name("locale_package.list");
            Route::post('/', [LocalePackageController::class, 'create'])->name("locale_package.create");
            Route::post('/export', [LocalePackageController::class, 'export'])->name("locale_package.export");
            Route::post('/import', [LocalePackageController::class, 'import'])->name('locale_package.import');
            Route::get('/template', [LocalePackageController::class, 'template'])->name('locale_package.template');
            Route::put('/{id}', [LocalePackageController::class, 'update'])->name("locale_package.update")->where('id', ID_REGEX);
        });


        //员工信息
        Route::group(['prefix' => '/user'], function () {
            Route::get('/', [UserController::class, 'index'])->name('user.index');
            Route::get('/list', [UserController::class, 'list'])->name('user.list');
            Route::get('/department/{department_id}', [UserController::class, 'department'])->name('user.department')->where('department_id', UUID_REGEX);
            Route::post('/', [UserController::class, 'create'])->name('user.create');
            Route::post('/export', [UserController::class, 'export'])->name("user.export");
            Route::post('/import', [UserController::class, 'import'])->name('user.import');
            Route::get('/template', [UserController::class, 'template'])->name('user.template');
            Route::post('/delete', [UserController::class, 'batchDelete'])->name('user.batch_delete');
            Route::get('/{id}', [UserController::class, 'detail'])->name('user.detail')->where('id', UUID_REGEX);
            Route::put('/{id}', [UserController::class, 'update'])->name('user.update')->where('id', UUID_REGEX);
            Route::post('/{id}', [UserController::class, 'updateDetail'])->name('user.update_detail')->where('id', UUID_REGEX);
            Route::post('/{slug}', [UserController::class, 'patch'])->name('user.patch')->where(['slug' => 'invalid|valid|unlock|lock']);
            Route::group(['prefix' => '/birthday'], function () {
                Route::get('/', [UserController::class, 'birthday'])->name('user.birthday');
                Route::get('/list', [UserController::class, 'birthdayList'])->name('user.birthday_list');
                Route::post('/{id}', [UserController::class, 'birthdayUpdate'])->name('user.birthday_update')->where('id', UUID_REGEX);
            });
        });

        //生日卡
        Route::group(['prefix' => '/birthday_card'], function () {
            Route::get('/list', [BirthdayCardController::class, 'list'])->name('birthday_card.list');
            Route::post('/', [BirthdayCardController::class, 'create'])->name('birthday_card.create');
            Route::put('/{id}', [BirthdayCardController::class, 'update'])->name('birthday_card.update')->where('id', UUID_REGEX);
            Route::delete('/{id}', [BirthdayCardController::class, 'delete'])->name('birthday_card.delete')->where('id', UUID_REGEX);
        });

        //培训计划
        Route::group(['prefix' => '/training'], function () {
            Route::get('/safe', [TrainingController::class, 'safe'])->name('training.safe');
            Route::get('/skill', [TrainingController::class, 'skill'])->name('training.skill');
            Route::get('/multiple', [TrainingController::class, 'multiple'])->name('training.multiple');
            Route::get('/{type}/list', [TrainingController::class, 'list'])->name('training.list')->where('type', TRAINING_REGEX);
            Route::post('/{type}/import', [TrainingController::class, 'import'])->name("training.import")->where('type', TRAINING_REGEX);
            Route::post('/{type}/export', [TrainingController::class, 'export'])->name("training.export")->where('type', TRAINING_REGEX);
            Route::post('/{type}/patch', [TrainingController::class, 'patch'])->name("training.patch")->where(['type' => TRAINING_REGEX]);
            Route::get('/{type}/template', [TrainingController::class, 'template'])->name("training.template")->where('type', TRAINING_REGEX);
            Route::post('/{type}/delete', [TrainingController::class, 'batchDelete'])->name('training.batch_delete')->where('type', TRAINING_REGEX);
            Route::put('/{type}/{id}', [TrainingController::class, 'update'])->name('training.update')->where(['type' => TRAINING_REGEX, 'id' => UUID_REGEX]);
            Route::post('/{type}/{id}/upload', [TrainingController::class, 'upload'])->name('training.upload')->where(['type' => TRAINING_REGEX, 'id' => UUID_REGEX]);
            Route::delete('/{type}/{id}/delete/{file}', [TrainingController::class, 'fileDelete'])->name('training.file_delete')->where(['type' => TRAINING_REGEX, 'id' => UUID_REGEX, 'file' => UUID_REGEX]);
        });

        //知识库
        Route::group(['prefix' => '/file'], function () {
            Route::get('/', [FileController::class, 'index'])->name('file.index');
            Route::get('/list', [FileController::class, 'list'])->name('file.list');
            Route::post('/', [FileController::class, 'create'])->name('file.create');
            Route::post('/upload', [FileController::class, 'upload'])->name('file.upload');
            Route::post('/batch/delete', [FileController::class, 'batchDelete'])->name('file.batch_delete');
            Route::post('/batch/move', [FileController::class, 'batchMove'])->name('file.batch_move');
            Route::get('/{id}', [FileController::class, 'view'])->name('file.view')->where(['id' => UUID_REGEX]);
            Route::put('/{id}', [FileController::class, 'update'])->name('file.update')->where(['id' => UUID_REGEX]);
            Route::put('/{id}/move', [FileController::class, 'move'])->name('file.move')->where(['id' => UUID_REGEX]);
            Route::put('/{id}/delete', [FileController::class, 'delete'])->name('file.delete')->where(['id' => UUID_REGEX]);
            Route::get('/{id}/download', [FileController::class, 'download'])->name('file.download')->where(['id' => UUID_REGEX]);
        });

        //基础数据信息
        Route::group(['prefix' => '/assembly'], function () {
            Route::get('/', [AssemblyController::class, 'index'])->name('assembly.index');
            Route::get('/list', [AssemblyController::class, 'list'])->name('assembly.list');
            Route::get('/option', [AssemblyController::class, 'option'])->name('assembly.option');
            Route::post('/', [AssemblyController::class, 'create'])->name('assembly.create');
            Route::post('/export', [AssemblyController::class, 'export'])->name("assembly.export");
            Route::post('/import', [AssemblyController::class, 'import'])->name('assembly.import');
            Route::get('/template', [AssemblyController::class, 'template'])->name('assembly.template');
            Route::put('/{id}', [AssemblyController::class, 'update'])->name('assembly.update')->where(['id' => UUID_REGEX]);
            Route::delete('/{id}', [AssemblyController::class, 'delete'])->name('assembly.delete')->where(['id' => UUID_REGEX]);
        });

        //扭矩数据库
        Route::group(['prefix' => '/torque'], function () {
            Route::get('/', [TorqueController::class, 'index'])->name("torque.index");
            Route::get('/list', [TorqueController::class, 'list'])->name('torque.list');
            Route::post('/import', [TorqueController::class, 'import'])->name('torque.import');
            Route::get('/template', [TorqueController::class, 'template'])->name('torque.template');
            Route::put('/{id}', [TorqueController::class, 'update'])->name('torque.update')->where(['id' => UUID_REGEX]);
            Route::get('/changed/{id}', [TorqueChangeRecordController::class, 'list'])->name('torque_change_record.list')->where(['id' => UUID_REGEX]);
        });

        //问题追踪
        Route::group(['prefix' => '/issue'], function () {
            Route::get('/', [IssueController::class, 'inline'])->name("issue.inline");
            Route::get('/product', [IssueController::class, 'product'])->name("issue.product");
            Route::get('/service', [IssueController::class, 'service'])->name("issue.service");
            Route::get('/finish', [IssueController::class, 'finish'])->name("issue.finish");
            Route::post('/export', [IssueController::class, 'export'])->name("issue.export");
            Route::get('/detail', [IssueController::class, 'detail'])->name("issue.detail")->where(['id' => UUID_REGEX]);
            Route::get('/update', [IssueController::class, 'update'])->name("issue.update")->where(['id' => UUID_REGEX]);
            Route::get('/list', [IssueController::class, 'list'])->name('issue.list');
        });

        //排产计划
        Route::group(['prefix' => '/plan'], function () {
            Route::get('/', [PlanController::class, 'index'])->name("plan.index");
            Route::get('/list', [PlanController::class, 'list'])->name('plan.list');
            Route::post('/', [PlanController::class, 'create'])->name('plan.create');
            Route::post('/export', [PlanController::class, 'export'])->name("plan.export");
            Route::put('/{id}', [PlanController::class, 'update'])->name('plan.update')->where(['id' => UUID_REGEX]);
            Route::delete('/{id}', [PlanController::class, 'delete'])->name('plan.delete')->where(['id' => UUID_REGEX]);
        });

        //SPC
        Route::group(['prefix' => '/spc'], function () {
            Route::get('/', [TorqueItemController::class, 'index'])->name("torque_item.index");
            Route::get('/list', [TorqueItemController::class, 'list'])->name("torque_item.list");
            Route::post('/export', [TorqueItemController::class, 'export'])->name("torque_item.export");
            Route::put('/{id}', [TorqueItemController::class, 'update'])->name('torque_item.update')->where(['id' => UUID_REGEX]);
        });

        //考核定义
        Route::group(['prefix' => '/examine'], function () {
            Route::get('/', [ExamineController::class, 'index'])->name("examine.index");
            Route::get('/list', [ExamineController::class, 'list'])->name("examine.list");
            Route::get('/option', [ExamineController::class, 'option'])->name("examine.option");
            Route::post('/export', [ExamineController::class, 'export'])->name("examine.export");
            Route::get('/{id}', [ExamineController::class, 'detail'])->name('examine.detail')->where(['id' => UUID_REGEX]);
            Route::delete('/{id}', [ExamineController::class, 'delete'])->name('examine.delete')->where(['id' => UUID_REGEX]);
            Route::get('/inline', [CommitController::class, 'inline'])->name("commit.inline");
            Route::get('/product', [CommitController::class, 'product'])->name("commit.product");
            Route::get('/service', [CommitController::class, 'service'])->name("commit.service");
        });

        //考核历史版本
        Route::group(['prefix' => '/commit'], function () {
            Route::get('/option', [CommitController::class, 'option'])->name("commit.option");
            Route::get('/list/{type?}', [CommitController::class, 'list'])->name("commit.list")->where('type', 'inline|product|service');
            Route::post('/{type}', [CommitController::class, 'create'])->name('commit.create')->where('type', 'inline|product|service');
            Route::get('/{id}', [CommitController::class, 'detail'])->name('commit.detail')->where(['id' => UUID_REGEX]);
            Route::get('/{id}/change', [CommitController::class, 'changed'])->name('commit.changed')->where(['id' => UUID_REGEX]);
            Route::put('/{id}', [CommitController::class, 'update'])->name('commit.update')->where(['id' => UUID_REGEX]);
            Route::post('/{id}', [CommitController::class, 'approve'])->name('commit.approve')->where(['id' => UUID_REGEX]);
            Route::delete('/{id}', [CommitController::class, 'delete'])->name('commit.delete')->where(['id' => UUID_REGEX]);
            Route::post('/import/{type}', [CommitController::class, 'import'])->name('commit.import')->where('type', 'inline|product|service');
            Route::get('/template/{type}', [CommitController::class, 'template'])->name('commit.template')->where('type', 'inline|product|service');

            //送审批
            Route::post('/approve/{id}', [CommitApproveController::class, 'create'])->name('commit_approve.create')->where(['id' => UUID_REGEX]);

            //考核项
            Route::group(['prefix' => '/items'], function () {
                Route::get('/{id}', [CommitItemController::class, 'list'])->name('commit_item.list')->where(['id' => UUID_REGEX]);
                Route::post('/{id}', [CommitItemController::class, 'create'])->name('commit_item.create')->where(['id' => UUID_REGEX]);
                Route::post('/{id}/order', [CommitItemController::class, 'order'])->name('commit_item.order')->where(['id' => UUID_REGEX]);
                Route::post('/{id}/upload', [CommitItemController::class, 'upload'])->name('commit_item.upload')->where(['id' => UUID_REGEX]);
                Route::delete('/{id}/upload/{uuid}', [CommitItemController::class, 'uploadDelete'])->name('commit_item.upload_delete')->where(['id' => UUID_REGEX, 'uuid' => UUID_REGEX]);
                Route::put('/{id}/{item_id}', [CommitItemController::class, 'update'])->name('commit_item.update')->where(['id' => UUID_REGEX, 'item_id' => UUID_REGEX]);
                Route::delete('/{id}/{item_id}', [CommitItemController::class, 'delete'])->name('commit_item.delete')->where(['id' => UUID_REGEX, 'item_id' => UUID_REGEX]);

                //实际测量项
                Route::group(['prefix' => '/option'], function () {
                    Route::get('/{id}/{item_id}', [CommitItemOptionController::class, 'list'])->name('commit_item_option.list')->where(['id' => UUID_REGEX, 'item_id' => UUID_REGEX]);
                    Route::post('/{id}/{item_id}', [CommitItemOptionController::class, 'save'])->name('commit_item_option.save')->where(['id' => UUID_REGEX, 'item_id' => UUID_REGEX]);
                });
            });
        });

        //任务中心
        Route::group(['prefix' => '/task'], function () {
            Route::get('/', [TaskController::class, 'index'])->name("task.index");
            Route::get('/list', [TaskController::class, 'list'])->name('task.list');
            Route::get('/option', [TaskController::class, 'option'])->name('task.option');
            Route::post('/', [TaskController::class, 'create'])->name('task.create');
            Route::delete('/{id}', [TaskController::class, 'delete'])->name('task.delete')->where('id', UUID_REGEX);
            Route::group(['prefix' => '/cron'], function () {
                Route::get('/', [TaskCronController::class, 'index'])->name('task_cron.index');
                Route::get('/list', [TaskCronController::class, 'list'])->name('task_cron.list');
                Route::post('/', [TaskCronController::class, 'create'])->name('task_cron.create');
                Route::patch('/{id}/{status}', [TaskCronController::class, 'patch'])->name('task_cron.patch')->where(['id' => UUID_REGEX, 'status' => 'valid|invalid']);
                Route::delete('/{id}', [TaskCronController::class, 'delete'])->name('task_cron.delete')->where('id', UUID_REGEX);
            });
        });

        //任务分配
        Route::group(['prefix' => '/work'], function () {
            Route::get('/', [WorkController::class, 'index'])->name("work.index");
            Route::get('/list', [WorkController::class, 'list'])->name('work.list');
            Route::post('/', [WorkController::class, 'create'])->name('work.create');
        });

        //消息中心
        Route::group(['prefix' => '/message'], function () {
            Route::get('/', [NoticeController::class, 'index'])->name('notice.index');
            Route::get('/list', [NoticeController::class, 'list'])->name("notice.list");
            Route::post('/', [NoticeController::class, 'create'])->name("notice.create");
            Route::get('/{id}', [NoticeController::class, 'detail'])->name("notice.detail")->where("id", UUID_REGEX);
            Route::put('/{id}', [NoticeController::class, 'update'])->name("notice.update")->where("id", UUID_REGEX);
            Route::post('/delete', [NoticeController::class, 'batchDelete'])->name("notice.batch_delete");
            Route::post('/retract', [NoticeController::class, 'retract'])->name("notice.retract");
            Route::post('/push', [NoticeController::class, 'push'])->name("notice.push");
        });

        //指导书
        Route::group(['prefix' => '/document'], function () {
            Route::get('/overhaul', [DocumentController::class, 'overhaul'])->name('document.overhaul');
            Route::get('/assembling', [DocumentController::class, 'assembling'])->name('document.assembling');
            Route::get('/torque', [DocumentController::class, 'torque'])->name('document.torque');
            Route::get('/list/{type}', [DocumentController::class, 'list'])->name('document.list')->where('type', ID_REGEX);
            Route::post('/overhaul/{engine}', [DocumentController::class, 'overhaulUpdate'])->name('document.overhaul_update')->where('engine', ID_REGEX);
            Route::post('/assembling/{engine}', [DocumentController::class, 'assemblingUpdate'])->name('document.assembling_update')->where('engine', ID_REGEX);
            Route::post('/torque/{engine}', [DocumentController::class, 'torqueUpdate'])->name('document.torque_update')->where('engine', ID_REGEX);
            Route::delete('/{id}', [DocumentController::class, 'delete'])->name('document.delete')->where('id', UUID_REGEX);
        });

        //发动机清单
        Route::group(['prefix' => '/product'], function () {
            Route::get('/', [ProductController::class, 'index'])->name('product.index');
            Route::get('/list', [ProductController::class, 'list'])->name('product.list');
            Route::post('/', [ProductController::class, 'create'])->name('product.create');
            Route::put('/{id}', [ProductController::class, 'update'])->name('product.update')->where('id', UUID_REGEX);
            Route::delete('/{id}', [ProductController::class, 'delete'])->name('product.delete')->where('id', UUID_REGEX);
            Route::post('/export', [ProductController::class, 'export'])->name("product.export");
            Route::post('/import', [ProductController::class, 'import'])->name('product.import');
            Route::get('/template', [ProductController::class, 'template'])->name('product.template');
        });

        //零件清单
        Route::group(['prefix' => '/part'], function () {
            Route::get('/', [PartController::class, 'index'])->name('part.index');
            Route::get('/list', [PartController::class, 'list'])->name('part.list');
            Route::post('/', [PartController::class, 'create'])->name('part.create');
            Route::put('/{id}', [PartController::class, 'update'])->name('part.update')->where('id', UUID_REGEX);
            Route::delete('/{id}', [PartController::class, 'delete'])->name('part.delete')->where('id', UUID_REGEX);
            Route::get('/{id}', [PartController::class, 'item'])->name('part.item')->where('id', UUID_REGEX);
            Route::post('/import', [PartController::class, 'import'])->name('part.import');
            Route::get('/template', [PartController::class, 'template'])->name('part.template');
        });
    });
});