<?php
defined('_EXEC') or die;

/**
*
* @package Valkyrie.Libraries.Administrator
*
* @since 1.0.0
* @version 1.0.0
* @license You can see LICENSE.txt
*
* @author David Miguel Gómez Macías < davidgomezmacias@gmail.com >
* @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
*/

class Cpanel
{
    /**
    *
    * @var object
    */
    private $framework;

    /**
    *
    * @var object
    */
    private $security;

    /**
    *
    * @var object
    */
    private $render;

    /**
    *
    * @var object
    */
    private $language;

    /**
    *
    * @var object
    */
    private $format;

    /**
    *
    * @var object
    */
    private $system;

    /**
    *
    * @var string
    */
    private $controller;

    /**
    *
    * @var string
    */
    private $method;

    /**
    *
    * @var string
    */
    private $params;

    /**
    * Constructor.
    *
    * @return  void
    */
    public function __construct()
    {
        $this->framework = new Framework();
        $this->security = new Security();
        $this->render = new Render();
        $this->language = new Language();
        $this->format = new Format();
        $this->system = new System();

        $this->load_page();
    }

    /**
    * Imprime todo el contendio ya procesado.
    *
    * @final
    *
    * @return  string
    */
    public function execute()
    {
        ob_start("ob_gzhandler");

        // Load template
        $this->load_controller();

        if ( !defined('_title') ) define('_title', Configuration::$web_page . ' - ' . Framework::PRODUCT);

        if ( !defined('_lang') ) define('_lang', Configuration::$lang_default);

        $buffer = ob_get_contents();

        // Rendering
        $buffer = $this->render($buffer);

        ob_end_clean();
        return $buffer;
    }

    /**
    * Prepara las variables a partir de la url para solicitar el controlador y metodos.
    *
    * @return void
    */
    private function load_page()
    {
        if ( $this->system->exists_session() )
        {
            $this->controller = isset ($_GET['c']) ? ucwords ( Security::clean_string( $_GET['c'] ) ) : 'Index';
            $this->method = isset ($_GET['m']) ? strtolower ( Security::clean_string( $_GET['m'] ) ) : 'index';

            unset($_GET['c'], $_GET['m']);

            $this->params = isset ($_GET) ? $_GET : [];

            if ( $this->controller === 'System' && $this->method === 'login' ) $this->system->go_to_location('Index');

            if (  $this->controller !== 'System' && $this->method !== 'logout' )
            {
                $permission = System::permissions($this->controller, $this->method);

                if ( !isset($permission) && $permission != true || empty($permission) )
                {
                    $this->controller = 'System';
                    $this->method     = 'permissions_denied';
                }
            }
        }
        else
        {
            $this->controller   = 'System';
            $this->method       = 'login';
            $this->params       = '';
        }
    }

    /**
    * Obtiene la informacion del controlador solicitado.
    *
    * @return  void
    */
    private function load_controller()
    {
        $path = Security::DS(PATH_ADMINISTRATOR_CONTROLLERS . $this->controller . CONTROLLER_PHP . '.php');

        if ( file_exists($path) )
        {
            require_once $path;

            $controller = $this->controller . CONTROLLER_PHP;
            $controller = new $controller();

            if ( isset($this->method) )
            {
                if ( method_exists($controller, $this->method) )
                {
                    if ( file_exists(Security::DS(PATH_ADMINISTRATOR_MY_LIBRARIES . 'Route_vkye_adm' . CLASS_PHP)) )
                    {
                        $this->route = new Route_vkye_adm("/{$this->controller}/{$this->method}");

                        $this->route->on_change_start();
                    }

                    if ( isset($this->params) ) $controller->{$this->method}($this->params);
                    else $controller->{$this->method}();

                    if ( file_exists(Security::DS(PATH_ADMINISTRATOR_MY_LIBRARIES . 'Route_vkye_adm' . CLASS_PHP)) ) $this->route->on_change_end();
                }
                else Errors::http('404', '{method_does_exists}');
            }
            else $controller->index();
        }
        else Errors::http('404', '{controller_does_exists}');
    }

    /**
    * Renderiza todo el buffer remplazando placeholder.
    *
    * @param   string    $buffer    Buffer pre-cargado.
    *
    * @return  string
    */
    private function render( $buffer )
    {
        if ( file_exists(Security::DS(PATH_ADMINISTRATOR_MY_LIBRARIES . 'Placeholders_vkye_adm' . CLASS_PHP)) )
        {
            $placeholders = new Placeholders_vkye_adm( $buffer );
            $buffer = $placeholders->run();
        }

        $buffer = Language::get_lang($buffer);
        $buffer = $this->render->placeholders($buffer);
        $buffer = $this->render->paths($buffer);

        if ( Configuration::$compress_html === true )
        {
            $buffer = preg_replace(array('//Uis', "/[[:blank:]]+/"), array('', ' '), str_replace(array("\n", "\r", "\t"), '', $buffer));
        }

        return $buffer;
    }
}
