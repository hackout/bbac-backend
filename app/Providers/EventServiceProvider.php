<?php

namespace App\Providers;

use App\Models\Part;
use App\Models\User;
use App\Models\Role;
use App\Models\Notice;
use App\Models\Torque;
use App\Models\WorkItem;
use App\Models\TaskItem;
use App\Models\Training;
use App\Models\Document;
use App\Models\Department;
use App\Models\DocumentLog;
use App\Models\IssueInline;
use App\Models\IssueProduct;
use App\Models\IssueVehicle;
use App\Models\CommitInline;
use App\Models\BirthdayCard;
use App\Models\CommitProduct;
use App\Models\CommitVehicle;
use App\Models\CommitApprove;
use App\Events\TorqueChanged;
use App\Events\PushTaskToUser;
use App\Models\TorqueChangeRecord;
use App\Events\CommitApproveSuccess;
use App\Listeners\TorqueChangedListener;
use App\Listeners\CommitApproveSuccessListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        TorqueChanged::class => [
            TorqueChangedListener::class
        ],
        CommitApproveSuccess::class => [
            CommitApproveSuccessListener::class
        ],
        PushTaskToUser::class => [
            PushTaskToUserListener::class
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
        User::observe('App\Observers\UserObserver');
        Role::observe('App\Observers\RoleObserver');
        Part::observe('App\Observers\PartObserver');
        Notice::observe('App\Observers\NoticeObserver');
        Torque::observe('App\Observers\TorqueObserver');
        Document::observe('App\Observers\DocumentObserver');
        WorkItem::observe('App\Observers\WorkItemObserver');
        TaskItem::observe('App\Observers\TaskItemObserver');
        Training::observe('App\Observers\TrainingObserver');
        Department::observe('App\Observers\DepartmentObserver');
        DocumentLog::observe('App\Observers\DocumentLogObserver');
        BirthdayCard::observe('App\Observers\BirthdayCardObserver');
        IssueInline::observe('App\Observers\IssueInlineObserver');
        IssueProduct::observe('App\Observers\IssueProductObserver');
        IssueVehicle::observe('App\Observers\IssueVehicleObserver');
        CommitInline::observe('App\Observers\CommitInlineObserver');
        CommitProduct::observe('App\Observers\CommitProductObserver');
        CommitVehicle::observe('App\Observers\CommitVehicleObserver');
        CommitApprove::observe('App\Observers\CommitApproveObserver');
        TorqueChangeRecord::observe('App\Observers\TorqueChangeRecordObserver');
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
