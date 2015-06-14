<?php

// From 
// https://github.com/SicoAnimal/fuel-model-generator/blob/master/orm.php
// But not work with pdo connection.

class Util_OrmFromDB
{

	static $string_max_lengths = array(
		'char'       => 255,
		'varchar'    => 255,
		'tinytext'   => 255,
		'tinyblob'   => 255,
		'text'       => 65536,
		'blob'       => 65536,
		'mediumtext' => 16777216,
		'mediumblob' => 16777216,
		'longtext'   => 4294967296,
		'longblob'   => 4294967296,
	);

	static $data_typing_types = array(
		'varchar',
		'tinytext',
		'text',
		'mediumtext',
		'longtext',
		'enum',
		'set',
		'bool',
		'boolean',
		'tinyint',
		'smallint',
		'int',
		'integer',
		'mediumint',
		'bigint',
		'float',
		'double',
		'decimal',
		'serialize',
		'json',
		'time_unix',
		'time_mysql',
	);

	public function getmysqli()
	{
		$db_configs =Config::load('db', true);
		$connection = $db_configs["default"]["connection"];
		$dsn = $connection["dsn"];
		$re = "/mysql:host=([a-zA-Z0-1.]+);dbname=([a-zA-Z0-9_.]*)/"; 
 
		if (preg_match($re, $dsn, $matches)) {
	        /* host   = $matches[1] */  
		/* dbname = $matches[2] */
                  $mysqli = new mysqli($matches[1] ,$connection["username"],$connection["password"],$matches[2]);
		  if ($mysqli->connect_errno) {
    			echo "Echec lors de la connexion   MySQL : (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
		  }
		  return $mysqli;
		} else {
		  return null;
		}
	}

	public function getTable($mysqli)
        {
		$tables = array();
		$res = $mysqli->query(" SHOW TABLES ");
                for ($row_no = 0; $row_no< $res->num_rows ; $row_no++) {
			$res->data_seek($row_no);
    			$row = $res->fetch_assoc();
			foreach( $row as $r )
			{
			   $tables[]= $r;
			}
		}
           return $tables;
        }

	public static function getAllTableAndNone() 
	{
		$orm = new Util_OrmFromDB();
		$mysqli = $orm->getmysqli();
		$tables = $orm->getTable($mysqli);
		$result = array();
		$result[] = "";
		foreach( $tables as $t )
		{
		  $result[] = $t;
		}
		return $result;
	}

	public function run($db = null)
	{
		$mysqli = $this->getmysqli();
                $tables= $this->getTable($mysqli);
		foreach ($tables as $table)
		{
			$this->generate_model($table,$mysqli);
		}
	}

