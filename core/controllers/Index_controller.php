<?php
defined('_EXEC') or die;

class Index_controller extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index( $params )
	{
		echo $this->view->render($this, 'index');
	}
}
