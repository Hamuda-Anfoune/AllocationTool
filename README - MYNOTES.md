## TODO:
-----------------
  *  Create a view for the admin to assign the weights for each module preferece;
        e.g. having helped with the module before would have 3 poiints in the weighing process

  *  Finalise show functionality for TA preferences

  *  Edit the module preferences submission view to add a new div for each new 'used language'

  *  Edit the TA preferences submission view to add a new div for each new 'module choice'

  *  Edit home page to be a dashboard

  *  Turn on Vue production mode when deploying for production:
     as it's currently running in development mode.
     See more tips at https://vuejs.org/guide/deployment.html

  *  edit error msg when attempting to register with an email that does not exist in university_users table

  *  Add foreign key drop statements to down() in the migrations

****************************


## Notes:
-----------------
  *  using the email as the primary key instead of an ID as the university uses that to identify users.

  *  usnig the acaemic year in both modules and module_preferences:
        modules: might be differnt from year to another
        module_prefernces: nead to know each preference belongs to which year

  *  Admin will submit control values:
        in the prefernces weighing process:
            weight of 'done before' by TA
            weight of re;evant languages to module based priority of language to TA
            weight of module priority to TA


****************************


## Supervisor Meeting Notes:
-----------------
  *  After setting weights to each TA in accordance to the module's preferences
  *  Where to save data while working on allocation: cash or db
  *  match all TA first choices first, the come to seconds; or match all of every TA then go to other TAs
  *  Merge contact and marking hours into working hours
