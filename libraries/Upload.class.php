<?php
defined('_EXEC') or die;

/**
 *
 * @package Valkyrie.Libraries
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 * @author David Miguel Gómez Macías < davidgomezmacias@gmail.com >
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 */

class Upload
{
    /**
     *
     * @var object
     */
    private $security;

    /**
     *
     * @var array
     */
    private $configs;

    /**
     *
     * @var string
     */
    private $file_name;

    /**
     *
     * @var string
     */
    private $temp_name;

    /**
     *
     * @var string
     */
    private $file_type;

    /**
     *
     * @var string
     */
    private $file_size;

    /**
     *
     * @var string
     */
    private $upload_directory;

    /**
     *
     * @var string
     */
    private $valid_extensions;

    /**
     *
     * @var integer
     */
    private $maximum_file_size;

    /**
     * Constructor.
     *
     * @return  void
     */
    public function __construct()
    {
        $this->security = new Security();
    }

    /**
     * Obtiene el tamaño maximo de subida, establecido en el servidor.
     *
     * @static
     *
     * @return   integer
     */
    static public function get_maximum_file_size()
    {
    	static $upload_size = NULL;

    	if ( $upload_size === NULL )
        {
    		$post_max_size = self::str_to_bytes( ini_get('post_max_size') );
    		$upload_max_filesize = self::str_to_bytes( ini_get('upload_max_filesize') );
    		$memory_limit = self::str_to_bytes( ini_get('memory_limit') );

    		if ( empty($post_max_size) && empty($upload_max_filesize) && empty($memory_limit) )
    			return false;

    		$upload_size = min($post_max_size, $upload_max_filesize, $memory_limit);
    	}

    	return $upload_size;
    }

    /**
     * Convierte el string del servidor, upload_max_filesize en bytes.
     *
     * @static
     *
     * @param   string    $value    Valor del servidor.
     *
     * @return   mixed
     */
    static private function str_to_bytes( $value )
    {
    	// only string
    	$unit_byte = strtolower(preg_replace('/[^a-zA-Z]/', '', $value));

    	// only number (allow decimal point)
        $num_val = (int) $value;

    	switch ( $unit_byte )
        {
    		case 'p':	// petabyte
    		case 'pb':
    			$num_val *= 1024;
    		case 't':	// terabyte
    		case 'tb':
    			$num_val *= 1024;
    		case 'g':	// gigabyte
    		case 'gb':
    			$num_val *= 1024;
    		case 'm':	// megabyte
    		case 'mb':
    			$num_val *= 1024;
    		case 'k':	// kilobyte
    		case 'kb':
    			$num_val *= 1024;
    		case 'b':	// byte
    			return $num_val *= 1;
    			break; // make sure
    		default:
    			return false;
        }

        return false;
    }

    /**
     * Establece las configuraciones.
     *
     * @param   array    $configs    Configuraciones.
     *
     * @return   void
     */
    public function set_configs( $configs = [] )
    {
        $this->configs = $configs;
    }

    /**
     * Establece el nombre del archivo.
     *
     * @param   string    $argv    nombre del archivo.
     * @param   boolean   $random  True, si desea que el nombre sea generado automaticamente.
     *
     * @return   void
     */
    public function set_file_name( $argv, $random = false )
    {
        if ( $random == true )
            $this->file_name = $this->security->random_string(16);
        else
        {
            $argv = explode('.', $argv);
            $argv = $argv[0];
            $argv = Security::clean_string( $argv );

            $this->file_name = str_replace('-', '_', $argv);
        }
    }

    /**
     * Establece archivo temporal a copiar.
     *
     * @param   string    $argv    nombre del archivo temporal.
     *
     * @return   void
     */
    public function set_temp_name( $argv )
    {
        $this->temp_name = $argv;
    }

    /**
     * Establece el tipo de archivo a copiar.
     *
     * @param   string    $argv    HTML Media Capture
     *
     * @return   void
     */
    public function set_file_type( $argv )
    {
        $this->file_type = explode( '/', $argv );
    }

    /**
     * Establece el tamaño del archivo.
     *
     * @param   integer    $argv    Tamaño del archivo.
     *
     * @return   void
     */
    public function set_file_size( $argv )
    {
        $this->file_size = $argv;
    }

    /**
     * Establece la direccion donde se guardara el archivo.
     *
     * @param   string    $argv    PATH
     *
     * @return   void
     */
    public function set_upload_directory( $argv )
    {
        $this->upload_directory = $argv;
    }

    /**
     * Establece la extensiones válidas.
     *
     * @param   array    $argv
     *
     * @return   void
     */
    public function set_valid_extensions( $argv )
    {
        $this->valid_extensions = $argv;
    }

    /**
     * Establece el maximo peso por archivo, para subir.
     *
     * @param   integer    $argv    Peso en bytes.
     *
     * @return   void
     */
    public function set_maximum_file_size( $argv )
    {
        $this->maximum_file_size = $argv;
    }

