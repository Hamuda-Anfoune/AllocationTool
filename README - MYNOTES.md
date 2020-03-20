## TODO:
-----------------
*  Turn on Vue production mode when deploying for production, as it's currently running in development mode.
    See more tips at https://vuejs.org/guide/deployment.html

* edit error msg when attempting to register with an email that does not exist in university_users table

* Add foreign key drop statements to down() in the migrations

****************************

## Notes:
-----------------
1. Set default database engine:
    config -> database.php -> {search: engine} -> change from null to 'InnoDB'

