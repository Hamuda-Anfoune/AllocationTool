
## POST-DEVELOPMENT TODO:
-----------------
 * Add insertion statement to modules migration with all modules

 * Turn on Vue production mode when deploying for production:
    as it's currently running in development mode.
    See more tips at https://vuejs.org/guide/deployment.html

 * //

****************************

## TODO:
-----------------

 * Give GTAs more weight than TAs // Not essential

 * Edit the module preferences submission view to add a new div for each new 'used language'

 * Edit the TA preferences submission view to add a new div for each new 'module choice'

 * Edit error msg when attempting to register with an email that does not exist in university_users table

 * Add foreign key drop statements to down() in the migrations

****************************


## Notes:
-----------------
 * using the email as the primary key instead of an ID as the university uses that to identify users.

 * using the acaemic year in both modules and module_preferences:
    modules: might be differnt from year to another
    module_prefernces: nead to know each preference belongs to which year

 * Admin will submit control values:
    in the prefernces weighing process:
        weight of 'done before' by TA
        weight of relevant languages to module based priority of language to TA
        weight of module priority to TA

 * Programming languages and weights:
    L is a set of all available programming languages, L = {l1, l2, ..., ln}
    TL is a set of languages chosen by the TA as a TA languages priority list: TL = {tl1, tl2, ..., tln} => |TL|<=5 where TL is a subset of L.
    ML is the set of module languages priority list, ML = {ml1, ml2, ..., mln} => |ML|<=5 where ML is a subset of L.
    LW is a set of weights chosen to for L, LW = {lw1, lw2, ..., lw5}
    DW (diffirence weight) is a set of values used to calculate the weight of a language when its order in TL and ML is not the same, DW = {dw1, dw2, ... dw5}
    alw is the actual weight to assign a language ln,

    A language ln will have actual weight alw = lw1 if and only if it was the first priority for TA  (ln == tl1) and also the first priority for the module (ln == ml1),
    the same apllies on alw == lw2 which will only be true if (ln == tl2) and (ln == ml2).

    This covers the cases where the TA and the module have the same language ln with the same priority; (ln == tln == mln) => (ln, lwn)

    Incase the language has difirent priorities in the TA's and the module's languages priority list; (ln == mln == tlm); in that case, its actual weight alw equals lwn divided by dwm; alw = lwn/dwm
        



****************************


## Supervisor Meeting Notes:
-----------------
 *  After setting weights to each TA in accordance to the module's preferences
 *  Where to save data while working on allocation: cache or db
 *  match all TA first choices first, the come to seconds; or match all of every TA then go to other TAs
 *  Merge contact and marking hours into working hours
