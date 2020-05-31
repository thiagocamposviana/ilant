for file in *.pdf; do pdftotext "$file" "$file.txt"; done
php clean.php