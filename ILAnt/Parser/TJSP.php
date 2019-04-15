<?php

namespace ILAnt\Parser;

use ILAnt\Utils\HTTPHelper;
use ILAnt\Utils\DBHelper;

class TJSP implements Parser
{

    private $pageURL = null;
    public function __construct( $pageURL )
    {
        $this->pageURL = $pageURL;
    }
    public function getDocs( $page = 1 )
    {
        file_put_contents('data/log', 'Page: ' . $page );
        $startPageResponse = HTTPHelper::resolveUrl( $this->pageURL );
        $currentPageURL = "https://esaj.tjsp.jus.br/cjpg/trocarDePagina.do?pagina={$page}&conversationId=";
        $currentPageResponse = $startPageResponse['client']->get( $currentPageURL )->send();
        $pageData = $currentPageResponse->getBody(true);
        $html = \str_get_html($pageData);
        $cases = $html->find('#tdResultados  .fundocinza1');
        if( count($cases) > 0 )
        {
            foreach( $cases as $case )
            {
                $caseInfo = [];
                $informations = $case->find('table .fonte');
                foreach( $informations as $information )
                {
                    $isLink = $information->find('a[title="Visualizar Inteiro Teor"]', 0);
                    if( $isLink )
                    {
                        $caseInfo['id'] = $isLink->getAttribute('name');
                        $caseInfo['code'] = trim( $isLink->plaintext );
                    }
                    else
                    {
                        $category = $information->find('strong', 0);
                        if( $category )
                        {
                            $caseInfo[trim( str_replace(':', '', $category->plaintext) )] = trim( str_replace( $category->plaintext, '', $information->plaintext ) );
                        }
                        else
                        {
                            $caseInfo['summary'] = trim( strip_tags( $information->plaintext ) );
                        }
                    }
                }
                // stores pdf data
                $docLink = $case->find('a[title="Visualizar Inteiro Teor"]', 0);
                $pdfFileName = $docLink->getAttribute('name');
                $pdfPath = 'data/pdf/' . $pdfFileName . '.pdf';
                if( !file_exists($pdfPath) )
                {
                    $resolverParams = explode( '-', $docLink->getAttribute('name') );
                    $resolverGetParams = [
                        'cdProcesso=' . urlencode( $resolverParams[0] ),
                        'cdForo=' .  urlencode( $resolverParams[1] ),
                        'nmAlias=' . urlencode( $resolverParams[2] ),
                        'cdDocumento=' . urlencode( $resolverParams[3] )
                    ];
                    $tempResponse = HTTPHelper::resolveUrl("https://esaj.tjsp.jus.br/cjpg/obterArquivo.do?" . implode( '&', $resolverGetParams ) );
                    $pdfViewerResponse = $tempResponse['client']->get( $tempResponse['url'] )->send();
                    $tempSTR = $pdfViewerResponse->getBody(true);
                    preg_match( '/var requestScope \= \[(.*)\];/i', $tempSTR, $matches );
                    $jsonData = json_decode( $matches[1],true );
                    $pdfFile = HTTPHelper::getData( 'https://esaj.tjsp.jus.br/pastadigital/getPDF.do?' . $jsonData['children'][0]['data']['parametros'] );
                    file_put_contents($pdfPath, $pdfFile );
                }
                // store db
                $this->store( $caseInfo );
            }
            return true;
        }
        else
        {
            return false;
        }
    }
    public function getDoc( $code )
    {
        $pageURL = "https://esaj.tjsp.jus.br/cjpg/pesquisar.do?conversationId=&dadosConsulta.pesquisaLivre=&tipoNumero=UNIFICADO&numeroDigitoAnoUnificado=0021265-49.1999&foroNumeroUnificado=0566&dadosConsulta.nuProcesso={$code}&dadosConsulta.nuProcessoAntigo=&classeTreeSelection.values=&classeTreeSelection.text=&assuntoTreeSelection.values=&assuntoTreeSelection.text=&agenteSelectedEntitiesList=&contadoragente=0&contadorMaioragente=0&cdAgente=&nmAgente=&dadosConsulta.dtInicio=&dadosConsulta.dtFim=&varasTreeSelection.values=&varasTreeSelection.text=&dadosConsulta.ordenacao=DESC";
        $startPageResponse = HTTPHelper::resolveUrl( $pageURL );
        $currentPageResponse = $startPageResponse['client']->get( $pageURL )->send();
        $pageData = $currentPageResponse->getBody(true);
        $html = \str_get_html($pageData);
        $cases = $html->find('#tdResultados  .fundocinza1');
        if( count($cases) > 0 )
        {
            foreach( $cases as $case )
            {
                $caseInfo = [];
                $informations = $case->find('table .fonte');
                foreach( $informations as $information )
                {
                    $isLink = $information->find('a[title="Visualizar Inteiro Teor"]', 0);
                    if( $isLink )
                    {
                        $caseInfo['id'] = $isLink->getAttribute('name');
                        $caseInfo['code'] = trim( $isLink->plaintext );
                    }
                    else
                    {
                        $category = $information->find('strong', 0);
                        if( $category )
                        {
                            $caseInfo[trim( str_replace(':', '', $category->plaintext) )] = trim( str_replace( $category->plaintext, '', $information->plaintext ) );
                        }
                        else
                        {
                            $caseInfo['summary'] = trim( strip_tags( $information->plaintext ) );
                        }
                    }
                }

                // stores pdf data
                $docLink = $case->find('a[title="Visualizar Inteiro Teor"]', 0);
                $pdfFileName = $docLink->getAttribute('name');
                $pdfPath = 'data/pdf/' . $pdfFileName . '.pdf';
                if( !file_exists($pdfPath) )
                {
                    $resolverParams = explode( '-', $docLink->getAttribute('name') );
                    $resolverGetParams = [
                        'cdProcesso=' . urlencode( $resolverParams[0] ),
                        'cdForo=' .  urlencode( $resolverParams[1] ),
                        'nmAlias=' . urlencode( $resolverParams[2] ),
                        'cdDocumento=' . urlencode( $resolverParams[3] )
                    ];
                    $tempResponse = HTTPHelper::resolveUrl("https://esaj.tjsp.jus.br/cjpg/obterArquivo.do?" . implode( '&', $resolverGetParams ) );
                    $pdfViewerResponse = $tempResponse['client']->get( $tempResponse['url'] )->send();
                    $tempSTR = $pdfViewerResponse->getBody(true);
                    preg_match( '/var requestScope \= \[(.*)\];/i', $tempSTR, $matches );
                    $jsonData = json_decode( $matches[1],true );
                    $pdfFile = HTTPHelper::getData( 'https://esaj.tjsp.jus.br/pastadigital/getPDF.do?' . $jsonData['children'][0]['data']['parametros'] );
                    file_put_contents($pdfPath, $pdfFile );
                }
            }
            return true;
        }
        else
        {
            return false;
        }
    }

    public function store( $data )
    {
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
            $this->getDoc($code);
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
    }

}
