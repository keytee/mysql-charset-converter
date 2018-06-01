# mysql-charset-converter
PHP Script to convert between two different character set database

The script uses iconv / str_replace to copy from source (old) database to the destination (new) all datas, tables, etc... The collation and the charset of text and varchar fields or tables will be changed.

# How to use:
1. edit `/includes/config.php`
2. execute `./mysql-charset-converter.php` from CLI or via web

# Configuration

All you can configure is in `/includes/config.php`

Database connection:

Define name | Meaning
----------- | -------
DB_USER | Usernam for the database connection
DB_PASS | Password
DB_HOST | Host of the mysql server (use `localhost` to connect via socket)

New database name and collation:

Define name | Meaning
----------- | -------
DB_NEWDB | Destination (new) database name
DB_CHARSET | Destination charset
DB_COLLATION | Destination collation

Old/source database name and collation

Define name | Meaning
----------- | -------
DB_OLDDB | Source (old) database name
DB_OLDCHARSET | Source (old) character set (not really used...)
DB_OLDCOLLATION | Source (old) character set (not really used...)

Tables to skip - list of tables which will NOT be copied from the source to the destination

`$tables_skip = [];`

 Tables to no convert charset, simply copy the whole table with `INSERT INTO xxx SELECT()`

`$tables_no_convert = [];`

Replace characters after iconv - NOTE the *after* word!

`$characters_replace = array(
        'from'  => array('Û', 'Õ', 'û', 'õ'),
        'to'    => array('Ű', 'Ő', 'ű', 'ő')
);`

