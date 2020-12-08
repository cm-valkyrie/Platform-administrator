<?php
defined('_EXEC') or die;

class Help_controller extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index( $params )
	{
		define('_title', 'Framework Valkyrie');
		echo $this->view->render($this, 'index');
	}
}
