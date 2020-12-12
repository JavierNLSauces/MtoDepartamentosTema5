<?php
/**
 *   @author: Javier Nieto Lorenzo
 *   @since: 30/11/2020
 *   Detalle
*/
session_start(); // inicia una sesion, o recupera una existente
if(!isset($_SESSION['usuarioDAW217MtoDepartamentosTema5'])){
    header('Location: programa.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Detalles</title>
        <style>
            .variables-globales{
                text-align: center;
                font-size: 110%;
            }
            
            .globals{
                margin: auto;
            }
            .globals th{
                position: inherit;
                font-size: 150%;
                background-color: #99c;
            }
            .globals tr td:first-child{
                background-color: #ccf;
                font-weight: bold;
                width: 300px !important;
            }
            .globals tr td{
                background-color: #ddd;
            }
            .button{
                font-size: 1.2rem;
                background-color: #ffcb8e;
                border-radius: 20px;
                border: 2px solid #ffa842;
            }
        </style>
    </head>
    <body>
        <a href="programa.php"><button class="button">Salir</button></a>
        <?php
        /**
         *   @author: Javier Nieto Lorenzo
         *   @since: 24/11/2020
         *   00. Mostrar el contenido de las variables superglobales y phpinfo().
        */
            // $GLOBALS : Hace referencia a todas las variables disponibles en el ámbito global
            // $ _SERVER - Información del entorno de ejecución y del servidor
            // $ _SERVER - Información del entorno de ejecución y del servidor
            // $ _POST - Variables HTTP POST
            // $ _FILES - Variables de carga de archivos HTTP
            // $ _COOKIE - Cookies HTTP
            // $ _SESSION - Variables de sesión
            // $ _REQUEST - Variables de solicitud HTTP
            // $ _ENV - Variables de entorno
        ?> 
        <table class="globals">
            <tbody>
                <tr>
                    <th>VARIABLES GLOBALES</th>
                </tr>
            </tbody>
        </table>
        
        <h2 class="variables-globales">_COOKIE</h2>
        <?php if(!empty($_COOKIE)){ ?>
        <table class="globals">
            <tbody>
                <?php 
                    foreach ($_COOKIE as $clave => $valor){
                ?>
                <tr>
                    <td><?php echo $clave ?></td>
                    <td><?php echo $valor ?></td>
                </tr>
                <?php 
                    }
                ?>
            </tbody>
        </table>
        <?php }?>
        
        <h2 class="variables-globales">_SESSION</h2>
        <?php if(!empty($_SESSION)){ ?>
        <table class="globals">
            <tbody>
                <?php 
                    foreach ($_SESSION as $clave => $valor){
                ?>
                <tr>
                    <td><?php echo $clave ?></td>
                    <td><?php echo $valor ?></td>
                </tr>
                <?php 
                    }
                ?>
            </tbody>
        </table>
        <?php }?>
        
        <h2 class="variables-globales">_SERVER</h2>
        <?php if(!empty($_SERVER)){ ?>
        <table class="globals">
            <tbody>
                <?php 
                    foreach ($_SERVER as $clave => $valor){
                ?>
                <tr>
                    <td><?php echo $clave ?></td>
                    <td><?php echo $valor ?></td>
                </tr>
                <?php 
                    }
                ?>
            </tbody>
        </table>
        <?php }?>
        
        <h2 class="variables-globales">_GET</h2>
        <?php if(!empty($_GET)){ ?>
        <table class="globals">
            <tbody>
                <?php 
                    foreach ($_GET as $clave => $valor){
                ?>
                <tr>
                    <td><?php echo $clave ?></td>
                    <td><?php echo $valor ?></td>
                </tr>
                <?php 
                    }
                ?>
            </tbody>
        </table>
        <?php }?>
        
        
        <h2 class="variables-globales">_POST</h2>
        <?php if(!empty($_POST)){ ?>
        <table class="globals">
            <tbody>
                <?php 
                    foreach ($_POST as $clave => $valor){
                ?>
                <tr>
                    <td><?php echo $clave ?></td>
                    <td><?php echo $valor ?></td>
                </tr>
                <?php 
                    }
                ?>
            </tbody>
        </table>
        <?php }?>

        <h2 class="variables-globales">_FILES</h2>
        <?php if(!empty($_FILES)){ ?>
        <table class="globals">
            <tbody>
                <?php 
                    foreach ($_FILES as $clave => $valor){
                ?>
                <tr>
                    <td><?php echo $clave ?></td>
                    <td><?php echo $valor ?></td>
                </tr>
                <?php 
                    }
                ?>
            </tbody>
        </table>
        <?php }?>
        
        <h2 class="variables-globales">_ENV</h2>
        <?php if(!empty($_ENV)){ ?>
        <table class="globals">
            <tbody>
                <?php 
                    foreach ($_ENV as $clave => $valor){
                ?>
                <tr>
                    <td><?php echo $clave ?></td>
                    <td><?php echo $valor ?></td>
                </tr>
                <?php 
                    }
                ?>
            </tbody>
        </table>
        <?php }?>

        <h2 class="variables-globales">_REQUEST</h2>
        <?php if(!empty($_REQUEST)){ ?>
        <table class="globals">
            <tbody>
                <?php 
                    foreach ($_REQUEST as $clave => $valor){
                ?>
                <tr>
                    <td><?php echo $clave ?></td>
                    <td><?php echo $valor ?></td>
                </tr>
                <?php 
                    }
                ?>
            </tbody>
        </table>
        <?php }?>

        
        <?php            
            phpinfo();
        ?> 
    </body>
</html>