	public function generate_model($table_name,$db)
	{
		$columns = array();
		$res=$db->query("SHOW COLUMNS FROM $table_name");

                for ($row_no = 0; $row_no< $res->num_rows ; $row_no++) {
			$res->data_seek($row_no);
    			$row = $res->fetch_assoc();
			$columns[]= $row;
		}

		// Change if there s a S at the end to  no S.
		if (  $table_name[ strlen( $table_name ) - 1 ] =="s" )
		{
		  $table_class = preg_replace ("/s$/","",$table_name);
		} 
		else
		{
		  $table_class = $table_name;
		}

		// Generate the full path for the model
		$file_path = APPPATH.'classes'.DS.'model'.DS;
		$file_path .= str_replace('_', '/', strtolower($table_class)).'.php';

		if (file_exists($file_path))
		{
			//echo 'Model already found for database table '.$table_name;
			/*$answer = \Cli::prompt('Overwrite model?', array('y', 'n'));

			if ($answer == 'n')
			{
				\Cli::write('Existing model not overwritten.');
				return false;
			} */
		}

		//$columns = \DB::list_columns($table_name, null, $db);

		//echo 'Found '.count($columns)." columns for the {$table_name} database table.";

		$model_properties = array();
		foreach ($columns as $column)
		{
			// Process some of the column info to allow easier detection
			$arraydelnumber = explode("(", $column['Type'] );
			$column_type= strtolower ( $arraydelnumber[0] ); // Concatenated space stops an error happening when data_type has no spaces

			// A hack to detect Bool data types
			if ($column_type == 'tinyint' /* and $column['display'] == 1*/)
			{
				$column_type = 'bool';
			}

			// Basic Properties
			$column_properties = array(
				'data_type' => in_array($column_type, static::$data_typing_types) ? $column_type : 'string',
				'label'     => $column['Field'],
				'null'      => $column['Null'],
			);

			$column['Default'] and $column_properties['Default'] = $column['Default'];

			// Validation
			// TODO: Add thresholds rather than having rediculously high max values
			$column_validation = array();
			$column['Null'] or $column_validation[] = 'required';

			if ($column_type == 'bool')
			{
				//$column_validation = array('required');
			}
			elseif (key_exists($column_type, static::$string_max_lengths))
			{
				//$column_validation['max_length'] = array( (int) min($column['character_maximum_length'], static::$string_max_lengths[$column_type]));
			}
			elseif ($column['Type'] == 'int')
			{
				$display_max = (int) str_repeat(9, $column['display']);
				$column_validation['numeric_min'] = array( (int) $column['min']);
				$column_validation['numeric_max'] = array( (int) min($column['max'], $display_max));
			}
			elseif ($column['Type'] == 'float')
			{
				$max = (float) (str_repeat(9, $column['numeric_precision'] - $column['numeric_scale']).'.'.str_repeat(9, $column['numeric_scale']));
				$min = substr($column['data_type'], -8) == 'unsigned' ? 0 : $max * -1;
				$column_validation['numeric_min'] = array($min);
				$column_validation['numeric_max'] = array($max);
			}

			// Form
			$column_form = array('type' => 'text');

			if (in_array($column['Field'], array('id', 'created_at', 'updated_at')))
			{
				$column_form['type'] = false;
			}
			/* @TODO need to test whether these would be correctly datatyped when passed from the relevant form elements
			elseif (in_array($column['name'], array('password', 'email', 'url', 'date', 'time')))
			{
				$column_form['type'] = $column['name'];
			}*/
			else
			{
				$column['Default'] and $column_form['value'] = $column['Default'];

				switch ($column_type)
				{
					case 'char':
					case 'varchar':
					case 'tinytext':
					case 'tinyblob':
						isset($column_validation['max_length']) and $column_form['maxlength'] = $column_validation['max_length'][0];
						break;

					case 'text':
					case 'blob':
					case 'mediumtext':
					case 'mediumblob':
					case 'longtext':
					case 'longblob':
						$column_form['type'] = 'textarea';
						break;

					case 'enum':
					case 'set':
						$column_form['type'] = 'select';
						$column_form['options'] = array();
						break;

					case 'bool':
						$column_form['type'] = 'radio';
						$column_form['options'] = array(1 => 'Yes', 0 => 'No');
						break;


					case 'decimal':
					case 'double':
					case 'float':
						$column_form['step'] = floatval('0.'.str_repeat(9, $column['numeric_scale']));
						// break is intentionally missing

					case 'tinyint':
					case 'smallint':
					case 'int':
					case 'mediumint':
					case 'bigint':
						$column_form['type'] = 'number';
						isset($column_validation['numeric_min']) and $column_form['min'] = $column_validation['numeric_min'][0];
						isset($column_validation['numeric_max']) and $column_form['max'] = $column_validation['numeric_max'][0];
						break;

					/* @TODO
					case 'date':
					case 'datetime':
					case 'time':
					case 'timestamp':
						break;*/
				}
			}

			// validation doesn t work well. 
			//$column_properties['validation'] = $column_validation;
			$column_properties['form'] = $column_form;
			$model_properties[$column['Field']] = $column_properties;
		}

		$model_properties_str = str_replace(array("\n", '  ', 'array ('), array("\n\t", "\t", 'array('), \Format::forge($model_properties)->to_php());
		$model_properties_str = preg_replace('/=>\s+array/m', '=> array', $model_properties_str);

		$model_str = <<<MODEL
<?php

class Model_{$table_class} extends Model_Base
{

	protected static \$_table_name = '{$table_name}';

	protected static \$_properties = {$model_properties_str};

	protected static \$_observers = array(
		'Orm\Observer_Validation' => array(
            'events' => array('before_save'),
        ),
		'Orm\Observer_Typing' => array(
            'events' => array('before_save', 'after_save', 'after_load'),
        ),
MODEL;

		if (isset($model_properties['created_at']))
		{
			$model_str .= <<<MODEL

		'Orm\Observer_CreatedAt' => array(
	        'events' => array('before_insert'),
	        'mysql_timestamp' => false,
	        'property' => 'created_at',
	    ),
MODEL;
		}

		if (isset($model_properties['updated_at']))
		{
			$model_str .= <<<MODEL

		'Orm\Observer_UpdatedAt' => array(
	        'events' => array('before_save'),
	        'mysql_timestamp' => false,
	        'property' => 'updated_at',
	    ),
MODEL;
		}

		$model_str .= <<<MODEL

	);

}
MODEL;

	//echo $model_str;
		// Make sure the directory exists
		is_dir(dirname($file_path)) or mkdir(dirname($file_path), 0775, true);

		// Show people just how clever FuelPHP can be
	 	\File::update(dirname($file_path), basename($file_path), $model_str);

		return true;
	}

}
