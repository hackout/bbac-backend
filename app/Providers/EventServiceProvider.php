<?php

namespace App\Providers;

use App\Models\Document;
use App\Models\DocumentLog;
use App\Models\Notice;
use App\Models\Part;
use App\Models\User;
use App\Models\Role;
use App\Models\Commit;
use App\Models\Torque;
use App\Models\Examine;
use App\Models\WorkItem;
use App\Models\TaskItem;
use App\Models\Training;
use App\Models\CommitItem;
use App\Models\Department;
use App\Models\ExamineItem;
use App\Models\BirthdayCard;
use App\Models\CommitApprove;
use App\Events\TorqueChanged;
use App\Events\PushTaskToUser;
use App\Models\TorqueChangeRecord;
use App\Events\CommitApproveSuccess;
use Illuminate\Support\Facades\Event;
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
        Commit::observe('App\Observers\CommitObserver');
        Torque::observe('App\Observers\TorqueObserver');
        Examine::observe('App\Observers\ExamineObserver');
        Document::observe('App\Observers\DocumentObserver');
        WorkItem::observe('App\Observers\WorkItemObserver');
        TaskItem::observe('App\Observers\TaskItemObserver');
        Training::observe('App\Observers\TrainingObserver');
        CommitItem::observe('App\Observers\CommitItemObserver');
        Department::observe('App\Observers\DepartmentObserver');
        DocumentLog::observe('App\Observers\DocumentLogObserver');
        ExamineItem::observe('App\Observers\ExamineItemObserver');
        BirthdayCard::observe('App\Observers\BirthdayCardObserver');
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
