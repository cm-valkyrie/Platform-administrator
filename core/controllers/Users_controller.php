<?php
defined('_EXEC') or die;

class Users_controller extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index( $params )
	{
		/* Action Ajax ------------------------------------------------------ */
		if ( Format::exist_ajax_request() )
		{
			$_POST['action'] = ( isset($_POST['action']) ) ? $_POST['action'] : '';

			switch ( $_POST['action'] )
			{
				case 'get_user':
					$this->get_data_user( $_POST['id'] );
					break;

				default:
					header("HTTP/1.0 404 Not Found");
					break;
			}
		}
		else
		{
			global $users, $levels, $permissions, $ladas;

			$users = $this->model->get_all_users();
			$levels = $this->model->get_all_levels();
			$permissions = $this->model->get_all_permissions();
			$ladas = $this->format->import_file(PATH_ADMINISTRATOR_INCLUDES, 'code_countries_lada', 'json');

			define('_title', 'Lista de usuarios registrados en {$vkye_webpage}');
			echo $this->view->render($this, 'index');
		}
	}

	private function get_data_user( $id = null )
	{
		echo json_encode([
			'status' => 'OK',
			'data' => $this->model->get_data_user($id)
		], JSON_PRETTY_PRINT);
	}

	public function create_user()
	{
		/* Action Ajax ------------------------------------------------------ */
		if ( Format::exist_ajax_request() )
		{
			$post['name'] = ( isset($_POST['name']) && !empty($_POST['name']) ) ? $_POST['name'] : null;
			$post['username'] = ( isset($_POST['username']) && !empty($_POST['username']) ) ? $_POST['username'] : null;
			$post['email'] = ( isset($_POST['email']) && !empty($_POST['email']) ) ? $_POST['email'] : null;
			$post['prefix'] = ( isset($_POST['prefix']) && !empty($_POST['prefix']) ) ? $_POST['prefix'] : null;
			$post['phone'] = ( isset($_POST['phone']) && !empty($_POST['phone']) ) ? $_POST['phone'] : null;
			$post['password'] = ( isset($_POST['password']) && !empty($_POST['password']) ) ? $_POST['password'] : null;
			$post['level'] = ( isset($_POST['level']) && !empty($_POST['level']) ) ? $_POST['level'] : null;
			$post['permissions'] = ( isset($_POST['permissions']) && !empty($_POST['permissions']) ) ? $_POST['permissions'] : null;

			$labels = [];

			if ( is_null($post['name']) )
				array_push($labels, ['name', 'Escribe el nombre completo.']);

			if ( is_null($post['username']) )
				array_push($labels, ['username', 'Escribe el usuario.']);

			if ( is_null($post['email']) )
				array_push($labels, ['email', 'Escribe el correo electrónico.']);

			if ( is_null($post['prefix']) )
				array_push($labels, ['prefix', 'Selecciona un prefijo de teléfono.']);

			if ( is_null($post['phone']) )
				array_push($labels, ['phone', 'Registra un número telefónico.']);

			if ( is_null($post['password']) )
				array_push($labels, ['password', 'Debe tener una contraseña.']);

			if ( is_null($post['level']) )
				array_push($labels, ['level', 'Selecciona un nivel de usuario.']);

			if ( !empty($labels) )
			{
				echo json_encode([
					'status' => 'error',
					'labels' => $labels
				], JSON_PRETTY_PRINT);
			}
			else
			{
				$this->model->create_user($post);

				echo json_encode([
					'status' => 'OK',
					'redirect' => 'index.php?c=users'
				], JSON_PRETTY_PRINT);
			}
		}
		else
			Errors::http('404');
	}

	public function update_data_user()
	{
		/* Action Ajax ------------------------------------------------------ */
		if ( Format::exist_ajax_request() )
		{
			$post['id'] = ( isset($_POST['id']) && !empty($_POST['id']) ) ? $_POST['id'] : null;
			$post['name'] = ( isset($_POST['name']) && !empty($_POST['name']) ) ? $_POST['name'] : null;
			$post['username'] = ( isset($_POST['username']) && !empty($_POST['username']) ) ? $_POST['username'] : null;
			$post['email'] = ( isset($_POST['email']) && !empty($_POST['email']) ) ? $_POST['email'] : null;
			$post['prefix'] = ( isset($_POST['prefix']) && !empty($_POST['prefix']) ) ? $_POST['prefix'] : null;
			$post['phone'] = ( isset($_POST['phone']) && !empty($_POST['phone']) ) ? str_replace(['(',')','-',' '], '', $_POST['phone']) : null;
			$post['password'] = ( isset($_POST['password']) && !empty($_POST['password']) ) ? $_POST['password'] : null;
			$post['level'] = ( isset($_POST['level']) && !empty($_POST['level']) ) ? $_POST['level'] : null;
			$post['permissions'] = ( isset($_POST['permissions']) && !empty($_POST['permissions']) ) ? $_POST['permissions'] : null;

			$labels = [];

			if ( is_null($post['id']) )
				array_push($labels, ['id', 'Hace falta seleccionar un usuario en el sistema.']);

			if ( is_null($post['name']) )
				array_push($labels, ['name', 'Escribe el nombre completo.']);

			if ( is_null($post['username']) )
				array_push($labels, ['username', 'Escribe el usuario.']);

			if ( is_null($post['email']) )
				array_push($labels, ['email', 'Escribe el correo electrónico.']);

			if ( is_null($post['prefix']) )
				array_push($labels, ['prefix', 'Selecciona un prefijo de teléfono.']);

			if ( is_null($post['level']) )
				array_push($labels, ['level', 'Selecciona un nivel de usuario.']);

			if ( !empty($labels) )
			{
				echo json_encode([
					'status' => 'error',
					'labels' => $labels
				], JSON_PRETTY_PRINT);
			}
			else
			{
				$this->model->update_data_user($post);

				echo json_encode([
					'status' => 'OK',
					'redirect' => 'index.php?c=users'
				], JSON_PRETTY_PRINT);
			}
		}
		else
			Errors::http('404');
	}

	public function delete_user()
	{
		header('Content-type: application/json');

		$post['id'] = ( isset($_POST['id']) && !empty($_POST['id']) ) ? $_POST['id'] : null;

		if ( is_null($post['id']) )
		{
			echo json_encode([
				'status' => 'error',
				'message' => 'Debes elegir un usuario.'
			], JSON_PRETTY_PRINT);
		}
		else if ( Session::get_value('_vkye_id_user') === $post['id'] )
		{
			echo json_encode([
				'status' => 'error',
				'message' => 'No puedes eliminar tu usuario.'
			], JSON_PRETTY_PRINT);
		}
		else
		{
			$this->model->delete_user($post);

			echo json_encode([
				'status' => 'OK',
				'redirect' => 'index.php?c=users'
			], JSON_PRETTY_PRINT);
		}
	}

	public function create_permission()
	{
		/* Action Ajax ------------------------------------------------------ */
		if ( Format::exist_ajax_request() )
		{
			$post['code'] = ( isset($_POST['code']) && !empty($_POST['code']) ) ? $_POST['code'] : null;
			$post['title'] = ( isset($_POST['title']) && !empty($_POST['title']) ) ? $_POST['title'] : null;

			$labels = [];

			if ( is_null($post['code']) )
				array_push($labels, ['code', 'Debes escribir el código del permiso.']);

			if ( is_null($post['title']) )
				array_push($labels, ['title', 'Escribe una pequeña descripción.']);

			if ( !empty($labels) )
			{
				echo json_encode([
					'status' => 'error',
					'labels' => $labels
				], JSON_PRETTY_PRINT);
			}
			else
			{
				$this->model->create_permission($post);

				echo json_encode([
					'status' => 'OK',
					'redirect' => 'index.php?c=users'
				], JSON_PRETTY_PRINT);
			}
		}
		else
			Errors::http('404');
	}

	public function delete_permission()
	{
		header('Content-type: application/json');

		$post['id'] = ( isset($_POST['id']) && !empty($_POST['id']) ) ? $_POST['id'] : null;

		if ( is_null($post['id']) )
		{
			echo json_encode([
				'status' => 'error',
				'message' => 'Debes elegir un permiso.'
			], JSON_PRETTY_PRINT);
		}
		else
		{
			$this->model->delete_permission($post);

			echo json_encode([
				'status' => 'OK',
				'redirect' => 'index.php?c=users'
			], JSON_PRETTY_PRINT);
		}
	}
}
