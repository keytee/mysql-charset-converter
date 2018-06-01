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

	class BindParam{
		private $values = array(), $types = ''; 
		public function add( $t, &$v ){ 
			$this->values[] = $v;
			$this->types .= $t;
		}
		public function get(){
			$arr = array_merge(array($this->types), $this->values); 
			$refs = array();
			foreach($arr as $key => $value)
				$refs[$key] = &$arr[$key];
			return $refs;
		}
	}

	function convert_table_data($db, $db_old, $database_old, $database_new, $info, $no_convert = false) {

		global $characters_replace;

		$db_old->query("SET NAMES '".$info['charset']."'");
		$db_old->query("SET CHARACTER SET '".$info['charset']."'");
		$db_old->set_charset($info['charset']);


		// Get table info
		$info_new = get_table_info($db, $database_new, $info['name']);

		// Check field charsets
		$charset_same = true;
		foreach ($info['fields'] as $key=>$val) {
			if ($info_new['fields'][$key]['charset'] != $val['charset']) {
				$charset_same = false;
			}
		}

		if (($charset_same) || ($no_convert == true)) {
			print " no charset differ/no need to convert. copying, please wait...";
			$db->query("INSERT INTO ".$info['name']." SELECT * FROM `".$database_old."`.".$info['name']);
			print "done.";
			return;
		}

		$insert = "INSERT INTO ".$info['name']." VALUES (".rtrim(str_repeat("?,", count(array_keys($info['fields']))), ",").")";

		$result = $db_old->query("SELECT * FROM ".$database_old.".".$info['name']);

		if ($result) {
		
			$i = 0;

			while ($row = $result->fetch_object()) {

				$bp = new BindParam();
				$stm = $db->prepare($insert);

				foreach ($info['fields'] as $key=>$val) {
					if (($val['type'] == "varchar") || ($val['type'] == "text")) {
						if ($val['charset'] != "") {
							$row->{$key} = iconv($val['charset'], DB_CHARSET.'//IGNORE', $row->{$key});
							if (count($characters_replace['from']) > 0) {
								$row->{$key} = str_replace($characters_replace['from'], $characters_replace['to'], $row->{$key});
							}
						}
					}

					$bp->add('s', $row->{$key});
				}
				call_user_func_array(array($stm, 'bind_param'), $bp->get());
				
				$stm->execute();				$stm->close();

				$i++;
				if (!($i % 100)) {
					print "\r converting data ... $i / " . $info['rows'] . " - " . (number_format($i/$info['rows']*100 , 2, ".", "")) . " %";
				}
			}

			if ($i > 0) {
			    print "\r converting data ... $i / " . $info['rows'] . " - " . (number_format($i/$info['rows']*100 , 2, ".", "")) . " %";
			} else {
			    print "\r empty table. sckipping.";
			}

		}
		

	}

	function create_table($db, $database, $info) {

		$db->select_db($database);

		// Drop table
		$db->query("DROP TABLE IF EXISTS " . $info['name']);
		$db->query($info['create']);
		
		// TABLE COLLATION
		$db->query("ALTER TABLE ".$info['name']." CONVERT TO CHARACTER SET ".DB_CHARSET." COLLATE ".DB_COLLATION);

	}

	function get_table_fields($db, $database, $table) {

		$fields = [];

		$result = $db->query("SELECT * FROM information_schema.`COLUMNS` WHERE TABLE_SCHEMA = \"".$database."\" AND table_name = \"".$table."\"");

		if ($result) {
			while ($row = $result->fetch_object()){
				$field = [];
				$field['type'] = $row->{'DATA_TYPE'};
				$field['charset'] = $row->{'CHARACTER_SET_NAME'};
				$field['collation'] = $row->{'COLLATION_NAME'};
				$fields[$row->{'COLUMN_NAME'}] = $field;
			}
			$result->close();
		}

		echo mysqli_error($db);

		return $fields;
	}

	function get_table_create($db, $database, $table) {

		$create = "";

		$result = $db->query("SHOW CREATE TABLE `".$database."`.`".$table."`");

		if ($result) {
			while ($row = $result->fetch_object()){
				$create = $row->{'Create Table'};
			}
			$result->close();
		}

		return $create;
	}

	function get_table_collation($db, $database, $table) {

		$collation = "";

		$result = $db->query("SHOW TABLE STATUS FROM ".$database." WHERE NAME='".$table."'");

		if ($result) {
			while ($row = $result->fetch_object()){
				$collation = $row->{'Collation'};
			}
			$result->close();
		}

		return $collation;
	}

	function get_table_charset($db, $database, $table) {

		$charset = "";

		$result = $db->query("SELECT CCSA.character_set_name FROM information_schema.`TABLES` T, information_schema.`COLLATION_CHARACTER_SET_APPLICABILITY` CCSA WHERE CCSA.collation_name = T.table_collation AND T.table_schema = '".$database."' AND T.table_name = '".$table."'");
		if ($result) {
			while ($row = $result->fetch_object()){
				$charset = $row->{'character_set_name'};
			}
			$result->close();
		}

		return $charset;
	}

	function get_table_rowcount($db, $database, $table) {

		$rows = 0;

		$result = $db->query("SELECT count(*) as rowcount FROM ".$table);
		echo $db->error;
		if ($result) {
			while ($row = $result->fetch_array()){
				$rows  = intval($row[0]);
			}
			$result->close();
		}

		return $rows;
	}


	function get_table_list($db, $database) {

		$tables = [];

		$db->select_db($database);

		$result = $db->query("SHOW TABLES");

		if ($result) {
			while ($row = $result->fetch_array()){
				array_push($tables, $row[0]);
			}
			$result->close();
		}

		return $tables;
	}

	function get_table_info($db, $database, $table) {

		$info = [];

		$info['database'] = $database;
		$info['name'] = $table;
		$info['collation'] = get_table_collation($db, $database, $table);
		$info['charset'] = get_table_charset($db, $database, $table);
		$info['create'] = get_table_create($db, $database, $table);
		$info['fields'] = get_table_fields($db, $database, $table);
		$info['rows'] = get_table_rowcount($db, $database, $table);

		return $info;
	}


?>