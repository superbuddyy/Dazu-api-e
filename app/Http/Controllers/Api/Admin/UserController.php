<?php
/**
 * File UserController.php
 *
 * @author Tuan Duong <bacduong@gmail.com>
 * @package Laravue
 * @version 1.0
 */

namespace App\Http\Controllers\Api\Admin;

use App\Http\Resources\PermissionResource;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;
use App\Laravue\JsonResponse;
use App\Laravue\Models\Permission;
use App\Laravue\Models\Role;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * Class UserController
 *
 * @package App\Http\Controllers\Api
 */
class UserController extends BaseController
{
    const ITEM_PER_PAGE = 15;

    /**
     * Display a listing of the user resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|ResourceCollection
     */
    public function index(Request $request)
    {
        $searchParams = $request->all();
        $userQuery = User::query();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $role = Arr::get($searchParams, 'role', '');
        $keyword = Arr::get($searchParams, 'keyword', '');

        if (!empty($role)) {
            $userQuery->whereHas('roles', function($q) use ($role) { $q->where('name', $role); });
        }

        if (!empty($keyword)) {
            $userQuery->whereHas('profile', function ($q) use ($keyword) {
                $q->where('name', 'LIKE', '%' . $keyword . '%');
            });
            $userQuery->orWhere('email', 'LIKE', '%' . $keyword . '%');
        }

        return response()->success(new UserCollection($userQuery->paginate($limit)));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            array_merge(
                $this->getValidationRules(),
                [
                    'password' => ['required', 'min:6'],
                    'confirmPassword' => 'same:password',
                ]
            )
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        } else {
            $params = $request->all();
            $user = User::create([
                'name' => $params['name'],
                'email' => $params['email'],
                'password' => Hash::make($params['password']),
            ]);
            $role = Role::findByName($params['role']);
            $user->syncRoles($role);

            return new UserResource($user);
        }
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function show(User $user)
    {
        return response()->success(new UserResource($user));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User    $user
     * @return UserResource|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $user)
    {
        if ($user === null) {
            return response()->json(['error' => 'User not found'], 404);
        }
        //if ($user->isAdmin()) {
        //    return response()->json(['error' => 'Admin can not be modified'], 403);
        //}

        $currentUser = Auth::user();
        if (!$currentUser->isAdmin()
            && $currentUser->id !== $user->id
            && !$currentUser->hasPermission(\App\Laravue\Acl::PERMISSION_USER_MANAGE)
        ) {
            return response()->json(['error' => 'Permission denied'], 403);
        }

        $validator = Validator::make($request->all(), $this->getValidationRules(false));
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        } else {
            $email = $request->get('email');
            $found = User::where('email', $email)->first();
            if ($found && $found->id !== $user->id) {
                return response()->json(['error' => 'Email has been taken'], 403);
            }
            $address = $request->get('address');
            $user->profile->name = $request->get('name');
            $user->profile->phone = $request->get('phone');
            $user->profile->nip = $request->get('nip');
            $user->profile->country = $address['country'];
            $user->profile->city = $address['city'];
            $user->profile->street = $address['street'];
            $user->profile->zip_code = $address['zip_code'];
            $user->email = $email;

            if ($request->has('email_verified') && $request->get('email_verified') === true) {
                $user->email_verified_at = Carbon::now();
            }

            $user->profile->save();
            $user->save();
            return new UserResource($user);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User    $user
     * @return UserResource|\Illuminate\Http\JsonResponse
     */
    public function updatePermissions(Request $request, User $user)
    {
        if ($user === null) {
            return response()->json(['error' => 'User not found'], 404);
        }

        //if ($user->isAdmin()) {
        //    return response()->json(['error' => 'Admin can not be modified'], 403);
       // }

        $permissionIds = $request->get('permissions', []);
        $rolePermissionIds = array_map(
            function($permission) {
                return $permission['id'];
            },

            $user->getPermissionsViaRoles()->toArray()
        );

        $newPermissionIds = array_diff($permissionIds, $rolePermissionIds);
        $permissions = Permission::allowed()->whereIn('id', $newPermissionIds)->get();
        $user->syncPermissions($permissions);
        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->isAdmin()) {
            return response()->json(['error' => 'Ehhh! Can not delete admin user'], 403);
        }

        try {
            $user->offers()->delete();
            $user->forceDelete();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 403);
        }

        return response()->json(null, 204);
    }

    /**
     * Get permissions from role
     *
     * @param User $user
     * @return array|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function permissions(User $user)
    {
        try {
            return new JsonResponse([
                'user' => PermissionResource::collection($user->getDirectPermissions()),
                'role' => PermissionResource::collection($user->getPermissionsViaRoles()),
            ]);
        } catch (\Exception $ex) {
            response()->json(['error' => $ex->getMessage()], 403);
        }
    }

    /**
     * @param bool $isNew
     * @return array
     */
    private function getValidationRules($isNew = true)
    {
        return [
            'name' => 'required',
            'email' => $isNew ? 'required|email|unique:users' : 'required|email',
            'roles' => [
                'required',
                'array'
            ],
        ];
    }
}
