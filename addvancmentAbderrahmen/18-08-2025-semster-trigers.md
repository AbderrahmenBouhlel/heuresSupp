

// this trigger ensure that in each acadmeic year a start of sem1 = start of acadmic year 
// and end of semster 2 = send of acadmic year 
SHOW CREATE TRIGGER check_semester_dates;
CREATE DEFINER=`avnadmin`@`%` TRIGGER `check_semester_dates` BEFORE INSERT ON `semesters` FOR EACH ROW BEGIN
     DECLARE academic_start DATE;
     DECLARE academic_end DATE;
 
     -- get academic year start and end
     SELECT start_date, end_date
     INTO academic_start, academic_end
     FROM academic_years
     WHERE id = NEW.academic_year_id;
 
     -- check S1 start
     IF NEW.code = 'S1' AND NEW.start_date != academic_start THEN
         SIGNAL SQLSTATE '45000' 
         SET MESSAGE_TEXT = 'S1 start date must equal academic year start date';
     END IF;
 
     -- check S2 end
     IF NEW.code = 'S2' AND NEW.end_date != academic_end THEN
         SIGNAL SQLSTATE '45000' 
         SET MESSAGE_TEXT = 'S2 end date must equal academic year end date';
     END IF;
 END





// this triger ensure acadmic years never overllap in our DB

 DELIMITER $$

CREATE TRIGGER prevent_overlapping_academic_years
BEFORE INSERT ON academic_years
FOR EACH ROW
BEGIN
    IF EXISTS (
        SELECT 1
        FROM academic_years
        WHERE (NEW.start_date BETWEEN start_date AND end_date)
           OR (NEW.end_date BETWEEN start_date AND end_date)
           OR (start_date BETWEEN NEW.start_date AND NEW.end_date)
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Academic years cannot overlap';
    END IF;
END$$

DELIMITER ;




// this trigger ensure inside one acadmic year semsters which are 2 they will never ovverrlap 
DELIMITER $$

CREATE TRIGGER prevent_overlapping_semesters
BEFORE INSERT ON semesters
FOR EACH ROW
BEGIN
    IF EXISTS (
        SELECT 1
        FROM semesters
        WHERE academic_year_id = NEW.academic_year_id
          AND (
              (NEW.start_date BETWEEN start_date AND end_date)
           OR (NEW.end_date BETWEEN start_date AND end_date)
           OR (start_date BETWEEN NEW.start_date AND NEW.end_date)
          )
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Semesters cannot overlap within an academic year';
    END IF;
END$$

DELIMITER ;




// this trigger insure we dont add more than 2 semsters for one acadmic year
DELIMITER $$

CREATE TRIGGER check_two_semesters
AFTER INSERT ON semesters
FOR EACH ROW
BEGIN
    DECLARE semester_count INT;
    SELECT COUNT(*) INTO semester_count
    FROM semesters
    WHERE academic_year_id = NEW.academic_year_id;

    IF semester_count > 2 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'An academic year can only have 2 semesters';
    END IF;
END$$

DELIMITER ;



<!-- 
The trigger you wrote is an AFTER INSERT trigger. That means:

MySQL first inserts the new row into semesters.

Then the trigger runs.

If your condition is true (semester_count > 2), the trigger raises an error with SIGNAL.

When an error is raised inside a trigger, the entire statement is rolled back — including the insert itself.

So:

The invalid third semester will not remain in the table.

The user will see an error message: -->