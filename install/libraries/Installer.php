<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CMS Canvas
 *
 * @author      Mark Price
 * @copyright   Copyright (c) 2012
 * @license     MIT License
 * @link        http://cmscanvas.com
 */

class Installer
{
    public $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    public function test_db_connection()
    {
        $hostname = $this->CI->input->post('hostname');
        $username = $this->CI->input->post('username');
        $password = $this->CI->input->post('password');
        $database = $this->CI->input->post('database');
        $port = $this->CI->input->post('port');

        if (function_exists('mysqli_connect'))
        {
            $mysqli = @new mysqli($hostname, $username, $password, $database, $port);

            if ($mysqli->connect_errno) 
            {
                throw new Exception("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
            }

            $mysqli->close();

            return 'mysqli';
        }
        else if (function_exists('mysql_connect'))
        {
            $link = @mysql_connect($hostname . ':' . $port, $username, $password);

            if ( ! $link) 
            {
                throw new Exception('Failed to connect to MySQL: ' . mysql_error());
            }

            $db_selected = mysql_select_db($database, $link);

            if ( ! $db_selected)
            {
                throw new Exception('Failed to connect to MySQL: ' . mysql_error());
            }

            mysql_close($link);

            return 'mysql';
        }
        else
        {
            throw new Exception('Unable to find MySQL on server.');
        }
    }

    public function write_db_config($hostname, $username, $password, $database, $port, $prefix, $driver)
    {
        // Get database config template
        $template = file_get_contents(APPPATH . 'assets/config/database.php');

        $replace = array(
            '__HOSTNAME__'  => $hostname,
            '__USERNAME__'  => $username,
            '__PASSWORD__'  => $password,
            '__DATABASE__'  => $database,
            '__PORT__'      => $port,
            '__DRIVER__'    => $driver,
            '__PREFIX__'    => $prefix,
        );

        $template = str_replace(array_keys($replace), $replace, $template);

        $handle = @fopen(ROOT . 'application/config/database.php', 'w+');

        if ($handle !== FALSE)
        {
            $response = @fwrite($handle, $template);
            fclose($handle);

            if ($response)
            {
                return TRUE;
            }
        }

        throw new Exception('Failed to write to ' . ROOT . 'application/config/database.php');
    }

    public function import_schema()
    {

    }
}