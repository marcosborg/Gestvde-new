<?php

declare(strict_types=1);

namespace App\Filament\Resources\Concerns;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Contracts\Auth\Authenticatable;

trait HasShieldPermissions
{
    protected static function hasShieldPermission(string $action): bool
    {
        $user = auth()->user();

        if (! ($user instanceof Authenticatable)) {
            return false;
        }

        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return true;
        }

        $permissions = FilamentShield::getResourcePolicyActionsWithPermissions(static::class);
        $permission = $permissions[$action] ?? null;

        return $permission ? $user->can($permission) : false;
    }

    public static function canViewAny(): bool
    {
        return static::hasShieldPermission('viewAny');
    }

    public static function canView($record): bool
    {
        return static::hasShieldPermission('view');
    }

    public static function canCreate(): bool
    {
        return static::hasShieldPermission('create');
    }

    public static function canEdit($record): bool
    {
        return static::hasShieldPermission('update');
    }

    public static function canDelete($record): bool
    {
        return static::hasShieldPermission('delete');
    }

    public static function canDeleteAny(): bool
    {
        return static::hasShieldPermission('deleteAny');
    }

    public static function canForceDelete($record): bool
    {
        return static::hasShieldPermission('forceDelete');
    }

    public static function canForceDeleteAny(): bool
    {
        return static::hasShieldPermission('forceDeleteAny');
    }

    public static function canRestore($record): bool
    {
        return static::hasShieldPermission('restore');
    }

    public static function canRestoreAny(): bool
    {
        return static::hasShieldPermission('restoreAny');
    }

    public static function canReplicate($record): bool
    {
        return static::hasShieldPermission('replicate');
    }

    public static function canReorder(): bool
    {
        return static::hasShieldPermission('reorder');
    }
}
