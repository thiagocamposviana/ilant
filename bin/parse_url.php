<?php

set_time_limit(0);
error_reporting(E_ERROR);

include 'vendor/autoload.php';

use ILAnt\Parser\Factory;
// Exemplo para sao carlos:
// php bin/parse_url.php https://esaj.tjsp.jus.br/cjpg/pesquisar.do?conversationId=&dadosConsulta.pesquisaLivre=&tipoNumero=UNIFICADO&numeroDigitoAnoUnificado=&foroNumeroUnificado=&dadosConsulta.nuProcesso=&dadosConsulta.nuProcessoAntigo=&classeTreeSelection.values=&classeTreeSelection.text=&assuntoTreeSelection.values=&assuntoTreeSelection.text=&agenteSelectedEntitiesList=&contadoragente=0&contadorMaioragente=0&cdAgente=&nmAgente=&dadosConsulta.dtInicio=&dadosConsulta.dtFim=&varasTreeSelection.values=555-1%2C566-7147%2C566-3%2C566-2%2C566-13%2C566-7148%2C566-11%2C566-4%2C566-9%2C566-7%2C566-5%2C566-10%2C566-1&varasTreeSelection.text=13+Registros+selecionados&dadosConsulta.ordenacao=DESC
// ibate https://esaj.tjsp.jus.br/cjpg/pesquisar.do;jsessionid=842B05A90B998A1B678936F40D80DB71.cjpg1?conversationId=&dadosConsulta.pesquisaLivre=&tipoNumero=UNIFICADO&numeroDigitoAnoUnificado=&foroNumeroUnificado=&dadosConsulta.nuProcesso=&dadosConsulta.nuProcessoAntigo=&classeTreeSelection.values=&classeTreeSelection.text=&assuntoTreeSelection.values=&assuntoTreeSelection.text=&agenteSelectedEntitiesList=&contadoragente=0&contadorMaioragente=0&cdAgente=&nmAgente=&dadosConsulta.dtInicio=&dadosConsulta.dtFim=&varasTreeSelection.values=233-1&varasTreeSelection.text=Vara+%C3%9Anica&dadosConsulta.ordenacao=DESC
// araraquara 2018 https://esaj.tjsp.jus.br/cjpg/pesquisar.do?conversationId=&dadosConsulta.pesquisaLivre=&tipoNumero=UNIFICADO&numeroDigitoAnoUnificado=&foroNumeroUnificado=&dadosConsulta.nuProcesso=&dadosConsulta.nuProcessoAntigo=&classeTreeSelection.values=&classeTreeSelection.text=&assuntoTreeSelection.values=&assuntoTreeSelection.text=&agenteSelectedEntitiesList=&contadoragente=0&contadorMaioragente=0&cdAgente=&nmAgente=&dadosConsulta.dtInicio=01%2F01%2F2018&dadosConsulta.dtFim=30%2F12%2F2018&varasTreeSelection.values=37-8%2C37-7%2C37-9%2C37-6%2C37-4%2C37-22%2C37-2%2C37-3%2C37-12%2C37-17%2C37-14%2C37-15%2C37-1%2C37-23%2C37-6918%2C37-5&varasTreeSelection.text=16+Registros+selecionados&dadosConsulta.ordenacao=DESC
$url = $argv[1];
$runData = @file_get_contents( 'cache_' . md5( $url ) );

if ($runData)
{
    $runData = unserialize($runData);
}
else
{
    $runData = array();
    $runData['page'] = 1;
}


$parser = Factory::instance()->getParser( $url );
while( $parser->getDocs($runData['page']) )
{
    $runData['page']++;
    echo "\nPage " . $runData['page'] . "\n";
    file_put_contents('cache_' . md5( $url ), serialize($runData));
}
