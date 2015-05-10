<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1252">
<title>EKMG Scheduler - LOAD</title>
</head>

<body bgcolor="#FFFF8F">
<? include "connect.php"; ?>

<?
echo "<br /><font size=5>Program start at " . date("Y-m-d h:i:s A") . "</font>\n";

// ----------------------------------------------------------------------

echo "<br /><font size=5>creating table events:</font>\n";

do_query("CREATE TABLE IF NOT EXISTS events ( x char(1) )");
do_query("DROP TABLE IF EXISTS events_new");

do_query("CREATE TABLE events_new (
	event_id		int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	event_code		varchar(20) NOT NULL,
	event_short		varchar(40) NOT NULL,
	event_long		varchar(100) NOT NULL,
	owner			varchar(20) NOT NULL,
	owner_email		varchar(100) NOT NULL,
	location		varchar(100) NOT NULL,
	status			varchar(20) NOT NULL,
	add_class_p		tinyint(1) NOT NULL,
	add_student_p	tinyint(1) NOT NULL,
	ek_eid			varchar(20) NOT NULL)
");

echo "<font size=3>loading data;</font>\n";
do_query("LOAD DATA
	LOCAL INFILE 'events.csv'
	INTO TABLE events_new
	FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
	LINES TERMINATED BY '\\n'
	IGNORE 1 LINES
	(event_id,event_code,event_short,event_long,
		owner,owner_email,location,
		status,add_class_p,add_student_p,
	ek_eid)
");

# echo "<font size=3>fixing columns;</font>\n";

# do_query("UPDATE ... ");

# do_query("ALTER TABLE ...");

echo "<font size=3>creating indexes;</font>\n";
do_query("ALTER TABLE events_new
	ADD INDEX(event_code),
	ADD INDEX(event_short),
	ADD INDEX(event_long),
	ADD INDEX(owner),
	ADD INDEX(location),
	ADD INDEX(status),
	ADD INDEX(add_class_p),
	ADD INDEX(add_student_p),
	ADD INDEX(ek_eid)
");

do_query("DROP TABLE IF EXISTS events_old");
do_query("RENAME TABLE events TO events_old, events_new TO events");

do_query("SELECT count(*) AS 'events' FROM events");		// should show the result

echo "<font size=3>done.</font>\n";

// ----------------------------------------------------------------------

echo "<br /><font size=6>NOT creating table teacher.</font>\n";

$file_contents = "event_id,teacher_name,teacher_mundane_name,address,email,phone,email_perm,address_perm,phone_perm,teacher_description,bringing_forge,bringing_books,anything_else";

$old_sql = "CREATE TABLE teacher_new (
	teacher_id				int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	event_id				int(11) NOT NULL,
	teacher_name			varchar(100) NOT NULL,
	teacher_mundane_name	varchar(100) NOT NULL,
	address					varchar(255) NOT NULL,
	email					varchar(100) NOT NULL,
	phone					varchar(50) NOT NULL,
	email_perm				tinyint(1) NOT NULL,
	address_perm			tinyint(1) NOT NULL,
	phone_perm				tinyint(1) NOT NULL,
	teacher_description		varchar(255) NOT NULL,
	bringing_forge			tinyint(1) NOT NULL,
	bringing_books			tinyint(1) NOT NULL,
	anything_else			varchar(255) NOT NULL)
";

// ----------------------------------------------------------------------

echo "<br /><font size=6>NOT creating table class.</font>\n";

$file_contents = "event_id,short_name,class_name,description,level,teacher_name,length,limit,cost,bring_with,need_from_us";

$old_sql = "CREATE TABLE class_new (
	class_id		int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	event_id		int(11) NOT NULL,
	short_name		varchar(30) NOT NULL,
	class_name		varchar(30) NOT NULL,
	description		varchar(255) NOT NULL,
	level			varchar(30) NOT NULL,
	teacher_name	varchar(50) NOT NULL,
	class_length	varchar(10) NOT NULL,
	class_limit		varchar(10) NOT NULL,
	cost			varchar(10) NOT NULL,
	bring_with		varchar(255) NOT NULL,
	need_from_us	varchar(255) NOT NULL)
";

// ----------------------------------------------------------------------

echo "<br /><font size=5>creating table schedule:</font>\n";

do_query("CREATE TABLE IF NOT EXISTS schedule ( x char(1) )");
do_query("DROP TABLE IF EXISTS schedule_new");

do_query("CREATE TABLE schedule_new (
	schedule_id			int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	event_id			int(11) NOT NULL,
	class_short_name	varchar(50) NOT NULL,
	day					int(3) NOT NULL,
	start_time			int(3) NOT NULL,
	class_length		int(3) NOT NULL,
	location			varchar(50) NOT NULL,
	comments			varchar(255) NOT NULL)
");

