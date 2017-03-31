<?php

namespace AppBundle\Model;

class FilePreprocessor
{
    function __construct($logger) {
        $this->logger = $logger;
    }

    public function toText($file_path){
        $output_file = "$file_path.txt";
        $pwd = realpath(dirname(__FILE__));
        $cmd = "java -jar $pwd/pdfbox.jar ExtractText $file_path $output_file";

        $locale='en_US.UTF-8';
        setlocale(LC_ALL,$locale);
        putenv('LC_ALL='.$locale);

        $output = shell_exec($cmd);
        $this->logger->debug($cmd);
        
        return $output_file;
    }

    public function toParagraph($file_path){
        $output_file = "$file_path.paragraph";

        $pwd = realpath(dirname(__FILE__));
        $cmd = "java -cp $pwd PDFSplitter $file_path $output_file";

        $locale='en_US.UTF-8';
        setlocale(LC_ALL,$locale);
        putenv('LC_ALL='.$locale);
        shell_exec($cmd);
        $this->logger->debug($cmd);

        $handle = fopen($output_file, "r");
        $lines = array();
        while (($line = fgets($handle)) !== false) {
            $lines[] = $line;
        }

        return $lines;
    }
}