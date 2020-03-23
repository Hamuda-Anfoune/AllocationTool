## TODO:
-----------------
*  Turn on Vue production mode when deploying for production, as it's currently running in development mode.
    See more tips at https://vuejs.org/guide/deployment.html

* Add foreign key references for academic year

* Edit home to show content related to signed in account type

* edit error msg when attempting to register with an email that does not exist in university_users table

* Add foreign key drop statements to down() in the migrations

****************************

## Notes:
-----------------
1. Set default database engine:
    config -> database.php -> {search: engine} -> change from null to 'InnoDB'

2. Validating rules for POSTed forms:
    https://laracasts.com/discuss/channels/laravel/how-to-edit-laravel-field-validation-message
    https://laravel.com/docs/5.7/validation#customizing-the-error-messages

3. Edited post-authentication event at:
    \vendor\laravel\framework\src\Illuminate\Foundation\Auth\AuthenticatesUsers.php
    It is the trait used by LoginController

4. Kiki, do you love me?    
