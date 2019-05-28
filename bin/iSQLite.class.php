<?php

/**
 * SQLite 数据库操作类
 *
 *
 * @author  <@spreevin9NB>
 * @site http://watchme4u.com
 * @licence http://watchme4u.com/license
 * @version 1.0.1
 * @package iDB
 */
class iSQLDB{
    public static $show_errors = false;
    public static $num_queries = 0;
    public static $last_query;
    public static $col_info;
    public static $queries;
    public static $func_call;
    public static $last_result;
    public static $num_rows;
    public static $insert_id;

    private static $collate;
    private static $time_start;
    private static $last_error ;
    private static $link;
    private static $result;


    /**
     *
     */
    public static function connect() {
		extension_loaded('PDO') OR die('您的 PHP 安装看起来缺少 PDO 部分，这对 iPHP 来说是必须的。');
		extension_loaded('PDO_SQLITE') OR die('您的 PHP 安装看起来缺少 PDO_SQLITE 部分，这对 iPHP 来说是必须的。');

        if (defined('iPHP_DB_COLLATE'))
            self::$collate = iPHP_DB_COLLATE;

		try {
		    //self::$link = new PDO('sqlsrv:server='.iPHP_SQL_SERVER.'; Database='.iPHP_SQL_DB, iPHP_SQL_USERNAME, iPHP_SQL_PASSWORD);
		    self::$link = mssql_connect(iPHP_SQL_SERVER, iPHP_SQL_USERNAME, iPHP_SQL_PASSWORD);
            $db =  mssql_select_db(iPHP_SQL_DB, self::$link );
		    //self::$link = new PDO('sqlite:'.iPHP_APP_CORE.'/'.iPHP_KEY.'.db', null, null, array(PDO::ATTR_PERSISTENT => true));
		} catch (PDOException $ex) {
		    die("<h1>数据库链接失败</h1><p>[Error] " . $ex->getMessage() . "</p>");
		}
        self::$link OR self::bail("<h1>数据库链接失败</h1><p>请检查 <em><strong>config.php</strong></em> 的配置是否正确!</p><ul><li>请确认主机支持SQLite3?</li><li>请确认用户名和密码正确?</li><li>请确认SQLite数据库是否存在?(一般在core目录下)</li></ul><p>如果你不确定这些情况,请询问你的主机提供商.如果你还需要帮助你可以随时浏览 <a href='http://watchme4u.com'>iPHP 支持论坛</a>.</p>");
		self::$link->query('PRAGMA encoding = "UTF-8"');
		self::$link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    // ==================================================================
    //	Print SQL/DB error.

    public static function print_error($str = '') {
        if (!$str){
            $info 	= self::$link->errorInfo();
            $str	= $info[2];
        }

        $EZSQL_ERROR[]	= array ('query' => self::$last_query, 'error_str' => $str);

        $str	= htmlspecialchars($str, ENT_QUOTES);
        $query	= htmlspecialchars(self::$last_query, ENT_QUOTES);
        // Is error output turned on or not..
        if ( self::$show_errors ) {
            // If there is an error then take note of it
            die("<div id='error'>
			<p class='iPHPDBerror'><strong>iPHP database error:</strong> [$str]<br />
			<code>$query</code></p>
			</div>");
        } else {
            return false;
        }
    }
    // ==================================================================
    //	Kill cached query results

    public static function flush() {
        self::$last_result	= array();
        self::$col_info		= null;
        self::$last_query	= null;
    }

    // ==================================================================
    //	Basic Query	- see docs for more detail

    public static function query($query,$QT=NULL) {
        if (!self::$link) {
            self::connect();
        }
        // filter the query, if filters are available
        // NOTE: some queries are made before the plugins have been loaded, and thus cannot be filtered with this method
        $query=str_replace(iPHP_DB_PREFIX_TAG,iPHP_DB_PREFIX, $query);

        // initialise return
        $return_val = 0;
        self::flush();

        // Log how the function was called
        self::$func_call = __CLASS__."::query(\"$query\")";

        // Keep track of the last query for debug..
        self::$last_query = $query;

        // Perform the query via std mysql_query function..
        if (SAVEQUERIES) self::timer_start();

		self::$result = mssql_query($query, self::$link);
        // If there is an error then take note of it..
        self::$last_error = self::$link->errorInfo();
        if (self::$last_error[0]!='00000') {
            self::print_error();
            return false;
        }
		self::$result->setFetchMode(PDO::FETCH_OBJ);
        self::$num_queries++;

        if (SAVEQUERIES) self::$queries[] = array( $query, self::timer_stop());

        $QH	= strtoupper(substr($query,0,strpos($query, ' ')));
        if (in_array($QH,array("INSERT","DELETE","UPDATE","REPLACE"))) {
            $rows_affected = self::$result->rowCount();
            // Take note of the insert_id
            if (in_array($QH,array("INSERT","REPLACE"))) {
                self::$insert_id = self::$link->lastInsertId();
            }
            // Return number of rows affected
            $return_val = $rows_affected;
        } else {
            if($QT=="field"){
                self::$col_info[] = self::$result->getColumnMeta();
            }else {
                $num_rows = 0;
                foreach  (self::$result AS $row){
                    self::$last_result[$num_rows] = $row;
                    $num_rows++;
                }
                // Log number of rows the query returned
                self::$num_rows = $num_rows;

                // Return number of rows selected
                $return_val = $num_rows;
            }
            //@mysql_free_result(self::$result);
        }

        return $return_val;
    }
    /**
     * Insert an array of data into a table
     * @param string $table WARNING: not sanitized!
     * @param array $data should not already be SQL-escaped
     * @return mixed results of self::query()
     */
    public static function insert($table, $data) {
//		$data = add_magic_quotes($data);
        $fields = array_keys($data);
        return self::query("INSERT INTO ".iPHP_DB_PREFIX_TAG."{$table} (`" . implode('`,`',$fields) . "`) VALUES ('".implode("','",$data)."')");
    }

    /**
     * Update a row in the table with an array of data
     * @param string $table WARNING: not sanitized!
     * @param array $data should not already be SQL-escaped
     * @param array $where a named array of WHERE column => value relationships.  Multiple member pairs will be joined with ANDs.  WARNING: the column names are not currently sanitized!
     * @return mixed results of self::query()
     */
    public static function update($table, $data, $where) {
//		$data = add_magic_quotes($data);
        $bits = $wheres = array();
        foreach ( array_keys($data) as $k )
            $bits[] = "`$k` = '$data[$k]'";

        if ( is_array( $where ) )
            foreach ( $where as $c => $v )
                $wheres[] = "$c = '" . addslashes( $v ) . "'";
        else
            return false;
        return self::query("UPDATE ".iPHP_DB_PREFIX_TAG."{$table} SET " . implode( ', ', $bits ) . ' WHERE ' . implode( ' AND ', $wheres ) . ' LIMIT 1' );
    }
    /**
     * Get one variable from the database
     * @param string $query (can be null as well, for caching, see codex)
     * @param int $x = 0 row num to return
     * @param int $y = 0 col num to return
     * @return mixed results
     */
    public static function value($query=null, $x = 0, $y = 0) {
        self::$func_call = __CLASS__."::value(\"$query\",$x,$y)";
        if ( $query )
            self::query($query);

        // Extract var out of cached results based x,y vals
        if ( !empty( self::$last_result[$y] ) ) {
            $values = array_values(get_object_vars(self::$last_result[$y]));
        }
        // If there is a value return it else return null
        return (isset($values[$x]) && $values[$x]!=='') ? $values[$x] : null;
    }

    /**
     * Get one row from the database
     * @param string $query
     * @param string $output ARRAY_A | ARRAY_N | OBJECT
     * @param int $y row num to return
     * @return mixed results
     */
    public static function row($query = null, $output = OBJECT, $y = 0) {
        self::$func_call = __CLASS__."::row(\"$query\",$output,$y)";
        if ( $query )
            self::query($query);

        if ( !isset(self::$last_result[$y]) )
            return null;

        if ( $output == OBJECT ) {
            return self::$last_result[$y] ? self::$last_result[$y] : null;
        } elseif ( $output == ARRAY_A ) {
            return self::$last_result[$y] ? get_object_vars(self::$last_result[$y]) : null;
        } elseif ( $output == ARRAY_N ) {
            return self::$last_result[$y] ? array_values(get_object_vars(self::$last_result[$y])) : null;
        } else {
            self::print_error(__CLASS__."::row(string query, output type, int offset) -- Output type must be one of: OBJECT, ARRAY_A, ARRAY_N");
        }
    }

    /**
     * Return an entire result set from the database
     * @param string $query (can also be null to pull from the cache)
     * @param string $output ARRAY_A | ARRAY_N | OBJECT
     * @return mixed results
     */
    public static function all($query = null, $output = ARRAY_A) {
        self::$func_call = __CLASS__."::array(\"$query\", $output)";

        if ( $query )
            self::query($query);

        // Send back array of objects. Each row is an object
        if ( $output == OBJECT ) {
            return self::$last_result;
        } elseif ( $output == ARRAY_A || $output == ARRAY_N ) {
            if ( self::$last_result ) {
                $i = 0;
                foreach( (array) self::$last_result as $row ) {
                    if ( $output == ARRAY_N ) {
                        // ...integer-keyed row arrays
                        $new_array[$i] = array_values( get_object_vars( $row ) );
                    } else {
                        // ...column name-keyed row arrays
                        $new_array[$i] = get_object_vars( $row );
                    }
                    ++$i;
                }
                return $new_array;
            } else {
                return null;
            }
        }
    }

    /**
     * Gets one column from the database
     * @param string $query (can be null as well, for caching, see codex)
     * @param int $x col num to return
     * @return array results
     */
    public static function col($query = null , $x = 0) {
        if ( $query )
            self::query($query);

        $new_array = array();
        // Extract the column values
        for ( $i=0; $i < count(self::$last_result); $i++ ) {
            $new_array[$i] = self::value(null, $x, $i);
        }
        return $new_array;
    }

    /**
     * Grabs column metadata from the last query
     * @param string $info_type one of name, table, def, max_length, not_null, primary_key, multiple_key, unique_key, numeric, blob, type, unsigned, zerofill
     * @param int $col_offset 0: col name. 1: which table the col's in. 2: col's max length. 3: if the col is numeric. 4: col's type
     * @return mixed results
     */
    public static function col_info($query = null ,$info_type = 'name', $col_offset = -1) {
        if ( $query )
            self::query($query,"field");

        if ( self::$col_info ) {
            if ( $col_offset == -1 ) {
                $i = 0;
                foreach(self::$col_info as $col ) {
                    $new_array[$i] = $col->{$info_type};
                    $i++;
                }
                return $new_array;
            } else {
                return self::$col_info[$col_offset]->{$info_type};
            }
        }
    }
    public static function version() {}

    /**
     * Starts the timer, for debugging purposes
     */
    public static function timer_start() {
        $mtime = microtime();
        $mtime = explode(' ', $mtime);
        self::$time_start = $mtime[1] + $mtime[0];
        return true;
    }

    /**
     * Stops the debugging timer
     * @return int total time spent on the query, in milliseconds
     */
    public static function timer_stop() {
        $mtime = microtime();
        $mtime = explode(' ', $mtime);
        $time_end = $mtime[1] + $mtime[0];
        $time_total = $time_end - self::$time_start;
        return $time_total;
    }

    /**
     * Wraps fatal errors in a nice header and footer and dies.
     * @param string $message
     */
    public static function bail($message){ // Just wraps errors in a nice header and footer
        if ( !self::$show_errors ) {
            return false;
        }
        trigger_error($message,E_USER_ERROR);
    }
}
