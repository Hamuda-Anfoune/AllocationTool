DELETE FROM `allocationtool`.`ta_language_choices` WHERE `preference_id`='salimY2019-2020-02';

DELETE FROM `allocationtool`.`ta_module_choices` WHERE `preference_id`='salimY2019-2020-02';

DELETE FROM `allocationtool`.`ta_preferences` WHERE `preference_id`='salimY2019-2020-02';

DELETE FROM `allocationtool`.`ta_module_choices` WHERE `preference_id`='salimY2019-2020-02';

DELETE FROM `allocationtool`.`ta_preferences` WHERE `preference_id`='salimY2019-2020-02';

DELETE FROM `allocationtool`.`module_preferences` WHERE `academic_year`='2019-2020-02';

DROP TABLE `allocationtool`.`trials`;

SELECT * FROM users WHERE unique_id='';


## adding university account

DELETE FROM `allocationtool`.`university_users` WHERE `email`='hamuda@gmail.com';