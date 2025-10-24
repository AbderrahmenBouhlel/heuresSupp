DELIMITER $$

CREATE TRIGGER check_semester_dates BEFORE INSERT ON semesters
FOR EACH ROW
BEGIN
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

END$$

DELIMITER ;

before inserting a new semster we ensure that it s start = start of the acdemic year of that semster if (S1)
if (S2) make sure the end of the semster = end of the academic year







👉 if someone tries to insert a reclamations row with a reclaimed_by that doesn’t match the teacher in the associated process, MySQL should throw an error.


DELIMITER $$

CREATE TRIGGER validate_reclaimed_by_before_insert
BEFORE INSERT ON reclamations
FOR EACH ROW
BEGIN
  DECLARE teacherId BIGINT UNSIGNED;

  -- Get teacher from the associated process
  SELECT os.teacher_id
  INTO teacherId
  FROM overtime_statuses os
  WHERE os.id = NEW.overtime_status_id
  LIMIT 1;

  -- If mismatch, throw an error
  IF NEW.reclaimed_by <> teacherId THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Invalid reclaimed_by: must match process teacher_id';
  END IF;
END$$

DELIMITER ;



DELIMITER $$

CREATE TRIGGER validate_reclaimed_by_before_update
BEFORE UPDATE ON reclamations
FOR EACH ROW
BEGIN
  DECLARE teacherId BIGINT UNSIGNED;

  SELECT os.teacher_id
  INTO teacherId
  FROM overtime_statuses os
  WHERE os.id = NEW.overtime_status_id
  LIMIT 1;

  IF NEW.reclaimed_by <> teacherId THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Invalid reclaimed_by: must match process teacher_id';
  END IF;
END$$

DELIMITER ;
