@load('config.scss')

<div class="x_page-header">
    <h1>{{ $lang->cmd_bfc_attendance }}</h1>
</div>

<ul class="x_nav x_nav-tabs">
    <li class="@if($act == 'dispBfc_attendanceAdminConfig') x_active @endif">
        <a href="{{ getUrl('','module','admin','act','dispBfc_attendanceAdminConfig') }}">경기장 관리</a>
    </li>
    <li class="@if($act == 'dispBfc_attendanceAdminGameList') x_active @endif">
        <a href="{{ getUrl('','module','admin','act','dispBfc_attendanceAdminGameList') }}">경기 목록 관리</a>
    </li>
    <li class="@if($act == 'dispBfc_attendanceAdminMemberLog') x_active @endif">
        <a href="{{ getUrl('','module','admin','act','dispBfc_attendanceAdminMemberLog') }}">직관 회원 관리</a>
    </li>
</ul>

<div class="x_pull-right" style="margin-bottom:10px;">
    <button type="button" class="x_btn x_btn-primary" onclick="jQuery('#game_form_area').toggle(); reset_game_form();"> + 새 경기 등록 </button>
</div>

<div id="game_form_area" style="display:none; background:#f9f9f9; padding:20px; border:1px solid #ddd; margin-bottom:20px; border-radius:5px;">
    <form class="x_form-horizontal" action="./" method="post">
        <input type="hidden" name="module" value="bfc_attendance" />
        <input type="hidden" name="act" value="procBfc_attendanceAdminInsertGame" />
		<input type="hidden" name="success_return_url" value="{{ getUrl('', 'module', 'admin', 'act', 'dispBfc_attendanceAdminGameList') }}" />
        <input type="hidden" name="game_srl" id="game_srl" value="" />
        
        <input type="hidden" name="lat" id="lat" value="" />
        <input type="hidden" name="lon" id="lon" value="" />

        <div class="x_control-group">
            <label class="x_control-label">경기 명칭</label>
            <div class="x_controls">
                <input type="text" name="game_title" id="game_title" value="" placeholder="예: [K리그1] 부천 vs 전북" required />
            </div>
        </div>

        <div class="x_control-group">
            <label class="x_control-label">경기 일자</label>
            <div class="x_controls">
                <input type="date" name="game_date" id="game_date" value="" required />
            </div>
        </div>

        <div class="x_control-group">
            <label class="x_control-label">경기장 선택</label>
            <div class="x_controls">
                <select id="stadium_selector" onchange="update_stadium_coords(this)" required>
                    <option value="">-- 경기장을 선택하세요 --</option>
                    @foreach($stadium_list as $stadium)
                        <option value="{{ $stadium->stadium_srl }}" data-lat="{{ $stadium->lat }}" data-lon="{{ $stadium->lon }}">
                            {{ $stadium->title }} ({{ $stadium->lat }}, {{ $stadium->lon }})
                        </option>
                    @endforeach
                </select>
                <p class="x_help-block">경기장 관리 탭에서 등록한 장소 리스트가 표시됩니다.</p>
            </div>
        </div>

        <div class="x_control-group">
            <label class="x_control-label">허용 반경 (m)</label>
            <div class="x_controls">
                <input type="number" name="limit_distance" id="limit_distance" value="100" placeholder="예: 100" style="width:100px;" required />
                <span class="x_help-inline">미터(m) 단위로 입력하세요. (기본 100m)</span>
            </div>
        </div>

        <div class="x_control-group">
            <label class="x_control-label">지급 포인트</label>
            <div class="x_controls">
                <input type="number" name="point" id="point" value="0" style="width:100px;" />
                <span class="x_help-inline">인증 성공 시 지급할 포인트를 입력하세요.</span>
            </div>
        </div>

        <div class="x_form-footer">
            <button type="submit" class="x_btn x_btn-primary">경기 저장</button>
            <button type="button" class="x_btn" onclick="jQuery('#game_form_area').hide();">취소</button>
        </div>
    </form>
</div>

