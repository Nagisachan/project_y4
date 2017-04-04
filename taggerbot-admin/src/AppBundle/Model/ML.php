<?php

namespace AppBundle\Model;


class ML
{
    function __construct($logger) {
        $this->logger = $logger;
    }

    public function azureml_predict($url,$key,$paragraphs){
        $newParagraphs = array();
        foreach($paragraphs as $paragraph){
            $newParagraphs[] = '"' . preg_replace('/"/',"'",$paragraph['content']) . '"';
        }

        $pwd = realpath(dirname(__FILE__));
        $cmd = "python $pwd/azureml.py '$url' '$key' " . implode(' ',$newParagraphs);

        $ret = json_decode(shell_exec($cmd));
        // $ret = json_decode('[{"Results":{"output1":{"type":"table","value":{"ColumnNames":["ScoredLabels","ScoredProbabilities"],"ColumnTypes":["Double","Double"],"Values":[["19.1","0.999567747116089"],["19.1","0.998714745044708"],["19.1","0.998138248920441"],["19.1","0.990875124931335"],["19.1","0.999539732933044"],["19.1","0.999224424362183"],["19.1","0.999411702156067"],["19.1","0.999554395675659"],["19.1","0.992735028266907"],["19.1","0.717194616794586"],["19.1","0.998971164226532"],["19.1","0.997676610946655"],["19.1","0.999614417552948"],["19.1","0.907475411891937"],["19.1","0.998333513736725"],["19.1","0.999332189559937"],["19.1","0.951090097427368"],["19.1","0.997485220432281"],["19.1","0.998674690723419"],["19.1","0.999038100242615"],["19.1","0.995681524276733"],["19.1","0.999812960624695"],["19.1","0.838428616523743"],["19.1","0.52249276638031"],["19.1","0.998615741729736"],["19.1","0.999346196651459"],["19.1","0.998660027980804"],["0","0.13287241756916"],["0","0.0818666815757751"]]}}}}]');

        return $ret;
    }

    public function predect(){

    }
}