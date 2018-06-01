#!/usr/bin/php
<?php
//
//    Drop tables from the new database based on the original one, just a helper script.
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

	require __DIR__."/includes/config.php";
	require __DIR__."/includes/functions.php";

	setlocale(LC_CTYPE, 'utf8');

	$tables = [];
	global $tables_skip;
	global $tables_no_convert;

	$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NEWDB);

	if(mysqli_connect_errno()){
		echo mysqli_connect_error();
	}

	print "Get tables...";
	$tables = get_table_list($db, DB_OLDDB);
	print count($tables) . " found\n\n";

	foreach ($tables as $key=>$table) {
		print "Dropping " . $table;
		$db->query("DROP TABLE `".DB_NEWDB."`.`".$table."`");
		print " done.\n";
	}

	$db->close();

?>