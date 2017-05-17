<?php

use App\Exceptions\DatabaseStateException;
use App\Model\Permission;
use App\Model\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // TODO: this pattern only matters for single-run seeds, extract it later if needed
        DB::transaction(function () {
            if (DB::table('seeds')
                ->where('seed', get_class($this))
                ->exists()
            ) {
                throw new DatabaseStateException('DatabaseSeeder has run already');
            }

            Model::unguard();
            DB::raw('TRUNCATE role_user');
            DB::raw('TRUNCATE permission_role');
            DB::raw('TRUNCATE permission');
            DB::raw('TRUNCATE role');

            // PERMISSIONS
            $permissions = new Collection([
                Permission::create(['name' => Permission::NAME_ISSUE_LIST_ASSIGNED, 'display_name' => Permission::DESC_ISSUE_LIST_ASSIGNED]),
                Permission::create(['name' => Permission::NAME_ISSUE_VERIFY, 'display_name' => Permission::DESC_ISSUE_VERIFY]),
                Permission::create(['name' => Permission::NAME_ISSUE_STATUS_TRANSITION, 'display_name' => Permission::DESC_ISSUE_STATUS_TRANSITION]),
                Permission::create(['name' => Permission::NAME_ISSUE_NOTES_APPEND, 'display_name' => Permission::DESC_ISSUE_NOTES_APPEND]),
                Permission::create(['name' => Permission::NAME_ISSUE_RESOURCE_ELECT, 'display_name' => Permission::DESC_ISSUE_RESOURCE_ELECT]),
            ]);

            foreach (['contact', 'issue', 'resource', 'status', 'user'] as $entity) {
                $permissions = $permissions->merge([
                    Permission::create(['name' => "${entity}._", 'display_name' => 'Manage ' . title_case($entity)]),
                    Permission::create(['name' => "${entity}.list", 'display_name' => 'List All ' . title_case($entity)]),
                    Permission::create(['name' => "${entity}.read", 'display_name' => 'View ' . title_case($entity)]),
                    Permission::create(['name' => "${entity}.write", 'display_name' => 'Modify ' . title_case($entity)]),
                ]);
            }

            $keyedPermissions = $permissions->keyBy('name');

            // ROLES
            $roles = [];

            foreach ([Role::ADMIN, Role::ANALYST, Role::PEER] as $role) {
                $roles[$role] = Role::create([
                    'name' => $role,
                    'display_name' => title_case($role),
                ]);
            }

            $roles[Role::ADMIN]->attachPermissions([
                $keyedPermissions['user._'],
                $keyedPermissions['issue._'],
            ]);

            $roles[Role::ANALYST]->attachPermissions([
                $keyedPermissions['issue._'],
                $keyedPermissions['resource.read'],
                $keyedPermissions[Permission::NAME_ISSUE_STATUS_TRANSITION],
                $keyedPermissions[Permission::NAME_ISSUE_VERIFY],
            ]);

            $roles[Role::PEER]->attachPermissions([
                $keyedPermissions[Permission::NAME_ISSUE_LIST_ASSIGNED],
                $keyedPermissions[Permission::NAME_ISSUE_NOTES_APPEND],
                $keyedPermissions[Permission::NAME_ISSUE_RESOURCE_ELECT],
                $keyedPermissions[Permission::NAME_ISSUE_STATUS_TRANSITION],
            ]);

            Model::reguard();

            DB::table('seeds')->insert([
                'seed' => get_class($this)
            ]);
        });
    }
}
