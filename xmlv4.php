<?php
// ======================= CONEXIÓN A LA BASE DE DATOS =========================
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "para_examen_01";

// Crear conexión
$conn = new mysqli($servername, $username, $password);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// ======================= VERIFICAR Y CREAR BASE DE DATOS =========================
$sql_check_db = "SHOW DATABASES LIKE '$dbname'";
$result_check_db = $conn->query($sql_check_db);

if ($result_check_db->num_rows == 0) {
    $sql_create_db = "CREATE DATABASE $dbname";
    if ($conn->query($sql_create_db) === TRUE) {
        echo "<p>Base de datos '$dbname' creada correctamente.</p>";
    } else {
        echo "<p>Error al crear la base de datos: " . $conn->error . "</p>";
    }
} else {
    echo "<p>La base de datos '$dbname' ya existe.</p>";
}

// Seleccionar la base de datos
$conn->select_db($dbname);

// ======================= VERIFICAR Y CREAR TABLAS =========================
// Tabla: sigi_programa_estudios
$sql_check_programa = "SHOW TABLES LIKE 'sigi_programa_estudios'";
$result_check_programa = $conn->query($sql_check_programa);

if ($result_check_programa->num_rows == 0) {
    $sql_programa = "
    CREATE TABLE IF NOT EXISTS sigi_programa_estudios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        codigo VARCHAR(10) NOT NULL,
        tipo VARCHAR(20) NOT NULL,
        nombre VARCHAR(100) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;
    ";
    $conn->query($sql_programa);
    echo "<p>Tabla 'sigi_programa_estudios' creada.</p>";
} else {
    echo "<p>La tabla 'sigi_programa_estudios' ya existe.</p>";
}

// Tabla: sigi_planes_estudio
$sql_check_planes = "SHOW TABLES LIKE 'sigi_planes_estudio'";
$result_check_planes = $conn->query($sql_check_planes);