<table class="x_table x_table-striped x_table-hover">
    <thead>
        <tr>
            <th>번호</th>
            <th>경기 명칭</th>
            <th>일자</th>
            <th>좌표(위도/경도)</th>
            <th>허용 반경</th>
            <th>포인트</th>
            <th>관리</th>
        </tr>
    </thead>
    <tbody>
        @if(!empty($game_list))
            @foreach($game_list as $game)
            <tr>
                <td>{{ $game->game_srl }}</td>
                <td><strong>{{ $game->title }}</strong></td>
                <td>{{ substr($game->game_date, 0, 4) }}-{{ substr($game->game_date, 4, 2) }}-{{ substr($game->game_date, 6, 2) }}</td>
                <td><small>{{ $game->lat }}, {{ $game->lon }}</small></td>
                <td>{{ $game->limit_distance }}m</td>
                <td>{{ number_format($game->point) }}</td>
                <td>
                    <button type="button" class="x_btn x_btn-mini" onclick="edit_game('{{ $game->game_srl }}', '{{ $game->title }}', '{{ substr($game->game_date, 0, 4) }}-{{ substr($game->game_date, 4, 2) }}-{{ substr($game->game_date, 6, 2) }}', '{{ $game->lat }}', '{{ $game->lon }}', '{{ $game->limit_distance }}', '{{ $game->point }}')">수정</button>
                    <form action="./" method="post" style="display:inline;">
						<input type="hidden" name="module" value="bfc_attendance" />
						<input type="hidden" name="act" value="procBfc_attendanceAdminDeleteGame" />
						<input type="hidden" name="game_srl" value="{{ $game->game_srl }}" />
						<input type="hidden" name="success_return_url" value="{{ getUrl('', 'module', 'admin', 'act', 'dispBfc_attendanceAdminGameList') }}" />
						<button type="submit" class="x_btn x_btn-mini x_btn-danger" onclick="return confirm('삭제하시겠습니까?')">삭제</button>
					</form>
                </td>
            </tr>
            @endforeach
        @else
            <tr><td colspan="7" style="text-align:center;">등록된 경기가 없습니다.</td></tr>
        @endif
    </tbody>
</table>

@if($total_page > 1)
    <div class="x_pagination x_pull-left">
        <ul>
            @for($i = 1; $i <= $total_page; $i++)
                <li class="@if($page == $i) x_active @endif"><a href="{{ getUrl('page', $i) }}">{{ $i }}</a></li>
            @endfor
        </ul>
    </div>
@endif

<script>
/**
 * 드롭박스 선택 시 숨겨진 위도/경도 필드 업데이트
 */
function update_stadium_coords(obj) {
    const selected = jQuery(obj).find('option:selected');
    const lat = selected.data('lat');
    const lon = selected.data('lon');
    
    jQuery('#lat').val(lat);
    jQuery('#lon').val(lon);
}

/**
 * 경기 수정 시 폼에 데이터 채우기
 */
function edit_game(srl, title, date, lat, lon, distance, point) {
    jQuery('#game_form_area').show();
    jQuery('#game_srl').val(srl);
    jQuery('#game_title').val(title);
    jQuery('#game_date').val(date);
    jQuery('#lat').val(lat);
    jQuery('#lon').val(lon);
    jQuery('#limit_distance').val(distance);
    jQuery('#point').val(point);
    
    // 드롭박스에서 해당 좌표를 가진 항목 선택 상태로 변경 (매칭 안될 경우 대비)
    jQuery('#stadium_selector option').each(function() {
        if (jQuery(this).data('lat') == lat && jQuery(this).data('lon') == lon) {
            jQuery(this).prop('selected', true);
        }
    });

    window.scrollTo(0, 0);
}

/**
 * 폼 초기화
 */
function reset_game_form() {
    jQuery('#game_srl').val('');
    jQuery('#game_title').val('');
    jQuery('#game_date').val('');
    jQuery('#lat').val('');
    jQuery('#lon').val('');
    jQuery('#limit_distance').val('100');
    jQuery('#point').val('0');
    jQuery('#stadium_selector').val('');
}
</script>