<?php

namespace App\Models\Concerns;

use App\Models\Campus;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\Student;
use App\Support\CampusContext;
use Illuminate\Database\Eloquent\Builder;

/**
 * Makes a model campus-aware.
 *
 *  - A global scope limits queries (and route-model bindings) to the campus of
 *    the logged-in admin / student. Super admins and public pages are unrestricted.
 *  - campus_id is filled automatically on create, from the related student,
 *    room or hostel when there is one, otherwise from the current admin.
 *
 * Models can set `protected bool $campusNullIsShared = true;` for records where
 * a NULL campus_id means "visible to every campus" (announcements, rules).
 */
trait BelongsToCampus
{
    public static function bootBelongsToCampus(): void
    {
        static::addGlobalScope('campus', function (Builder $builder) {
            $campusId = CampusContext::restriction();
            if ($campusId === null) {
                return;
            }

            $model = $builder->getModel();
            $column = $model->getTable() . '.campus_id';

            if ($model->campusNullIsShared ?? false) {
                $builder->where(function (Builder $q) use ($column, $campusId) {
                    $q->where($column, $campusId)->orWhereNull($column);
                });
            } else {
                $builder->where($column, $campusId);
            }
        });

        static::creating(function ($model) {
            if (empty($model->campus_id)) {
                $model->campus_id = $model->resolveCampusIdForCreate();
            }
        });
    }

    public function initializeBelongsToCampus(): void
    {
        if (!in_array('campus_id', $this->fillable, true)) {
            $this->fillable[] = 'campus_id';
        }
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    /**
     * Prefer the campus of the record this one hangs off; fall back to the admin's.
     */
    protected function resolveCampusIdForCreate(): ?int
    {
        $attrs = $this->getAttributes();

        if (!empty($attrs['student_id']) && static::class !== Student::class) {
            $id = Student::withoutGlobalScopes()->whereKey($attrs['student_id'])->value('campus_id');
            if ($id) {
                return (int) $id;
            }
        }
        if (!empty($attrs['room_id']) && static::class !== Room::class) {
            $id = Room::withoutGlobalScopes()->whereKey($attrs['room_id'])->value('campus_id');
            if ($id) {
                return (int) $id;
            }
        }
        if (!empty($attrs['hostel_id']) && static::class !== Hostel::class) {
            $id = Hostel::withoutGlobalScopes()->whereKey($attrs['hostel_id'])->value('campus_id');
            if ($id) {
                return (int) $id;
            }
        }

        return CampusContext::currentCampusId();
    }
}