if ($result_check_planes->num_rows == 0) {
    $sql_planes = "
    CREATE TABLE IF NOT EXISTS sigi_planes_estudio (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_programa_estudios INT NOT NULL,
        nombre VARCHAR(20) NOT NULL,
        resolucion VARCHAR(100) NOT NULL,
        fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        perfil_egresado VARCHAR(3000) NOT NULL,
        FOREIGN KEY (id_programa_estudios) REFERENCES sigi_programa_estudios(id) ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;
    ";
    $conn->query($sql_planes);
    echo "<p>Tabla 'sigi_planes_estudio' creada.</p>";
} else {
    echo "<p>La tabla 'sigi_planes_estudio' ya existe.</p>";
}

// Tabla: sigi_modulo_formativo
$sql_check_modulos = "SHOW TABLES LIKE 'sigi_modulo_formativo'";
$result_check_modulos = $conn->query($sql_check_modulos);

if ($result_check_modulos->num_rows == 0) {
    $sql_modulos = "
    CREATE TABLE IF NOT EXISTS sigi_modulo_formativo (
        id INT AUTO_INCREMENT PRIMARY KEY,
        descripcion VARCHAR(1000) NOT NULL,
        nro_modulo INT NOT NULL,
        id_plan_estudio INT NOT NULL,
        FOREIGN KEY (id_plan_estudio) REFERENCES sigi_planes_estudio(id) ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;
    ";
    $conn->query($sql_modulos);
    echo "<p>Tabla 'sigi_modulo_formativo' creada.</p>";
} else {
    echo "<p>La tabla 'sigi_modulo_formativo' ya existe.</p>";
}

// Tabla: sigi_semestre
$sql_check_semestres = "SHOW TABLES LIKE 'sigi_semestre'";
$result_check_semestres = $conn->query($sql_check_semestres);

if ($result_check_semestres->num_rows == 0) {
    $sql_semestres = "
    CREATE TABLE IF NOT EXISTS sigi_semestre (
        id INT AUTO_INCREMENT PRIMARY KEY,
        descripcion VARCHAR(5) NOT NULL,
        id_modulo_formativo INT NOT NULL,
        FOREIGN KEY (id_modulo_formativo) REFERENCES sigi_modulo_formativo(id) ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;
    ";
    $conn->query($sql_semestres);
    echo "<p>Tabla 'sigi_semestre' creada.</p>";
} else {
    echo "<p>La tabla 'sigi_semestre' ya existe.</p>";
}

// Tabla: sigi_unidad_didactica
$sql_check_unidades = "SHOW TABLES LIKE 'sigi_unidad_didactica'";
$result_check_unidades = $conn->query($sql_check_unidades);

if ($result_check_unidades->num_rows == 0) {
    $sql_unidades = "
    CREATE TABLE IF NOT EXISTS sigi_unidad_didactica (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(200) NOT NULL,
        id_semestre INT NOT NULL,
        creditos_teorico INT NOT NULL,
        creditos_practico INT NOT NULL,
        tipo VARCHAR(20) NOT NULL,
        orden INT NOT NULL,
        FOREIGN KEY (id_semestre) REFERENCES sigi_semestre(id) ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;
    ";
    $conn->query($sql_unidades);
    echo "<p>Tabla 'sigi_unidad_didactica' creada.</p>";
} else {
    echo "<p>La tabla 'sigi_unidad_didactica' ya existe.</p>";
}

// ======================= LEER Y PROCESAR XML =========================
$xmlFile = "ies_db.xml";
if (!file_exists($xmlFile)) {
    die("<p>El archivo XML '$xmlFile' no existe.</p>");
}

$xml = simplexml_load_file($xmlFile);
if ($xml === false) {
    die("<p>Error al cargar el archivo XML.</p>");
}

// ======================= VERIFICAR Y INSERTAR DATOS DESDE XML =========================
echo "<h2>Inserción de datos desde el XML:</h2>";

// Insertar programas de estudio
foreach ($xml->children() as $pe) {
    $codigo = $pe->codigo;
    $tipo = $pe->tipo;
    $nombre = $pe->nombre;

    // Verificar si el programa ya existe
    $sql_check_pe = "SELECT id FROM sigi_programa_estudios WHERE codigo = '$codigo'";
    $result_check_pe = $conn->query($sql_check_pe);

    if ($result_check_pe->num_rows == 0) {
        $sql_insert_pe = "INSERT INTO sigi_programa_estudios (codigo, tipo, nombre) VALUES ('$codigo', '$tipo', '$nombre')";
        if ($conn->query($sql_insert_pe) === TRUE) {
            $id_programa = $conn->insert_id;
            echo "<p>Programa de estudio insertado: <strong>$nombre</strong> (ID: $id_programa)</p>";
        } else {
            echo "<p>Error al insertar programa de estudio: " . $conn->error . "</p>";
        }
    } else {
        $id_programa = $result_check_pe->fetch_assoc()['id'];
        echo "<p>El programa de estudio <strong>$nombre</strong> ya existe (ID: $id_programa).</p>";
    }

    // Insertar planes de estudio
    foreach ($pe->planes_estudio->children() as $plan) {
        $nombre_plan = $plan->nombre;
        $resolucion = $plan->resolucion;
        $fecha_registro = $plan->fecha_registro;
        $perfil_egresado = $conn->real_escape_string($plan->perfil_egresado);

        // Verificar si el plan ya existe
        $sql_check_plan = "SELECT id FROM sigi_planes_estudio WHERE nombre = '$nombre_plan' AND id_programa_estudios = $id_programa";
        $result_check_plan = $conn->query($sql_check_plan);

        if ($result_check_plan->num_rows == 0) {
            $sql_insert_plan = "INSERT INTO sigi_planes_estudio (id_programa_estudios, nombre, resolucion, fecha_registro, perfil_egresado)
                                VALUES ($id_programa, '$nombre_plan', '$resolucion', '$fecha_registro', '$perfil_egresado')";
            if ($conn->query($sql_insert_plan) === TRUE) {
                $id_plan = $conn->insert_id;
                echo "<p>Plan de estudio insertado: <strong>$nombre_plan</strong> (ID: $id_plan)</p>";
                echo "<p>Perfil de egresado: <em>" . substr($perfil_egresado, 0, 100) . "...</em></p>";
            } else {
                echo "<p>Error al insertar plan de estudio: " . $conn->error . "</p>";
            }
        } else {
            $id_plan = $result_check_plan->fetch_assoc()['id'];
            echo "<p>El plan de estudio <strong>$nombre_plan</strong> ya existe (ID: $id_plan).</p>";
        }

        // Insertar módulos formativos
        foreach ($plan->modulos_formativos->children() as $modulo) {
            $descripcion = $conn->real_escape_string($modulo->descripcion);
            $nro_modulo = $modulo->nro_modulo;

            // Verificar si el módulo ya existe
            $sql_check_modulo = "SELECT id FROM sigi_modulo_formativo WHERE descripcion = '$descripcion' AND id_plan_estudio = $id_plan";
            $result_check_modulo = $conn->query($sql_check_modulo);

            if ($result_check_modulo->num_rows == 0) {
                $sql_insert_modulo = "INSERT INTO sigi_modulo_formativo (descripcion, nro_modulo, id_plan_estudio)
                                      VALUES ('$descripcion', $nro_modulo, $id_plan)";
                if ($conn->query($sql_insert_modulo) === TRUE) {
                    $id_modulo = $conn->insert_id;
                    echo "<p>Módulo formativo insertado: <strong>$descripcion</strong> (ID: $id_modulo)</p>";
                } else {
                    echo "<p>Error al insertar módulo formativo: " . $conn->error . "</p>";
                }
            } else {
                $id_modulo = $result_check_modulo->fetch_assoc()['id'];
                echo "<p>El módulo formativo <strong>$descripcion</strong> ya existe (ID: $id_modulo).</p>";
            }

            // Insertar semestres
            foreach ($modulo->periodos->children() as $periodo) {
                $descripcion_periodo = $periodo->descripcion;

                // Verificar si el semestre ya existe
                $sql_check_periodo = "SELECT id FROM sigi_semestre WHERE descripcion = '$descripcion_periodo' AND id_modulo_formativo = $id_modulo";
                $result_check_periodo = $conn->query($sql_check_periodo);

                if ($result_check_periodo->num_rows == 0) {
                    $sql_insert_periodo = "INSERT INTO sigi_semestre (descripcion, id_modulo_formativo)
                                           VALUES ('$descripcion_periodo', $id_modulo)";
                    if ($conn->query($sql_insert_periodo) === TRUE) {
                        $id_periodo = $conn->insert_id;
                        echo "<p>Semestre insertado: <strong>$descripcion_periodo</strong> (ID: $id_periodo)</p>";
                    } else {
                        echo "<p>Error al insertar semestre: " . $conn->error . "</p>";
                    }
                } else {
                    $id_periodo = $result_check_periodo->fetch_assoc()['id'];
                    echo "<p>El semestre <strong>$descripcion_periodo</strong> ya existe (ID: $id_periodo).</p>";
                }

                // Insertar unidades didácticas
                foreach ($periodo->unidades_didacticas->children() as $unidad) {
                    $nombre_unidad = $conn->real_escape_string($unidad->nombre);
                    $creditos_teorico = $unidad->creditos_teorico;
                    $creditos_practico = $unidad->creditos_practico;
                    $tipo = $unidad->tipo;

                    // Extraer el valor de "orden" del nombre del nodo (ej: "ud_1" -> orden = 1)
                    $nodo_nombre = $unidad->getName();
                    $orden = (int) str_replace('ud_', '', $nodo_nombre);

                    // Verificar si la unidad didáctica ya existe
                    $sql_check_unidad = "SELECT id FROM sigi_unidad_didactica WHERE nombre = '$nombre_unidad' AND id_semestre = $id_periodo";
                    $result_check_unidad = $conn->query($sql_check_unidad);

                    if ($result_check_unidad->num_rows == 0) {
                        $sql_insert_unidad = "INSERT INTO sigi_unidad_didactica
                                              (nombre, id_semestre, creditos_teorico, creditos_practico, tipo, orden)
                                              VALUES
                                              ('$nombre_unidad', $id_periodo, $creditos_teorico, $creditos_practico, '$tipo', $orden)";
                        if ($conn->query($sql_insert_unidad) === TRUE) {
                            echo "<p>Unidad didáctica insertada: <strong>$nombre_unidad</strong> (Semestre ID: $id_periodo, Orden: $orden)</p>";
                        } else {
                            echo "<p>Error al insertar unidad didáctica: " . $conn->error . "</p>";
                        }
                    } else {
                        echo "<p>La unidad didáctica <strong>$nombre_unidad</strong> ya existe (Semestre ID: $id_periodo).</p>";
                    }
                }
            }
        }
    }
}

echo "<h2>Proceso completado.</h2>";

// Cerrar conexión
$conn->close();
?>