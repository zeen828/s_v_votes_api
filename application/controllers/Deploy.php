<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
ini_set ( "display_errors", "On" );
class Deploy extends CI_Controller {
	private $data_debug;
	private $data_result;
	public function __construct() {
		parent::__construct ();
		$this->config->load ( 'github_deploy' );
		$this->data_debug = $this->config->item ( 'github_deploy_debug' );
	}
	public function __destruct() {
		unset ( $this->data_debug );
		unset ( $this->data_result );
	}
	public function receive() {
		try {
			// 變數
			$data_input = array ();
			$data_config = array ();
			$data_system = array ();
			// 接收變數
			$data_input ['project'] = strtolower ( $this->input->get ( 'project' ) );
			$data_input ['key'] = $this->input->post ( 'key' );
			$data_input ['payload'] = $this->input->post ( 'payload' );
			$data_config ['git_path'] = $this->config->item ( 'git_path' );
			$data_config ['local_project_name'] = $this->config->item ( 'local_project_name' );
			$data_config ['github'] = $this->config->item ( 'github' );
			// 沒有指定專案就拿本地專案比較
			if (empty ( $data_input ['project'] )) {
				$data_input ['project'] = $data_config ['local_project_name'];
			}
			// 接收的資料反解
			$payload = json_decode ( $data_input ['payload'] );
			// 必要資訊是否短缺?
			if (! empty ( $data_input ['project'] ) && ! empty ( $data_input ['key'] ) && ! empty ( $data_input ['payload'] ) && ! empty ( $payload->ref )) {
				// 取出github傳來的branch
				$branch = str_replace ( 'refs/heads/', '', $payload->ref );
				unset ( $payload );
				// 設定檔是否包含該專案?
				if (array_key_exists ( $data_input ['project'], $data_config ['github'] )) {
					$config = $data_config ['github'] [$data_input ['project']];
					// 設定檔是否包含該分支?
					if (array_key_exists ( $branch, $config )) {
						$config = $config [$branch];
						// 有無設定key與路徑?
						if (array_key_exists ( 'secret_key', $config ) && array_key_exists ( 'project_path', $config )) {
							// key
							if ($data_input ['key'] == $config ['secret_key']) {
								$git_path = $data_config ['git_path'];
								$base_path = realpath ( $config ['project_path'] ) . '/';
								$shell = sprintf ( '%s --git-dir="%s.git" --work-tree="%s" reset --hard HEAD', $git_path, $base_path, $base_path );
								$data_system ['shell_reset'] = $shell;
								$output = shell_exec ( escapeshellcmd ( $shell ) );
								$data_system ['shell_reset_output'] = $output;
								if (! empty ( $config ['git_clean'] )) {
									$shell = sprintf ( '%s --git-dir="%s.git" --work-tree="%s" clean -f', $git_path, $base_path, $base_path );
									$data_system ['shell_clean'] = $shell;
									$output = shell_exec ( escapeshellcmd ( $shell ) );
									$data_system ['shell_clean_output'] = $output;
								}
								$shell = sprintf ( '%s --git-dir="%s.git" --work-tree="%s" pull origin %s', $git_path, $base_path, $base_path, $branch );
								$data_system ['shell_pull'] = $shell;
								$output = shell_exec ( escapeshellcmd ( $shell ) );
								$data_system ['shell_pull_output'] = $output;
							} else {
								$this->data_result ['message'] = 'key錯誤';
							}
						} else {
							$this->data_result ['message'] = '無設定key與路徑';
						}
					} else {
						$this->data_result ['message'] = '設定檔不包含該分支';
					}
				} else {
					$this->data_result ['message'] = '設定檔不包含該專案';
				}
			} else {
				$this->data_result ['message'] = '必要資訊短缺';
			}
			if ($this->data_debug) {
				$this->data_result ['data_input'] = $data_input;
				$this->data_result ['data_config'] = $data_config;
				$this->data_result ['data_system'] = $data_system;
				log_message ( 'debug', json_encode ( $data_system, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) );
			}
			return $this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $this->data_result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) );
		} catch ( Exception $e ) {
			show_error ( $e->getMessage () . ' --- ' . $e->getTraceAsString () );
		}
	}
}
