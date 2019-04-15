<?php

set_time_limit(0);
error_reporting(0);

include 'vendor/autoload.php';

use ILAnt\Parser\TJSP;
use ILAnt\Utils\DBHelper;

$parser = new TJSP('');

$dir = new DirectoryIterator( dirname('data/info_json/*') );
$limit = 10;
$count = 0;
foreach ($dir as $fileinfo) {
    if (!$fileinfo->isDot()) {
        $data = json_decode( file_get_contents( 'data/info_json/' . $fileinfo->getFilename() ), true );
        $db = DBHelper::instance();
        $id = $db->escapeString( $data['id'] );
        $code = $db->escapeString( $data['code'] );
        $classe = $db->escapeString( $data['Classe'] );
        $assunto = $db->escapeString( $data['Assunto'] );
        $magistrado = $db->escapeString( $data['Magistrado'] );
        $comarca = $db->escapeString( $data['Comarca'] );
        $foro = $db->escapeString( $data['Foro'] );
        $vara = $db->escapeString( $data['Vara'] );
        $date = $db->escapeString( date_create_from_format( 'd/m/Y',  $data['Data de Disponibilização'] )->setTime(11,0)->getTimestamp() );
        $sumario = $db->escapeString( $data['summary'] );
        $file = "data/pdf/{$id}.pdf";
        if( !file_exists($file) )
        {
            $parser->getDoc($code);
            if( !file_exists($file) )
            {
                $file = "";
                file_put_contents('pdf_not_found', $code . "\n", FILE_APPEND);
            }
        }
        $sql = "INSERT INTO processo( id, code, classe, assunto, magistrado, comarca, foro, vara, data, sumario, file ) VALUES ( "
                . "'{$id}', '{$code}', '{$classe}', '{$assunto}', '{$magistrado}', '{$comarca}', '{$foro}', '{$vara}', '{$date}', '{$sumario}', '{$file}'"
                . ")";
        $db->query($sql);
        $count++;
        echo "{$count}\n";
    }
}
