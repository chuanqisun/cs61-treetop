SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

USE `evenstar_db` ;

-- -----------------------------------------------------
-- Table `evenstar_db`.`airport`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `evenstar_db`.`airport` ;

CREATE  TABLE IF NOT EXISTS `evenstar_db`.`airport` (
  `code` CHAR(3) NOT NULL ,
  `city` VARCHAR(45) NOT NULL ,
  `nation` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`code`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `evenstar_db`.`flight_schedule`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `evenstar_db`.`flight_schedule` ;

CREATE  TABLE IF NOT EXISTS `evenstar_db`.`flight_schedule` (
  `flight_number` VARCHAR(20) NOT NULL ,
  `from` CHAR(3) NOT NULL ,
  `to` CHAR(3) NOT NULL ,
  `dep_time` TIME NOT NULL ,
  `arr_time` TIME NOT NULL ,
  `aircraft` VARCHAR(20) NOT NULL ,
  PRIMARY KEY (`flight_number`) ,
  CONSTRAINT `used by`
    FOREIGN KEY (`from`)
    REFERENCES `evenstar_db`.`airport` (`code`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
    FOREIGN KEY (`to`)
    REFERENCES `evenstar_db`.`airport` (`code`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE INDEX `flight_schedule_airport_idx` ON `evenstar_db`.`flight_schedule` (`from` ASC, `to` ASC) ;


-- -----------------------------------------------------
-- Table `evenstar_db`.`flight_weekday`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `evenstar_db`.`flight_weekday` ;

CREATE  TABLE IF NOT EXISTS `evenstar_db`.`flight_weekday` (
  `flight_number` VARCHAR(20) NOT NULL ,
  `weekday` ENUM('1','2','3','4','5','6','7') NOT NULL ,
  PRIMARY KEY (`flight_number`, `weekday`) ,
  CONSTRAINT `operates on`
    FOREIGN KEY (`flight_number` )
    REFERENCES `evenstar_db`.`flight_schedule` (`flight_number` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `evenstar_db`.`flight`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `evenstar_db`.`flight` ;

CREATE  TABLE IF NOT EXISTS `evenstar_db`.`flight` (
  `flight_number` VARCHAR(20) NOT NULL ,
  `date` DATE NOT NULL ,
  `status` ENUM('unknown', 'on time', 'delayed', 'canceled', 'departed', 'arrived') NOT NULL DEFAULT 'unknown' ,
  `capacity` INT NOT NULL ,
  `passenger_count` INT NOT NULL DEFAULT 0 ,
  `actual_dep_time` TIME NULL ,
  `actual_arr_time` TIME NULL ,
  PRIMARY KEY (`flight_number`, `date`) ,
  CONSTRAINT `scheduled as`
    FOREIGN KEY (`flight_number` )
    REFERENCES `evenstar_db`.`flight_schedule` (`flight_number` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `evenstar_db`.`passenger`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `evenstar_db`.`passenger` ;

CREATE  TABLE IF NOT EXISTS `evenstar_db`.`passenger` (
  `id` VARCHAR(40) NOT NULL ,
  `name` VARCHAR(20) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `evenstar_db`.`reservation`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `evenstar_db`.`reservation` ;

CREATE  TABLE IF NOT EXISTS `evenstar_db`.`reservation` (
  `passenger_id` VARCHAR(40) NOT NULL ,
  `flight_number` VARCHAR(20) NOT NULL ,
  `flight_date` DATE NOT NULL ,
  `seat_number` INT NULL ,
  PRIMARY KEY (`passenger_id`, `flight_date`, `flight_number`) ,
  CONSTRAINT `makes`
    FOREIGN KEY (`passenger_id` )
    REFERENCES `evenstar_db`.`passenger` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `on`
    FOREIGN KEY (`flight_number` , `flight_date` )
    REFERENCES `evenstar_db`.`flight` (`flight_number` , `date` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `makes_idx` ON `evenstar_db`.`reservation` (`passenger_id` ASC) ;

CREATE INDEX `on_idx` ON `evenstar_db`.`reservation` (`flight_number` ASC, `flight_date` ASC) ;


-- -----------------------------------------------------
-- Table `evenstar_db`.`account`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `evenstar_db`.`account` ;

CREATE  TABLE IF NOT EXISTS `evenstar_db`.`account` (
  `user_id` VARCHAR(45) NOT NULL ,
  `username` VARCHAR(40) NOT NULL ,
  `password` CHAR(40) NOT NULL ,
  `passenger_id` VARCHAR(45) NULL ,
  `account_type` ENUM('employee','customer') NOT NULL ,
  PRIMARY KEY (`user_id`) ,
  CONSTRAINT `flys as`
    FOREIGN KEY (`passenger_id` )
    REFERENCES `evenstar_db`.`passenger` (`id` )
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE UNIQUE INDEX `username_UNIQUE` ON `evenstar_db`.`account` (`username` ASC) ;

CREATE INDEX `flys as_idx` ON `evenstar_db`.`account` (`passenger_id` ASC) ;

USE `evenstar_db` ;

-- -----------------------------------------------------
-- Placeholder table for view `evenstar_db`.`flight_customer`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `evenstar_db`.`flight_customer` (`flight_number` INT, `date` INT, `dep_city` INT, `arr_city` INT, `status` INT, `available_seat` INT, `dep_time` INT, `arr_time` INT, `aircraft` INT);

-- -----------------------------------------------------
-- Placeholder table for view `evenstar_db`.`flight_schedule_human`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `evenstar_db`.`flight_schedule_human` (`flight_number` INT, `dep_airport` INT, `dep_city` INT, `arr_airport` INT, `arr_city` INT, `weekday` INT, `dep_time` INT, `arr_time` INT, `aircraft` INT);

-- -----------------------------------------------------
-- Placeholder table for view `evenstar_db`.`reservation_customer`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `evenstar_db`.`reservation_customer` (`passenger_id` INT, `flight_number` INT, `flight_date` INT, `dep_city` INT, `arr_city` INT, `status` INT, `dep_time` INT, `seat_number` INT, `aircraft` INT);

-- -----------------------------------------------------
-- function get_available_seat
-- -----------------------------------------------------

USE `evenstar_db`;
DROP function IF EXISTS `evenstar_db`.`get_available_seat`;

DELIMITER $$
USE `evenstar_db`$$
-- Return
--   0 can't be reserved
--   -1 Full
--   >0 A good seat #

CREATE FUNCTION `evenstar_db`.`get_available_seat`
(
	chosen_flight_number  VARCHAR(20),
	chosen_flight_date	   DATE	
)
RETURNS INT READS SQL DATA
BEGIN
	DECLARE chosen_status ENUM('unknown', 'on time', 'delayed', 'canceled', 'departed', 'arrived');
	DECLARE chosen_capacity	INT;
	DECLARE chosen_passenger_count INT;
	DECLARE current_seat INT DEFAULT 0;

	

	SELECT status, capacity, passenger_count INTO chosen_status, chosen_capacity, chosen_passenger_count
	FROM flight
	WHERE flight.flight_number = chosen_flight_number AND flight.date = chosen_flight_date;
	
	-- first check if flight is bookable
	IF chosen_status = 'canceled' OR chosen_status = 'departed' OR chosen_status = 'arrived'
	THEN RETURN 0;
	END IF;
	
	-- check if flight is full
	IF chosen_passenger_count = chosen_capacity
	THEN RETURN -1;
	END IF;

	-- then return available seat
	WHILE current_seat < chosen_capacity DO
		SET current_seat = current_seat + 1;
		IF NOT EXISTS (SELECT seat_number
		FROM reservation	
		WHERE reservation.seat_number = current_seat AND reservation.flight_number = chosen_flight_number AND reservation.flight_date = chosen_flight_date)
		 THEN
			RETURN (current_seat);
		END IF;
	END WHILE;
	
	RETURN 0;
	
END$$

DELIMITER ;

-- -----------------------------------------------------
-- function make_reservation
-- -----------------------------------------------------

USE `evenstar_db`;
DROP function IF EXISTS `evenstar_db`.`make_reservation`;

DELIMITER $$
USE `evenstar_db`$$
-- Return
--   3 Already reserved
--   2 Not reservable
--   1 Full
--   0 Success

CREATE FUNCTION `make_reservation`(
	chosen_passenger_id		  VARCHAR(40),
	chosen_flight_number  VARCHAR(20),
	chosen_flight_date	  DATE	
) RETURNS INT
    READS SQL DATA
BEGIN
	DECLARE chosen_seat_number INT;
	-- check if passenger is on this flight
	IF EXISTS (SELECT passenger_id
		FROM reservation	
		WHERE reservation.flight_number = chosen_flight_number 
		AND reservation.flight_date = chosen_flight_date
		AND reservation.passenger_id = chosen_passenger_id)
	THEN
		RETURN 3;
	END IF;
	SET chosen_seat_number = get_available_seat(chosen_flight_number, chosen_flight_date);
	IF chosen_seat_number > 0 THEN
		INSERT INTO reservation VALUES(chosen_passenger_id, chosen_flight_number, chosen_flight_date, chosen_seat_number);
		RETURN 0;
	END IF;
	RETURN chosen_seat_number + 2;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- function cancel_reservation
-- -----------------------------------------------------

USE `evenstar_db`;
DROP function IF EXISTS `evenstar_db`.`cancel_reservation`;

DELIMITER $$
USE `evenstar_db`$$
-- Return
--  0 success
--  1 not on this flight
--  2 flight is not cancelable

CREATE FUNCTION `evenstar_db`.`cancel_reservation`
(
	chosen_passenger_id		  VARCHAR(40),
	chosen_flight_number  VARCHAR(20),
	chosen_flight_date	  DATE	
) RETURNS INT
    READS SQL DATA
BEGIN
	DECLARE chosen_status ENUM('unknown', 'on time', 'delayed', 'canceled', 'departed', 'arrived');

	SELECT status INTO chosen_status
	FROM flight
	WHERE flight.flight_number = chosen_flight_number AND flight.date = chosen_flight_date;

	-- first check if flight is cancelable
	IF chosen_status = 'departed' OR chosen_status = 'arrived'
	THEN RETURN 2;
	END IF;

	-- check if passenger is on this flight
	IF NOT EXISTS (SELECT passenger_id
		FROM reservation	
		WHERE reservation.flight_number = chosen_flight_number 
		AND reservation.flight_date = chosen_flight_date
		AND reservation.passenger_id = chosen_passenger_id)
		 THEN
			RETURN 1;
	END IF;

	-- then cancel the flight
	DELETE FROM reservation WHERE 
	(passenger_id = chosen_passenger_id AND
	flight_number = chosen_flight_number AND
	flight_date = chosen_flight_date);
	RETURN 0;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure get_passenger_list
-- -----------------------------------------------------

USE `evenstar_db`;
DROP procedure IF EXISTS `evenstar_db`.`get_passenger_list`;

DELIMITER $$
USE `evenstar_db`$$
-- Get a list of passengers on a given flight

CREATE PROCEDURE `evenstar_db`.`get_passenger_list` (
	IN  chosen_flight_number VARCHAR(20),
	IN  chosen_flight_date DATE
)
BEGIN
SELECT seat_number, id, name FROM reservation AS R 
INNER JOIN passenger AS P 
ON R.passenger_id = P.id 
WHERE flight_number = chosen_flight_number
AND flight_date = chosen_flight_date
ORDER BY seat_number;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure get_popular_flight
-- -----------------------------------------------------

USE `evenstar_db`;
DROP procedure IF EXISTS `evenstar_db`.`get_popular_flight`;

DELIMITER $$
USE `evenstar_db`$$
-- Get 5 most popular flights, measured by number of reservations
-- in a given range of time 

CREATE PROCEDURE `evenstar_db`.`get_popular_flight` (
	IN begin_date DATE,
	IN end_date DATE
)
BEGIN
	SELECT F.flight_number, 
	C.dep_city, 
	C.arr_city,
	SUM(passenger_count) as total_passenger 
	FROM flight AS F
	JOIN flight_customer AS C
	ON F.flight_number = C.flight_number
	AND F.date = C.date
	WHERE F.date>=begin_date 
	AND F.date<=end_date 
	GROUP BY F.flight_number 
	ORDER BY total_passenger 
	DESC 
	LIMIT 5;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- View `evenstar_db`.`flight_customer`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `evenstar_db`.`flight_customer` ;
DROP TABLE IF EXISTS `evenstar_db`.`flight_customer`;
USE `evenstar_db`;
CREATE  OR REPLACE VIEW `evenstar_db`.`flight_customer` AS
SELECT t1.flight_number, 
f.date,
b1.city AS dep_city,  
b2.city AS arr_city, 
f.status,
(f.capacity-f.passenger_count) as available_seat,
t1.dep_time,
t1.arr_time,
t1.aircraft 
FROM (flight_schedule AS t1 JOIN airport as b1 ON t1.from = b1.code)
INNER JOIN (flight_schedule AS t2 JOIN airport AS b2 ON t2.to = b2.code) 
ON t1.flight_number = t2.flight_number
INNER JOIN flight AS f on t1.flight_number = f.flight_number;

-- -----------------------------------------------------
-- View `evenstar_db`.`flight_schedule_human`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `evenstar_db`.`flight_schedule_human` ;
DROP TABLE IF EXISTS `evenstar_db`.`flight_schedule_human`;
USE `evenstar_db`;
CREATE  OR REPLACE VIEW `evenstar_db`.`flight_schedule_human` AS
SELECT t1.flight_number, 
t1.from AS dep_airport,
b1.city AS dep_city, 
t1.to AS arr_airport, 
b2.city AS arr_city, 
w.weekday,
t1.dep_time, t1.arr_time, 
t1.aircraft 
FROM (flight_schedule t1 JOIN airport as b1 ON t1.from = b1.code)
INNER JOIN (flight_schedule AS t2 JOIN airport AS b2 ON t2.to = b2.code) 
ON t1.flight_number = t2.flight_number
INNER JOIN flight_weekday AS w ON t1.flight_number = w.flight_number;

-- -----------------------------------------------------
-- View `evenstar_db`.`reservation_customer`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `evenstar_db`.`reservation_customer` ;
DROP TABLE IF EXISTS `evenstar_db`.`reservation_customer`;
USE `evenstar_db`;
CREATE  OR REPLACE VIEW `evenstar_db`.`reservation_customer` AS
SELECT passenger_id, F.flight_number, flight_date, dep_city, arr_city, status, dep_time, seat_number, aircraft 
FROM reservation AS R 
JOIN flight_customer AS F 
ON R.flight_number = F.flight_number 
AND R.flight_date = F.date
ORDER BY flight_date
DESC;
USE `evenstar_db`;

DELIMITER $$

USE `evenstar_db`$$
DROP TRIGGER IF EXISTS `evenstar_db`.`reservation_BINS` $$
USE `evenstar_db`$$


CREATE TRIGGER `reservation_BINS` BEFORE INSERT ON reservation FOR EACH ROW
-- Edit trigger body code below this line. Do not edit lines above this one
BEGIN
	UPDATE flight
	SET passenger_count = passenger_count + 1
	WHERE flight.flight_number = NEW.flight_number AND flight.date = NEW.flight_date;
END
$$


USE `evenstar_db`$$
DROP TRIGGER IF EXISTS `evenstar_db`.`reservation_BDEL` $$
USE `evenstar_db`$$


CREATE TRIGGER `reservation_BDEL` BEFORE DELETE ON reservation FOR EACH ROW
-- Edit trigger body code below this line. Do not edit lines above this one
BEGIN
	UPDATE flight
	SET passenger_count = passenger_count - 1
	WHERE flight.flight_number = OLD.flight_number AND flight.date = OLD.flight_date;
END

$$


USE `evenstar_db`$$
DROP TRIGGER IF EXISTS `evenstar_db`.`reservation_BUPD` $$
USE `evenstar_db`$$


CREATE TRIGGER `reservation_BUPD` BEFORE UPDATE ON reservation FOR EACH ROW
-- Edit trigger body code below this line. Do not edit lines above this one
BEGIN
	UPDATE flight
	SET passenger_count = passenger_count - 1
	WHERE flight.flight_number = OLD.flight_number AND flight.date = OLD.flight_date;
	UPDATE flight
	SET passenger_count = passenger_count + 1
	WHERE flight.flight_number = NEW.flight_number AND flight.date = NEW.flight_date;
END
$$


DELIMITER ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

