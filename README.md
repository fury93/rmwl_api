###INSTALLATION

**Install via Composer**

```
composer global require fxp/composer-asset-plugin --no-plugins
composer update --prefer-dist
```

###GETTING STARTED

- Run command `php init --env=Development` to initialize the application with a specific environment.
- Create a new database and adjust the `components['db']` configuration in `common/config/main-local.php` accordingly.
- Run `composer install`.
- Apply migrations with console command ``php yii migrate``. This will create tables needed for the application to work.
- Apply rbac with console command ``yii rbac/init``.
- Set document roots of your Web server:

for rest `/path/to/rmwl_api/rest/web/` and using the URL `http://rmwl_api.loc/`
for backend `/path/to/rmwl_api/backend/web/` and using the URL `http://rmwl.loc/`

