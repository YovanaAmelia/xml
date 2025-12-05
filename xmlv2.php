<?php
$conexion= new mysqli("localhost","root","root","para_xml");
if($conexion->connect_errno){  
echo "fallo al conectar a MySQL:(" .$conexion->connect_errno.") ".$conexion->connect_error;
}
$xml = new DOMDocument('1.0','UTF-8');
$xml->formatOutput = true;
//programas de estudios
$et1 = $xml->createElement('programas_estudio');
$xml->appendChild($et1);

$consulta ="SELECT*FROM sigi_programa_estudios";
$resultado = $conexion->query($consulta);
while($pe=mysqli_fetch_assoc($resultado)){
//
    echo $pe['nombre']."<br>";
    $num_pe=$xml->createElement('pe_'.$pe['id']);
    $codigo_pe=$xml->createElement('codigo',$pe['codigo']);
    $num_pe->appendChild($codigo_pe);
    $tipo_pe=$xml->createElement('tipo',$pe['tipo']);
    $num_pe->appendChild($tipo_pe);
    $nombre_pe=$xml->createElement('nombre',$pe['nombre']);
    $num_pe->appendChild($nombre_pe);
   
//planes de estudio
   // Nodo principal <planes_estudio>
$et_planes = $xml->createElement('planes_estudio');
$xml->appendChild($et_planes);
$consulta = "SELECT * FROM sigi_planes_estudio";
$resultado = $conexion->query($consulta);
while($pe = mysqli_fetch_assoc($resultado)){

  
    $plan = $xml->createElement('plan_'.$pe['id']);
    $id_programa = $xml->createElement('id_programa_estudios', $pe['id_programa_estudios']);
    $plan->appendChild($id_programa);
    $nombre = $xml->createElement('nombre', $pe['nombre']);
    $plan->appendChild($nombre);
    $resolucion = $xml->createElement('resolucion', $pe['resolucion']);
    $plan->appendChild($resolucion);
    $fecha_registro = $xml->createElement('fecha_registro', $pe['fecha_registro']);
    $plan->appendChild($fecha_registro);

   

  
    $et_planes->appendChild($plan);


    
    }

    $et_planes->appendChild($plan);
    $et1->appendChild($num_pe);

}

$archivo = "ies_db.xml";
$xml->save($archivo);

?>