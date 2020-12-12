<?php
/**
 *   @author: Javier Nieto Lorenzo
 *   @since: 02/12/2020
 *   Login
 */
if(!isset($_COOKIE['idiomaActual'])){
    setcookie('idiomaActual','es',time()+2592000); // crea la cookie 'idioma' con el valor 'es' para 30 dias
    header('Location: login.php');
    exit;
}

if (isset($_REQUEST['idioma'])) { // si se ha pulsado el botton de cerrar sesion
    setcookie('idiomaActual', $_REQUEST['idioma'], time() + 2592000); // modifica la cookie 'idioma' con el valor recibido del formulario para 30 dias
    header('Location: login.php');
    exit;
}
$aLang['es']=[ // array de las traducciones al castellano
             'user' => 'Usuario',
             'password' => 'Contraseña',
             'login' => 'Iniciar Sesion',
             'signup' => 'Registrarse'
];

$aLang['en']=[ // array de las traducciones al ingles
             'user' => 'User',
             'password' => 'Password',
             'login' => 'Login',
             'signup' => 'Sign Up'
];

require_once '../core/libreriaValidacion.php'; // incluyo la libreria de validacion para validar los campos de formularios
require_once '../config/confDBPDO.php'; // incluyo el fichero de configuracion de acceso a la basde de datos


define("OBLIGATORIO", 1); // defino e inicializo la constante a 1 para los campos que son obligatorios

$entradaOK = true;

$aErrores = [ //declaro e inicializo el array de errores
    'CodUsuario' => null,
    'Password' => null
];


if (isset($_REQUEST["IniciarSesion"])) { // comprueba que el usuario le ha dado a al boton de IniciarSesion y valida la entrada de todos los campos
    $aErrores['CodUsuario'] = validacionFormularios::comprobarAlfaNumerico($_REQUEST['CodUsuario'], 15, 3, OBLIGATORIO); // comprueba que la entrada del codigo de usuario es correcta

    $aErrores['Password'] = validacionFormularios::validarPassword($_REQUEST['Password'], 8, 1, 1, OBLIGATORIO);// comprueba que la entrada del password es correcta

    if ($aErrores['CodUsuario'] != null || $aErrores['Password']!=null) { // compruebo si hay algun mensaje de error en algun campo
        $entradaOK = false; // le doy el valor false a $entradaOK
        unset($_REQUEST); 
    }
} else { // si el usuario no le ha dado al boton de enviar
    $entradaOK = false; // le doy el valor false a $entradaOK
}

if ($entradaOK) { // si la entrada esta bien recojo los valores introducidos y hago su tratamiento

    try { // Bloque de código que puede tener excepciones en el objeto PDO
        $miDB = new PDO(DNS, USER, PASSWORD); // creo un objeto PDO con la conexion a la base de datos

        $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Establezco el atributo para la apariciopn de errores y le pongo el modo para que cuando haya un error se lance una excepcion

        $sqlUsuario = "SELECT * FROM T01_Usuario WHERE T01_CodUsuario=:CodUsuario AND T01_Password=:Password" ;

        $consultaUsuario = $miDB->prepare($sqlUsuario); // prepara la consulta
        $parametros = [':CodUsuario' => $_REQUEST['CodUsuario'],// creo el array de parametros con el valor de los parametros de la consulta
                       ':Password' => hash("sha256",$_REQUEST['CodUsuario'].$_REQUEST['Password'])
                      ]; 

        $consultaUsuario->execute($parametros); // ejecuto la consulta pasando los parametros del array de parametros

        if($consultaUsuario->rowCount() == 1){ // si encuentra el usuario con el codigo de usuario y el password introducido
            $oUsuario = $consultaUsuario->fetchObject(); // guarda en la variable un objeto con los datos solicitados en la consulta
            
            session_start(); // inicia una sesion, o recupera una existente
            $_SESSION['usuarioDAW217MtoDepartamentosTema5'] = $oUsuario->T01_CodUsuario; // guarda en la session el valor del usuario
            $_SESSION['fechaHoraUltimaConexionAnterior'] = $oUsuario->T01_FechaHoraUltimaConexion; // guarda en la sesion el valor de la ultima conexion del usuario
            
            $sqlUpdateDatosUsuario = "UPDATE T01_Usuario SET T01_NumConexiones = (T01_NumConexiones + 1) , T01_FechaHoraUltimaConexion = :FechaHoraUltimaConexion WHERE T01_CodUsuario=:CodUsuario";

            $consultaUpdateDatosUsuario = $miDB->prepare($sqlUpdateDatosUsuario); // prepara la consulta
            
            // creo el array de parametros con el valor de los parametros de la consulta
            $parametros = [':FechaHoraUltimaConexion' => time(), // time() devuelve el timestamp de el tiempo actual
                           ':CodUsuario' => $_REQUEST['CodUsuario'] 
                          ]; 

            $consultaUpdateDatosUsuario->execute($parametros); // ejecuto la consulta pasando los parametros del array de parametros
            
            header('Location: programa.php'); // redirige al programa
            exit;
        }else{
            unset($_REQUEST); 
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
    ?> 
<!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title>LoginLogoffTema5</title>
            <meta name="viewport"   content="width=device-width, initial-scale=1.0">
            <meta name="author"     content="Javier Nieto Lorenzo">
            <meta name="robots"     content="index, follow">      
            <link rel="stylesheet"  href="../webroot/css/estilosLoginLogoff.css"       type="text/css" >
            <link rel="icon"        href="../webroot/media/favicon.ico"    type="image/x-icon">
        </head>
        <body>
            <header>
                <h1>LoginLogoffTema5</h1>
            </header>
            <main class="flex-container-align-item-center">
                <form name="formularioIdioma" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <button <?php echo ($_COOKIE['idiomaActual']=="es")? "style='color: black;'" : null ;?> class="idioma " type="submit" name="idioma" value="es"> Castellano</button>
                    <button <?php echo ($_COOKIE['idiomaActual']=="en")? "style='color: black;'" : null ;?> class="idioma" type="submit" name="idioma" value="en"> English</button>
                </form>
                <form name="login" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

                    <div>
                        <label for="CodUsuario"><?php echo $aLang[$_COOKIE['idiomaActual']]['user']; ?></label>
                        <input class="required" type="text" id="CodUsuario" name="CodUsuario" placeholder="<?php echo $aLang[$_COOKIE['idiomaActual']]['user']; ?>" value="<?php
                            echo (isset($_REQUEST['CodUsuario'])) ? $_REQUEST['CodUsuario'] : null; 
                            ?>">
                    </div>
                    <div>
                        <label for="Password"><?php echo $aLang[$_COOKIE['idiomaActual']]['password']; ?></label>
                        <input class="required" type="password" id="Password" name="Password" value="<?php
                            echo (isset($_REQUEST['Password'])) ? $_REQUEST['Password'] : null; 
                            ?>" placeholder="<?php echo $aLang[$_COOKIE['idiomaActual']]['password']; ?>">
                    </div>                
                    
                    <div>
                        <button class="button" type="submit" name="IniciarSesion"><?php echo $aLang[$_COOKIE['idiomaActual']]['login']; ?></button>
                        <a class="registrarse" href="registro.php"><?php echo $aLang[$_COOKIE['idiomaActual']]['signup']; ?></a>
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





