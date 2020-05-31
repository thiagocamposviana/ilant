<?php

function cleanDoc( $filePath )
{
    $content = file_get_contents($filePath);

    echo "$filePath\n\n\n";
    $patterns = [
        '/Este documento é cópia do original, assinado digitalmente(.*)\n/m', // assinatura
        '/Para conferir o original, acesse o site https:\/\/esaj(.*)\n/m', // assinatura
        '/\n\n[0-9](.*) - lauda [0-9]+\n/i', // parte do footer
        '/\n[0-9\.-]+\n/m', 
        '/\n[A-Z0-9 ]+\n/m',
        '/fls\. [0-9]+\n/m',
        '/(^|\n)[A-Z][A-Z0-9\sÀ-ú\.\/ªº-]+(\n)/m',
        '/(^|\n)DOCUMENTO ASSINADO DIGITALMENTE NOS TERMOS DA(.*)(\n|$)/m',
        
        ];
    
    $content = preg_replace($patterns, "\n", $content);
    // remove completely all duplicated lines
    $lines = explode("\n", $content);
    $counts = array_count_values($lines);
    foreach($counts as $value => $cnt)
    {
        if( $cnt > 1 )
        {
            foreach( $lines as $index => $line )
            {
                if( $line == $value )
                {
                    unset($lines[$index]);
                }
            }
        }
    }
    // remove classification lines, example "Processo:" or empty lines
    foreach( $lines as $index => $line )
    {
        if( (preg_match( '/^[A-Z](.*):$/', $line ) && strlen($line) < 30) || preg_match( '/[A-Z0-9À-ú\.\/ªº-]/i', $line ) !== 1 )
        {
            unset($lines[$index]);
        }
    }
    $lines = array_values(array_unique($lines));

    // ver se ultima linha é local e data
    if( preg_match( '/(.*) de [0-9]+\.$/', $lines[count($lines)-1] ) )
    {
         array_pop($lines);
    }
    $content = implode("\n", $lines);
    // lets join paragraphs
    $content = preg_replace('/([a-zÀ-ú\,])\n([a-zÀ-ú ])/m', "$1 $2", $content);
    
    file_put_contents($filePath, $content);


}
// for file in *.pdf; do pdftotext "$file" "$file.txt"; done

if ($handle = opendir('.')) {

    while (false !== ($entry = readdir($handle))) {

        if ($entry != "." && $entry != "..") {
            if( pathinfo($entry, PATHINFO_EXTENSION) === 'txt' )
            {
                cleanDoc( $entry );
            }
            else if( pathinfo($entry, PATHINFO_EXTENSION) === 'pdf' )
            {
                unlink($entry);
            }
        }
    }
    closedir($handle);
}