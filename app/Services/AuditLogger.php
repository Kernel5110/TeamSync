<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    public static function log($action, $model = null, $modelId = null, $details = null)
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model' => is_string($model) ? $model : ($model ? get_class($model) : null),
            'model_id' => $modelId,
            'details' => is_array($details) ? json_encode($details) : $details,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
