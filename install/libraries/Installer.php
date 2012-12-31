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
    public $hostname;
    public $username;
    public $password;
    public $database;
    public $port;
    public $prefix;
    public $driver = null;
    public $encryption_key = null;
    private $_conn = null;

    public function __construct($db)
    {
        $this->CI =& get_instance();
        $this->hostname = $db['hostname'];
        $this->username = $db['username'];
        $this->password = $db['password'];
        $this->database = $db['database'];
        $this->port = $db['port'];
        $this->prefix = $db['prefix'];
    }

    public function test_db_connection()
    {
        if (function_exists('mysqli_connect'))
        {
            $mysqli = @new mysqli($this->hostname, $this->username, $this->password, $this->database, $this->port);

            if ($mysqli->connect_errno) 
            {
                throw new Exception("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
            }

            $mysqli->close();

            $this->driver = 'mysqli';
        }
        else if (function_exists('mysql_connect'))
        {
            $link = @mysql_connect($this->hostname . ':' . $this->port, $this->username, $this->password);

            if ( ! $link) 
            {
                throw new Exception('Failed to connect to MySQL: ' . mysql_error());
            }

            $db_selected = mysql_select_db($this->database, $link);

            if ( ! $db_selected)
            {
                throw new Exception('Failed to connect to MySQL: ' . mysql_error());
            }

            mysql_close($link);

            $this->driver = 'mysql';
        }
        else
        {
            throw new Exception('Unable to find MySQL on server.');
        }
    }

    public function write_db_config()
    {
        // Get database config template
        $template = file_get_contents(APPPATH . 'assets/config/database.php');

        $replace = array(
            '__HOSTNAME__'  => $this->hostname,
            '__USERNAME__'  => $this->username,
            '__PASSWORD__'  => $this->password,
            '__DATABASE__'  => $this->database,
            '__PORT__'      => $this->port,
            '__DRIVER__'    => $this->driver,
            '__PREFIX__'    => $this->prefix,
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

    public function write_ci_config()
    {
        $this->encryption_key = md5(uniqid('', true));

        $test_mod_rewrite = @file_get_contents(base_url() . '/step1');

        $index_page = 'index.php';

        if ($test_mod_rewrite !== FALSE)
        {
            $index_page = '';
        }

        // Get database config template
        $template = file_get_contents(APPPATH . 'assets/config/config.php');

        $replace = array(
            '__INDEX_PAGE__'      => $index_page,
            '__ENCRYPTION_KEY__'  => $this->encryption_key,
        );

        $template = str_replace(array_keys($replace), $replace, $template);

        $handle = @fopen(ROOT . 'application/config/config.php', 'w+');

        if ($handle !== FALSE)
        {
            $response = @fwrite($handle, $template);
            fclose($handle);

            if ($response)
            {
                return TRUE;
            }
        }

        throw new Exception('Failed to write to ' . ROOT . 'application/config/config.php');
    }

    public function import_schema()
    {
        $file = APPPATH . 'assets/schema/cmscanvas.sql';
    
        if ($sql = file($file)) 
        {
            $query = '';

            foreach($sql as $line) 
            {
                $tsl = trim($line);

                if (($sql != '') && (substr($tsl, 0, 2) != "--") && (substr($tsl, 0, 1) != '#')) 
                {
                    $query .= $line;
  
                    if (preg_match('/;\s*$/', $line)) 
                    {
                        $query = str_replace("DROP TABLE IF EXISTS `", "DROP TABLE IF EXISTS `" . $this->prefix, $query);
                        $query = str_replace("CREATE TABLE IF NOT EXISTS `", "CREATE TABLE IF NOT EXISTS `" . $this->prefix, $query);
                        $query = str_replace("CREATE TABLE `", "CREATE TABLE `" . $this->prefix, $query);
                        $query = str_replace("INSERT INTO `", "INSERT INTO `" . $this->prefix, $query);
                        
                        $this->db_query($query);
                        $query = '';
                    }
                }
            }
        }
    }

    public function insert_administrator($email, $password, $first_name, $last_name)
    {
        if (empty($this->encryption_key))
        {
            throw new Exception("No encryption key is set to generate a password.");
        }

        $password = md5($this->encryption_key . $password);
        $this->db_query("INSERT INTO `" . $this->prefix . "users` (`email`, `password`, `first_name`, `last_name`, `group_id`, `enabled`, `activated`, `created_date`) VALUES ('" . $this->db_escape($email) . "', '" . $this->db_escape($password) . "', '" . $this->db_escape($first_name) . "', '" . $this->db_escape($last_name) . "', 1, 1, 1, '" . date('Y-m-d H:i:s') . "')");
    }

    public function update_site_name($site_name)
    {
        $this->db_query("UPDATE `" . $this->prefix . "settings` SET `value` = '" . $this->db_escape($site_name) . "' WHERE `slug` = 'site_name'");
    }

    public function update_notification_email($notification_email)
    {
        $this->db_query("UPDATE `" . $this->prefix . "settings` SET `value` = '" . $this->db_escape($notification_email) . "' WHERE `slug` = 'notification_email'");
    }

    public function db_connect()
    {
        if (empty($this->driver))
        {
            throw new Exception('Unable to determine which MySQL driver to use.');
        }

        if ($this->driver == 'mysqli')
        {
            $this->_conn = new mysqli($this->hostname, $this->username, $this->password, $this->database, $this->port);
        }
        else if ($this->driver == 'mysql')
        {
            $this->_conn = mysql_connect($this->hostname . ':' . $this->port, $this->username, $this->password);
            mysql_select_db($this->database, $this->_conn);
        }
    }

    public function db_query($query)
    {
        if ($this->driver == 'mysqli')
        {
            $result = $this->_conn->query($query);

            if ( ! $result)
            {
                throw new Exception('Invalid Query: ' . $this->_conn->error);  
            }
        }
        else if ($this->driver == 'mysql')
        {
            $result = mysql_query($query, $this->_conn);

            if ( ! $result)
            {
                throw new Exception('Invalid Query: ' . mysql_error($this->_conn)); 
            }
        }
    }

    public function db_escape($string)
    {
        if ($this->driver == 'mysqli')
        {
            return $this->_conn->real_escape_string($string);
        }
        else if ($this->driver == 'mysql')
        {
            return mysql_real_escape_string($string, $this->_conn);
        }
    }

    public function db_close()
    {
        if ($this->driver == 'mysqli')
        {
            $this->_conn->close();
        }
        else if ($this->driver == 'mysql')
        {
            mysql_close($this->_conn);
        }
    }
}