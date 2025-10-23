@servers(['web-1' => 'equipedsi@143.198.146.43'])
 
@task('deploy-travail', ['on' => ['web-1']])
    sudo su
    cd /home/equipedsi/projects/TRAVAIL-API/
    git pull origin {{ $branch }}

    @if ($composer && $composer==1)
       COMPOSER_ALLOW_SUPERUSER=1 composer install 
    @endif

    @if ($composer && $composer==0)
       COMPOSER_ALLOW_SUPERUSER=1 composer update 
    @endif



    @if ($migrate)
    yes | php artisan migrate
    @endif

     @if ($permission)
    php artisan db:seed --class=PermissionSeeder
    @endif

    php artisan optimize
    php artisan config:clear
@endtask