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

<div style="padding: 15px; background: #f0fdf4; border: 1px solid #bbf7d0; margin-bottom: 20px; border-radius: 8px; color: #166534;">
    <h3 style="margin-top:0;">🔗 사용자 인증 페이지</h3>
    <p>아래 주소를 복사하여 메뉴에 등록하세요.</p>
    <div style="display: flex; align-items: center; gap: 10px;">
        <input type="text" value="{{ $attendance_url }}" readonly style="width: 400px; padding: 8px;" />
        <a href="{{ $attendance_url }}" target="_blank" class="x_btn x_btn-primary">🌐 페이지 바로가기</a>
    </div>
</div>

<section class="section">
    <h1>경기장 등록</h1>
    <form class="x_form-horizontal" action="./" method="post">
        <input type="hidden" name="module" value="bfc_attendance" />
		<input type="hidden" name="act" value="procBfc_attendanceAdminInsertStadium" />
		<input type="hidden" name="success_return_url" value="{{ getUrl('', 'module', 'admin', 'act', 'dispBfc_attendanceAdminConfig') }}" />
		<input type="hidden" name="stadium_srl" id="stadium_srl" value="" />

        <div class="x_control-group">
            <label class="x_control-label">경기장 명칭</label>
            <div class="x_controls">
                <input type="text" name="stadium_title" id="stadium_title" value="" placeholder="예: 부천종합운동장" required />
            </div>
        </div>
        <div class="x_control-group">
            <label class="x_control-label">위도(Lat)</label>
            <div class="x_controls">
                <input type="text" name="lat" id="lat" value="" placeholder="예: 37.5033" required />
            </div>
        </div>
        <div class="x_control-group">
            <label class="x_control-label">경도(Lon)</label>
            <div class="x_controls">
                <input type="text" name="lon" id="lon" value="" placeholder="예: 126.7900" required />
            </div>
        </div>
        <div class="x_form-footer">
            <button type="submit" class="x_btn x_btn-primary">저장</button>
            <button type="button" class="x_btn" onclick="location.reload();">취소</button>
        </div>
    </form>
</section>

<section class="section">
    <h1>등록된 경기장 리스트</h1>
    <table class="x_table x_table-striped x_table-hover">
        <thead>
            <tr>
                <th>명칭</th>
                <th>위도</th>
                <th>경도</th>
                <th>등록일</th>
                <th>관리</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stadium_list as $stadium)
            <tr>
                <td><strong>{{ $stadium->title }}</strong></td>
                <td>{{ $stadium->lat }}</td>
                <td>{{ $stadium->lon }}</td>
                <td>{{ zdate($stadium->regdate, 'Y-m-d') }}</td>
                <td>
                    <button type="button" class="x_btn x_btn-mini" onclick="edit_stadium('{{ $stadium->stadium_srl }}', '{{ $stadium->title }}', '{{ $stadium->lat }}', '{{ $stadium->lon }}')">수정</button>
					<form action="./" method="post" style="display:inline;">
						<input type="hidden" name="module" value="bfc_attendance" />
						<input type="hidden" name="act" value="procBfc_attendanceAdminDeleteStadium" />
						<input type="hidden" name="stadium_srl" value="{{ $stadium->stadium_srl }}" />
						<input type="hidden" name="success_return_url" value="{{ getUrl('', 'module', 'admin', 'act', 'dispBfc_attendanceAdminConfig') }}" />
						<button type="submit" class="x_btn x_btn-mini x_btn-danger" onclick="return confirm('삭제하시겠습니까?')">삭제</button>
					</form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</section>

<script>
function edit_stadium(srl, title, lat, lon) {
    jQuery('#stadium_srl').val(srl);
    jQuery('#stadium_title').val(title);
    jQuery('#lat').val(lat);
    jQuery('#lon').val(lon);
    window.scrollTo(0, 0);
}
</script>