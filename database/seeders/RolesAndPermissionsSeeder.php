<?php

namespace Database\Seeders;

use App\Filament\Resources\Companies\CompanyResource;
use App\Filament\Resources\Drivers\DriverResource;
use App\Filament\Resources\DriverVehicleAssignments\DriverVehicleAssignmentResource;
use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\Maintenances\MaintenanceResource;
use App\Filament\Resources\SupplierCategories\SupplierCategoryResource;
use App\Filament\Resources\Suppliers\SupplierResource;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Resources\VehicleCheckins\VehicleCheckinResource;
use App\Filament\Resources\VehicleExpenses\VehicleExpenseResource;
use App\Filament\Resources\VehicleRentals\VehicleRentalResource;
use App\Filament\Resources\Vehicles\VehicleResource;
use App\Filament\Resources\VehicleSupplierContracts\VehicleSupplierContractResource;
use App\Models\User;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource;
use Filament\Facades\Filament;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Seed roles and permissions for the Filament admin panel.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $panel = Filament::getPanel('admin');
        if ($panel) {
            Filament::setCurrentPanel($panel);
        }

        $allResources = [
            CompanyResource::class,
            DriverResource::class,
            DriverVehicleAssignmentResource::class,
            EventResource::class,
            MaintenanceResource::class,
            SupplierCategoryResource::class,
            SupplierResource::class,
            UserResource::class,
            VehicleCheckinResource::class,
            VehicleExpenseResource::class,
            VehicleRentalResource::class,
            VehicleResource::class,
            VehicleSupplierContractResource::class,
            RoleResource::class,
        ];

        $fleetResources = [
            VehicleResource::class,
            DriverResource::class,
            DriverVehicleAssignmentResource::class,
            VehicleRentalResource::class,
            VehicleCheckinResource::class,
            MaintenanceResource::class,
            EventResource::class,
        ];

        $financeResources = [
            VehicleExpenseResource::class,
            SupplierResource::class,
            SupplierCategoryResource::class,
            VehicleSupplierContractResource::class,
        ];

        $nonAdminViewResources = array_values(array_diff($allResources, [
            CompanyResource::class,
            UserResource::class,
            RoleResource::class,
        ]));

        $viewActions = ['viewAny', 'view'];
        $manageActions = [
            'viewAny',
            'view',
            'create',
            'update',
            'delete',
            'deleteAny',
            'restore',
            'restoreAny',
            'forceDelete',
            'forceDeleteAny',
            'replicate',
            'reorder',
        ];

        $allResourcePermissions = $this->allResourcePermissions($allResources);
        $pageAndWidgetPermissions = $this->pageAndWidgetPermissions();

        foreach (array_unique(array_merge($allResourcePermissions, $pageAndWidgetPermissions)) as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => config('auth.defaults.guard'),
            ]);
        }

        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => config('auth.defaults.guard'),
        ]);

        $fleetRole = Role::firstOrCreate([
            'name' => 'fleet_manager',
            'guard_name' => config('auth.defaults.guard'),
        ]);

        $financeRole = Role::firstOrCreate([
            'name' => 'finance',
            'guard_name' => config('auth.defaults.guard'),
        ]);

        $readonlyRole = Role::firstOrCreate([
            'name' => 'readonly',
            'guard_name' => config('auth.defaults.guard'),
        ]);

        $adminRole->syncPermissions(array_unique(array_merge(
            $allResourcePermissions,
            $pageAndWidgetPermissions,
        )));

        $fleetRole->syncPermissions(array_unique(array_merge(
            $this->resourcePermissionsForActions($fleetResources, $manageActions),
            $this->resourcePermissionsForActions($nonAdminViewResources, $viewActions),
            $pageAndWidgetPermissions,
        )));

        $financeRole->syncPermissions(array_unique(array_merge(
            $this->resourcePermissionsForActions($financeResources, $manageActions),
            $this->resourcePermissionsForActions($nonAdminViewResources, $viewActions),
            $pageAndWidgetPermissions,
        )));

        $readonlyRole->syncPermissions(array_unique(array_merge(
            $this->resourcePermissionsForActions($nonAdminViewResources, $viewActions),
            $pageAndWidgetPermissions,
        )));

        if (User::query()->whereHas('roles')->doesntExist()) {
            $firstUser = User::query()->orderBy('id')->first();
            if ($firstUser) {
                $firstUser->assignRole($adminRole);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @param  array<class-string>  $resources
     * @return array<int, string>
     */
    private function allResourcePermissions(array $resources): array
    {
        return collect($resources)
            ->flatMap(fn (string $resource): array => FilamentShield::getResourcePermissions($resource))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  array<class-string>  $resources
     * @param  array<int, string>  $actions
     * @return array<int, string>
     */
    private function resourcePermissionsForActions(array $resources, array $actions): array
    {
        return collect($resources)
            ->flatMap(function (string $resource) use ($actions): array {
                $permissions = FilamentShield::getResourcePolicyActionsWithPermissions($resource);

                return collect($actions)
                    ->map(fn (string $action): ?string => $permissions[$action] ?? null)
                    ->filter()
                    ->all();
            })
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function pageAndWidgetPermissions(): array
    {
        $pages = collect(FilamentShield::getPages())
            ->flatMap(fn (array $page): array => array_keys($page['permissions'] ?? []));

        $widgets = collect(FilamentShield::getWidgets())
            ->flatMap(fn (array $widget): array => array_keys($widget['permissions'] ?? []));

        $custom = collect(FilamentShield::getCustomPermissions())
            ->keys();

        return $pages
            ->merge($widgets)
            ->merge($custom)
            ->unique()
            ->values()
            ->all();
    }
}
