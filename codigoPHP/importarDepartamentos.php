<?php
/**
 *   @author: Javier Nieto Lorenzo
 *   @since: 18/11/2020
 *   Importar Departamentos
 */


session_start(); // inicia una sesion, o recupera una existente
if(!isset($_SESSION['usuarioDAW217MtoDepartamentosTema5'])){ // si no se ha logueado le usuario
    header('Location: login.php'); // redireige a la pagina del login
    exit;
}

require_once '../config/config.php'; // incluyo el fichero de configuracion de la aplicacion
if (isset($_REQUEST['Cancelar'])) { // si se ha pulsado el boton cancelar
    header('Location: mtoDepartamentos.php'); // redirige a la pagina principal de la aplicacion
    exit;
}

require_once '../config/confDBPDO.php'; // incluyo el fichero de configuracion de acceso a la basde de datos

$entradaOK = true;

$errorArchivo = null;

if (isset($_REQUEST['Aceptar'])) { // si se ha pulsado Aceptar

    if ($_FILES['archivo']['type'] != 'text/xml') { // comprueba si el fichero no es de tipo xml
        $errorArchivo = "El fomato de archivo debe ser .xml"; // guarda un mensaje en la variable de error del archivo
    }
    
    if($errorArchivo!=null){ // si hay algun error
        $entradaOK=false;
    }
}else{
    $entradaOK=false;
}

if ($entradaOK) {
    try { // Bloque de código que puede tener excepciones en el objeto PDO
        $miDB = new PDO(DNS, USER, PASSWORD); // creo un objeto PDO con la conexion a la base de datos
        $file_name = $_FILES['archivo']['tmp_name'];
        $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Establezco el atributo para la apariciopn de errores y le pongo el modo para que cuando haya un error se lance una excepcion
        $fechaActual = date('Ymd');
        move_uploaded_file($file_name, '../tmp/' . $fechaActual . 'importarXML.xml');
        $documentoXML = new DOMDocument("1.0", "utf-8"); // creo el objeto de tipo DOMDocument que recibe 2 parametros: la version y la codificacion del XML que queremos crear
        $documentoXML->load('../tmp/' . $fechaActual . 'importarXML.xml');
        
        $sqlBorrarDepartamentos = 'TRUNCATE TABLE T02_Departamento';
        
        $consultaBorrarDepartamentos = $miDB->prepare($sqlBorrarDepartamentos); // prepara la consulta
        
        $consultaBorrarDepartamentos->execute(); // ejecuta la consulta
        

        $sqlInserccion = 'INSERT INTO T02_Departamento VALUES (:CodDepartamento, :DescDepartamento,:FechaCreacion,:VolumenNegocio,:FechaBaja)';

        $consultaInserccion = $miDB->prepare($sqlInserccion); // preparta la consulta

        $nDepartamentos = $documentoXML->getElementsByTagName('Departamento')->length; // saco cuantos departamentos hay

        for ($nDepartamento = 0; $nDepartamento < $nDepartamentos; $nDepartamento++) { //recorro los departamentos
        $departamento = $documentoXML->getElementsByTagName('Departamento')->item($nDepartamento)->childNodes;
        
        // guarda los valores impares hasta 9, debido a que en los valores pares se almacena un espacio en blanco y en los impares el valor del nodo
        $parametros = [":CodDepartamento" => $departamento->item(1)->nodeValue,
            ":DescDepartamento" => $departamento->item(3)->nodeValue,
            ":FechaCreacion" => $departamento->item(5)->nodeValue,
            ":FechaBaja" => $departamento->item(7)->nodeValue,
            ":VolumenNegocio" => $departamento->item(9)->nodeValue
        ];
        
        if (empty($parametros[':FechaBaja'])) { // si la fecha de baja esta vacia
            $parametros[':FechaBaja'] = null; // establece el parametro fecha de baja a null
        }

        $consultaInserccion->execute($parametros); // ejecuta la consulta
        }
        header('Location: mtoDepartamentos.php'); // redirige a la pagina principal de la aplicacion
    } catch (PDOException $miExceptionPDO) { // Codigo que se ejecuta si hay alguna excepcion
        echo "<p style='color:red;'>Código de error: " . $miExceptionPDO->getCode() . "</p>"; // Muestra el codigo del error
        echo "<p style='color:red;'>Error: " . $miExceptionPDO->getMessage() . "</p>"; // Muestra el mensaje de error
        die(); // Finalizo el script
    } finally { // codigo que se ejecuta haya o no errores
        unset($miDB); // destruyo la variable 
    }
}
?> 
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Importar Departamento</title>
        <meta name="viewport"   content="width=device-width, initial-scale=1.0">
        <meta name="author"     content="Javier Nieto Lorenzo">
        <meta name="robots"     content="index, follow">      
        <link rel="stylesheet"  href="../webroot/css/estilosMtoDepartamentos.css"       type="text/css" >
        <link rel="icon"        href="../webroot/media/favicon.ico"    type="image/x-icon">
    </head>
    <body>
        <header>
            <h1>Mto. de Departamentos - Importar Departamento</h1>
        </header>
        <main class="flex-container-align-item-center">
            <form name="departamento" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
                <div>
                    <label for="archivo">Archivo XML </label>
                    <input id="archivo" name="archivo" type="file">
                    <?php
                        echo($errorArchivo != null) ? "<span style='color:#FF0000'>" . $errorArchivo . "</span>" : null; // si el campo es erroneo se muestra un mensaje de error
                     ?>
                </div>
                <div class="flex-container-align-item-center">
                    <input class="button" type="submit" value="Aceptar" name="Aceptar">
                    <input class="button" type="submit" value="Cancelar" name="Cancelar">
                </div>
            </form>
        </main>
        <footer class="fixed">
            <a href="http://daw217.ieslossauces.es/" target="_blank"> <img src="../webroot/media/oneandone.png" alt="oneandone icon" width="40"></a>
            <address>  <a href="../../index.html">&copy; 2020-2021 Javier Nieto Lorenzo</a> </address>
            <a href="https://github.com/JavierNLSauces/" target="_blank"><img width="40" src="../webroot/media/github.png" ></a>
        </footer>
    </body>
</html>
    