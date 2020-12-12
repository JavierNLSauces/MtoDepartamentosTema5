<?php
/**
 *   @author: Javier Nieto Lorenzo
 *   @since: 03/12/2020
 *   Editar Departamentos
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

require_once '../core/libreriaValidacion.php'; // incluyo la libreria de validacion para validar los campos de formulario
require_once '../config/confDBPDO.php'; // incluyo el fichero de configuracion de acceso a la basde de datos


define("OBLIGATORIO", 1); // defino e inicializo la constante a 1 para los campos que son obligatorios
define('MYSQL_FLOAT_MAX', 3.402823466E+38); // defino e inicializo la constante de el maximo float que acepta MySQL

$entradaOK = true;

$aErrores = [//declaro e inicializo el array de errores
    'DescDepartamento' => null,
    'VolumenNegocio' => null
];

$aRespuestas = [// declaro e inicializo el array de las respuestas del usuario
    'DescDepartamento' => null,
    'VolumenNegocio' => null
];

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

if (isset($_REQUEST["Aceptar"])) { // compruebo que el usuario le ha dado a al boton de enviar y valido la entrada de todos los campos
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

if ($entradaOK ) { // si la entrada esta bien recojo los valores introducidos y hago su tratamiento
    try { // Bloque de código que puede tener excepciones en el objeto PDO
        $miDB = new PDO(DNS, USER, PASSWORD); // creo un objeto PDO con la conexion a la base de datos

        $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Establezco el atributo para la apariciopn de errores y le pongo el modo para que cuando haya un error se lance una excepcion

        $sql2 = "UPDATE T02_Departamento SET T02_DescDepartamento=:DescDepartamento, T02_VolumenNegocio=:VolumenNegocio WHERE T02_CodDepartamento=:CodDepartamento";

        $consulta2 = $miDB->prepare($sql2); // preparo la consulta

        $parametros = [":DescDepartamento" => $_REQUEST['DescDepartamento'], // asigno los valores del formulario en el array de parametros
                       ":VolumenNegocio" => $_REQUEST['VolumenNegocio'],
                       ":CodDepartamento" => $codDepartamento];

        $consulta2->execute($parametros); // ejecuto la consulta pasando los parametros del array de parametros
    } catch (PDOException $miExceptionPDO) { // Codigo que se ejecuta si hay alguna excepcion
        echo "<p style='color:red;'>ERROR EN LA CONEXION</p>";
        echo "<p style='color:red;'>Código de error: " . $miExceptionPDO->getCode() . "</p>"; // Muestra el codigo del error
        echo "<p style='color:red;'>Error: " . $miExceptionPDO->getMessage() . "</p>"; // Muestra el mensaje de error
        die(); // Finalizo el script
    } finally { // codigo que se ejecuta haya o no errores
        unset($miDB); // destruyo la variable 
    }
    header('Location: mtoDepartamentos.php'); // redirige a la pagina principal de la aplicacion
}
    ?> 
<!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title>Editar Departamento</title>
            <meta name="viewport"   content="width=device-width, initial-scale=1.0">
            <meta name="author"     content="Javier Nieto Lorenzo">
            <meta name="robots"     content="index, follow">      
            <link rel="stylesheet"  href="../webroot/css/estilosMtoDepartamentos.css"       type="text/css" >
            <link rel="icon"        href="../webroot/media/favicon.ico"    type="image/x-icon">
        </head>
        <body>
            <header>
                <h1>Mto. de Departamentos - Editar Departamento</h1>
            </header>
            <main class="flex-container-align-item-center">
                <form name="departamento" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

                    <div>
                        <label for="CodDepartamento">Codigo de Departamento</label>
                        <input type="text" id="CodDepartamento" name="CodDepartamento" value="<?php echo $codDepartamento?>" readonly>
                    </div>
                    <div>
                        <label for="DescDepartamento">Descripcion del Departamento</label>
                        <input class="required" type="text" id="DescDepartamento" name="DescDepartamento" placeholder="Introduzca descripcion del departamento" value="<?php
                            if(isset($_REQUEST['DescDepartamento'])){
                                if($aErrores['DescDepartamento'] != null){
                                    echo $descDepartamento;
                                }else{
                                    echo $_REQUEST['DescDepartamento'];
                                }
                            }else{
                                echo $descDepartamento;
                            }
                            ?>">
                            <?php
                                echo ($aErrores['DescDepartamento'] != null) ? "<span style='color:#FF0000'>" . $aErrores['DescDepartamento'] . "</span>" : null; // si el campo es erroneo se muestra un mensaje de error
                            ?>
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
                        <input class="required" type="text" id="VolumenNegocio" name="VolumenNegocio" placeholder="Decimal" value="<?php
                            if(isset($_REQUEST['VolumenNegocio'])){
                                if($aErrores['VolumenNegocio'] != null){
                                    echo $volumenNegocio;
                                }else{
                                    echo $_REQUEST['VolumenNegocio'];
                                }
                            }else{
                                echo $volumenNegocio;
                            }
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

        </main>
        <footer class="fixed">
            <a href="http://daw217.ieslossauces.es/" target="_blank"> <img src="../webroot/media/oneandone.png" alt="oneandone icon" width="40"></a>
            <address>  <a href="../../index.html">&copy; 2020-2021 Javier Nieto Lorenzo</a> </address>
            <a href="https://github.com/JavierNLSauces/" target="_blank"><img width="40" src="../webroot/media/github.png" ></a>
        </footer>
    </body>
</html>

