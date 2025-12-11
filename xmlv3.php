<?php
$xml=simplexml_load_file('ies_db.xml') or die('Error no se cargo el xml.Escribe correctamente el nombre del archivo');

foreach($xml as $i_pe=>$pe){
    echo 'Codigo:'.$pe->codigo."<br>";
    echo 'Tipo:'.$pe->tipo."<br>";
    echo 'Nombre:'.$pe->nombre."<br>";
/*$consulta="INSERT INTO SIGI_PROGRAMAS_ESTUDIOS () VALUES";*/
    foreach ($pe->planes_estudio[0] as $i_ple => $plan){
echo'--'.$plan->nombre."<br>";
echo'--'.$plan->resolucion."<br>";
echo'--'.$plan->fecha_registro."<br>";

foreach ($plan->modulos_formativos[0] as $id_mod=>$modulo){
    echo'----'.$modulo->descripcion."<br>"; 
    echo'----'.$modulo->nro_modulo."<br>";
    
    foreach($modulo->periodos[0]as $id_per =>$per){
        echo'----'.$per->descripcion."<br>";

        foreach($per->unidades_didacticas[0]as $id_ud=>$ud){
            echo'----'.$ud->nombre."<br>";
            echo'----'.$ud->creditos_teorico."<br>";
            echo'----'.$ud->tipo."<br>";
            echo'----'.$ud->horas_semanal."<br>";
            echo'----'.$ud->horas_semestral."<br>";
        }
    }
}

    }
}

/*
echo $xml->pe_1->nombre."<br>";
echo $xml->pe_2->nombre;*/
?>