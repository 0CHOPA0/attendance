@load('config.scss')

<div class="x_page-header">
    <h1>직관 인증 관리</h1>
</div>

<ul class="x_nav x_nav-tabs">
    <li class="@if($act == 'dispBfc_attendanceAdminConfig') x_active @endif"><a href="{{ getUrl('','module','admin','act','dispBfc_attendanceAdminConfig') }}">경기장 관리</a></li>
    <li class="@if($act == 'dispBfc_attendanceAdminGameList') x_active @endif"><a href="{{ getUrl('','module','admin','act','dispBfc_attendanceAdminGameList') }}">경기 목록 관리</a></li>
    <li class="@if($act == 'dispBfc_attendanceAdminMemberLog') x_active @endif"><a href="{{ getUrl('','module','admin','act','dispBfc_attendanceAdminMemberLog') }}">직관 회원 관리</a></li>
</ul>

<section class="section">
    <table class="x_table x_table-striped x_table-hover">
        <thead>
            <tr>
                <th>번호</th>
                <th>경기명</th>
                <th>닉네임(ID)</th>
                <th>인증일시</th>
                <th>포인트</th>
                <th>관리</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($log_list) && count($log_list) > 0)
                @foreach($log_list as $log)
                <tr>
                    <td>{{ $log->attendance_srl }}</td>
                    <td>{{ $log->game_title ?? '정보 없음' }}</td>
                    <td>
                        @if(!empty($log->nick_name))
                            <strong>{{ $log->nick_name }}</strong> ({{ $log->user_id ?? 'N/A' }})
                        @else
                            <span style="color:#ccc">탈퇴 회원</span>
                        @endif
                    </td>
                    <td>{{ zdate($log->regdate ?? '', "Y-m-d H:i:s") }}</td>
                    <td>{{ number_format($log->point_amount ?? 0) }}</td>
                    <td>
                        <button type="button" class="x_btn x_btn-mini" onclick="edit_log('{{ $log->attendance_srl }}', '{{ zdate($log->regdate ?? '', 'Y-m-d H:i:s') }}', '{{ $log->point_amount ?? 0 }}')">수정</button>
                        <form action="./" method="post" style="display:inline;">
                            <input type="hidden" name="module" value="bfc_attendance" />
                            <input type="hidden" name="act" value="procBfc_attendanceAdminDeleteLog" />
                            <input type="hidden" name="attendance_srl" value="{{ $log->attendance_srl }}" />
                            <input type="hidden" name="success_return_url" value="{{ Context::getRequestUri() }}" />
                            <button type="submit" class="x_btn x_btn-mini x_btn-danger" onclick="return confirm('삭제하시겠습니까?')">삭제</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6" style="text-align:center; padding:50px 0;">인증 내역이 없거나 쿼리 오류가 발생했습니다.</td>
                </tr>
            @endif
        </tbody>
    </table>
</section>

{{-- 페이지네이션 --}}
@if(isset($total_page) && $total_page > 1)
    <div class="x_pagination">
        <ul>
            @for($i = 1; $i <= $total_page; $i++)
                <li class="@if(isset($page) && $page == $i) x_active @endif"><a href="{{ getUrl('page', $i) }}">{{ $i }}</a></li>
            @endfor
        </ul>
    </div>
@endif

{{-- 수정 모달 레이어 --}}
<div id="log_modify_layer" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:#fff; border:1px solid #ddd; box-shadow:0 0 20px rgba(0,0,0,0.3); z-index:1001; width:350px; padding:20px; border-radius:10px;">
    <h3 style="margin-top:0;">인증 기록 수정</h3>
    <form action="./" method="post" class="x_form-horizontal">
        <input type="hidden" name="module" value="bfc_attendance" />
        <input type="hidden" name="act" value="procBfc_attendanceAdminUpdateLog" />
        <input type="hidden" name="attendance_srl" id="mod_attendance_srl" />
        <input type="hidden" name="success_return_url" value="{{ Context::getRequestUri() }}" />
        <div style="margin-bottom:10px;">
            <label style="font-weight:bold;">인증 일시</label>
            <input type="text" name="regdate" id="mod_regdate" style="width:100%;" />
        </div>
        <div style="margin-bottom:20px;">
            <label style="font-weight:bold;">지급 포인트</label>
            <input type="number" name="point_amount" id="mod_point_amount" style="width:100%;" />
        </div>
        <div style="text-align:right;">
            <button type="submit" class="x_btn x_btn-primary">저장</button>
            <button type="button" class="x_btn" onclick="jQuery('#log_modify_layer').hide();">취소</button>
        </div>
    </form>
</div>

<script>
function edit_log(srl, regdate, point) {
    jQuery('#mod_attendance_srl').val(srl);
    jQuery('#mod_regdate').val(regdate);
    jQuery('#mod_point_amount').val(point);
    jQuery('#log_modify_layer').fadeIn(200);
}
</script>