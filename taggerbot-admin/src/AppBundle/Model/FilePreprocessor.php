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

    public function toTextDocx($file_path){
        $output_file = "$file_path.txt";
        $pwd = realpath(dirname(__FILE__));
        $cmd = "$pwd/extract_docx.sh $file_path $output_file";

        $locale='en_US.UTF-8';
        setlocale(LC_ALL,$locale);
        putenv('LC_ALL='.$locale);

        $output = shell_exec($cmd);
        $this->logger->debug($file_path);
        $this->logger->debug($output_file);
        $this->logger->debug($cmd);
        $this->logger->debug($output);
        
        return $output_file;
    }

    public function toTextTxt($file_path){
        $output_file = "$file_path.txt";
        $pwd = realpath(dirname(__FILE__));
        $cmd = "$pwd/extract_docx.sh $file_path $output_file";

        $locale='en_US.UTF-8';
        setlocale(LC_ALL,$locale);
        putenv('LC_ALL='.$locale);

        #$output = shell_exec($cmd);
        $this->logger->debug($file_path);
        $this->logger->debug($output_file);
        $this->logger->debug($cmd);
        #$this->logger->debug($output);
        
        return $file_path;
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

    public function toParagraphSimple($file_path){
        $this->logger->debug($file_path);
        $handle = fopen($file_path, "r");
        $lines = array();
        while (($line = fgets($handle)) !== false) {
            if(strlen($line) > 300){
                $lines[] = $line;
            }
        }

        return $lines;
    }

    public function crawlUrl($url,$output_file){
        $pwd = realpath(dirname(__FILE__));
        $cmd = "$pwd/wiki.R $url $output_file 2>&1";
        $output = shell_exec($cmd);
        $this->logger->debug($cmd);
        $this->logger->debug($output);
    }
}