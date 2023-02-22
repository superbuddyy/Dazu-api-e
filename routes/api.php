<?php

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Laravue\Faker;
use \App\Laravue\JsonResponse;
use \App\Laravue\Acl;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace('Api')->group(function() {
    Route::namespace('Auth')->group(function() {
        Route::post('auth/login', 'AuthController@login')->name('auth.login');
        Route::post('auth/register', 'AuthController@register')->name('auth.register');
        Route::post('auth/complete-registration', 'AuthController@completeRegistration')
            ->name('auth.complete-registration');
        Route::post('auth/resend-email', 'AuthController@resendEmail')->name('auth.resend-email');
        Route::patch('auth/change-password', 'AuthController@changePassword')
            ->name('auth.change-password');
        Route::post('auth/set-password', 'AuthController@setPassword')
            ->name('auth.set-password');
        Route::post('auth/remind-password', 'AuthController@remindPassword')
            ->name('auth.remind-password');
        Route::get('auth/user', 'AuthController@user')->name('auth.user');
        Route::post('auth/logout', 'AuthController@logout')->name('auth.logout');
        Route::get('auth/check', 'AuthController@check')->name('auth.check');

        Route::post('auth/sendmail', 'AuthController@sendmail')->name('auth.sendmail');
        Route::post('auth/getToken', 'AuthController@getToken')->name('auth.getToken');
        Route::post('auth/resetPassword', 'AuthController@resetPassword')->name('auth.resetPassword');
    });

    Route::namespace('Client')->group(function() {
        Route::group(['middleware' => 'auth:sanctum'], function () {
            Route::get('recent-search', 'RecentSearchController@index')
                ->name('user.recentsearch');
            Route::post('recent-search', 'RecentSearchController@store')
                ->name('user.recentsearch');
            Route::delete('recent-search', 'RecentSearchController@delete')
                ->name('user.recentsearch');
            Route::get('user/profile', 'UserProfileController@show')
                ->name('user.profile')
                ->middleware('permission:' . Acl::PERMISSION_VIEW_OWN_PROFILE);
            Route::put('user/profile', 'UserProfileController@update')
                ->name('user.profile.update')
                ->middleware('permission:' . Acl::PERMISSION_UPDATE_OWN_PROFILE);
            Route::put('user/default-avatar', 'UserProfileController@updateDefaultAvatar')
                ->name('user.profile.update.default_avatar');
            Route::delete('user', 'UserController@delete')->name('user.delete');
            Route::patch('user/profile/newsletter', 'UserProfileController@toggleNewsletter')
                ->name('user.profile.newsletter');

            Route::post('user/avatar', 'UserController@storeAvatar')
                ->name('user.avatar.store');
            Route::delete('user/avatar', 'UserController@deleteAvatar')
                ->name('user.avatar.delete');

            /** Subscriptions */
            Route::post('subscriptions/{subscription}/{offer}', 'SubscriptionController@buy')
                ->name('subscriptions.buy');

            /** Transactions */
            Route::get('transactions', 'TransactionController@index')
                ->name('transactions.index');

            Route::get('transactions/{transaction}', 'TransactionController@generateInvoice')
                ->name('transactions.invoice');

            /** User offers */
            Route::get('user/offers', 'UserController@getMyoffers')
                ->name('user.offers');

            /** User Notifications */
            Route::get('user/notifications', 'UserController@getMyNotifications')
                ->name('user.notifications');

            Route::patch('user/notifications/deactivate', 'UserController@deactivateNotifications')
                ->name('user.notifications.deactivate');

            /** Agents */
            Route::get('user/agents', 'UserController@getAgents')
                ->name('user.agent.get_agents')
                ->middleware('permission:' . Acl::PERMISSION_SHOW_AGENT);
            Route::post('user/agents', 'UserController@storeAgent')
                ->name('user.agent.store')
                ->middleware('permission:' . Acl::PERMISSION_STORE_AGENT);
            Route::delete('user/agents/{user}', 'UserController@deleteAgent')
                ->name('user.agent.delete')
                ->middleware('permission:' . Acl::PERMISSION_DELETE_AGENT);

            /** Offers */
            Route::post('offers/{offer}', 'OfferController@update')
                ->name('offers.update')
                ->middleware('censor');
            Route::patch('offers/activate', 'OfferController@activate')
                ->name('offers.activate');
            Route::patch('offers/deactivate', 'OfferController@deactivate')
                ->name('offers.deactivate');
            Route::patch('offers/refresh/{offer}', 'OfferController@refresh')
                ->name('offers.refresh');
            Route::patch('offers/raise/{offer}', 'OfferController@raise')
                ->name('offers.raise');

            /** Favorites */
            Route::get('favorites', 'FavoriteController@index')
                ->name('favorites.index');
            Route::post('favorites/{offer}', 'FavoriteController@store')
                ->name('favorites.store');
            Route::patch('favorites/{offer}/activate', 'FavoriteController@activateNotifications')
                ->name('favorites.activate');
            Route::patch('favorites/{offer}/deactivate', 'FavoriteController@deactivateNotifications')
                ->name('favorites.deactivate');
            Route::delete('favorites/{offer}', 'FavoriteController@destroy')
                ->name('favorites.destroy');

            /** Favorites */
            Route::get('favorite-users', 'FavoriteUserController@index')
                ->name('favorites-users.index');
            Route::post('favorite-users/{user}', 'FavoriteUserController@store')
                ->name('favorite-users.store');
            Route::delete('favorite-users/{user}', 'FavoriteUserController@destroy')
                ->name('favorite-users.destroy');

            /** Favorite Filters */
            Route::get('favorite-filters', 'FavoriteFilterController@index')
                ->name('favorite-filters.index');
            Route::post('favorite-filters', 'FavoriteFilterController@store')
                ->name('favorite-filters.store');
            Route::put('favorite-filters', 'FavoriteFilterController@update')
                ->name('favorite-filters.update');
            Route::delete('favorite-filters/{favorite_id}', 'FavoriteFilterController@destroy')
                ->name('favorite-filters.destroy');

            /** Recent search Filters */
            Route::get('recent-search-filters', 'RecentSearchFilterController@index')
                ->name('recent-search-filters.index');
            Route::post('recent-search-filters', 'RecentSearchFilterController@store')
                ->name('recent-search-filters.store');
            Route::put('recent-search-filters', 'RecentSearchFilterController@update')
                ->name('recent-search-filters.update');
            Route::delete('recent-search-filters/{favorite_id}', 'RecentSearchFilterController@destroy')
                ->name('recent-search-filters.destroy');
        });

        /** Popup */
        Route::get('/popups', 'PopupController@index')
            ->name('popup.index');
        Route::get('/popups/{popup}', 'PopupController@show')
            ->name('popup.show');

        /** Faq */
        Route::get('faq', 'FaqController@index')
            ->name('faq.index');
            // FAQ
        Route::post('faq', 'FaqController@updateOrCreate')
            ->name('faq.updateOrCreate');
        Route::get('faq/{id}', 'FaqController@show')
            ->name('faq.show');
        Route::delete('faq/{id}', 'FaqController@delete')
            ->name('faq.delete');

        /** PAGES */
        Route::get('pages', 'PagesController@index')
            ->name('pages.index');
        Route::get('pages/{key}', 'PagesController@show')
            ->name('pages.show');

        /** User profile */
        Route::get('profile/{user}', 'UserController@showProfile')
            ->name('user.showProfile');
        Route::get('my-profile/{user}', 'UserController@showMyProfile')
            ->name('user.showMyProfile');
        /** Contact */
        Route::post('user/{user}/phone', 'UserController@getPhone')
            ->name('user.getPhone');

        /** Contact */
        Route::post('contact/offer/{offer}', 'ContactController@sendOfferEmail')
            ->name('contact.offer');

        Route::post('contact/profile/{user}', 'ContactController@sendProfileEmail')
            ->name('contact.profile');

        Route::post('contact/contact-form', 'ContactController@sendContactForm')
            ->name('contact.contactForm');
        Route::post('contact/confirm-contact', 'ContactController@confirmContact')
            ->name('contact.contactForm');
        /** Newsletter */
        Route::post('newsletter', 'NewsletterController@store')
            ->name('newsletter.store');
        Route::patch('newsletter', 'NewsletterController@activate')
            ->name('newsletter.activate');

        /** Payments */
        Route::get('payments/callback', 'PaymentController@callback')
            ->name('payment.callback');
        Route::post('payments/callback', 'PaymentController@callback')
            ->name('payment.callback');

        /** Blog */
        Route::get('blog/last-post', 'PostController@lastPost')
            ->name('blog.lastPost');
        Route::get('blog', 'PostController@index')
            ->name('blog.index');
        Route::get('blog/{post}', 'PostController@show')
            ->name('blog.show');

        /** footer */

        Route::get('footers/last-post', 'PostController@lastPost')
        ->name('footers.lastPost');
        Route::get('footers', 'PostController@index')
            ->name('footers.index');
        Route::get('footers/{post}', 'PostController@show')
        ->name('footers.show');
        /** Subscriptions */
        Route::get('subscriptions', 'SubscriptionController@index')
            ->name('subscriptions.index');

        /** Offers */
        Route::get('offers', 'OfferController@index')
            ->name('offers.index');

        Route::get('offers/{offer}', 'OfferController@show')
            ->name('offers.show');

        Route::get('offers/{offer}/similar', 'OfferController@getSmimilar')
            ->name('offers.similar');

        Route::get('offers/{offer}/stats', 'OfferController@getStats')
            ->name('offers.stats');

        Route::post('offers', 'OfferController@store')
            ->name('offers.store')
            ->middleware('censor');
        Route::post('offers-preview', 'PreviewOfferController@store')
            ->name('offers.preview')
            ->middleware('censor');
        Route::post('offers-preview/{offer}', 'PreviewOfferController@update')
            ->name('offers-preview.update')
            ->middleware('censor');
        Route::post('migrate/offers-preview/{offer}', 'OfferController@migrate')
            ->name('offers-preview.migrate')
            ->middleware('censor');
        Route::get('offers-preview/{offer}', 'PreviewOfferController@show')
            ->name('offers-preview.show');
        Route::get('offers/bill/{offer}', 'OfferController@calculateBill')
            ->name('offers.bill');

        Route::post('offers/charge/{offer}', 'OfferController@charge')
            ->name('offers.charge');

        Route::post('offers/{offer}/report', 'OfferController@report')
            ->name('offers.report');

        /** Categories */
        Route::get('categories', 'CategoryController@index')
            ->name('categories.index');

        /** Attributes */
        Route::get('attributes', 'AttributeController@index')
            ->name('attributes.index');

        /** Search */
        Route::get('search', 'SearchController@index')
            ->name('search.index');
        Route::get('search/filters', 'SearchController@getFilters')
            ->name('search.filters');

        /** Settings */
        Route::get('settings', 'SettingController@index')
            ->name('settings.index');
    });

    /** Admin */
    Route::namespace('Admin')->group(function() {
        Route::group(['middleware' => 'auth:sanctum'], function () {
            Route::get('admin/stats/general', 'StatController@index')
                ->name('stats.general');

            Route::get('/user', function (Request $request) {
                return new UserResource($request->user());
            });

            // POPUP
            Route::post('popups/{popup}', 'PopupController@update')
                ->name('popup.update');
            Route::patch('popups/{popup}/activate', 'PopupController@activate')
                ->name('popup.activate');
            Route::patch('popups/{popup}/deactivate', 'PopupController@deactivate')
                ->name('popup.deactivate');

            // FAQ
            Route::post('faq', 'FaqController@updateOrCreate')
                ->name('faq.updateOrCreate');
            Route::post('faq/upload', 'FaqController@uploadFile')
                ->name('faq.uploadFile');
            Route::get('faq/{id}', 'FaqController@show')
                ->name('faq.show');
            Route::delete('faq/{id}', 'FaqController@delete')
                ->name('faq.delete');

            // PAGES
            Route::get('admin/pages', 'PagesController@index')
                ->name('admin.pages.index');
            Route::post('admin/pages', 'PagesController@store')
                ->name('admin.pages.store');
            Route::put('admin/pages/{id}', 'PagesController@update')
                ->name('admin.pages.update');
            Route::get('admin/pages/{id}', 'PagesController@show')
                ->name('admin.pages.show');
            Route::delete('admin/pages/{id}', 'PagesController@destroy')
                ->name('admin.pages.destroy');

            // Api resource routes
            Route::apiResource('roles', 'RoleController')->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
            Route::apiResource('users', 'UserController')->middleware('permission:' . Acl::PERMISSION_USER_MANAGE);
            Route::apiResource('permissions', 'PermissionController')->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);

            // Custom routes
            Route::put('users/{user}', 'UserController@update');
            Route::get('users/{user}/permissions', 'UserController@permissions')->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
            Route::put('users/{user}/permissions', 'UserController@updatePermissions')->middleware('permission:' .Acl::PERMISSION_PERMISSION_MANAGE);
            Route::get('roles/{role}/permissions', 'RoleController@permissions')->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);

            /** Settings */
            Route::get('admin/settings', 'SettingController@index')
                ->name('settings.index');

            Route::get('admin/settings/{setting}', 'SettingController@show')
                ->name('settings.show');

            Route::put('admin/settings/{setting}','SettingController@update')
                ->name('settings.update');

            /** Subscriptions */
            Route::get('admin/subscriptions', 'SubscriptionController@index')
                ->name('subscriptions.index');

            Route::get('admin/subscriptions/{subscription}', 'SubscriptionController@show')
                ->name('subscriptions.show');

            Route::put('admin/subscriptions/{subscription}', 'SubscriptionController@update')
                ->name('subscriptions.update');

            /** Offers */
            Route::get('admin/offers/{offer}', 'OfferController@show')
                ->name('admin.offers.show');
            Route::get('admin/offers', 'OfferController@index')
                ->name('admin.offers.index')
                ->middleware('permission:' . Acl::PERMISSION_ADMIN_OFFERS_INDEX);
            Route::patch('admin/offers/{offer}', 'OfferController@changeStatus')
                ->name('offers.change-status');
            Route::post('admin/offers/{offer}', 'OfferController@update')
                ->name('offers.update');
            Route::delete('admin/offers/{offer}', 'OfferController@destroy')
                ->name('offers.update');


            /** Blog (posts) */
            Route::get('admin/posts', 'PostController@index')
                ->middleware('permission:' . Acl::PERMISSION_LIST_POST)
                ->name('posts.index');

            Route::get('admin/posts/{post}', 'PostController@show')
                ->middleware('permission:' . Acl::PERMISSION_SHOW_POST)
                ->name('posts.show');

            Route::post('admin/posts', 'PostController@store')
                ->middleware('permission:' . Acl::PERMISSION_ADD_POST)
                ->name('posts.store');

            Route::post('admin/posts/{post}', 'PostController@update')
                ->middleware('permission:' . Acl::PERMISSION_UPDATE_POST)
                ->name('posts.update');

            Route::delete('admin/posts/{post}', 'PostController@destroy')
                ->middleware('permission:' . Acl::PERMISSION_DELETE_POST)
                ->name('posts.destroy');

            /** BlackList */
            Route::get('admin/black-list', 'BlackListController@index')
                ->middleware('permission:' . Acl::PERMISSION_LIST_BLACK_LIST)
                ->name('black-list.index');

            Route::post('admin/black-list', 'BlackListController@store')
                ->middleware('permission:' . Acl::PERMISSION_ADD_BLACK_LIST)
                ->name('posts.store');

            Route::delete('admin/black-list/{blackList}', 'BlackListController@destroy')
                ->middleware('permission:' . Acl::PERMISSION_DELETE_BLACK_LIST)
                ->name('black-list.destroy');

            /** Newsletter */
            Route::get('admin/newsletter', 'NewsletterMailController@index')
                ->middleware('permission:' . Acl::PERMISSION_NEWSLETTER_LIST)
                ->name('newsletter.index');

            Route::post('admin/newsletter', 'NewsletterMailController@store')
                ->middleware('permission:' . Acl::PERMISSION_ADD_NEWSLETTER)
                ->name('newsletter.store');
                /** Footer */
            Route::get('admin/footer', 'FooterController@index')
            ->middleware('permission:' . Acl::PERMISSION_NEWSLETTER_LIST)
            ->name('footer.index');

            Route::post('admin/footer', 'FooterController@store')
            ->middleware('permission:' . Acl::PERMISSION_ADD_NEWSLETTER)
            ->name('footer.store');

            Route::get('admin/footer/{post}', 'FooterController@show')
            ->middleware('permission:' . Acl::PERMISSION_SHOW_POST)
            ->name('footer.show');
            Route::post('admin/footer/{post}', 'FooterController@update')
            ->middleware('permission:' . Acl::PERMISSION_UPDATE_POST)
            ->name('footer.update');
            Route::delete('admin/footer/{post}', 'FooterController@destroy')
            ->middleware('permission:' . Acl::PERMISSION_DELETE_POST)
            ->name('footer.destroy');
            /** Announcement Newsletter */
            Route::get('admin/announcement-newsletter', 'AnnouncementNewsletterMailController@index')
                ->middleware('permission:' . Acl::PERMISSION_NEWSLETTER_LIST)
                ->name('announcement-newsletter.index');

            Route::post('admin/announcement-newsletter', 'AnnouncementNewsletterMailController@store')
                ->middleware('permission:' . Acl::PERMISSION_ADD_NEWSLETTER)
                ->name('announcement-newsletter.store');
        });
    });
});

