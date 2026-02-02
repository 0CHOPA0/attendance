<?php
namespace Rhymix\Modules\Bfc_attendance\Controllers;

use Rhymix\Framework\DB;

class Install extends Base
{
	public function moduleInstall()
	{
		return $this->moduleUpdate();
	}

	/**
	 * 업데이트 체크를 비활성화하여 강제 알림을 제거합니다.
	 */
	public function checkUpdate()
	{
		return false;
	}

	public function moduleUpdate()
	{
		$db = DB::getInstance();

		// 기본 테이블 생성 로직만 유지
		if (!$db->isTableExists('bfc_attendance_log')) {
			$db->createTableByXml($this->module_path . 'schemas/bfc_attendance_log.xml');
		}
		if (!$db->isTableExists('bfc_attendance_games')) {
			$db->createTableByXml($this->module_path . 'schemas/bfc_attendance_games.xml');
		}
		if (!$db->isTableExists('bfc_attendance_stadiums')) {
			$db->createTableByXml($this->module_path . 'schemas/bfc_attendance_stadiums.xml');
		}

		return new \BaseObject();
	}
}