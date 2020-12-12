<?php
/**
 *   @author: Javier Nieto Lorenzo
 *   @since: 03/12/2020
 *   Alta de Departamentos
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

require_once '../core/libreriaValidacion.php'; // incluyo la libreria de validacion para validar los campos de formularios
require_once '../config/confDBPDO.php'; // incluyo el fichero de configuracion de acceso a la basde de datos


define("OBLIGATORIO", 1); // defino e inicializo la constante a 1 para los campos que son obligatorios
define('MYSQL_FLOAT_MAX', 3.402823466E+38); // defino e inicializo la constante de el maximo float que acepta MySQL

$entradaOK = true;

$aErrores = [ //declaro e inicializo el array de errores
    'CodDepartamento' => null,
    'DescDepartamento' => null,
    'VolumenNegocio' => null
];

$aRespuestas = [ // declaro e inicializo el array de las respuestas del usuario
    'CodDepartamento' => null,
    'DescDepartamento' => null,
    'VolumenNegocio' => null
];



if (isset($_REQUEST["Aceptar"])) { // comprueba que el usuario le ha dado a al boton de Aceptar y valida la entrada de todos los campos
    $aErrores['CodDepartamento'] = validacionFormularios::comprobarAlfabetico($_REQUEST['CodDepartamento'], 3, 3, OBLIGATORIO); // comprueba que la entrada del codigo de departamento es correcta
    if ($aErrores['CodDepartamento'] == null) { // si no ha habido ningun error de validacion del campo del codigo del departamento
        if (!ctype_upper($_REQUEST['CodDepartamento'])) { // si el usuario introduce el codigo del departamento en minuscula
            $aErrores['CodDepartamento'] = "El código de Departamento debe introducirse en mayusculas"; // genera un mensaje de error para que el usuario lo meta en mayusculas
        }
    }
    if ($aErrores['CodDepartamento'] == null) { // si no ha habido ningun error de validacion del campo del codigo del departamento
        try { // Bloque de código que puede tener excepciones en el objeto PDO
            $miDB = new PDO(DNS, USER, PASSWORD); // creo un objeto PDO con la conexion a la base de datos

            $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Establezco el atributo para la apariciopn de errores y le pongo el modo para que cuando haya un error se lance una excepcion

            $sqlBusquedaDepartamento = "SELECT T02_CodDepartamento FROM T02_Departamento WHERE T02_CodDepartamento=:codDepartamento"; 
            $consultaBusquedaDepartamento = $miDB->prepare($sqlBusquedaDepartamento); // prepara la consulta
            $parametros = [':codDepartamento' => $_REQUEST['CodDepartamento']]; // creo el array de parametros con el valor de los parametros de la consulta
            $consultaBusquedaDepartamento->execute($parametros); // ejecuta la consulta 
            if ($consultaBusquedaDepartamento->rowCount() > 0) { // si encuentra algun departamento
                $aErrores['CodDepartamento'] = "El código de Departamento introducido ya existe"; // introduce un mensaje de error en el array de errores del codigo del departamento
            }
        } catch (PDOException $miExceptionPDO) { // Codigo que se ejecuta si hay alguna excepcion
            echo "<p style='color:red;'>ERROR</p>";
            echo "<p style='color:red;'>Código de error: " . $miExceptionPDO->getCode() . "</p>"; // Muestra el codigo del error
            echo "<p style='color:red;'>Error: " . $miExceptionPDO->getMessage() . "</p>"; // Muestra el mensaje de error
            die(); // Finalizo la ejecucion del script
        } finally {
            unset($miDB); // destruyo la variable de la conexion a la base de datos
        }
    }

    $aErrores['DescDepartamento'] = validacionFormularios::comprobarAlfaNumerico($_REQUEST['DescDepartamento'], 255, 1, OBLIGATORIO); // compruebo que la entrada de la descripcion del departamento es correcta
    $aErrores['VolumenNegocio'] = validacionFormularios::comprobarFloat($_REQUEST['VolumenNegocio'], 3.402823466E+38, 0, OBLIGATORIO); // compruebo que la entrada del volumen de negocio del departamento es correcta

    foreach ($aErrores as $campo => $error) { // recorro el array de errores
        if ($error != null) { // compruebo si hay algun mensaje de error en algun campo
            $entradaOK = false; // le doy el valor false a $entradaOK
            $_REQUEST[$campo] = ""; // si hay algun campo que tenga mensaje de error pongo $_REQUEST a null
        }
    }
} else { // si el usuario no le ha dado al boton de enviar
    $entradaOK = false; // le doy el valor false a $entradaOK
}

if ($entradaOK) { // si la entrada esta bien recojo los valores introducidos y hago su tratamiento
    $aRespuestas['CodDepartamento'] = $_REQUEST['CodDepartamento']; // strtoupper() transforma los caracteres de un string a mayuscula
    $aRespuestas['DescDepartamento'] = $_REQUEST['DescDepartamento'];
    $aRespuestas['VolumenNegocio'] = $_REQUEST['VolumenNegocio'];
    // Inserccion del departamento

    try { // Bloque de código que puede tener excepciones en el objeto PDO
        $miDB = new PDO(DNS, USER, PASSWORD); // creo un objeto PDO con la conexion a la base de datos

        $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Establezco el atributo para la apariciopn de errores y le pongo el modo para que cuando haya un error se lance una excepcion

        $sqlInserccion = "INSERT INTO T02_Departamento(T02_CodDepartamento,T02_DescDepartamento,T02_FechaCreacionDepartamento,T02_VolumenNegocio) VALUES (:CodDepartamento, :DescDepartamento,:FechaCreacionDepartamento,:VolumenNegocio)"; // Inserta un departamento 

        $consultaInserccion = $miDB->prepare($sqlInserccion); // prepara la consulta

        $parametros = [":CodDepartamento" => $aRespuestas['CodDepartamento'], // asigno los valores del formulario en el array de parametros
                       ":DescDepartamento" => $aRespuestas['DescDepartamento'],
                       ":FechaCreacionDepartamento" => time(),
                       ":VolumenNegocio" => $aRespuestas['VolumenNegocio']];

        $consultaInserccion->execute($parametros); // ejecuto la consulta pasando los parametros del array de parametros
    } catch (PDOException $miExceptionPDO) { // Codigo que se ejecuta si hay alguna excepcion
        echo "<p style='color:red;'>ERROR EN LA CONEXION</p>";
        echo "<p style='color:red;'>Código de error: " . $miExceptionPDO->getCode() . "</p>"; // Muestra el codigo del error
        echo "<p style='color:red;'>Error: " . $miExceptionPDO->getMessage() . "</p>"; // Muestra el mensaje de error
        die(); // Finalizo el script
    } finally { // codigo que se ejecuta haya o no errores
        unset($miDB); // destruyo la variable 
    }
    header('Location: mtoDepartamentos.php'); // redirige a la pagina principal de la aplicacion
} else { // si hay algun campo de la entrada que este mal muestro el formulario hasta que introduzca bien los campos
    ?> 
<!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title>Alta Departamento</title>
            <meta name="viewport"   content="width=device-width, initial-scale=1.0">
            <meta name="author"     content="Javier Nieto Lorenzo">
            <meta name="robots"     content="index, follow">      
            <link rel="stylesheet"  href="../webroot/css/estilosMtoDepartamentos.css"       type="text/css" >
            <link rel="icon"        href="../webroot/media/favicon.ico"    type="image/x-icon">
        </head>
        <body>
            <header>
                <h1>Mto. de Departamentos - Insertar Departamento</h1>
            </header>
            <main class="flex-container-align-item-center">
                <form name="departamento" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

                    <div>
                        <label for="CodDepartamento">Codigo de Departamento</label>
                        <input class="required" type="text" id="CodDepartamento" name="CodDepartamento" placeholder="AAA" value="<?php
                            echo (isset($_REQUEST['CodDepartamento'])) ? $_REQUEST['CodDepartamento'] : null; // si el campo esta correcto mantengo su valor en el formulario
                            ?>">
                            <?php
                                echo ($aErrores['CodDepartamento'] != null) ? "<span style='color:#FF0000'>" . $aErrores['CodDepartamento'] . "</span>" : null; // si el campo es erroneo se muestra un mensaje de error
                            ?>
                    </div>
                    <div>
                        <label for="DescDepartamento">Descripcion del Departamento</label>
                        <input class="required" type="text" id="DescDepartamento" name="DescDepartamento" placeholder="Introduzca descripcion del departamento" value="<?php
                            echo (isset($_REQUEST['DescDepartamento'])) ? $_REQUEST['DescDepartamento'] : null; // si el campo esta correcto mantengo su valor en el formulario
                            ?>">
                            <?php
                                echo ($aErrores['DescDepartamento'] != null) ? "<span style='color:#FF0000'>" . $aErrores['DescDepartamento'] . "</span>" : null; // si el campo es erroneo se muestra un mensaje de error
                            ?>
                    </div>                
                    <div>
                        <label for="VolumenNegocio">Volumen Negocio</label>
                        <input class="required" type="text" id="VolumenNegocio" name="VolumenNegocio" placeholder="Decimal" value="<?php
                            echo (isset($_REQUEST['VolumenNegocio'])) ? $_REQUEST['VolumenNegocio'] : null; // si el campo esta correcto mantengo su valor en el formulario
                            ?>">
                            <?php
                               echo($aErrores['VolumenNegocio'] != null) ? "<span style='color:#FF0000'>" . $aErrores['VolumenNegocio'] . "</span>" : null; // si el campo es erroneo se muestra un mensaje de error
                            ?>
                    </div>
                    <div class="flex-container-align-item-center">
                        <input class="button" type="submit" value="Aceptar" name="Aceptar">
                        <input class="button" type="submit" value="Cancelar" name="Cancelar">
                    </div>

                </form>

            <?php
                }
            ?>
        </main>
        <footer class="fixed">
            <a href="http://daw217.ieslossauces.es/" target="_blank"> <img src="../webroot/media/oneandone.png" alt="oneandone icon" width="40"></a>
            <address>  <a href="../../index.html">&copy; 2020-2021 Javier Nieto Lorenzo</a> </address>
            <a href="https://github.com/JavierNLSauces/" target="_blank"><img width="40" src="../webroot/media/github.png" ></a>
        </footer>
    </body>
</html>




