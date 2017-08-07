<?php
defined ( "BASEPATH" ) or exit ( "No direct script access allowed" );
class SwaggerDoc extends CI_Controller {
	private $data_view;
	function __construct() {
		parent::__construct ();
	}
	public function index() {
		if (isset ( $_GET [''] )) {
		}
		// $api_host = (ENVIRONMENT == 'production') ? "plugin-billing.vidol.tv" : "cplugin-billing.vidol.tv";
		$api_host = $_SERVER ['HTTP_HOST'];
		$doc_array = array (
				"swagger" => "2.0",
				"info" => array (
						"title" => "RESTful API Documentation",
						"description" => "RESTful api control panel of technical documents.",
						"termsOfService" => "#",
						"contact" => array (
								"email" => "zeren828@gmail.com" 
						),
						"license" => array (
								"name" => "Apache 2.0",
								"url" => "#" 
						),
						"version" => "V 1.0" 
				),
				"host" => $api_host,
				"basePath" => "/v1",
				"tags" => array (
						array (
								"name" => "1.Auth",
								"description" => "1.登入" 
						),
						array (
								"name" => "2.Vote",
								"description" => "2.投票" 
						)
				),
				"schemes" => array (
						"http" 
				),
				"paths" => array (
						"/auth/login" => array (
								"post" => array (
										"tags" => array (
												"1.Auth" 
										),
										"summary" => "會員登入",
										"description" => "會員登入",
										"parameters" => array (
												array (
														"name" => "Authorization",
														"description" => "token",
														"in" => "header",
														"type" => "string",
														"required" => TRUE 
												),
												array (
														"name" => "username",
														"description" => "帳號",
														"in" => "formData",
														"type" => "string",
														"required" => TRUE 
												),
												array (
														"name" => "password",
														"description" => "密碼",
														"in" => "formData",
														"type" => "string",
														"required" => TRUE 
												)
										),
										"responses" => array (
												"200" => array (
														"description" => "成功",
														"schema" => array (
																"title" => "result",
																"type" => "object",
																"description" => "api result data",
																"properties" => array (
																		"result" => $this->__get_responses_data ( "ml_api_oauth_token" ),
																		"code" => array (
																				"type" => "string",
																				"description" => "狀態碼" 
																		),
																		"message" => array (
																				"type" => "string",
																				"description" => "訊息" 
																		),
																		"time" => array (
																				"type" => "string",
																				"description" => "耗費時間" 
																		) 
																) 
														) 
												),
												"401" => array (
														"description" => "會員登入錯誤" 
												),
												"403" => array (
														"description" => "token未授權" 
												),
												"416" => array (
														"description" => "傳遞資料錯誤" 
												) 
										) 
								) 
						),
						"/votes/vote" => array (
								"get" => array (
										"tags" => array (
												"2.Vote" 
										),
										"summary" => "投票清單",
										"description" => "投票清單",
										"parameters" => array (
												array (
														"name" => "Authorization",
														"description" => "token",
														"in" => "header",
														"type" => "string",
														"required" => TRUE 
												),
												array (
														"name" => "username",
														"description" => "帳號",
														"in" => "formData",
														"type" => "string",
														"required" => TRUE 
												),
												array (
														"name" => "password",
														"description" => "密碼",
														"in" => "formData",
														"type" => "string",
														"required" => TRUE 
												)
										),
										"responses" => array (
												"200" => array (
														"description" => "成功",
														"schema" => array (
																"title" => "result",
																"type" => "object",
																"description" => "api result data",
																"properties" => array (
																		"result" => $this->__get_responses_data ( "ml_api_oauth_token" ),
																		"code" => array (
																				"type" => "string",
																				"description" => "狀態碼" 
																		),
																		"message" => array (
																				"type" => "string",
																				"description" => "訊息" 
																		),
																		"time" => array (
																				"type" => "string",
																				"description" => "耗費時間" 
																		) 
																) 
														) 
												),
												"401" => array (
														"description" => "會員登入錯誤" 
												),
												"403" => array (
														"description" => "token未授權" 
												),
												"416" => array (
														"description" => "傳遞資料錯誤" 
												) 
										) 
								),
								"post" => array (
										"tags" => array (
												"2.Vote" 
										),
										"summary" => "投票",
										"description" => "投票",
										"parameters" => array (
												array (
														"name" => "Authorization",
														"description" => "token",
														"in" => "header",
														"type" => "string",
														"required" => TRUE 
												),
												array (
														"name" => "username",
														"description" => "帳號",
														"in" => "formData",
														"type" => "string",
														"required" => TRUE 
												),
												array (
														"name" => "password",
														"description" => "密碼",
														"in" => "formData",
														"type" => "string",
														"required" => TRUE 
												)
										),
										"responses" => array (
												"200" => array (
														"description" => "成功",
														"schema" => array (
																"title" => "result",
																"type" => "object",
																"description" => "api result data",
																"properties" => array (
																		"result" => $this->__get_responses_data ( "ml_api_oauth_token" ),
																		"code" => array (
																				"type" => "string",
																				"description" => "狀態碼" 
																		),
																		"message" => array (
																				"type" => "string",
																				"description" => "訊息" 
																		),
																		"time" => array (
																				"type" => "string",
																				"description" => "耗費時間" 
																		) 
																) 
														) 
												),
												"401" => array (
														"description" => "會員登入錯誤" 
												),
												"403" => array (
														"description" => "token未授權" 
												),
												"416" => array (
														"description" => "傳遞資料錯誤" 
												) 
										) 
								) 
						) 
				) 
		);
		$this->output->set_content_type ( "application/json" );
		$this->output->set_output ( json_encode ( $doc_array ) );
	}
	
	/**
	 * 回傳的資料整理
	 *
	 * @param unknown $type        	
	 * @return string[]
	 */
	function __get_responses_data($type) {
		$responses = array ();
		switch ($type) {
			case "ml_api_oauth_token" :
				$responses = array (
						"title" => "user login info",
						"type" => "object",
						"description" => "會員登入訊息",
						"properties" => array (
								"access_token" => array (
										"type" => "string",
										"description" => "access token" 
								),
								"token_type" => array (
										"type" => "string",
										"description" => "token type" 
								),
								"expires_in" => array (
										"type" => "integer",
										"description" => "有效時限" 
								),
								"refresh_token" => array (
										"type" => "string",
										"description" => "refresh token" 
								),
								"created_at" => array (
										"type" => "string",
										"description" => "建立時間" 
								),
								"status_code" => array (
										"type" => "string",
										"description" => "序號" 
								),
								"message" => array (
										"type" => "string",
										"description" => "會員等級" 
								)
						) 
				);
				break;
			default :
				$responses = array (
						"description" => "OK" 
				);
				break;
		}
		return $responses;
	}
}

/* End of file swaggerDoc.php */
/* Location: ./application/controllers/v1/swaggerDoc.php */
