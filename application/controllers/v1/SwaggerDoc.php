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
														"type" => "string" 
												),
												array (
														"name" => "password",
														"description" => "密碼",
														"in" => "formData",
														"type" => "string" 
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
																		"result" => $this->__get_responses_data ( "user level info" ),
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
			case "user level info" :
				$responses = array (
						"title" => "user level info",
						"type" => "object",
						"description" => "會員等級資訊",
						"properties" => array (
								"no" => array (
										"type" => "integer",
										"description" => "序號" 
								),
								"title" => array (
										"type" => "integer",
										"description" => "會員等級" 
								),
								"tag" => array (
										"type" => "string",
										"description" => "會員等級代號" 
								) 
						) 
				);
				break;
			case "order info" :
				$responses = array (
						"title" => "order info",
						"type" => "object",
						"description" => "訂單資訊",
						"properties" => array (
								"order_sn" => array (
										"type" => "integer",
										"description" => "訂單序號" 
								),
								"package_no" => array (
										"type" => "integer",
										"description" => "產品包編號" 
								),
								"package_title" => array (
										"type" => "string",
										"description" => "產品包標題" 
								),
								"coupon_sn" => array (
										"type" => "string",
										"description" => "序號" 
								),
								"coupon_title" => array (
										"type" => "string",
										"description" => "序號設定標題" 
								),
								"expenses" => array (
										"type" => "integer",
										"description" => "折扣" 
								),
								"subtotal" => array (
										"type" => "integer",
										"description" => "實際付金額" 
								),
								"status" => array (
										"type" => "integer",
										"description" => "狀態(-1:fail,0:pending,1:success,2:cancel)" 
								),
								"createdAt" => array (
										"type" => "string",
										"description" => "建立時間" 
								),
								"activeAt" => array (
										"type" => "string",
										"description" => "啟用時間" 
								),
								"deadlineAt" => array (
										"type" => "string",
										"description" => "到期時間" 
								),
								"note" => array (
										"type" => "string",
										"description" => "備註" 
								) 
						) 
				);
				break;
			case "package info" :
				$responses = array (
						"title" => "package info",
						"type" => "object",
						"description" => "產品包資訊",
						"properties" => array (
								"no" => array (
										"type" => "integer",
										"description" => "產品編號" 
								),
								"title" => array (
										"type" => "string",
										"description" => "標題" 
								),
								"description" => array (
										"type" => "string",
										"description" => "描述" 
								),
								"cost" => array (
										"type" => "integer",
										"description" => "成本" 
								),
								"price" => array (
										"type" => "integer",
										"description" => "銷售價錢" 
								),
								"createdAt" => array (
										"type" => "string",
										"description" => "建立時間" 
								),
								"updatedAt" => array (
										"type" => "string",
										"description" => "更新時間" 
								) 
						) 
				);
				break;
			case "payment info" :
				$responses = array (
						"title" => "payment info",
						"type" => "object",
						"description" => "金流通路",
						"properties" => array (
								"no" => array (
										"type" => "integer",
										"description" => "通路號碼" 
								),
								"title" => array (
										"type" => "string",
										"description" => "通路" 
								),
								"description" => array (
										"type" => "string",
										"description" => "描述" 
								),
								"proxy" => array (
										"type" => "string",
										"description" => "代理商代號(spgateway:智付通,pay2go:智付寶)" 
								),
								"type" => array (
										"type" => "string",
										"description" => "付款類型(CREDIT:信用卡,WEBATM:WEBATM,VACC:ATM轉帳,CVS:超商代碼,BARCODE:條碼)" 
								) 
						) 
				);
				break;
			case "pagination info" :
				$responses = array (
						"title" => "pagination info",
						"type" => "object",
						"description" => "分頁資訊",
						"properties" => array (
								"page_previous" => array (
										"type" => "integer",
										"description" => "上一頁數" 
								),
								"page" => array (
										"type" => "integer",
										"description" => "當前頁數" 
								),
								"page_next" => array (
										"type" => "integer",
										"description" => "下一頁數" 
								),
								"page_size" => array (
										"type" => "integer",
										"description" => "每頁資料筆數" 
								),
								"page_total" => array (
										"type" => "integer",
										"description" => "總頁數" 
								),
								"count_total" => array (
										"type" => "integer",
										"description" => "總資料筆數" 
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
