<?php
require_once 'db.php';


function error422($message){

    $data = [
        'status' => 422,
        'message' => $message,
    ];
    header("HTTP/1.0 422 Unprocessable Entity");
    echo json_encode($data);
    exit();
}
//Setting
function getSetting($settingParams){
    global $conn;

    if(!isset($settingParams['user_id'])){
        return error422('ID của người dùng không tìm thấy');
    } elseif($settingParams['user_id'] == null) {
        return error422('Nhập ID của người dùng');
    }

    $userId = intval(mysqli_real_escape_string($conn, $settingParams['user_id']));

    // Câu truy vấn để lấy thông tin setting
    $query = "SELECT * FROM game_settings WHERE user_id = '$userId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0){
        $setting = mysqli_fetch_assoc($result);

        // Trả về thông tin setting dưới dạng JSON
        $data = [
            'status' => 200,
            'setting' => $setting
        ];
        header("HTTP/1.0 200 Success");
        echo json_encode($data);

    } else {
        // Nếu không tìm thấy kết quả
        $data = [
            'status' => 404,
            'message' => 'Không tìm thấy setting với user tương ứng'
        ];
        header("HTTP/1.0 404 Not Found");
        echo json_encode($data);
    }
}


function updateSetting($settingParams){
    global $conn;

    // Kiểm tra xem có tồn tại tham số setting ID và user ID không
    // if(!isset($settingParams['setting_id'])){
    //     return error422('ID của setting không tìm thấy');
    // } elseif($settingParams['setting_id'] == null) {
    //     return error422('Nhập ID của setting');
    // }

    if(!isset($settingParams['user_id'])){
        return error422('ID của người dùng không tìm thấy');
    } elseif($settingParams['user_id'] == null) {
        return error422('Nhập ID của người dùng');
    }

    // $settingId = intval(mysqli_real_escape_string($conn, $settingParams['setting_id']));
    $userId = intval(mysqli_real_escape_string($conn, $settingParams['user_id']));
    $soundOn = mysqli_real_escape_string($conn, $_POST['sound_on']);
    $musicOn = mysqli_real_escape_string($conn, $_POST['music_on']);
    $updateAt = date('Y-m-d H:i:s');  // Lấy thời gian hiện tại cho update_at

    // Kiểm tra dữ liệu nhập vào
    if(!isset($soundOn) || ($soundOn != '0' && $soundOn != '1')){
        return error422('Hãy nhập trạng thái sound (0 hoặc 1)');
    } elseif(!isset($musicOn) || ($musicOn != '0' && $musicOn != '1')){
        return error422('Hãy nhập trạng thái music (0 hoặc 1)');
    } else {
        // Câu truy vấn cập nhật setting
        $query = "UPDATE setting SET sound_on='$soundOn', music_on='$musicOn', update_at='$updateAt' WHERE user_id = '$userId' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if($result) {
            $data = [
                'status' => 200,
                'message' => 'Setting đã được cập nhật thành công',
            ];
            header("HTTP/1.0 200 Success");
            echo json_encode($data);
        } else {
            $data = [
                'status' => 500,
                'message' => 'Lỗi hệ thống, cập nhật thất bại',
            ];
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode($data);
        }
    }
}

