<?php
$ies = [];

//------------------UNIDADES DIDÁCTICAS-----------------//
$udp1=[
'FUNDAMENTOS DE PROGRAMACIÓN',
'REDES E INTERNET',
'ANÁLISIS Y DISEÑO DE SISTEMAS',
'INTRODUCCIÓN DE BASE DE DATOS',
'ARQUITECTURA DE COMPUTADORAS',
'COMUNICACIÓN ORAL',
'APLICACIONES EN INTERNET'];

$udp2=[ 
'OFIMÁTICA',
'INTERPRETACIÓN Y PRODUCCIÓN TEXTOS',
'METODOLOGÍA DE DESARROLLO DE SOFTWARE',
'PROGRAMACIÓN ORIENTADA A OBJETOS',
'ARQUITECTURA DE SERVIDORES WEB',
'APLICACIONES SISTEMATIZADAS',
'TALLER DE BASE DE DATOS'];

$udp3=[
'ADMINISTRACIÓN DE BASE DE DATOS',
'PROGRAMACIÓN DE APLICACIONES WEB',
'DISEÑO DE INTERFACES WEB',
'PRUEBAS DE SOFTWARE',
'INGLÉS PARA LA COMUNICACIÓN ORAL'];

$udp4=[
'DESARROLLO DE ENTORNOS WEB',
'PROGRAMACIÓN DE SOLUCIONES WEB',
'PROYECTOS DE SOFTWARE',
'SEGURIDAD EN APLICACIONES WEB',
'COMPRENSIÓN Y REDACCIÓN EN INGLÉS',
'COMPORTAMIENTO ÉTICO'];

$udp5=[
'PROGRAMACIÓN DE APLICACIONES MÓVILES',
'MARKETING DIGITAL',
'DISEÑO DE SOLUCIONES WEB',
'GESTIÓN Y ADMINISTRACIÓN DE SITIOS WEB',
'DIAGRAMACIÓN DIGITAL',
'SOLUCIÓN DE PROBLEMAS',
'OPORTUNIDADES DE NEGOCIOS'];

$udp6=[
'PLATAFORMA DE SERVICIOS WEB',
'ILUSTRACIÓN Y GRÁFICA DIGITAL',
'ADMINISTRACIÓN DE SERVIDORES WEB',
'COMERCIO ELECTRÓNICO',
'PLAN DE NEGOCIOS'];

//------------------PERIODOS-----------------//
$p1=['nombre'=>"I", 'unidades_didacticas'=> $udp1];
$p2=['nombre'=>"II", 'unidades_didacticas'=> $udp2];
$p3=['nombre'=>"III", 'unidades_didacticas'=> $udp3];
$p4=['nombre'=>"IV", 'unidades_didacticas'=> $udp4]; // ← error corregido ($P4)
$p5=['nombre'=>"V", 'unidades_didacticas'=> $udp5];
$p6=['nombre'=>"VI", 'unidades_didacticas'=> $udp6];

//---------MODULOS-------//
$m1 = ['nombre'=>"ANÁLISIS Y DISEÑO DE SISTEMAS WEB", 'periodos'=>[$p1 ,$p2]];
$m2 = ['nombre'=>"DESARROLLO DE APLICACIONES WEB", 'periodos'=>[$p3 ,$p4]];
$m3 = ['nombre'=>"DISEÑO DE SERVICIOS WEB", 'periodos'=>[$p5 ,$p6]];

//-----------PROGRAMAS DE ESTUDIO------//
$pe1= ['nombre'=>"diseño y programacion web", 'modulos'=>[$m1, $m2, $m3]];
$pe2= ['nombre'=>"Enfermeria tecnica", 'modulos'=>[]];
$pe3= ['nombre'=>"industrias de alimentos y bebidas", 'modulos'=>[]];
$pe4= ['nombre'=>"mecanica automotriz", 'modulos'=>[]];
$pe5= ['nombre'=>"produccion agropecuaria", 'modulos'=>[]];

$ies['nombre']="IES Público HUANTA";
$ies['programas_estudio']=[$pe1, $pe2, $pe3, $pe4, $pe5];

//------------------------- CREAR XML --------------------------//
$xml = new DOMDocument('1.0','UTF-8');
$xml->formatOutput = true;

$et1 = $xml->createElement('ies');
$xml->appendChild($et1);

// Nombre IES
$nombre_ies = $xml->createElement("nombre",$ies['nombre']);
$programas_ies = $xml->createElement("programas_estudio");
$et1->appendChild($nombre_ies);
$et1->appendChild($programas_ies);

// PROGRAMAS DE ESTUDIO
foreach($ies["programas_estudio"] as $indicePE => $PEs) {

    $num_pe = $xml->createElement("pe".($indicePE+1));
    $nombre_pe = $xml->createElement("nombre",$PEs['nombre']);
    $num_pe->appendChild($nombre_pe);

    // MODULOS
    foreach ($PEs['modulos'] as $indice_modulo => $Modulo){
        $num_mod = $xml->createElement("mod".($indice_modulo+1));
        $nom_mod = $xml->createElement("nombre",$Modulo['nombre']);
        $num_mod->appendChild($nom_mod);

        // PERIODOS
        foreach ($Modulo['periodos'] as $indice_periodo=>$Periodo){
            $num_per = $xml->createElement("per".($indice_periodo+1));
            $nom_per = $xml->createElement("nombre",$Periodo['nombre']);
            $num_per->appendChild($nom_per);

            // Unidades Didácticas
            $uds = $xml->createElement("unidades_didacticas");

            foreach ($Periodo['unidades_didacticas'] as $indice_ud => $Ud){ 
                $num_ud = $xml->createElement("ud".($indice_ud+1));
                $nom_ud = $xml->createElement("nombre",$Ud);
                $num_ud->appendChild($nom_ud);
                $uds->appendChild($num_ud);
            }
            $num_per->appendChild($nom_per);
            $num_per->appendChild($uds);
            $num_mod->appendChild($num_per);
        }
        $num_mod->appendChild($nom_mod);
        $num_pe->appendChild($num_mod);
    }
    $num_pe->appendChild($nombre_pe);
    $programas_ies->appendChild($num_pe);
}

$archivo = "ies.xml";
$xml->save($archivo);

echo "XML generado correctamente";


?>
