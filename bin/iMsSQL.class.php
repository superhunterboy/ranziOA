<?php
/**
 * Created by PhpStorm.
 * User: goslin
 * Date: 2018/11/19
 * Time: 14:48
 */

class MSSQLDB
{
    var $db_connection;

    // Constructor
    function __construct()
    {
        $this->db_connection    = mssql_connect(iPHP_SQL_SERVER, iPHP_SQL_USERNAME,iPHP_SQL_PASSWORD)
        or die('Could not connect to '.iPHP_SQL_SERVER.' server');

        mssql_select_db(iPHP_SQL_DB)
        or die('Could not select to '.iPHP_SQL_DB.' database');
    }

    // Generic query function
    function query_database($query)
    {
        // Always include the link identifier (in this case $this->db_connection) in mssql_query
        $query_result     = mssql_query($query, $this->db_connection)
        or die('Query failed: '.$query);

        if (strpos($query, 'insert') === false)
        {
            // fetch the results as an array
            $result            = array();
            while ($row = mssql_fetch_object($query_result))
            {
                $result[]    = $row;
            }

            // dispose of the query
            mssql_free_result($query_result);

            // return result
            return $result;
        }
        else
        {
            // dispose of the query
            mssql_free_result($query_result);

            // get the last insert id
            $query            = 'select SCOPE_IDENTITY() AS last_insert_id';
            $query_result     = mssql_query($query)
            or die('Query failed: '.$query);

            $query_result    = mssql_fetch_object($query_result);

            mssql_free_result($query_result);

            return $query_result->last_insert_id;
        }
    }

}