function handleUserSetting($userId) {
    global $conn;

    // Kiểm tra xem user đã có setting trong bảng chưa
    $query = "SELECT * FROM setting WHERE user_id = '$userId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0) {
        // Nếu đã có setting, trả về thông tin
        $setting = mysqli_fetch_assoc($result);

        $data = [
            'status' => 200,
            'setting' => $setting
        ];
        header("HTTP/1.0 200 Success");
        echo json_encode($data);
    } else {
        // Nếu chưa có setting, tạo bản ghi mặc định với sound_on và music_on = 1
        $soundOn = 1;
        $musicOn = 1;
        $createAt = date('Y-m-d H:i:s');
        $updateAt = $createAt;

        // Câu truy vấn để chèn dữ liệu mới
        $insertQuery = "INSERT INTO setting (user_id, sound_on, music_on, create_at, update_at) 
                        VALUES ('$userId', '$soundOn', '$musicOn', '$createAt', '$updateAt')";
        $insertResult = mysqli_query($conn, $insertQuery);

        if($insertResult) {
            // Lấy lại dữ liệu setting vừa tạo
            $newSetting = [
                'setting_id' => mysqli_insert_id($conn),
                'user_id' => $userId,
                'sound_on' => $soundOn,
                'music_on' => $musicOn,
                'create_at' => $createAt,
                'update_at' => $updateAt
            ];

            $data = [
                'status' => 201,
                'setting' => $newSetting,
                'message' => 'Setting mặc định đã được tạo thành công'
            ];
            header("HTTP/1.0 201 Created");
            echo json_encode($data);
        } else {
            // Lỗi khi tạo setting mới
            $data = [
                'status' => 500,
                'message' => 'Lỗi hệ thống, không thể tạo setting mới'
            ];
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode($data);
        }
    }
}
// End setting

//matches
function storeMatches($matchesInput){
    global $conn;

    $player_1_id = mysqli_real_escape_string($conn, $matchesInput['player_1_id']);
    $player_2_id = mysqli_real_escape_string($conn, $matchesInput['player_2_id']);
    $score_player_1 = mysqli_real_escape_string($conn, $matchesInput['score_player_1']);
    $score_player_2 = mysqli_real_escape_string($conn, $matchesInput['score_player_2']);
    $winer_id = mysqli_real_escape_string($conn, $matchesInput['winer_id']);
    $match_time = date('Y-m-d'); 
    $match_date = date('H:i:s'); 
    $lose_id = mysqli_real_escape_string($conn, $matchesInput['lose_id']);
    if(empty(trim($player_1_id))){
        return error422('Hãy nhập id người chơi 1');
    }elseif(empty(trim($player_2_id))){
        return error422('Hãy nhập id người chơi 2');
    }elseif(empty(trim($score_player_1))){
        return error422('Hãy nhập điểm người chơi 1');
    }elseif(empty(trim($score_player_2))){
        return error422('Hãy nhập điểm người chơi 2');
    }elseif(empty(trim($winer_id))){
        return error422('Hãy nhập id người chơi thắng');
    }
    else{
        $query = "INSERT INTO matches (player_1_id,player_2_id,score_player_1,score_player_2,winer_id,match_time,match_date,lose_id) 
        VALUES ('$player_1_id','$player_2_id', '$score_player_1', '$score_player_2', '$winer_id', '$match_time', '$match_date', '$lose_id')";
        $result = mysqli_query($conn,$query);

        if($result){

            $data = [
                'status' => 201,
                'messange' => 'Trận đấu đã được thêm thành công',
            ];
            header("HTTP/1.0 201 Created");
            echo json_encode($data);

        }else{
            $data = [
                'status' => 500,
                'messange' => 'Internal server error',
            ];
            header("HTTP/1.0 500 Method not allowed");
            echo json_encode($data);
        }
    }

}

