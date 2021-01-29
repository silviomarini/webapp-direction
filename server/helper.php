<?php
    function formatDateAndTime($data){
        $split=explode(" ", $data);
        $split1=explode("-", $split[0]);
        $split2=explode(":", $split[1]);
        $data_visualizzata=$split1[2].'-'.$split1[1].'-'.$split1[0].' '.$split2[0].':'.$split2[1];
        return $data_visualizzata;
    }
    function formatTime($data){
        $split=explode(" ", $data);
        $split1=explode("-", $split[0]);
        $split2=explode(":", $split[1]);
        $data_visualizzata= $split2[0].':'.$split2[1];
        return $data_visualizzata;
    }
?>