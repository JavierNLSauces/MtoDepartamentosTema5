<?php
/**
 *   @author: Javier Nieto Lorenzo
 *   @since: 02/12/2020
 *   cambiarPassword
 */
session_start(); // inicia una sesion, o recupera una existente
if(!isset($_SESSION['usuarioDAW217MtoDepartamentosTema5'])){ // si no se ha logueado le usuario
    header('Location: login.php'); // redirige al login
    exit;
}
if(isset($_REQUEST['Cancelar'])){ // si se ha pulsado el boton cancelar
    header('Location: editarPerfil.php'); // redirige a editarPerfil.php
    exit;
}

$aLang['es']=[ // array de las traducciones al castellano
             'title' => 'Cambiar Password',
             'password' => 'Contraseña',
             'newPassword' => 'Nueva contraseña',
             'confirmPassword' => 'Confirmar contraseña',
             'change' => 'Cambiar',  
             'cancel' => 'Cancelar'  
];

$aLang['en']=[ // array de las traducciones al ingles
             'title' => 'Change Password',
             'password' => 'Password',
             'newPassword' => 'New password',
             'confirmPassword' => 'Confirm password',
             'change' => 'Change',  
             'cancel' => 'Cancel'  
];

require_once '../core/libreriaValidacion.php'; // incluyo la libreria de validacion para validar los campos de formularios
require_once '../config/confDBPDO.php'; // incluyo el fichero de configuracion de acceso a la basde de datos


define("OBLIGATORIO", 1); // defino e inicializo la constante a 1 para los campos que son obligatorios

$entradaOK = true;

$aErrores = [ //declaro e inicializo el array de errores
    'Password' => null,
    'PasswordNueva' => null,
    'PasswordRepetida' => null
];