//user
function storeUser($userInput) {
    global $conn;

    $username = mysqli_real_escape_string($conn, $userInput['username']);
    $password = mysqli_real_escape_string($conn, $userInput['password']);
    $email = mysqli_real_escape_string($conn, $userInput['email']);
    $created_at = date('Y-m-d H:i:s');
    $last_login = mysqli_real_escape_string($conn, $userInput['last_login']);
    $is_ai = mysqli_real_escape_string($conn, $userInput['is_ai']);
    $dienTen = mysqli_real_escape_string($conn, $userInput['dienTen']);
    $birthday = mysqli_real_escape_string($conn, $userInput['birthday']);

    // Kiểm tra nếu cả username và dienTen bị bỏ trống
    if (empty(trim($username)) && empty(trim($dienTen))) {
        return error422('Hãy nhập thông tin username hoặc điền tên');
    } else {
        // Kiểm tra sự tồn tại của username (nếu có)
        if (!empty($username)) {
            $checkUsernameQuery = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
            $checkUsernameResult = mysqli_query($conn, $checkUsernameQuery);
            if (mysqli_num_rows($checkUsernameResult) > 0) {
                // Username đã tồn tại
                $data = [
                    'status' => 422,
                    'message' => 'Username đã tồn tại. Vui lòng chọn tên khác.',
                ];
                header("HTTP/1.0 422 Unprocessable Entity");
                echo json_encode($data);
                return;
            }
        }

        // Kiểm tra sự tồn tại của dienTen (nếu có)
        if (!empty($dienTen)) {
            $checkDienTenQuery = "SELECT * FROM users WHERE dienTen = '$dienTen' LIMIT 1";
            $checkDienTenResult = mysqli_query($conn, $checkDienTenQuery);
            if (mysqli_num_rows($checkDienTenResult) > 0) {
                // dienTen đã tồn tại
                $data = [
                    'status' => 422,
                    'message' => 'Điền tên đã tồn tại. Vui lòng chọn tên khác.',
                ];
                header("HTTP/1.0 422 Unprocessable Entity");
                echo json_encode($data);
                return;
            }
        }

        // Chèn dữ liệu người dùng mới vào bảng
        $query = "INSERT INTO users (username, password, email, created_at, last_login, is_ai, dienTen, birthday) 
                  VALUES ('$username', '$password', '$email', '$created_at', '$last_login', '$is_ai', '$dienTen', '$birthday')";
        $result = mysqli_query($conn, $query);

        if ($result) {
            // Lấy user_id của bản ghi vừa được thêm vào
            $user_id = mysqli_insert_id($conn);
            $data = [
                'status' => 201,
                'message' => 'Tài khoản đã được thêm thành công',
                'user_id' => $user_id, 
                'dienTen' => $dienTen   
            ];
            header("HTTP/1.0 201 Created");
            echo json_encode($data);
        } else {
            $data = [
                'status' => 500,
                'message' => 'Internal server error',
            ];
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode($data);
        }
    }
}


//end user

//leaderboard
function storeLeaderboard($leaderboardInput){
    global $conn;

    $user_id = mysqli_real_escape_string($conn, $leaderboardInput['user_id']);
    $total_score = mysqli_real_escape_string($conn, $leaderboardInput['total_score']);
    $last_update = date('Y-m-d H:i:s'); 
    if(empty(trim($user_id))){
        return error422('Hãy nhập user id');
    }elseif(empty(trim($total_score))){
        return error422('Hãy nhập tổng điểm');
    }
    else{
        $query = "INSERT INTO leaderboard (user_id,total_score,last_update) 
        VALUES ('$user_id','$total_score', '$last_update')";
        $result = mysqli_query($conn,$query);

        if($result){

            $data = [
                'status' => 201,
                'messange' => 'Bảng xếp hạng đã được thêm thành công',
            ];
            header("HTTP/1.0 201 Created");
            echo json_encode($data);

        }else{
            $data = [
                'status' => 500,
                'messange' => 'Internal server error',
            ];
            header("HTTP/1.0 500 Method not allowed");
            echo json_encode($data);
        }
    }

}

function getLeaderboardList(){

    global $conn;
    $query = "SELECT leaderboard.*, users.username, MAX(leaderboard.total_score) AS max_total_score
                FROM leaderboard
                JOIN users ON leaderboard.user_id = users.user_id
                GROUP BY leaderboard.user_id, users.username
                ORDER BY max_total_score DESC;";
    $query_run = mysqli_query($conn,$query);

    if($query_run){

        if(mysqli_num_rows($query_run) > 0){

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Leaderboard List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 405,
                'messange' =>  'No leaderboard found',
            ];
            header("HTTP/1.0 405 Method not allowed");
            echo json_encode($data);
        }
    }else{
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}

//end leaderboard


