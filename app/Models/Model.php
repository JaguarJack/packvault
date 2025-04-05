<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use DateTimeInterface;

class Model extends EloquentModel
{
    use softDeletes;

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' => 'date:Y-m-d H:i:s',
    ];

    /**
     * 重写 serializeDate
     */
    protected function serializeDate(DateTimeInterface|string $date): ?string
    {
        if (is_string($date)) {
            return $date;
        }

        return Carbon::instance($date)->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s');
    }
}
