<?php
/**
 *   @author: Javier Nieto Lorenzo
 *   @since: 03/12/2020
 *   Exportacion de Departamentos
 */

session_start(); // inicia una sesion, o recupera una existente
if(!isset($_SESSION['usuarioDAW217MtoDepartamentosTema5'])){ // si no se ha logueado le usuario
    header('Location: login.php'); // redireige a la pagina del login
    exit;
}

require_once '../config/confDBPDO.php';
try { // Bloque de código que puede tener excepciones en el objeto PDO
    $miDB = new PDO(DNS, USER, PASSWORD); // creo un objeto PDO con la conexion a la base de datos
    $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Establezco el atributo para la apariciopn de errores y le pongo el modo para que cuando haya un error se lance una excepcion
    $sql = "SELECT * FROM T02_Departamento";
    $resultadoConsulta = $miDB->prepare($sql); // prepara la consulta
    $resultadoConsulta->execute(); // ejecuta la consulta
    $documentoXML = new DOMDocument("1.0", "utf-8"); // creo el objeto de tipo DOMDocument que recibe 2 parametros: ela version y la codificacion del XML que queremos crear
    $documentoXML->formatOutput = true; // establece la salida formateada
    $root = $documentoXML->appendChild($documentoXML->createElement('Departamentos')); // creo el nodo raiz
    $oDepartamento = $resultadoConsulta->fetchObject(); // Obtengo el primer registro de la consulta como un objeto
    while ($oDepartamento) { // recorro los registros que devuelve la consulta y por cada uno de ellos ejecuto el codigo siguiente
        $departamento = $root->appendChild($documentoXML->createElement('Departamento')); // creo el nodo para el departamento 
        $departamento->appendChild($documentoXML->createElement('CodDepartamento', $oDepartamento->T02_CodDepartamento)); // añado como hijo el codigo de departamento con su valor
        $departamento->appendChild($documentoXML->createElement('DescDepartamento', $oDepartamento->T02_DescDepartamento)); // añado como hijo la descripcion del departamento con su valor
        $departamento->appendChild($documentoXML->createElement('FechaCreacion', $oDepartamento->T02_FechaCreacionDepartamento)); // añado como hijo la fecha de creacion del departamento con su valor
        $departamento->appendChild($documentoXML->createElement('FechaBaja', $oDepartamento->T02_FechaBajaDepartamento)); // añado como hijo la fecha de baja del departamento con su valor
        $departamento->appendChild($documentoXML->createElement('VolumenNegocio', $oDepartamento->T02_VolumenNegocio)); // añado como hijo el volumen de negocio del departamento con su valor
        $oDepartamento = $resultadoConsulta->fetchObject(); // guardo el registro actual como un objeto y avanzo el puntero al siguiente registro de la consulta
    }
    $fechaActual = date('Ymd');
    $documentoXML->save("../tmp/".$fechaActual."exportacionXMLLocal.xml"); // guarda el arbol XML en la ruta especificada con la fecha del dia que se ejecuta
    header('Content-Type: text/xml');
    header("Content-Disposition: attachment; filename=".$fechaActual."exportacionXMLLocal.xml");
    readfile("../tmp/".$fechaActual."exportacionXMLLocal.xml"); // mostrar desde el fichero del servidor en el navegador el documento xml si este no se descarga
} catch (PDOException $miExceptionPDO) { // Codigo que se ejecuta si hay alguna excepcion
    echo "<p style='color:red;'>Código de error: " . $miExceptionPDO->getCode() . "</p>"; // Muestra el codigo del error
    echo "<p style='color:red;'>Error: " . $miExceptionPDO->getMessage() . "</p>"; // Muestra el mensaje de error
    die(); // Finalizo el script
} finally { // codigo que se ejecuta haya o no errores
    unset($miDB); // destruyo la variable 
}
?>