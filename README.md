# Dazu - API

## Getting started
### Installing
```bash
# Clone repo
git clone git@bitbucket.org:waszka73/api.git

# Migration and DB seeder (after changing your DB settings in .env)
php artisan migrate --seed

# Run queue
php artisan queue:listen
```
## Built with
* [Laravel](https://laravel.com/) - The PHP Framework For Web Artisans
* [Laravel Sanctum](https://github.com/laravel/sanctum/) - Laravel Sanctum provides a featherweight authentication system for SPAs and simple APIs.
* [spatie/laravel-permission](https://github.com/spatie/laravel-permission) - Associate users with permissions and roles.
* [Vue Admin Template](https://github.com/PanJiaChen/vue-admin-template) - A minimal vue admin template with Element UI

## Authors

* **Artur Jurkiewicz** - artur.jurkiewiczpl@gmail.com