// Đếm số trận thắng của từng người chơi
function getCountUser(){

    global $conn;
    $query = "SELECT user_id, 
                    SUM(win_count) AS win_count, 
                    SUM(lose_count) AS lose_count, 
                    (SUM(win_count) + SUM(lose_count)) AS total_matches
                FROM (
                    SELECT winer_id AS user_id, COUNT(*) AS win_count, 0 AS lose_count
                    FROM matches
                    GROUP BY winer_id

                    UNION ALL

                    SELECT lose_id AS user_id, 0 AS win_count, COUNT(*) AS lose_count
                    FROM matches
                    GROUP BY lose_id
                ) AS user_stats
                GROUP BY user_id;";
    $query_run = mysqli_query($conn,$query);

    if($query_run){

        if(mysqli_num_rows($query_run) > 0){

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Leaderboard List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 405,
                'messange' =>  'No leaderboard found',
            ];
            header("HTTP/1.0 405 Method not allowed");
            echo json_encode($data);
        }
    }else{
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}
// End Đếm số trận thắng của từng người chơi


// Tính số trận thắng thua của user
function getCountMatchesUser(){

    global $conn;
    $query = "SELECT 
                    SUM(CASE WHEN winer_id IN (SELECT user_id FROM users WHERE is_ai = false) THEN 1 ELSE 0 END) AS total_wins,
                    SUM(CASE WHEN lose_id IN (SELECT user_id FROM users WHERE is_ai = false) THEN 1 ELSE 0 END) AS total_losses
                FROM matches;";
    $query_run = mysqli_query($conn,$query);

    if($query_run){

        if(mysqli_num_rows($query_run) > 0){

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Leaderboard List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 405,
                'messange' =>  'No leaderboard found',
            ];
            header("HTTP/1.0 405 Method not allowed");
            echo json_encode($data);
        }
    }else{
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}
// End Tính số trận thắng thua của user

//Tính số trận thắng thua của AI
function getCountMatchesAI(){

    global $conn;
    $query = "SELECT 
                    SUM(CASE WHEN winer_id IN (SELECT user_id FROM users WHERE is_ai = true) THEN 1 ELSE 0 END) AS total_wins,
                    SUM(CASE WHEN lose_id IN (SELECT user_id FROM users WHERE is_ai = true) THEN 1 ELSE 0 END) AS total_losses
                FROM matches;";
    $query_run = mysqli_query($conn,$query);

    if($query_run){

        if(mysqli_num_rows($query_run) > 0){

            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Leaderboard List Fetched Successfully',
                'data' => $res
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }else{
            $data = [
                'status' => 405,
                'messange' =>  'No leaderboard found',
            ];
            header("HTTP/1.0 405 Method not allowed");
            echo json_encode($data);
        }
    }else{
        $data = [
            'status' => 500,
            'messange' => 'Internal server error',
        ];
        header("HTTP/1.0 500 Internal server error");
        echo json_encode($data);
    }
}
// End Tính số trận thắng thua của AI

//Login
function loginUser($userInput) {
    global $conn;

    // Lấy thông tin username và password từ đầu vào
    $username = mysqli_real_escape_string($conn, trim($userInput['username']));
    $password = mysqli_real_escape_string($conn, trim($userInput['password']));

    // Kiểm tra xem username và password có trống không
    if (empty($username) || empty($password)) {
        return error422('Vui lòng nhập cả username và mật khẩu');
    }

    // Truy vấn cơ sở dữ liệu để lấy thông tin người dùng theo username
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    // Kiểm tra xem người dùng có tồn tại không
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // So sánh mật khẩu trực tiếp
        if ($password === $user['password']) { // So sánh mật khẩu
            // Mật khẩu đúng, đăng nhập thành công
            $data = [
                'status' => 200,
                'message' => 'Đăng nhập thành công',
                'user' => [
                    'user_id' => $user['user_id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'dienTen' => $user['dienTen'],
                ]
            ];
            header("HTTP/1.0 200 OK");
            echo json_encode($data);
        } else {
            // Mật khẩu sai
            return error422('Mật khẩu không đúng');
        }
    } else {
        // Không tìm thấy username
        return error422('Username không tồn tại');
    }
}


?>