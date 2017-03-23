<?php

namespace AppBundle\Model;

class FilePreprocessor
{
    public function toText($file_path){
        $output_file = "$file_path.out";
        $pwd = realpath(dirname(__FILE__));
        $cmd = "java -jar $pwd/pdfbox.jar ExtractText $file_path $output_file";

        $locale='en_US.UTF-8';
        setlocale(LC_ALL,$locale);
        putenv('LC_ALL='.$locale);

        $output = shell_exec($cmd);

        return $output_file;
    }

    public function toParagraph($file_path){
        $pwd = realpath(dirname(__FILE__));
        $cmd = "java -jar $pwd/ParagraphSplitter.jar $file_path";

        $locale='en_US.UTF-8';
        setlocale(LC_ALL,$locale);
        putenv('LC_ALL='.$locale);

        $output = shell_exec($cmd);

        return $output ? json_decode($output)->content : array();
    }
}