<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Laravue\Models\Role;
use App\Laravue\Models\Permission;
use App\Laravue\Acl;

class SetupRolePermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (Acl::roles() as $role) {
            Role::findOrCreate($role);
        }

        $adminRole = Role::findByName(Acl::ROLE_ADMIN);
        $modRole = Role::findByName(Acl::ROLE_MOD);
        $userRole = Role::findByName(Acl::ROLE_USER);
        $agentRole = Role::findByName(Acl::ROLE_AGENT);
        $companyRole = Role::findByName(Acl::ROLE_COMPANY);

        foreach (Acl::permissions() as $permission) {
            Permission::findOrCreate($permission, 'api');
        }

        // Setup basic permission
        $adminRole->givePermissionTo(Acl::permissions());
        $modRole->givePermissionTo(Acl::permissions([Acl::PERMISSION_PERMISSION_MANAGE]));
        $userRole->givePermissionTo([
            Acl::PERMISSION_VIEW_OWN_PROFILE,
            Acl::PERMISSION_UPDATE_OWN_PROFILE,
        ]);
        $companyRole->givePermissionTo([
            Acl::PERMISSION_VIEW_OWN_PROFILE,
            Acl::PERMISSION_UPDATE_OWN_PROFILE,
            Acl::PERMISSION_SHOW_AGENT,
            Acl::PERMISSION_STORE_AGENT,
            Acl::PERMISSION_DELETE_AGENT,
        ]);
        $agentRole->givePermissionTo([
            Acl::PERMISSION_VIEW_OWN_PROFILE,
            Acl::PERMISSION_UPDATE_OWN_PROFILE,
        ]);

        foreach (Acl::roles() as $role) {
            /** @var \App\User[] $users */
            $users = \App\Laravue\Models\User::where('role', $role)->get();
            $role = Role::findByName($role);
            foreach ($users as $user) {
                $user->syncRoles($role);
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('editor');
            });
        }

        /** @var \App\User[] $users */
        $users = \App\Models\User::all();
        foreach ($users as $user) {
            $roles = array_reverse(Acl::roles());
            foreach ($roles as $role) {
                if ($user->hasRole($role)) {
                    $user->role = $role;
                    $user->save();
                }
            }
        }
    }
}
