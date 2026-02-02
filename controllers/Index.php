<?php
namespace Rhymix\Modules\Bfc_attendance\Controllers;

use Rhymix\Framework\DB;
use Rhymix\Modules\Bfc_attendance\Models\Config as ConfigModel;
use Context;

class Index extends Base
{
    public function init()
    {
        $this->setTemplatePath($this->module_path . 'views/');
    }

    public function dispBfc_attendanceIndex()
    {
        $args = new \stdClass();
        $args->game_date = date('Ymd');
        $output = executeQueryArray('bfc_attendance.getGameList', $args); // 날짜 조건은 쿼리에서 처리하거나 컨트롤러에서 필터링
        
        Context::set('game_list', $output->data ?: []);
        $this->setTemplateFile('index');
    }

    /**
     * GPS 인증 처리 핵심 로직
     */
    public function procBfc_attendanceIndexLog()
    {
        // 1. 로그인 체크
        if (!Context::get('is_logged')) {
            return new \BaseObject(-1, '로그인이 필요합니다.');
        }

        $logged_info = Context::get('logged_info');
        $vars = Context::getRequestVars();
        
        $user_lat = (float)$vars->lat;
        $user_lon = (float)$vars->lon;
        $game_srl = (int)$vars->game_srl;

        if (!$game_srl || !$user_lat || !$user_lon) {
            return new \BaseObject(-1, '잘못된 요청입니다. GPS 정보를 확인해주세요.');
        }

        // 2. 해당 경기 정보 가져오기
        $args = new \stdClass();
        $args->game_srl = $game_srl;
        $game_output = executeQuery('bfc_attendance.getGame', $args); // 아래에서 쿼리 추가 예정
        $game = $game_output->data;

        if (!$game) return new \BaseObject(-1, '존재하지 않는 경기입니다.');

        // 3. 중복 인증 체크 (오늘 이미 인증했는지)
        $check_args = new \stdClass();
        $check_args->member_srl = $logged_info->member_srl;
        $check_args->regdate_more = date('Ymd000000');
        $check_args->regdate_less = date('Ymd235959');
        $log_output = executeQuery('bfc_attendance.checkLogToday', $check_args);
        
        if ($log_output->data) {
            return new \BaseObject(-1, '오늘은 이미 인증을 완료하셨습니다.');
        }

        // 4. 거리 계산 (Haversine Formula)
        $distance = $this->_getDistance($user_lat, $user_lon, (float)$game->lat, (float)$game->lon);
        $limit = (int)($game->limit_distance ?: 100); // 경기별 허용 반경 적용

        if ($distance > $limit) {
            return new \BaseObject(-1, sprintf('경기장과의 거리가 너무 멉니다. (현재 거리: %dm / 허용 반경: %dm)', $distance, $limit));
        }

        // 5. 인증 성공 처리: 로그 저장 및 포인트 지급
        $db = DB::getInstance();
        $db->begin();

        try {
            // 로그 저장
            $log_args = new \stdClass();
			$log_args->attendance_srl = getNextSequence();
			$log_args->game_srl = $game_srl; // 이 줄을 추가하세요
			$log_args->member_srl = $logged_info->member_srl;
			$log_args->regdate = date('YmdHis');
			$log_args->ipaddress = $_SERVER['REMOTE_ADDR'];
			$log_args->point_amount = $game->point;
			executeQuery('bfc_attendance.insertLog', $log_args);
            // 포인트 지급 (포인트 모듈 연동)
            if ($game->point > 0) {
                $oPointController = getController('point');
                $oPointController->setPoint($logged_info->member_srl, $game->point, 'add');
            }

            $db->commit();
        } catch (\Exception $e) {
            $db->rollback();
            return new \BaseObject(-1, '인증 처리 중 오류가 발생했습니다.');
        }

        $this->setMessage(sprintf('직관 인증 성공! %d포인트가 지급되었습니다.', $game->point));
    }

    /**
     * 두 좌표 간의 직선 거리 계산 (단위: m)
     */
    private function _getDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earth_radius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return (int)($earth_radius * $c);
    }
}