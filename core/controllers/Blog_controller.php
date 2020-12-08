<?php
defined('_EXEC') or die;

class Blog_controller extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index( $params )
	{
		global $articles, $categories;

		$articles = $this->model->get_articles();
		$categories = $this->model->get_categories();

		define('_title', 'Artículos del blog - {$vkye_webpage}');
		echo $this->view->render($this, 'index');
	}

	private function validate_article( $edit = false )
	{
		$post['title'] = ( isset($_POST['title']) && !empty($_POST['title']) ) ? $_POST['title'] : null;
		$post['category'] = ( isset($_POST['category']) && !empty($_POST['category']) ) ? $_POST['category'] : null;
		$post['description'] = ( isset($_POST['description']) && !empty($_POST['description']) ) ? $_POST['description'] : null;
		$post['tags'] = ( isset($_POST['tags']) && !empty($_POST['tags']) ) ? trim($_POST['tags']) : null;
		$post['image_cover'] = ( isset($_FILES['image_cover']) && $_FILES['image_cover']['error'] == 0 && $_FILES['image_cover']['size'] > 0 ) ? $_FILES['image_cover'] : null;
		$post['sm_title'] = ( isset($_POST['sm_title']) && !empty($_POST['sm_title']) ) ? $_POST['sm_title'] : null;
		$post['sm_description'] = ( isset($_POST['sm_description']) && !empty($_POST['sm_description']) ) ? $_POST['sm_description'] : null;
		$post['sm_image_cover'] = ( isset($_FILES['sm_image_cover']) && $_FILES['sm_image_cover']['error'] == 0 && $_FILES['sm_image_cover']['size'] > 0 ) ? $_FILES['sm_image_cover'] : null;

		$labels = [];

		if ( is_null($post['title']) || strlen($post['title']) < 5 )
			array_push($labels, ['title', 'Escribe un título con más de 5 caracteres.']);

		if ( is_null($post['description']) || strlen($post['description']) < 20 )
			array_push($labels, ['description', 'Escribe una descripción más larga de 20 caracteres.']);

		if ( $edit == false )
		{
			if ( is_null($post['image_cover']) )
				array_push($labels, ['image_cover', 'Debes poner una imágen de portada.']);
		}

		if ( !empty($labels) )
		{
			return [
				'status' => 'error',
				'labels' => $labels
			];
		}
		else
		{
			return [
				'status' => 'OK',
				'post' => $post
			];
		}
	}

	public function create_article( $params )
	{
		/* Action Ajax ------------------------------------------------------ */
		if ( Format::exist_ajax_request() )
		{
			$response = $this->validate_article();

			if ( $response['status'] == 'error' )
			{
				echo json_encode($response, JSON_PRETTY_PRINT);
			}
			else
			{
				$response = $this->model->save_article( $response['post'] );

				if ( $response['status'] == 'error' )
				{
					echo json_encode([
						'status' => 'error',
						'labels' => $response['labels']
					], JSON_PRETTY_PRINT);
				}
				else
				{
					echo json_encode([
						'status' => 'OK',
						'redirect' => 'index.php?c=blog'
					], JSON_PRETTY_PRINT);
				}
			}
		}
		else
		{
			global $categories;

			$categories = $this->model->get_categories();

			define('_title', 'Crear nuevo artículo en el blog - {$vkye_webpage}');
			echo $this->view->render($this, 'new');
		}
	}

	public function update_article( $params )
	{
		if ( isset($params['id']) && !empty($params['id']) )
		{
			$response = $this->model->get_article($params['id']);

			if ( isset($response) && !empty($response) )
			{
				/* Action Ajax ------------------------------------------------------ */
				if ( Format::exist_ajax_request() )
				{
					$response = $this->validate_article(true);

					if ( $response['status'] == 'error' )
					{
						echo json_encode($response, JSON_PRETTY_PRINT);
					}
					else
					{
						$response['post']['id'] = $params['id'];

						$response = $this->model->save_article( $response['post'], true );

						if ( $response['status'] == 'error' )
						{
							echo json_encode([
								'status' => 'error',
								'labels' => $response['labels']
							], JSON_PRETTY_PRINT);
						}
						else
						{
							echo json_encode([
								'status' => 'OK',
								'redirect' => 'index.php?c=blog&m=update_article&id='. $params['id']
							], JSON_PRETTY_PRINT);
						}
					}
				}
				else
				{
					global $categories, $article;

					$categories = $this->model->get_categories();
					$article = $response;

					define('_title', $article['title'][Configuration::$lang_default] .' - {$vkye_webpage}');
					echo $this->view->render($this, 'update');
				}
			}
			else
				Errors::http('404');
		}
		else
			Errors::http('404');
	}

	public function delete_article()
	{
		header('Content-type: application/json');

		$post['id'] = ( isset($_POST['id']) && !empty($_POST['id']) ) ? $_POST['id'] : null;

		if ( is_null($post['id']) )
		{
			echo json_encode([
				'status' => 'error',
				'message' => 'Debes seleccionar un artículo.'
			], JSON_PRETTY_PRINT);
		}
		else
		{
			$this->model->delete_article($post);

			echo json_encode([
				'status' => 'OK',
				'redirect' => 'index.php?c=users'
			], JSON_PRETTY_PRINT);
		}
	}

	public function create_category()
	{
		/* Action Ajax ------------------------------------------------------ */
		if ( Format::exist_ajax_request() )
		{
			$post['category'] = ( isset($_POST['category']) && !empty($_POST['category']) ) ? $_POST['category'] : null;
			$post['description'] = ( isset($_POST['description']) && !empty($_POST['description']) ) ? $_POST['description'] : null;

			$labels = [];

			if ( is_null($post['category']) )
				array_push($labels, ['category', 'Debes escribir el nombre de la categoría.']);

			if ( is_null($post['description']) )
				array_push($labels, ['description', 'Escribe una pequeña descripción.']);

			if ( !empty($labels) )
			{
				echo json_encode([
					'status' => 'error',
					'labels' => $labels
				], JSON_PRETTY_PRINT);
			}
			else
			{
				$this->model->create_category($post);

				echo json_encode([
					'status' => 'OK',
					'redirect' => 'index.php?c=blog'
				], JSON_PRETTY_PRINT);
			}
		}
		else
			Errors::http('404');
	}

	public function delete_category()
	{
		header('Content-type: application/json');

		$post['id'] = ( isset($_POST['id']) && !empty($_POST['id']) ) ? $_POST['id'] : null;

		if ( is_null($post['id']) )
		{
			echo json_encode([
				'status' => 'error',
				'message' => 'Debes elegir una categoría.'
			], JSON_PRETTY_PRINT);
		}
		else
		{
			$this->model->delete_category($post);

			echo json_encode([
				'status' => 'OK',
				'redirect' => 'index.php?c=blog'
			], JSON_PRETTY_PRINT);
		}
	}
}
