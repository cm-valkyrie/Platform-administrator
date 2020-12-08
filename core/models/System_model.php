<?php
defined('_EXEC') or die;

class System_model extends Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function get_user( $data = null )
	{
		if ( is_null($data) )
			return false;

		$query = $this->database->select("users", [
			"[>]levels" => [
				"id_level" => "id"
			]
		], [
			"users.id",
			"users.username",
			"users.email",
			"users.password",
			"users.permissions [Object]",
			"levels.code"
		], [
			"AND" => [
				'OR' => [
					"email" => $data['username'],
					"username" => $data['username']
				],
				"users.id_level[=]levels.id"
			]
		]);

		return ( isset($query[0]) && !empty($query[0]) ) ? $query[0] : null;
	}

	public function log_session( $data = null, $action = 'login' )
	{
		if ( is_null($data) )
			return false;

		switch ( $action )
		{
			case 'login':
			default:
				$this->database->insert('sessions', [
					'id_user' => $data['id_user'],
					'token' => $data['token'],
					'login_date' => date('Y-m-d H:i:s'),
					'connection' => Security::get_client_info()
				]);

				if ( $this->database->id() )
					return true;
				else
					return false;
				break;

			case 'logout':
				$this->database->update('sessions', [
					'logout_date' => date('Y-m-d H:i:s')
				], [
					'AND' => [
						'id_user' => $data['id_user'],
						'token' => $data['token']
					]
				]);

				return true;
				break;
		}
	}
}