echo "<font size=3>loading data;</font>\n";
do_query("LOAD DATA
	LOCAL INFILE 'schedule.csv'
	INTO TABLE schedule_new
	FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
	LINES TERMINATED BY '\\n'
	IGNORE 1 LINES
	(event_id,class_short_name,day,start_time,class_length,location,comments)
");

# echo "<font size=3>fixing columns;</font>\n";

# do_query("UPDATE ... ");

# do_query("ALTER TABLE ...");

echo "<font size=3>creating indexes;</font>\n";
do_query("ALTER TABLE schedule_new
	ADD INDEX(event_id),
    ADD INDEX(class_short_name),
    ADD INDEX(day),
    ADD INDEX(start_time),
    ADD INDEX(class_length),
    ADD INDEX(location),
    ADD INDEX(comments)
");

do_query("DROP TABLE IF EXISTS schedule_old");
do_query("RENAME TABLE schedule TO schedule_old, schedule_new TO schedule");

do_query("SELECT count(*) AS 'schedule' FROM schedule");		// should show the result

echo "<font size=3>done.</font>\n";

// ----------------------------------------------------------------------

echo "<br /><font size=5>creating table days:</font>\n";

do_query("CREATE TABLE IF NOT EXISTS days ( x char(1) )");
do_query("DROP TABLE IF EXISTS days_new");

do_query("CREATE TABLE days_new (
	days_id		int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	event_id	int(11) NOT NULL,
	day_num		int(3) NOT NULL,
	day_abbr	varchar(10) NOT NULL,
	day_short	varchar(20) NOT NULL,
	day_long	varchar(50) NOT NULL)
");

echo "<font size=3>loading data;</font>\n";
do_query("LOAD DATA
	LOCAL INFILE 'days.csv'
	INTO TABLE days_new
	FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
	LINES TERMINATED BY '\\n'
	IGNORE 1 LINES
	(event_id,day_num,day_abbr,day_short,day_long)
");

# echo "<font size=3>fixing columns;</font>\n";

# do_query("UPDATE ... ");

# do_query("ALTER TABLE ...");

echo "<font size=3>creating indexes;</font>\n";
do_query("ALTER TABLE days_new
	ADD INDEX(event_id),
    ADD INDEX(day_num),
    ADD INDEX(day_abbr),
    ADD INDEX(day_short),
    ADD INDEX(day_long)
");

do_query("DROP TABLE IF EXISTS days_old");
do_query("RENAME TABLE days TO days_old, days_new TO days");

do_query("SELECT count(*) AS 'days' FROM days");		// should show the result

echo "<font size=3>done.</font>\n";

// ----------------------------------------------------------------------

echo "<br /><font size=5>creating table timeslots:</font>\n";

do_query("CREATE TABLE IF NOT EXISTS timeslots ( x char(1) )");
do_query("DROP TABLE IF EXISTS timeslots_new");

do_query("CREATE TABLE timeslots_new (
	timeslots_id	int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	event_id		int(11) NOT NULL,
	day_num			int(3) NOT NULL,
	start_time		int(3) NOT NULL,
	english			varchar(20) NOT NULL)
");

echo "<font size=3>loading data;</font>\n";
do_query("LOAD DATA
	LOCAL INFILE 'timeslots.csv'
	INTO TABLE timeslots_new
	FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
	LINES TERMINATED BY '\\n'
	IGNORE 1 LINES
	(event_id,day_num,start_time,english)
");

# echo "<font size=3>fixing columns;</font>\n";

# do_query("UPDATE ... ");

# do_query("ALTER TABLE ...");

echo "<font size=3>creating indexes;</font>\n";
do_query("ALTER TABLE timeslots_new
	ADD INDEX(event_id),
    ADD INDEX(day_num),
    ADD INDEX(start_time),
    ADD INDEX(english)
");

do_query("DROP TABLE IF EXISTS timeslots_old");
do_query("RENAME TABLE timeslots TO timeslots_old, timeslots_new TO timeslots");

do_query("SELECT count(*) AS 'timeslots' FROM timeslots");		// should show the result

echo "<font size=3>done.</font>\n";

// ----------------------------------------------------------------------

echo "<br /><font size=5>creating table locations:</font>\n";

do_query("CREATE TABLE IF NOT EXISTS locations ( x char(1) )");
do_query("DROP TABLE IF EXISTS locations_new");

do_query("CREATE TABLE locations_new (
	location_id	int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	event_id	int(11) NOT NULL,
	location	varchar(30) NOT NULL,
	comments	varchar(255) NOT NULL)
");

echo "<font size=3>loading data;</font>\n";
do_query("LOAD DATA
	LOCAL INFILE 'locations.csv'
	INTO TABLE locations_new
	FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
	LINES TERMINATED BY '\\n'
	IGNORE 1 LINES
	(event_id,location,comments)
");

# echo "<font size=3>fixing columns;</font>\n";

# do_query("UPDATE ... ");

# do_query("ALTER TABLE ...");

echo "<font size=3>creating indexes;</font>\n";
do_query("ALTER TABLE locations_new
	ADD INDEX(event_id),
    ADD INDEX(location),
    ADD INDEX(comments)
");

do_query("DROP TABLE IF EXISTS locations_old");
do_query("RENAME TABLE locations TO locations_old, locations_new TO locations");

do_query("SELECT count(*) AS 'locations' FROM locations");		// should show the result

echo "<font size=3>done.</font>\n";

// ----------------------------------------------------------------------

echo "<br /><font size=5>Program stop at " . date("Y-m-d h:i:s A") . "</font>\n";
?>

</body>
</html>
