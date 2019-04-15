<?php

namespace ILAnt\Parser;

class Factory
{

    private static $instance = null;

    /**
     * Call this method to get singleton
     *
     * @return Factory
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

    }

    public function getParser( $url )
    {
        $urlData = parse_url ( $url );
        if( $urlData['host'] == 'esaj.tjsp.jus.br' )
        {
            return new TJSP( $url );
        }
    }
}
