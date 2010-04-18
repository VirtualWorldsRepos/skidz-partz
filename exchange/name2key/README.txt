INTRODUCTION:
-------------
Due to the nature of secondlife and the use of its keys we have come up with an solution,
to reuse existing databases to start off the preload keys for your website.
this means this will install the latest curruent files for avatar names and its keys into MYSQL database.
as these keys contain alot of keys be sure to have atleast 1 gig of space just for the name2key service.
if you wish to use this feature. 
without this feature you are unable to gift objects to an friend.

-------------------------------------------------------------------------------------------------------

first visit: http://w-hat.com/name2key and looking at the bottom of the page you will see csv snapshots
we will be using these to preload our database, and new keys can be added automatically from signup.

-------------------------------------------------------------------------------------------------------

upload the newly downloaded *.csv file into your name2key folder containing bigdump.php
please dont replace our bigdump with the offical release as this will not work as we have modified bigdump to support csv imports from w-hat.com

next open bigdump.php with your favorite editor and edit the following:

// Database configuration

$db_server   = 'localhost';
$db_name     = 'database_name';
$db_username = 'database_username';
$db_password = 'database_password';

// CSV Settings

// Specify the dump filename to suppress the file selection dialog
$filename           = 'name2key.csv';

// Destination table for CSV files
$csv_insert_table   = 'secondlife_name2key';

-------------------------------------------------------------------------------------------------------

Next lets run the installer this may take some time so please don't open new tabs in your browser as you will have to start over if the process fails.

visit http://yoursite.com/name2key/bigdump.php
