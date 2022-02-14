<?php

use App\Models\Company;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use App\Laravue\Acl;
use App\Laravue\Models\Role;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = factory(User::class)->create([
            'email' => 'r.w84@hotmail.co.uk',
            'password' => Hash::make('dazu'),
        ]);

        $mod = factory(User::class)->create([
            'email' => 'mod@dazu.app',
            'password' => Hash::make('dazu'),
        ]);

        $user = factory(User::class)->create([
            'email' => 'user@dazu.app',
            'password' => Hash::make('dazu'),
        ]);

        $agent = factory(User::class)->create([
            'email' => 'agent@dazu.app',
            'password' => Hash::make('dazu'),
        ]);

        $adminRole = Role::findByName(\App\Laravue\Acl::ROLE_ADMIN);
        $modRole = Role::findByName(\App\Laravue\Acl::ROLE_MOD);
        $agentRole = Role::findByName(\App\Laravue\Acl::ROLE_AGENT);
        $userRole = Role::findByName(\App\Laravue\Acl::ROLE_USER);
        $admin->syncRoles($adminRole);
        $mod->syncRoles($modRole);
        $user->syncRoles($userRole);
        $agent->syncRoles($agentRole);
        $admin->profile()->save(factory(UserProfile::class)->make(['user_id' => $admin->id]));
        $mod->profile()->save(factory(UserProfile::class)->make(['user_id' => $mod->id]));

        $user->profile()->save(factory(UserProfile::class)->make(['user_id' => $user->id]));
        $user->company()->associate(factory(Company::class)->create());
        $user->save();

        $agent->profile()->save(factory(UserProfile::class)->make(['user_id' => $agent->id]));


//        $userList = [
//            "Adriana C. Ocampo Uria",
//            "Albert Einstein",
//            "Anna K. Behrensmeyer",
//            "Blaise Pascal",
//            "Caroline Herschel",
//            "Cecilia Payne-Gaposchkin",
//        ];
//
//        foreach ($userList as $fullName) {
//            $name = str_replace(' ', '.', $fullName);
//            $roleName = \App\Laravue\Faker::randomInArray([
//                Acl::ROLE_MOD,
//                Acl::ROLE_USER,
//                Acl::ROLE_AGENT,
//            ]);
//            $user = \App\Models\User::create([
//                'email' => strtolower($name) . '@laravue.dev',
//                'password' => \Illuminate\Support\Facades\Hash::make('laravue'),
//            ]);
//
//            $role = Role::findByName($roleName);
//            $user->syncRoles($role);
//        }
    }
}