    /**
     * Valída la subida de un archivo.
     *
     * @return   array
     */
    public function validation()
    {
        if ( empty($this->maximum_file_size) )
            $this->maximum_file_size = self::get_maximum_file_size();

        if ( empty($this->upload_directory) )
            $this->upload_directory = PATH_UPLOADS;

        if ( empty( $this->valid_extensions ) )
        {
            if ( isset($this->configs['404']) && $this->configs['404'] == true )
                header("HTTP/1.0 404 Not Found");

            return [
                'status' => 'error',
                'message' => $this->get_code_errors('extension_not_valid')
            ];
        }

        if ( !isset($this->file_type[1]) || empty($this->file_type[1]) )
        {
            if ( isset($this->configs['404']) && $this->configs['404'] == true )
                header("HTTP/1.0 404 Not Found");

            return [
                'status' => 'error',
                'message' => $this->get_code_errors('file_error')
            ];
        }

        if ( !in_array( $this->file_type[1], $this->valid_extensions ) )
        {
            if ( isset($this->configs['404']) && $this->configs['404'] == true )
                header("HTTP/1.0 404 Not Found");

            return [
                'status' => 'error',
                'message' => $this->get_code_errors('file_not_allowed')
            ];
        }

        if ( $this->file_size > $this->maximum_file_size )
        {
            if ( isset($this->configs['404']) && $this->configs['404'] == true )
                header("HTTP/1.0 404 Not Found");

            return [
                'status' => 'error',
                'message' => $this->get_code_errors('file_is_larger')
            ];
        }

        return [
            'status' => 'success'
        ];
    }

    /**
     * Copia el archivo despues de validarlo.
     *
     * @return   array
     */
    public function copy()
    {
        $validation = $this->validation();

        if ( $validation['status'] == 'success' )
        {
            if ( isset($this->configs['tmp']) && $this->configs['tmp'] == true )
            {
                $tmp = explode(DIRECTORY_SEPARATOR, $this->temp_name);
                $key = count($tmp) - 1;

                $_tmp_file_type = explode('.', $tmp[$key]);
                $_tmp_file_name = $_tmp_file_type[0];
                $_tmp_upload_directory = Security::DS(PATH_ROOT . "/tmp");

                if ( !file_exists($_tmp_upload_directory) )
                {
                    if ( !mkdir($_tmp_upload_directory, 0600) )
                    {
                        if ( isset($this->configs['404']) && $this->configs['404'] == true )
                            header("HTTP/1.0 404 Not Found");

                        return [
                            'status' => 'error',
                            'message' => $this->get_code_errors('system_failed')
                        ];
                    }
                }

                $file = Security::DS("{$_tmp_upload_directory}/{$_tmp_file_name}");
            }
            else
                $file = Security::DS("{$this->upload_directory}/{$this->file_name}.{$this->file_type[1]}");

            if ( isset($this->configs['rename']) && $this->configs['rename'] == true && file_exists($file) )
            {
                $tmp = $this->security->random_string(5);
                $this->file_name = "{$this->file_name}_{$tmp}";
                $file = Security::DS("{$this->upload_directory}/{$this->file_name}.{$this->file_type[1]}");
            }

            if ( @copy( $this->temp_name, $file ) )
            {
                $file_name = $this->file_name;
                $file_type = $this->file_type;
                $file_size = $this->file_size;

                unset($this->file_name, $this->temp_name, $this->file_type, $this->file_size);

                return [
                    'status'    => 'success',
                    'name'      => "{$file_name}.{$file_type[1]}",
                    'type'      => "{$file_type[0]}/{$file_type[1]}",
                    'route'     => $file,
                    'size'      => $file_size
                ];
            }
            else
            {
                if ( isset($this->configs['404']) && $this->configs['404'] == true )
                    header("HTTP/1.0 404 Not Found");

                return [
                    'status' => 'error',
                    'message' => $this->get_code_errors('system_failed')
                ];
            }
        }
        else
            return $validation;
    }

    /**
     * Obtiene el mensaje del codigo de error enviado. El siguiente texto es el archivo de lenguaje.
     *
     * @example es_codes_uploads_class.ini
     *
     * ;Codigos de error para la clase Upload.class.php de Valkyrie
     *
     * extension_not_valid = "Extension no permitida por el sistema"
     * file_error = "Error en el tipo archivo"
     * file_not_allowed = "Archivo no permitido"
     * file_is_larger = "El archivo es demasiado grande"
     * system_failed = "El sistema no pudo guardar el archivo"
     *
     * @param   string    $code_error    Código de error a requerir.
     *
     * @return string
     */
    private function get_code_errors( $code_error )
    {
        $format = new Format();

        $path = ( Format::check_path_admin() ) ? PATH_ADMINISTRATOR_LANGUAGE : PATH_LANGUAGE;

        $ini = $format->import_file($path, Session::get_value('lang') . '_codes_uploads_class', 'ini');

        if ( !empty($ini) )
        {
            return $ini[$code_error];
        }
        else
            return "{". $code_error ."}";
    }
}
