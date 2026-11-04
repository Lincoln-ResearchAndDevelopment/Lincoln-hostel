<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

/**
 * Works out which campus the current request is allowed to see.
 *
 * restriction() returns:
 *   - null  => unrestricted (public pages, console, queue, super admins)
 *   - int   => restricted to that campus id
 *   - 0     => restricted to nothing (fails closed: admin without a campus)
 *
 * The result drives the BelongsToCampus global scope, so every Eloquent query
 * and every route-model binding is campus-filtered without touching call sites.
 */
class CampusContext
{
    /** Re-entrancy guard: resolving a guard user runs an Eloquent query. */
    private static bool $resolving = false;

    public static function restriction(): ?int
    {
        if (self::$resolving || app()->runningInConsole() && !app()->runningUnitTests()) {
            return null;
        }

        $request = request();
        if (!$request) {
            return null;
        }

        self::$resolving = true;
        try {
            // Student portal: restricted to the logged-in student's campus.
            if ($request->is('student', 'student/*')) {
                $student = Auth::guard('student')->user();
                return $student ? (int) ($student->campus_id ?? 0) : null;
            }

            // Super admin area: sees every campus.
            if ($request->is('superadmin', 'superadmin/*')) {
                return null;
            }

            // Everything else: the campus admin (web guard), or a master super admin.
            $admin = Auth::guard('web')->user();
            if ($admin && method_exists($admin, 'isAdmin') && $admin->isAdmin()) {
                return (int) ($admin->campus_id ?? 0);
            }

            // Master super admin on the admin pages: unrestricted, unless they chose
            // a campus to work in (see CampusController::switch).
            if (Auth::guard('superadmin')->check()) {
                $active = session('active_campus_id');

                return $active ? (int) $active : null;
            }

            return null;
        } finally {
            self::$resolving = false;
        }
    }

    /**
     * Campus id to stamp on records created in the current request, or null.
     */
    public static function currentCampusId(): ?int
    {
        $id = self::restriction();

        return $id ? $id : null;
    }

    /**
     * Validation rule: the id must exist AND (for a campus-restricted user) belong
     * to the same campus. Laravel's plain `exists:` rule reads the raw table and
     * ignores the Eloquent campus scope, so without this a campus admin could
     * submit another campus's hostel / room / bed / student id.
     */
    public static function exists(string $table, string $column = 'id'): Exists
    {
        $rule = Rule::exists($table, $column);
        $campusId = self::restriction();

        return $campusId === null ? $rule : $rule->where('campus_id', $campusId);
    }
}
