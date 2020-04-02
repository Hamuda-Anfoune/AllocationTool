## TODO:
-----------------
  *  Alter the module prefernces submission to add used languages

  *  Edit home page to be a dashboard

  *  Turn on Vue production mode when deploying for production, as it's currently running in development mode.
    See more tips at https://vuejs.org/guide/deployment.html

  * edit error msg when attempting to register with an email that does not exist in university_users table

  * Add foreign key drop statements to down() in the migrations

****************************


## Notes:
-----------------
  *  using the email as the primary key instead of an ID as the university uses that to identify users.

  *  usnig the acaemic year in both modules and module_preferences:
        modules: might be differnt from year to another
        module_prefernces: nead to know each preference belongs to which year

  *  //

****************************


## How Tos:
-----------------
  1. Set default database engine:
    config -> database.php -> {search: engine} -> change from null to 'InnoDB'

  2. Validating rules for POSTed forms:
    https://laracasts.com/discuss/channels/laravel/how-to-edit-laravel-field-validation-message
    https://laravel.com/docs/5.7/validation#customizing-the-error-messages

  3. Edited post-authentication events at:
    to add user data to the session at successful login
    \vendor\laravel\framework\src\Illuminate\Foundation\Auth\AuthenticatesUsers.php
    It is the trait used by LoginController

  4. Kiki, do you love me?    
