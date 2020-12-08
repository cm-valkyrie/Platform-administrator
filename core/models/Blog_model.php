<?php
defined('_EXEC') or die;

class Blog_model extends Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function get_articles()
	{
		return $this->database->select("blog", [
			"[>]blog_categories" => [
				"id_category" => "id"
			],
			"[>]users" => [
				"author" => "id"
			]
		], [
			'blog.id',
			'blog.url',
			'blog.title [Object]',
			'blog.publication_date',
			'blog_categories.category [Object]',
			'users.username',
		], [
			'status[!]' => 'removed',
			'ORDER' => [
				'blog.publication_date' => 'DESC'
			]
		]);
	}

	public function get_article( $id = null )
	{
		if ( is_null($id) )
			return null;

		$response = $this->database->select("blog", [
			'url',
			'title [Object]',
			'id_category',
			'image',
			'article [JSON]',
			'sm_title [Object]',
			'sm_description [Object]',
			'sm_image',
			'tags [Object]',
		], [
			'id' => $id
		]);

		return ( isset($response[0]) && !empty($response[0]) ) ? $response[0] : null;
	}

	public function get_categories()
	{
		return $this->database->select('blog_categories', [
			'id',
			'category [Object]',
			'description [Object]'
		]);
	}

	public function create_category( $data = null )
	{
		if ( is_null($data) )
			return null;

		$this->database->insert('blog_categories', [
			'category' => [ Configuration::$lang_default => $data['category'] ],
			'description' => [ Configuration::$lang_default => $data['description'] ]
		]);
	}

	public function save_article( $data = [], $edit = false )
	{
		if ( empty($data) )
			return null;

		if ( $edit == false )
		{
			$data['url'] = $this->security->clean_string($data['title']);
			$data['url'] = str_replace('_', '-', $data['url']);
		}

		$data['title'] = [ Configuration::$lang_default => $data['title'] ];
		$data['description'] = json_encode([ Configuration::$lang_default => $data['description'] ]);
		$data['tags'] = ( !is_null($data['tags']) ) ? explode(',', trim($data['tags'], ',')) : null;
		$data['sm_title'] = [ Configuration::$lang_default => $data['sm_title'] ];
		$data['sm_description'] = [ Configuration::$lang_default => $data['sm_description'] ];

		$upload = new Upload();
		$upload->set_valid_extensions(['jpeg', 'jpg', 'png']);

		if ( !is_null($data['image_cover']) )
		{
			$upload->set_file_name(explode('.', $data['image_cover']['name'])[0] .'_'. $this->security->random_string(6) .'.'. explode('.', $data['image_cover']['name'])[1]);
			$upload->set_temp_name($data['image_cover']['tmp_name']);
			$upload->set_file_type($data['image_cover']['type']);
			$upload->set_file_size($data['image_cover']['size']);

			$image = $upload->copy();

			if ( $image['status'] == 'success' )
			{
				$data['image_cover'] = $image['name'];
				unset($image);
			}
			else
			{
				return [
					'status' => 'error',
					'labels' => [['image_cover', $image['message']]]
				];
			}
		}

		if ( !is_null($data['sm_image_cover']) )
		{
			$upload->set_file_name(explode('.', $data['sm_image_cover']['name'])[0] .'_'. $this->security->random_string(6) .'.'. explode('.', $data['sm_image_cover']['name'])[1]);
			$upload->set_temp_name($data['sm_image_cover']['tmp_name']);
			$upload->set_file_type($data['sm_image_cover']['type']);
			$upload->set_file_size($data['sm_image_cover']['size']);

			$image = $upload->copy();

			if ( $image['status'] == 'success' )
			{
				$data['sm_image_cover'] = $image['name'];
				unset($image);
			}
			else
			{
				return [
					'status' => 'error',
					'labels' => [['sm_image_cover', $image['message']]]
				];
			}
		}

		$save = [];
		$save['title'] = $data['title'];
		$save['id_category'] = $data['category'];
		$save['article'] = $data['description'];
		$save['sm_title'] = $data['sm_title'];
		$save['sm_description'] = $data['sm_description'];
		$save['tags'] = $data['tags'];
		$save['author'] = Session::get_value('_vkye_id_user');

		if ( $edit == false )
			$save['url'] = $data['url'];

		if ( !is_null($data['image_cover']) )
			$save['image'] = $data['image_cover'];

		if ( !is_null($data['sm_image_cover']) )
			$save['sm_image'] = $data['sm_image_cover'];

		if ( $edit == false )
		{
			$this->database->insert('blog', $save);

			if ( $this->database->id() )
			{
				return [
					'status' => 'OK'
				];
			}
			else
			{
				return [
					'status' => 'error',
					'labels' => [['title', 'Ocurrió un problema al guardar el artículo.']]
				];
			}
		}

		if ( $edit == true )
		{
			$this->database->update('blog', $save, [
				'id' => $data['id']
			]);

			return [
				'status' => 'OK'
			];
		}
	}

	public function delete_article( $data )
	{
		$this->database->update('blog', [
			'status' => 'removed'
		],[
			'id' => $data['id']
		]);
	}

	public function delete_category( $data = null )
	{
		if ( is_null($data) )
			return null;

		$this->database->delete('blog_categories', [
			'id' => $data['id']
		]);
	}
}
