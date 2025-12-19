<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

class ActivityLogObserver
{
    protected function log(Model $model, string $action)
    {
        // Avoid recursive logging of ActivityLog itself
        if ($model instanceof ActivityLog) {
            return;
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'action' => $action,
            'changes' => $this->getChangesData($model, $action),
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'url' => request()->fullUrl(),
        ]);
    }

    protected function getChangesData(Model $model, string $action)
    {
        if ($action === 'updated') {
            return [
                'old' => array_intersect_key($model->getOriginal(), $model->getChanges()),
                'new' => $model->getChanges(),
            ];
        }

        if ($action === 'created') {
            return ['new' => $model->getAttributes()];
        }

        if ($action === 'deleted') {
            return ['old' => $model->getOriginal()];
        }

        return null;
    }

    public function created(Model $model)  { $this->log($model, 'created'); }
    public function updated(Model $model)  { $this->log($model, 'updated'); }
    public function deleted(Model $model)  { $this->log($model, 'deleted'); }
    public function restored(Model $model) { $this->log($model, 'restored'); }
}
