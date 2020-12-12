<?php
/**
 *   @author: Javier Nieto Lorenzo
 *   @since: 03/12/2020
 *   Consultar Departamento
 */


session_start(); // inicia una sesion, o recupera una existente
if(!isset($_SESSION['usuarioDAW217MtoDepartamentosTema5'])){ // si no se ha logueado le usuario
    header('Location: login.php'); // redireige a la pagina del login
    exit;
}

require_once '../core/libreriaValidacion.php'; // incluyo la libreria de validacion para validar los campos de formulario
require_once '../config/confDBPDO.php'; // incluyo el fichero de configuracion de acceso a la basde de datos
require_once '../config/config.php'; // incluyo el fichero de configuracion de la aplicacion


try { // Bloque de código que puede tener excepciones en el objeto PDO
    $miDB = new PDO(DNS, USER, PASSWORD); // creo un objeto PDO con la conexion a la base de datos

    $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Establezco el atributo para la apariciopn de errores y le pongo el modo para que cuando haya un error se lance una excepcion

    $sql = "SELECT * FROM T02_Departamento WHERE T02_CodDepartamento=:CodDepartamento";

    $consultaObtencionDepartamento = $miDB->prepare($sql); // preparo la consulta

    $parametros = [":CodDepartamento" => $_SESSION['CodDepartamento']]; // asigno los valores del formulario en el array de parametros

    $consultaObtencionDepartamento->execute($parametros); // ejecuto la consulta pasando los parametros del array de parametros
    
    $oDepartamento = $consultaObtencionDepartamento->fetchObject(); //guarda en la variable el resultado de la consulta en forma de objeto
    
    $codDepartamento = $oDepartamento->T02_CodDepartamento; // guarda el codigo de departamento en una variable
    $descDepartamento = $oDepartamento->T02_DescDepartamento; // guarda la descripcion del departamento en una variable
    $fechaCreacion = date('d/m/Y',$oDepartamento->T02_FechaCreacionDepartamento);
    $fechaBaja = $oDepartamento->T02_FechaBajaDepartamento; // guarda la fecha de baja del departamento en una variable
    $volumenNegocio = $oDepartamento->T02_VolumenNegocio; // guarda el volumen de negocio del departamento en una variable
    
} catch (PDOException $miExceptionPDO) { // Codigo que se ejecuta si hay alguna excepcion
    echo "<p style='color:red;'>ERROR EN LA CONEXION</p>";
    echo "<p style='color:red;'>Código de error: " . $miExceptionPDO->getCode() . "</p>"; // Muestra el codigo del error
    echo "<p style='color:red;'>Error: " . $miExceptionPDO->getMessage() . "</p>"; // Muestra el mensaje de error
    die(); // Finalizo el script
} finally { // codigo que se ejecuta haya o no errores
    unset($miDB); // destruyo la variable 
}


if (isset($_REQUEST['Aceptar'])) { // si la entrada esta bien recojo los valores introducidos y hago su tratamiento
    header('Location: mtoDepartamentos.php'); // redirige a la pagina principal de la aplicacion
    exit;
}
    ?> 
<!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title>Consultar Departamento</title>
            <meta name="viewport"   content="width=device-width, initial-scale=1.0">
            <meta name="author"     content="Javier Nieto Lorenzo">
            <meta name="robots"     content="index, follow">      
            <link rel="stylesheet"  href="../webroot/css/estilosMtoDepartamentos.css"       type="text/css" >
            <link rel="icon"        href="../webroot/media/favicon.ico"    type="image/x-icon">
        </head>
        <body>
            <header>
                <h1>Mto. de Departamentos - Consultar Departamento</h1>
            </header>
            <main class="flex-container-align-item-center">
                <form name="departamento" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

                    <div>
                        <label for="CodDepartamento">Codigo de Departamento</label>
                        <input type="text" id="CodDepartamento" name="CodDepartamento" value="<?php echo $codDepartamento?>" readonly>
                    </div>
                    <div>
                        <label for="DescDepartamento">Descripcion del Departamento</label>
                        <input type="text" id="DescDepartamento" name="DescDepartamento" value="<?php echo $descDepartamento?>" readonly>
                    </div>
                    <div>
                        <label for="fechaCreacion">Fecha Creacion</label>
                        <input type="text" id="fechaCreacion" name="fechaCreacion" value="<?php echo empty($fechaCreacion)?"NULL":$fechaCreacion; // si la fecha esta vacia imprime null, si no su valor?>" readonly>
                    </div>
                    <div>
                        <label for="FechaBaja">Fecha Baja</label>
                        <input type="text" id="FechaBaja" name="FechaBaja" value="<?php echo empty($fechaBaja)?"NULL":date('d/m/Y',$fechaBaja);?>" readonly>
                    </div>
                    <div>
                        <label for="VolumenNegocio">Volumen Negocio</label>
                        <input type="text" id="VolumenNegocio" name="VolumenNegocio" value="<?php echo $volumenNegocio?>" readonly>
                    </div>
                    <div class="flex-container-align-item-center">
                        <input class="button" type="submit" value="Aceptar" name="Aceptar">
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
