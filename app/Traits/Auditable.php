<?php

namespace App\Traits;

use App\Jobs\LogAuditEventJob;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            $model->audit('created');
        });

        static::updated(function ($model) {
            $model->audit('updated');
        });

        static::deleted(function ($model) {
            $model->audit('deleted');
        });
    }

    protected function audit($event)
    {
        $payload = null;

        if ($event === 'updated') {
            // Salva apenas o DIFF (o que mudou)
            $newValues = $this->getDirty();
            $oldValues = [];

            foreach ($newValues as $attribute => $value) {
                $oldValues[$attribute] = $this->getOriginal($attribute);
            }

            $payload = [
                'old' => $oldValues,
                'new' => $newValues
            ];
        } elseif ($event === 'created') {
            $payload = ['new' => $this->getAttributes()];
        } elseif ($event === 'deleted') {
            $payload = ['old' => $this->getOriginal()];
        }

        // Despacha para a fila para não travar o usuário
        LogAuditEventJob::dispatch([
            'auditable_id' => $this->id,
            'auditable_type' => get_class($this),
            'user_id' => auth()->id(),
            'event_type' => $event,
            'payload' => $payload,
            'url' => Request::fullUrl(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
