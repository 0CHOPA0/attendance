<div class="bfc-attendance-container" style="padding:20px; text-align:center;">
    <h2>REDSGO 직관 인증</h2>
    
    <div id="status-message" style="margin:20px 0; padding:15px; border-radius:8px; background:#f8f9fa;">
        부천FC1995와 함께할 준비가 되셨나요?
    </div>

    @if(count($game_list) > 0)
        <div class="game-selector" style="margin-bottom:20px;">
            <label>인증할 경기를 선택하세요:</label>
            <select id="selected_game" style="padding:10px; width:100%; max-width:300px;">
                @foreach($game_list as $game)
                    <option value="{{ $game->game_srl }}" data-lat="{{ $game->lat }}" data-lon="{{ $game->lon }}">
                        {{ $game->title }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <button type="button" onclick="startAttendance()" style="padding:15px 30px; font-size:18px; background:#e11d48; color:#fff; border:none; border-radius:5px; cursor:pointer;">
            현재 위치로 직관 인증하기
        </button>
    @else
        <div style="padding:40px 0; color:#888;">
            <i class="xi-calendar-cancle" style="font-size:40px;"></i>
            <p>오늘은 등록된 경기 일정이 없습니다.</p>
        </div>
    @endif
</div>

<script>
function startAttendance() {
    if (!navigator.geolocation) {
        alert("이 브라우저는 GPS를 지원하지 않습니다.");
        return;
    }
    
    document.getElementById('status-message').innerText = "현재 위치 좌표를 수신 중입니다...";
    
    navigator.geolocation.getCurrentPosition(function(position) {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;
        const game_srl = document.getElementById('selected_game').value;

        // 서버로 인증 요청 (jQuery 사용)
        jQuery.exec_json('bfc_attendance.procBfc_attendanceIndexLog', {
            lat: lat,
            lon: lon,
            game_srl: game_srl
        }, function(data) {
            alert(data.message);
            location.reload();
        });
        
    }, function(error) {
        alert("GPS 위치 정보를 가져오는데 실패했습니다: " + error.message);
    });
}
</script>