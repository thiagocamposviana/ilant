<?php

namespace ILAnt\Parser;

interface Parser
{
    public function getDocs( $page );
    public function getDoc( $code );
    public function store( $data );
}