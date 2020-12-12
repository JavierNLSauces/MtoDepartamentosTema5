<?php
/**
 *   @author: Javier Nieto Lorenzo
 *   @since: 02/12/2020
 *   registro
 */
if(isset($_REQUEST['Cancelar'])){
    header('Location: login.php');
    exit;
}

$aLang['es']=[ // array de las traducciones al castellano
             'title' => 'Registro',
             'user' => 'Usuario',
             'description' => 'Descripcion',
             'password' => 'Contraseña',
             'confirmPassword' => 'Confirmar contraseña',  
             'signup' => 'Registrarse',
             'cancel' => 'Cancelar',
];

$aLang['en']=[ // array de las traducciones al ingles
             'title' => 'Sign Up',
             'user' => 'User',
             'description' => 'Description',
             'password' => 'Password',
             'confirmPassword' => 'Confirm password',  
             'signup' => 'Sign Up',
             'cancel' => 'Cancel',
];

require_once '../core/libreriaValidacion.php'; // incluyo la libreria de validacion para validar los campos de formularios
require_once '../config/confDBPDO.php'; // incluyo el fichero de configuracion de acceso a la basde de datos


define("OBLIGATORIO", 1); // defino e inicializo la constante a 1 para los campos que son obligatorios

$entradaOK = true;

$aErrores = [ //declaro e inicializo el array de errores
    'CodUsuario' => null,
    'DescUsuario' => null,
    'Password' => null,
    'PasswordConfirmacion' => null
];


if (isset($_REQUEST["Registrarse"])) { // comprueba que el usuario le ha dado a al boton de IniciarSesion y valida la entrada de todos los campos
    $aErrores['CodUsuario'] = validacionFormularios::comprobarAlfaNumerico($_REQUEST['CodUsuario'], 15, 3, OBLIGATORIO); // comprueba que la entrada del codigo de usuario es correcta
    
    if($aErrores['CodUsuario']==null){
        try { // Bloque de código que puede tener excepciones en el objeto PDO
        $miDB = new PDO(DNS, USER, PASSWORD); // creo un objeto PDO con la conexion a la base de datos

        $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Establezco el atributo para la apariciopn de errores y le pongo el modo para que cuando haya un error se lance una excepcion

        $sqlUsuario = "SELECT T01_CodUsuario FROM T01_Usuario WHERE T01_CodUsuario=:CodUsuario" ;

        $consultaUsuario = $miDB->prepare($sqlUsuario); // prepara la consulta
        $parametros = [':CodUsuario' => $_REQUEST['CodUsuario'],// creo el array de parametros con el valor de los parametros de la consulta
                      ]; 

        $consultaUsuario->execute($parametros); // ejecuto la consulta pasando los parametros del array de parametros
        if($consultaUsuario->rowCount() == 1){
            $aErrores['CodUsuario']="El nombre de usuario ya existe";
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
    $aErrores['DescUsuario'] = validacionFormularios::comprobarAlfaNumerico($_REQUEST['DescUsuario'], 255, 3, OBLIGATORIO); // comprueba que la entrada del codigo de usuario es correcta
    
    $aErrores['Password'] = validacionFormularios::validarPassword($_REQUEST['Password'], 8, 1, 1, OBLIGATORIO);// comprueba que la entrada del password es correcta
    $aErrores['PasswordConfirmacion'] = validacionFormularios::validarPassword($_REQUEST['PasswordConfirmacion'], 8, 1, 1, OBLIGATORIO);// comprueba que la entrada del password es correcta
    if($_REQUEST['Password'] != $_REQUEST['PasswordConfirmacion']){
        $aErrores['PasswordConfirmacion'] = "Las contraseñas no coinciden";
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

        $sqlUsuario = "INSERT INTO T01_Usuario (T01_CodUsuario,T01_DescUsuario, T01_Password, T01_NumConexiones, T01_FechaHoraUltimaConexion ) VALUES (:CodUsuario, :DescUsuario, :Password, 1,:FechaHoraUltimaConexion)" ;

        $consultaUsuario = $miDB->prepare($sqlUsuario); // prepara la consulta
        $parametros = [':CodUsuario' => $_REQUEST['CodUsuario'],// creo el array de parametros con el valor de los parametros de la consulta
                       ':DescUsuario' => $_REQUEST['DescUsuario'], 
                       ':Password' => hash("sha256",$_REQUEST['CodUsuario'].$_REQUEST['Password']),
                       ':FechaHoraUltimaConexion' => time()
                      ]; 

        $consultaUsuario->execute($parametros); // ejecuto la consulta pasando los parametros del array de parametros
            
            session_start(); // inicia una sesion, o recupera una existente
            $_SESSION['usuarioDAW217MtoDepartamentosTema5'] = $_REQUEST['CodUsuario']; // guarda en la session el valor del usuario
            $_SESSION['fechaHoraUltimaConexionAnterior'] = null; // guarda en la sesion el valor de la ultima conexion del usuario
            
            
            header('Location: programa.php'); // redirige al programa
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
                        <label for="CodUsuario"><?php echo $aLang[$_COOKIE['idiomaActual']]['user']; ?></label>
                        <input class="required" type="text" id="CodUsuario" name="CodUsuario" placeholder="<?php echo $aLang[$_COOKIE['idiomaActual']]['user']; ?>" value="<?php
                            echo (isset($_REQUEST['CodUsuario'])) ? $_REQUEST['CodUsuario'] : null; 
                            ?>">
                        
                    </div>
                    <?php
                        echo ($aErrores['CodUsuario']!=null) ? "<span style='color:#FF0000'>".$aErrores['CodUsuario']."</span>" : null;// si el campo es erroneo se muestra un mensaje de error
                    ?>
                    <div>
                        <label for="DescUsuario"><?php echo $aLang[$_COOKIE['idiomaActual']]['description']; ?></label>
                        <input class="required" type="text" id="DescUsuario" name="DescUsuario" placeholder="<?php echo $aLang[$_COOKIE['idiomaActual']]['description']; ?>" value="<?php
                            echo (isset($_REQUEST['DescUsuario'])) ? $_REQUEST['DescUsuario'] : null; 
                            ?>">
                        
                    </div>
                    <?php
                        echo ($aErrores['DescUsuario']!=null) ? "<span style='color:#FF0000'>".$aErrores['DescUsuario']."</span>" : null;// si el campo es erroneo se muestra un mensaje de error
                    ?>
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
                        <label for="PasswordConfirmacion"><?php echo $aLang[$_COOKIE['idiomaActual']]['confirmPassword']; ?></label>
                        <input style="width: 250px;" class="required" type="password" id="PasswordConfirmacion" name="PasswordConfirmacion" value="<?php
                            echo (isset($_REQUEST['PasswordConfirmacion'])) ? $_REQUEST['PasswordConfirmacion'] : null; 
                            ?>" placeholder="<?php echo $aLang[$_COOKIE['idiomaActual']]['confirmPassword']; ?>">
                        
                    </div>          
                    <?php
                        echo ($aErrores['PasswordConfirmacion'] != null) ? "<span style='color:#FF0000'>" . $aErrores['PasswordConfirmacion'] . "</span>" : null; // si el campo es erroneo se muestra un mensaje de error
                    ?>
                    <div >
                        <button class="button" type="submit" name="Registrarse"><?php echo $aLang[$_COOKIE['idiomaActual']]['signup']; ?></button>
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

