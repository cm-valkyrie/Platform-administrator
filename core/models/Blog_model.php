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
		/*  */ return null;

		$response = $this->database->select("blog", [
			'url',
			'title [Object]',
			'id_category',
			'image',
			'article [Object]',
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
		/*  */ return null;

		$this->database->insert('blog_categories', [
			'category' => [ Configuration::$lang_default => $data['category'] ],
			'description' => [ Configuration::$lang_default => $data['description'] ]
		]);
	}

	public function save_article( $data = [], $edit = false )
	{
		if ( empty($data) )
		/*  */ return null;

		$config_uploads = [
			'path_uploads' => PATH_UPLOADS,
			'set_name' => 'FILE_NAME_LAST_RANDOM'
		];

		$save = [
			'title' => [ Configuration::$lang_default => $data['title'] ],
			'id_category' => $data['category'],
			'article' => [ Configuration::$lang_default => json_encode($data['description']) ],
			'tags' => ( !is_null($data['tags']) ) ? explode(',', trim($data['tags'], ',')) : null,
			'author' => Session::get_value('_vkye_id_user'),
			'sm_title' => [ Configuration::$lang_default => $data['sm_title'] ],
			'sm_description' => [ Configuration::$lang_default => $data['sm_description'] ]
		];

		if ( !empty($data['image_cover']['name']) )
		{
			$data['image_cover'] = Upload::upload_file($data['image_cover'], $config_uploads);
			$save['image'] = $data['image_cover']['file'];
		}

		if ( !empty($data['sm_image_cover']['name']) )
		{
			$data['sm_image_cover'] = Upload::upload_file($data['sm_image_cover'], $config_uploads);
			$save['sm_image'] = $data['sm_image_cover']['file'];
		}

		if ( $edit == false )
		{
			$data['url'] = $this->security->clean_string($data['title']);
			$data['url'] = str_replace('_', '-', $data['url']);
			$save['url'] = $data['url'];

			$this->database->insert('blog', $save);

			if ( $this->database->id() )
			/*  */ return [ 'status' => 'OK' ];
			else
			{
				return [
					'status' => 'fatal_error',
					'message' => 'Ocurrió un problema al guardar el artículo.'
				];
			}
		}

		if ( $edit == true )
		{
			$this->database->update('blog', $save, [
				'id' => $data['id']
			]);

			return [ 'status' => 'OK' ];
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
		/*  */ return null;

		$this->database->delete('blog_categories', [
			'id' => $data['id']
		]);
	}
}
