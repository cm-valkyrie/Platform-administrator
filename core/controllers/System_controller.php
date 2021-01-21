<?php
defined('_EXEC') or die;

class System_controller extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->login();
	}

	public function login()
	{
		/* Action Ajax ------------------------------------------------------ */
		if ( Format::exist_ajax_request() )
		{
			header('Content-type: application/json');

			$_POST['action'] = ( isset($_POST['action']) ) ? $_POST['action'] : 'default';

			switch ( $_POST['action'] )
			{
				case 'login':
				default:
					$post['username'] =
						( isset($_POST['username']) && !empty($_POST['username']) ) ? $_POST['username'] : null;
					$post['password'] =
						( isset($_POST['password']) && !empty($_POST['password']) ) ? $_POST['password'] : null;

					$labels = [];

					if ( is_null($post['username']) )
						array_push($labels, ['username', 'Por favor, escriba el correo electrónico con el que está registrado.']);

					if ( is_null($post['password']) )
						array_push($labels, ['password', 'Por favor, escriba una contraseña.']);

					if ( !empty($labels) )
					{
						echo json_encode([
							'status' => 'error',
							'labels' => $labels,
							'message' => 'Por favor, inicie sesión.'
						], JSON_PRETTY_PRINT);
					}
					else
					{
						$user = $this->model->get_user($post);

						if ( is_null($user) )
						{
							echo json_encode([
								'status' => 'error',
								'labels' => [
									['username', 'El correo electrónico no se encuentra registrado.']
								]
							], JSON_PRETTY_PRINT);
						}
						else
						{
							$crypto = explode(':', $user['password']);
							$check_password = ( $this->security->create_hash('sha1', $post['password'] . $crypto[1]) === $crypto[0] ) ? true : false;

							if ( $check_password == false )
							{
								echo json_encode([
									'status' => 'error',
									'labels' => [
										['password', 'La contraseña es incorrecta.']
									],
								], JSON_PRETTY_PRINT);
							}
							else
							{
								$token = $this->security->random_string(128);

								if ( $this->model->log_session([ 'id_user' => $user['id'], 'token' => $token ]) )
								{
									setcookie('_vkye_token', $user['id'] .':'. $this->security->random_string(4) .':'. $token, time() + (Configuration::$cookie_lifetime * 30), "/");

									Session::create_session_login([
										'token' => $token,
										'id_user' => $user['id'],
										'user' => $user['username'],
										'last_access' => Format::get_date_hour(),
										'level' => $user['code']
									]);

									Session::set_value('session_permissions', $user['permissions']);
								}

								echo json_encode([
									"status" => "OK",
									"redirect" => 'index.php'
								], JSON_PRETTY_PRINT);
							}
						}
					}

					break;
			}
		}
		else
		{
			define('_title', 'Iniciar Sesion - Panel de Control');

			$template = $this->view->render($this, [
				'head' => [
					"path" => PATH_ADMINISTRATOR_LAYOUTS . "Login",
					"file" => "head"
				],
				'main' => [
					"path" => PATH_ADMINISTRATOR_LAYOUTS . "Login",
					"file" => "index"
				],
				'footer' => [
					"path" => PATH_ADMINISTRATOR_LAYOUTS . "Login",
					"file" => "footer"
				]
			]);

			echo $template;
		}
	}

	public function logout()
	{
		$this->model->log_session([ 'id_user' => Session::get_value('_vkye_id_user'), 'token' => Session::get_value('_vkye_token') ], 'logout');

		setcookie("_vkye_token", null, -1, '/');
		Session::destroy();
		$this->system->go_to_location();
	}

	public function permissions_denied()
	{
		Errors::http('other', '{permissions_denied}', 'permissions_denied');
	}

	public function get_url()
	{
		if ( Format::exist_ajax_request() && isset($_POST['string']) )
		{
			$data['url'] = $this->security->clean_string($_POST['string']);
			$data['url'] = str_replace('_', '-', $data['url']);

			echo json_encode([
				'status' => 'OK',
				'url' => $data['url']
			], JSON_PRETTY_PRINT);
		}
	}

	public function validate_image()
	{
		if ( Format::exist_ajax_request() )
		{
			$image = Upload::validate_file($_FILES['image'], ['jpg', 'jpeg', 'png']);

			if ( $image['status'] == 'OK' )
			{
				$token = $this->security->random_string('5');

				echo json_encode([
					'status' => 'OK',
					'token' => $token
				], JSON_PRETTY_PRINT);
			}
			else
			{
				echo json_encode([
					'status' => 'fatal_error',
					'message' => $image['message']
				], JSON_PRETTY_PRINT);
			}
		}
	}
}
