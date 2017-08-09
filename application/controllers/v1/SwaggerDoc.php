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
						"/auth/vidol" => array (
								"post" => array (
										"tags" => array (
												"1.Auth" 
										),
										"summary" => "Vidol會員登入",
										"description" => "Vidol會員登入",
										"parameters" => array (
												array (
														"name" => "Authorization",
														"description" => "token",
														"in" => "header",
														"type" => "string",
														"required" => TRUE 
												),
												array (
														"name" => "random",
														"description" => "隨機碼(JQ:$.now();)",
														"in" => "formData",
														"type" => "integer",
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
												"408" => array (
														"description" => "請求超時" 
												),
												"416" => array (
														"description" => "傳遞資料錯誤" 
												) 
										) 
								) 
						),
						"/auth/facebook" => array (
								"post" => array (
										"tags" => array (
												"1.Auth" 
										),
										"summary" => "FaceBook會員登入",
										"description" => "FaceBook會員登入",
										"parameters" => array (
												array (
														"name" => "Authorization",
														"description" => "token",
														"in" => "header",
														"type" => "string",
														"required" => TRUE 
												),
												array (
														"name" => "random",
														"description" => "隨機碼(JQ:$.now();)",
														"in" => "formData",
														"type" => "integer",
														"required" => TRUE 
												),
												array (
														"name" => "uid",
														"description" => "Uid",
														"in" => "formData",
														"type" => "string",
														"required" => TRUE 
												),
												array (
														"name" => "facebook_token",
														"description" => "Facebook Token",
														"in" => "formData",
														"type" => "string",
														"required" => TRUE 
												),
												array (
														"name" => "expiration",
														"description" => "expiration",
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
												"408" => array (
														"description" => "請求超時" 
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
														"name" => "config_id",
														"description" => "投票設定號碼",
														"in" => "query",
														"type" => "integer",
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
														"name" => "random",
														"description" => "隨機碼(JQ:$.now();)",
														"in" => "formData",
														"type" => "integer",
														"required" => TRUE 
												),
												array (
														"name" => "token",
														"description" => "token",
														"in" => "formData",
														"type" => "string",
														"required" => TRUE 
												),
												array (
														"name" => "config_id",
														"description" => "設定檔ID",
														"in" => "formData",
														"type" => "string",
														"required" => TRUE 
												),
												array (
														"name" => "item_id",
														"description" => "項目ID",
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
																		"result" => $this->__get_responses_data ( "vote_item_info" ),
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
			case "vote_item_info" :
				$responses = array (
						"title" => "vote item info",
						"type" => "object",
						"description" => "投票項目資料",
						"properties" => array (
								"id" => array (
										"type" => "string",
										"description" => "項目ID" 
								),
								"config" => array (
										"type" => "string",
										"description" => "設定ID" 
								),
								"group" => array (
										"type" => "integer",
										"description" => "分群" 
								),
								"title" => array (
										"type" => "string",
										"description" => "標題" 
								),
								"des" => array (
										"type" => "string",
										"description" => "描述" 
								),
								"img" => array (
										"type" => "string",
										"description" => "圖片網址" 
								),
								"url" => array (
										"type" => "string",
										"description" => "連結網址" 
								),
								"proportion" => array (
										"type" => "string",
										"description" => "得票率" 
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
