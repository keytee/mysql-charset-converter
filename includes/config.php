<?php
//
//    Configuration file for database charset converter
//
//    Copyright (C) 2018 Tibor Koleszar <kt@esh.hu>
//
//    Permission is hereby granted, free of charge, to any person obtaining a copy
//    of this software and associated documentation files (the "Software"), to deal
//    in the Software without restriction, including without limitation the rights
//    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
//    copies of the Software, and to permit persons to whom the Software is
//    furnished to do so, subject to the following conditions:
//
//    The above copyright notice and this permission notice shall be included in all
//    copies or substantial portions of the Software.
//
//    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
//    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
//    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
//    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
//    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
//    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
//    SOFTWARE.

// Delete this line after configured
print "Please configure first! Edit config.php in /includes dir.\n"; exit(0);

// Database connection
define("DB_USER", "testuser");
define("DB_PASS", "testpass");
define("DB_HOST", "127.0.0.1");

// New database name and collation
define("DB_NEWDB", "testnewdb");
define("DB_CHARSET", "utf8");
define("DB_COLLATION", "utf8_general_ci");

// Old/original database name and collation
define("DB_OLDDB", "testolddb");
define("DB_OLDCHARSET", "latin2");
define("DB_OLDCOLLATION", "latin2_hungarian_ci");

// Tables to skip
$tables_skip = [];

// Tables to no convert charset, simply copy the whole table with INSERT INTO xxx SELECT()
$tables_no_convert = [];

// Replace characters after iconv
$characters_replace = array(
	'from'	=> array('Û', 'Õ', 'û', 'õ'),
	'to'	=> array('Ű', 'Ő', 'ű', 'ő')
);

?>