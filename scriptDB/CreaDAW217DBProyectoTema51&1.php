<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>BorraDAW217DBProyectoTema51&1</title>
    </head>
    <body>
        <h1>Javier Nieto Lorenzo</h1>
        <?php
        /**
         *   @author: Javier Nieto Lorenzo
         *   @since: 27/11/2020
         *   CreaDAW217DBProyectoTema51&1

        */ 
            require_once '../config/confDBPDO.php';
            echo "<h2>CreaDAW217DBProyectoTema51&1</h2>";
            try { // Bloque de código que puede tener excepciones en el objeto PDO
                $miDB = new PDO(DNS,USER,PASSWORD); // creo un objeto PDO con la conexion a la base de datos
                
                $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Establezco el atributo para la apariciopn de errores y le pongo el modo para que cuando haya un error se lance una excepcion
                
                $sql = <<<SQL
                    CREATE TABLE IF NOT EXISTS T02_Departamento (
                    T02_CodDepartamento VARCHAR(3) PRIMARY KEY,
                    T02_DescDepartamento VARCHAR(255) NOT NULL,
                    T02_FechaCreacionDepartamento INT NOT NULL,
                    T02_VolumenNegocio FLOAT NOT NULL,
                    T02_FechaBajaDepartamento INT DEFAULT NULL
                )ENGINE=INNODB;

                CREATE TABLE IF NOT EXISTS T01_Usuario(
                    T01_CodUsuario VARCHAR(10) PRIMARY KEY,
                    T01_Password VARCHAR(64) NOT NULL,
                    T01_DescUsuario VARCHAR(255) NOT NULL,
                    T01_NumConexiones INT DEFAULT 0,
                    T01_FechaHoraUltimaConexion INT,
                    T01_Perfil enum('administrador', 'usuario') DEFAULT 'usuario', -- Valor por defecto usuario
                    T01_ImagenUsuario MEDIUMBLOB
                )ENGINE=INNODB;
SQL;
                $miDB->exec($sql);

               
               echo "<p style='color:green;'>CREACION CORRECTA</p>";
            }catch (PDOException $miExceptionPDO) { // Codigo que se ejecuta si hay alguna excepcion
                echo "<p style='color:red;'>ERROR</p>";
                echo "<p style='color:red;'>Código de error: ".$miExceptionPDO->getCode()."</p>"; // Muestra el codigo del error
                echo "<p style='color:red;'>Error: ".$miExceptionPDO->getMessage()."</p>"; // Muestra el mensaje de error
            }finally{
                unset($miDB);
            }

        ?> 
    </body>
</html>