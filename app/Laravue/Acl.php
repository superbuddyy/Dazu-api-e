<?php
/**
 * File Acl.php
 *
 * @author Tuan Duong <bacduong@gmail.com>
 * @package Laravue
 * @version 1.0
 */
namespace App\Laravue;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class Acl
 *
 * @package App\Laravue
 */
final class Acl
{
    const ROLE_ADMIN = 'admin';
    const ROLE_MOD = 'mod';
    const ROLE_USER = 'user';
    const ROLE_AGENT = 'agent';
    const ROLE_COMPANY = 'company';
    const ROLE_DEVELOPER = 'developer';

    const PERMISSION_VIEW_OWN_PROFILE = 'view own profile';
    const PERMISSION_UPDATE_OWN_PROFILE = 'update own profile';

    const PERMISSION_SHOW_AGENT = 'show agent';
    const PERMISSION_STORE_AGENT = 'store agent';
    const PERMISSION_DELETE_AGENT = 'delete agent';

    const PERMISSION_VIEW_MENU_ADMINISTRATOR = 'view menu administrator';
    const PERMISSION_ADMIN_OFFERS_INDEX = 'admin offers index';
    const PERMISSION_MANAGE_OFFER = 'manage offer';

    const PERMISSION_USER_MANAGE = 'manage user';
    const PERMISSION_PERMISSION_MANAGE = 'manage permission';

    const PERMISSION_LIST_POST = 'list post';
    const PERMISSION_SHOW_POST = 'show post';
    const PERMISSION_ADD_POST = 'add post';
    const PERMISSION_UPDATE_POST = 'update post';
    const PERMISSION_DELETE_POST = 'delete post';

    const PERMISSION_LIST_BLACK_LIST = 'list black_list';
    const PERMISSION_ADD_BLACK_LIST = 'add black_list';
    const PERMISSION_DELETE_BLACK_LIST = 'delete black_list';

    const PERMISSION_NEWSLETTER_LIST = 'list newsletter';
    const PERMISSION_ADD_NEWSLETTER = 'add newsletter';

    /**
     * @param array $exclusives Exclude some permissions from the list
     * @return array
     */
    public static function permissions(array $exclusives = []): array
    {
        try {
            $class = new \ReflectionClass(__CLASS__);
            $constants = $class->getConstants();
            $permissions = Arr::where($constants, function($value, $key) use ($exclusives) {
                return !in_array($value, $exclusives) && Str::startsWith($key, 'PERMISSION_');
            });

            return array_values($permissions);
        } catch (\ReflectionException $exception) {
            return [];
        }
    }

    public static function menuPermissions(): array
    {
        try {
            $class = new \ReflectionClass(__CLASS__);
            $constants = $class->getConstants();
            $permissions = Arr::where($constants, function($value, $key) {
                return Str::startsWith($key, 'PERMISSION_VIEW_MENU_');
            });

            return array_values($permissions);
        } catch (\ReflectionException $exception) {
            return [];
        }
    }

    /**
     * @return array
     */
    public static function roles(): array
    {
        try {
            $class = new \ReflectionClass(__CLASS__);
            $constants = $class->getConstants();
            $roles =  Arr::where($constants, function($value, $key) {
                return Str::startsWith($key, 'ROLE_');
            });

            return array_values($roles);
        } catch (\ReflectionException $exception) {
            return [];
        }
    }
}
