-- MODULES
CREATE TABLE `allocationtool`.`modules` (
  `module_id` VARCHAR(15) NOT NULL,
  `module_name` VARCHAR(150) NOT NULL,
  PRIMARY KEY (`module_id`));

-- MODULE_PREFERENCES
CREATE TABLE `allocationtool`.`module_preferences` (
  `field_id` BIGINT(20) NOT NULL,
  `module_id` VARCHAR(15) NOT NULL,
  `convenor_user_id` VARCHAR(255) NOT NULL,
  `no_of_assistants` INT(2) NOT NULL,
  `no_of_contact_hours` FLOAT NOT NULL,
  `no_of_marking_hours` FLOAT NOT NULL,
  `academic_year` VARCHAR(15) NOT NULL,
  `semester` INT(2) NOT NULL,
  PRIMARY KEY (`field_id`),
  INDEX `module_id_idx` (`module_id` ASC),
  INDEX `convenor_user_id_idx` (`convenor_user_id` ASC),
  CONSTRAINT `module_id`
    FOREIGN KEY (`module_id`)
    REFERENCES `allocationtool`.`modules` (`module_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `convenor_user_id`
    FOREIGN KEY (`convenor_user_id`)
    REFERENCES `allocationtool`.`users` (`user_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

-- TA_MODULE_CHOICES
CREATE TABLE `allocationtool`.`ta_module_choices` (
  `field_id` BIGINT(20) NOT NULL,
  `presference_id` VARCHAR(45) NOT NULL,
  `ta_user_id` VARCHAR(255) NOT NULL,
  `module_id` VARCHAR(15) NOT NULL,
  `priority` INT(2) NOT NULL,
  `did_before` TINYINT(1) NULL,
  PRIMARY KEY (`field_id`),
  INDEX `preference_id_idx` (`presference_id` ASC),
  INDEX `ta_user_id_idx` (`ta_user_id` ASC),
  INDEX `module_id_idx` (`module_id` ASC),
  CONSTRAINT `preference_id`
    FOREIGN KEY (`presference_id`)
    REFERENCES `allocationtool`.`ta_preferences` (`preference_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `ta_user_id`
    FOREIGN KEY (`ta_user_id`)
    REFERENCES `allocationtool`.`users` (`user_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `module_id`
    FOREIGN KEY (`module_id`)
    REFERENCES `allocationtool`.`modules` (`module_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

-- ALLOCATIONS
CREATE TABLE `allocationtool`.`allocations` (
  `allocation_id` VARCHAR(15) NOT NULL,
  `academic_year` VARCHAR(15) NOT NULL,
  `semester` INT(2) NOT NULL,
  `module_id` VARCHAR(15) NOT NULL,
  `ta_user_id` VARCHAR(255) NOT NULL,
  `creator_user_id` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL,
  PRIMARY KEY (`allocation_id`),
  INDEX `ta_user_id_idx` (`ta_user_id` ASC),
  INDEX `creator_user_id_idx` (`creator_user_id` ASC),
  INDEX `module_id_idx` (`module_id` ASC),
  CONSTRAINT `module_id`
    FOREIGN KEY (`module_id`)
    REFERENCES `allocationtool`.`modules` (`module_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `ta_user_id`
    FOREIGN KEY (`ta_user_id`)
    REFERENCES `allocationtool`.`users` (`user_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `creator_user_id`
    FOREIGN KEY (`creator_user_id`)
    REFERENCES `allocationtool`.`users` (`user_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

-- LANGUAGES
CREATE TABLE `allocationtool`.`languages` (
  `language_id` VARCHAR(10) NOT NULL,
  `language_name` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`language_id`));

-- USED_LANGUAGES
CREATE TABLE `allocationtool`.`used_languages` (
  `field_id` BIGINT(20) NOT NULL,
  `module_id` VARCHAR(15) NOT NULL,
  `language_id` VARCHAR(10) NOT NULL,
  PRIMARY KEY (`field_id`),
  INDEX `module_id_idx` (`module_id` ASC),
  INDEX `language_id_idx` (`language_id` ASC),
  CONSTRAINT `module_id`
    FOREIGN KEY (`module_id`)
    REFERENCES `allocationtool`.`modules` (`module_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `language_id`
    FOREIGN KEY (`language_id`)
    REFERENCES `allocationtool`.`languages` (`language_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
    ENGINE = InnoDB;


-- modifications:
ALTER TABLE `allocationtool`.`used_languages`
ENGINE = InnoDB ;


--
-- TRIAL
--
CREATE TABLE `allocationtool`.`new_table` (
  `idnew_table` INT NOT NULL,
  PRIMARY KEY (`idnew_table`))
ENGINE = MEMORY;