if (isset($_REQUEST["Cambiar"])) { // comprueba que el usuario le ha dado a al boton de IniciarSesion y valida la entrada de todos los campos
    $aErrores['Password'] = validacionFormularios::validarPassword($_REQUEST['Password'], 8, 1, 1, OBLIGATORIO);// comprueba que la entrada del password es correcta
    $aErrores['PasswordNueva'] = validacionFormularios::validarPassword($_REQUEST['PasswordNueva'], 8, 1, 1, OBLIGATORIO);// comprueba que la entrada del password es correcta
    $aErrores['PasswordRepetida'] = validacionFormularios::validarPassword($_REQUEST['PasswordRepetida'], 8, 1, 1, OBLIGATORIO);// comprueba que la entrada del password es correcta
    
    if($aErrores['Password']==null){
        try { // Bloque de código que puede tener excepciones en el objeto PDO
        $miDB = new PDO(DNS, USER, PASSWORD); // creo un objeto PDO con la conexion a la base de datos

        $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Establezco el atributo para la apariciopn de errores y le pongo el modo para que cuando haya un error se lance una excepcion

        $sqlUsuario = "SELECT T01_Password FROM T01_Usuario WHERE T01_CodUsuario=:CodUsuario" ;

        $consultaUsuario = $miDB->prepare($sqlUsuario); // prepara la consulta
        $parametros = [':CodUsuario' => $_SESSION['usuarioDAW217MtoDepartamentosTema5'],// creo el array de parametros con el valor de los parametros de la consulta
                      ]; 

        $consultaUsuario->execute($parametros); // ejecuto la consulta pasando los parametros del array de parametros
        $oUsuario = $consultaUsuario->fetchObject(); // se almacenan los datos de la consulta en forma de objeto
        if($oUsuario->T01_Password != hash('sha256', $_SESSION['usuarioDAW217MtoDepartamentosTema5'].$_REQUEST['Password'])){ // si el password que ha introducido y el de la base de datos no coincide
            $aErrores['Password']="Contraseña incorrecta"; // se almacena un mensaje de error en el campo del password
        }

        } catch (PDOException $miExceptionPDO) { // Codigo que se ejecuta si hay alguna excepcion
            echo "<p style='color:red;'>ERROR EN LA CONEXION</p>";
            echo "<p style='color:red;'>Código de error: " . $miExceptionPDO->getCode() . "</p>"; // Muestra el codigo del error
            echo "<p style='color:red;'>Error: " . $miExceptionPDO->getMessage() . "</p>"; // Muestra el mensaje de error
            die(); // Finalizo el script
        } finally { // codigo que se ejecuta haya o no errores
            unset($miDB); // destruyo la variable 
        }
    }
    
    if($_REQUEST['PasswordNueva']!=$_REQUEST['PasswordRepetida']){ // si la password nueva y la pasword de confirmacion no coinciden
        $aErrores['PasswordRepetida'] = "Las contraseñas no coinciden"; // se almacena un mensaje de error en el campo del password repetido
    }
    
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

    try { // Bloque de código que puede tener excepciones en el objeto PDO
        $miDB = new PDO(DNS, USER, PASSWORD); // creo un objeto PDO con la conexion a la base de datos

        $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Establezco el atributo para la apariciopn de errores y le pongo el modo para que cuando haya un error se lance una excepcion

        $consultaUpdatePassword = "UPDATE T01_Usuario SET T01_Password=:Password WHERE T01_CodUsuario=:CodUsuario" ;

        $consultaUpdatePassword = $miDB->prepare($consultaUpdatePassword); // prepara la consulta
        $parametros = [':Password' => hash('sha256', $_SESSION['usuarioDAW217MtoDepartamentosTema5'].$_REQUEST['PasswordRepetida']),// creo el array de parametros con el valor de los parametros de la consulta
                       ':CodUsuario' => $_SESSION['usuarioDAW217MtoDepartamentosTema5']
                       ]; 

        $consultaUpdatePassword->execute($parametros); // ejecuto la consulta pasando los parametros del array de parametros
        
        
        header('Location: editarPerfil.php'); // redirige al programa
        exit;
        
    } catch (PDOException $miExceptionPDO) { // Codigo que se ejecuta si hay alguna excepcion
        echo "<p style='color:red;'>ERROR EN LA CONEXION</p>";
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
            <title><?php echo $aLang[$_COOKIE['idiomaActual']]['title']; ?></title>
            <meta name="viewport"   content="width=device-width, initial-scale=1.0">
            <meta name="author"     content="Javier Nieto Lorenzo">
            <meta name="robots"     content="index, follow">      
            <link rel="stylesheet"  href="../webroot/css/estilosLoginLogoff.css"       type="text/css" >
            <link rel="icon"        href="../webroot/media/favicon.ico"    type="image/x-icon">
        </head>
        <body>
            <header>
                <h1><?php echo $aLang[$_COOKIE['idiomaActual']]['title']; ?></h1>
            </header>
            <main class="flex-container-align-item-center">
                
                <form name="singup" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

                    <div>
                        <label for="Password"><?php echo $aLang[$_COOKIE['idiomaActual']]['password']; ?></label>
                        <input class="required" type="password" id="Password" name="Password" value="<?php
                            echo (isset($_REQUEST['Password'])) ? $_REQUEST['Password'] : null; 
                            ?>" placeholder="<?php echo $aLang[$_COOKIE['idiomaActual']]['password']; ?>">
                        
                    </div>          
                    <?php
                        echo ($aErrores['Password'] != null) ? "<span style='color:#FF0000'>" . $aErrores['Password'] . "</span>" : null; // si el campo es erroneo se muestra un mensaje de error
                    ?>
                    
                    <div>
                        <label for="PasswordNueva"><?php echo $aLang[$_COOKIE['idiomaActual']]['newPassword']; ?></label>
                        <input style="width: 210px;" class="required" type="password" id="PasswordNueva" name="PasswordNueva" value="<?php
                            echo (isset($_REQUEST['PasswordNueva'])) ? $_REQUEST['PasswordNueva'] : null; 
                            ?>" placeholder="<?php echo $aLang[$_COOKIE['idiomaActual']]['newPassword']; ?>">
                        
                    </div>          
                    <?php
                        echo ($aErrores['PasswordNueva'] != null) ? "<span style='color:#FF0000'>" . $aErrores['PasswordNueva'] . "</span>" : null; // si el campo es erroneo se muestra un mensaje de error
                    ?>
                    
                    <div>
                        <label for="PasswordRepetida"><?php echo $aLang[$_COOKIE['idiomaActual']]['confirmPassword']; ?></label>
                        <input style="width: 235px;" class="required" type="password" id="PasswordRepetida" name="PasswordRepetida" value="<?php
                            echo (isset($_REQUEST['PasswordRepetida'])) ? $_REQUEST['PasswordRepetida'] : null; 
                            ?>" placeholder="<?php echo $aLang[$_COOKIE['idiomaActual']]['confirmPassword']; ?>">
                        
                    </div>          
                    <?php
                        echo ($aErrores['PasswordRepetida'] != null) ? "<span style='color:#FF0000'>" . $aErrores['PasswordRepetida'] . "</span>" : null; // si el campo es erroneo se muestra un mensaje de error
                    ?>
                    <div >
                        <button class="button" type="submit" name="Cambiar"><?php echo $aLang[$_COOKIE['idiomaActual']]['change']; ?></button>
                        <button class="button" name="Cancelar"><?php echo $aLang[$_COOKIE['idiomaActual']]['cancel']; ?></button>
                    </div>

                </form>

        </main>
        <footer class="fixed">
            <a href="http://daw217.ieslossauces.es/" target="_blank"> <img src="../webroot/media/oneandone.png" alt="oneandone icon" width="40"></a>
            <address>  <a href="../../index.html">&copy; 2020-2021 Javier Nieto Lorenzo</a> </address>
            <a href="https://github.com/JavierNLSauces/" target="_blank"><img class="github" width="40" src="../webroot/media/github.png" ></a>
        </footer>
    </body>
</html>

