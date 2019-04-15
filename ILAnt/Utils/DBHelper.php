<?php

namespace ILAnt\Utils;

class DBHelper
{

    private static $instance = null;
    private $link = null;

    /**
     * Call this method to get singleton
     *
     * @return DBHelper
     */
    public static function instance()
    {
        if ( !isset( static::$instance ) ) {
            static::$instance = new static;
        }
        return static::$instance;
    }
    /**
     * Private constructor so nobody else can clone it
     *
     */
    private function __clone(){}

    /**
     * Private constructor so nobody else can instance it
     *
     */
    private function __construct()
    {
        $this->link = mysqli_connect("127.0.0.1", "root", "start", "ilant");
        $this->link->set_charset("utf8mb4");
        if (!$this->link) {
            echo "Error: Unable to connect to MySQL." . PHP_EOL;
            echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
            echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
            exit;
        }
    }
    public function query( $sql )
    {
        return mysqli_query($this->link, $sql);
    }
    public function escapeString( $string )
    {
        return mysqli_real_escape_string( $this->link, $string );
    }
    public function arrayQuery( $query )
    {
        $results = [];
        $result = $this->link->query( $query );
        if( $result ) {

            /* fetch associative array */
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }

            /* free result set */
            $result->free();
        }
        return $results;
    }
}
