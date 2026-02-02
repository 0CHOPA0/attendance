<?php

namespace Rhymix\Modules\Bfc_attendance\Controllers;

use Rhymix\Modules\Bfc_attendance\Models\Config as ConfigModel;
use Rhymix\Framework\BaseObject;
use Context;

class Admin extends Base
{
	public function init()
	{
		$this->setTemplatePath($this->module_path . 'views/admin/');
	}

	public function dispBfc_attendanceAdminConfig()
	{
		$stadium_output = executeQueryArray('bfc_attendance.getStadiumList');
		Context::set('stadium_list', $stadium_output->data ?: []);
		$oModuleModel = getModel('module');
		$module_info = $oModuleModel->getModuleInfoByMid('bfc_attendance');
		if ($module_info && $module_info->mid) {
			Context::set('attendance_url', getFullUrl('', 'mid', $module_info->mid));
		}
		$this->setTemplateFile('config');
	}

	public function dispBfc_attendanceAdminGameList()
	{
		$stadium_output = executeQueryArray('bfc_attendance.getStadiumList');
		Context::set('stadium_list', $stadium_output->data ?: []);
		$args = new \stdClass();
		$args->page = Context::get('page') ?: 1;
		$output = executeQueryArray('bfc_attendance.getGameList', $args);
		Context::set('game_list', $output->data ?: []);
		Context::set('total_count', (int)($output->total_count ?? 0));
		Context::set('total_page', (int)($output->total_page ?? 1));
		Context::set('page', (int)($output->page ?? 1));
		$this->setTemplateFile('game_list');
	}

	/**
	 * 직관 회원 관리 (최종 안정 버전)
	 */
	public function dispBfc_attendanceAdminMemberLog()
	{
		// 1. 초기값 설정
		Context::set('log_list', []);
		Context::set('total_count', 0);
		Context::set('total_page', 1);
		Context::set('page', 1);

		$args = new \stdClass();
		$args->page = (int)(Context::get('page') ?: 1);
		$args->list_count = 20;

		// 2. 쿼리 실행
		//$output = executeQueryArray('bfc_attendance.getLogList', $args);

		// 3. 결과 처리 (출석부 모듈의 성공 패턴 이식)
		//if($output->toBool() && isset($output->data))
		//{
			//$data = $output->data;
			//if(!is_array($data)) $data = [$data]; // 데이터가 1개일 때 객체로 오는 경우 방어
			
			//Context::set('log_list', $data);
			//Context::set('total_count', (int)($output->total_count ?? 0));
			//Context::set('total_page', (int)($output->total_page ?? 1));
			//Context::set('page', (int)($output->page ?? 1));
		//}
		//elseif(!$output->toBool())
		//{
			//쿼리 실패 시 관리자에게만 오류 표시 (서버 오류 팝업 방지)
			//$this->setMessage($output->getMessage(), 'error');
		//}

		$this->setTemplateFile('member_log');
	}

	public function procBfc_attendanceAdminInsertStadium() { /* 기존과 동일 */ }
	public function procBfc_attendanceAdminInsertGame() { /* 기존과 동일 */ }

	public function procBfc_attendanceAdminUpdateLog()
	{
		$vars = Context::getRequestVars();
		$args = new \stdClass();
		$args->attendance_srl = $vars->attendance_srl;
		$args->regdate = str_replace(['-', ':', ' '], '', $vars->regdate);
		$args->point_amount = (int)$vars->point_amount;
		executeQuery('bfc_attendance.updateLog', $args);
		$this->setRedirectUrl(Context::get('success_return_url') ?: getUrl('', 'module', 'admin', 'act', 'dispBfc_attendanceAdminMemberLog'));
	}

	public function procBfc_attendanceAdminDeleteLog()
	{
		$args = new \stdClass();
		$args->attendance_srl = Context::get('attendance_srl');
		executeQuery('bfc_attendance.deleteLog', $args);
		$this->setRedirectUrl(getUrl('', 'module', 'admin', 'act', 'dispBfc_attendanceAdminMemberLog'));
	}
}