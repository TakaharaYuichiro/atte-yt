<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AttendancesController extends Controller
{
    // ログイン中のユーザーの本日の勤務状況を表示
    public function index(){
        // 現在認証されているユーザーを取得
        $user = Auth::user();
        $id = Auth::id();
        $profile = User::where('id', $id)->first();

        // このユーザーの本日の勤務状況のみを取得するクエリを生成
        $query = Attendance::query();
        if (!empty($profile->id)) {
            $query->where('user_id', '=', $profile->id)
                ->whereDate('created_at', '=', now());                
        }

        // このユーザーの本日の勤務データを取得
        $data = $this -> getAttendance($query, $id);

        // $workState = 0; // 0:勤務開始前、1:勤務中、 2:勤務終了

        // ユーザーIDからユーザー名を取得
        // $name = User::where('id',$id)->first()['name'];

        // // このユーザーの勤務開始時刻を取得
        // $queryStart = clone $query;
        // $startTime = $queryStart
        //         ->where('content_index', '=', 1)    // content_index=1は勤務開始を表す
        //         ->min('created_at');

        // $startTimeStr = '記録がありません';
        // if(strtotime($startTime)){
        //     $workState = 1;
        //     $startTimeStr = date("H:i:s", strtotime($startTime));
        // }

        // // このユーザーの勤務終了時刻を取得
        // $queryEnd = clone $query;
        // $endTime = $queryEnd
        //         ->where('content_index', '=', 2)    // content_index=2は勤務終了を表す
        //         ->max('created_at');

        // if(strtotime($endTime)) $workState = 2;

        // // 休憩開始/終了時刻の取得と集計
        // $queryRest = clone $query;
        // $restData = $queryRest
        //     ->where('content_index', '=', 3)        // content_index=3は休憩開始/終了を表す。1日の中で奇数個目が休憩開始、偶数個目が休憩終了。
        //     ->get();

        // $restCount = count($restData);
        // $restTimeTotal = 0;
        // if ($restCount > 1){
        //     // 休憩あり
        //     $restTimeTotal = 0;
        //     for ($i = 0; $i < $restCount-1; $i+=2) {
        //         $restStart = strtotime($restData[$i]['created_at']);
        //         $restEnd = strtotime($restData[$i+1]['created_at']);
        //         $restTimeTotal += ($restEnd - $restStart);
        //     }
        // } 
        
        // // 現在休憩中かどうかのフラグ
        // $isRest = ($restCount % 2 == 1);

        // // 休憩時間合計をhh:mm:ssで表す
        // $restTimeHours = floor($restTimeTotal/60/60);
        // $restTimeMinutes = floor(($restTimeTotal - $restTimeHours * 60) / 60);
        // $restTimeSeconds = $restTimeTotal - $restTimeHours * 60 * 60 - $restTimeMinutes * 60;
        // $restTimeTotalStr = sprintf('%02d', $restTimeHours). ":". sprintf('%02d', $restTimeMinutes). ":". sprintf('%02d', $restTimeSeconds) ;

        // // データのまとめ
        // $data =[
        //     'startTime' => $startTimeStr,
        //     'workState' => $workState,
        //     'isRest' => $isRest,
        //     'restTimeTotal' => $restTimeTotalStr,
        // ];

        return view('index', compact( 'profile', 'data'));
    }

    // indexページで入力された勤務データをデータベースに記録
    public function store(Request $request){
        // ギリギリの日跨ぎなど、イレギュラーの時を想定した処理(入力画面で制限されているので、ほとんど発生しないはず)
        // ①勤務開始前に勤務終了が押された
        // ②勤務開始前または勤務終了後に、休憩開始または休憩終了が押された

        // このユーザーの本日の勤務状況のみを取得するクエリを生成
        $query = Attendance::query();
        if (!empty($request->user_id)) {
            $query->where('user_id', '=', $request->user_id)
                ->whereDate('created_at', '=', now());                
        }

        $workState = 0; 

        // 勤務開始時刻の取得
        $queryStart = clone $query;
        $startTime = $queryStart
                ->where('content_index', '=', 1)    // content_index=1は勤務開始を表す
                ->min('created_at');
        if(strtotime($startTime)) $workState = 1;

        // 勤務終了時刻の取得
        $queryEnd = clone $query;
        $endTime = $queryEnd
                ->where('content_index', '=', 2)    // content_index=2は勤務終了を表す
                ->max('created_at');
        if(strtotime($endTime)) $workState = 2;

        // 休憩開始/終了時刻の取得と集計
        $queryRest = clone $query;
        $restData = $queryRest
            ->where('content_index', '=', 3)        // content_index=3は休憩開始/終了を表す。1日の中で奇数個目が休憩開始、偶数個目が休憩終了。
            ->get();

        $restCount = count($restData);
        $isRest = ($restCount % 2 == 1);

        $contentIndex = $request -> content_index;

        // イレギュラー判定
        if ($workState == 0 && $contentIndex == 2) return redirect('/');    // ①
        if ($workState != 1 && $contentIndex == 3) return redirect('/');    // ②

        // ここから本番。データベースに書き込む
        $attendance = $request->only([
            'user_id',
            'content_index'
        ]);
        Attendance::create($attendance);

        // 勤務終了が押された時に休憩中だったら、休憩終了時刻も記録しておく
        if ($contentIndex == 2 && $isRest) {
            $attendance = [
                'user_id' => $request->user_id,
                'content_index' => 3,
            ];
            Attendance::create($attendance);
        }

        return redirect('/');
    }

    public function attendance(Request $request){
        // 表示の対象日を決める
        $targetDate = date("Y-m-d");    // 初期値=本日
        if (!empty($request->targetDate)) $targetDate = ($request->targetDate);  

        // 対象日の勤務データのクエリ
        $query = Attendance::query() -> whereDate('created_at', '=', $targetDate);

        // 対象日に勤務データが登録されているユーザーのIDの一覧をコレクションで取得
        $queryClone = clone $query;
        $userIds = $queryClone -> groupBy('user_id')->get(['user_id']);

        // データ作成メイン
        $attendances = [];
        foreach($userIds as $userId){
            $queryPerUser = clone $query;
            $queryPerUser -> where('user_id', '=', $userId['user_id']);

            $attendance = $this->getAttendance($queryPerUser, $userId['user_id']);

            // // ユーザーIDからユーザー名を取得
            // $name = User::where('id',$userId['user_id'])->first()['name'];

            // $workState = 0; // 0:勤務開始前、1:勤務中、 2:勤務終了

            // // このユーザーの勤務開始時刻を取得
            // $queryStart = clone $queryPerUser;
            // $startTime = $queryStart
            //         ->where('content_index', '=', 1)
            //         ->min('created_at');

            // $startTimeStr = '記録がありません';
            // if(strtotime($startTime)){
            //     $workState = 1;
            //     $startTimeStr = date("H:i:s", strtotime($startTime));
            // }

            // // このユーザーの勤務終了時刻を取得
            // $queryEnd = clone $queryPerUser;
            // $endTime = $queryEnd
            //         ->where('content_index', '=', 2)
            //         ->max('created_at');

            // $endTimeStr = '記録がありません';
            // if(strtotime($endTime)){
            //     $workState = 2;
            //     $endTimeStr = date("H:i:s", strtotime($endTime));
            // }

            // // 勤務時間の計算
            // $workingTimeTotalStr = '未確定';
            // if (strtotime($startTime) && strtotime($endTime)){
            //     $workingTimeTotal = strtotime($endTime) - strtotime($startTime);
            //     $workingTimeHours = floor($workingTimeTotal/60/60);
            //     $workingTimeMinutes = floor(($workingTimeTotal - $workingTimeHours * 60 * 60) / 60);
            //     $workingTimeSeconds = $workingTimeTotal - $workingTimeHours * 60 * 60 - $workingTimeMinutes * 60;
    
            //     $workingTimeTotalStr = sprintf('%02d', $workingTimeHours). ":". sprintf('%02d', $workingTimeMinutes). ":". sprintf('%02d', $workingTimeSeconds) ;
            // }

            // // 休憩開始/終了時刻の取得と集計
            // $queryRest = clone $queryPerUser;
            // $restData = $queryRest
            //     ->where('content_index', '=', 3)
            //     ->get();

            // $restCount = count($restData);
            // $restTimeTotal = 0;
            // if ($restCount > 1){
            //     // 休憩ありの時は休憩時間合計を計算
            //     $restTimeTotal = 0;
            //     for ($i = 0; $i < $restCount-1; $i+=2) {
            //         $restStart = strtotime($restData[$i]['created_at']);
            //         $restEnd = strtotime($restData[$i+1]['created_at']);
            //         $restTimeTotal += ($restEnd - $restStart);
            //     }
            // } 

            // // 現在休憩中かどうかのフラグ
            // $isRest = ($restCount % 2 == 1);
            
            // // 休憩時間合計をhh:mm:ssで表す
            // $restTimeHours = floor($restTimeTotal/60/60);
            // $restTimeMinutes = floor(($restTimeTotal - $restTimeHours * 60 * 60) / 60);
            // $restTimeSeconds = $restTimeTotal - $restTimeHours * 60 * 60 - $restTimeMinutes * 60;
            // $restTimeTotalStr = sprintf('%02d', $restTimeHours). ":". sprintf('%02d', $restTimeMinutes). ":". sprintf('%02d', $restTimeSeconds) ;

            // // 1行分のデータをまとめる
            // $attendance = [
            //     'name' => $name,
            //     'startTime' =>  $startTimeStr,
            //     'endTime' => $endTimeStr,
            //     'restTimeTotal' => $restTimeTotalStr,
            //     'workingTime' => $workingTimeTotalStr,
            //     'workState' => $workState,
            //     'isRest' => $isRest,
            // ];

            array_push($attendances, $attendance);
        }

        // 戻り値用データ作成
        // Laravelで配列をページネーションする
        // https://qiita.com/higuaki/items/e5b34315f07b471f065b
        $coll = collect($attendances);
        $data = $this->paginate($coll, 5, null, ['path'=>'/attendance?']);

        // その他のデータ
        $condition = [
            'targetDate' => $targetDate,
        ];

        return view('attendance', compact('data', 'condition'));
    }

    private function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    // 1人、1日分の勤務データセットを取得する関数
    private function getAttendance($queryPerUser, $userId){
        $workState = 0; // 0:勤務開始前、1:勤務中、 2:勤務終了（※本日のデータの表示用にしか使っていない）

        // ユーザーIDからユーザー名を取得
        $name = User::where('id', $userId) -> first()['name'];

        // このユーザーの勤務開始時刻を取得
        $queryStart = clone $queryPerUser;
        $startTime = $queryStart
                ->where('content_index', '=', 1)
                ->min('created_at');

        $startTimeStr = '記録がありません';
        if(strtotime($startTime)){
            $workState = 1;
            $startTimeStr = date("H:i:s", strtotime($startTime));
        }

        // このユーザーの勤務終了時刻を取得
        $queryEnd = clone $queryPerUser;
        $endTime = $queryEnd
                ->where('content_index', '=', 2)
                ->max('created_at');

        $endTimeStr = '記録がありません';
        if(strtotime($endTime)){
            $workState = 2;
            $endTimeStr = date("H:i:s", strtotime($endTime));
        }

        // 勤務時間の計算
        $workingTimeTotalStr = '未確定';
        if (strtotime($startTime) && strtotime($endTime)){
            $workingTimeTotal = strtotime($endTime) - strtotime($startTime);
            $workingTimeHours = floor($workingTimeTotal/60/60);
            $workingTimeMinutes = floor(($workingTimeTotal - $workingTimeHours * 60 * 60) / 60);
            $workingTimeSeconds = $workingTimeTotal - $workingTimeHours * 60 * 60 - $workingTimeMinutes * 60;

            $workingTimeTotalStr = sprintf('%02d', $workingTimeHours). ":". sprintf('%02d', $workingTimeMinutes). ":". sprintf('%02d', $workingTimeSeconds) ;
        }

        // 休憩開始/終了時刻の取得と集計
        $queryRest = clone $queryPerUser;
        $restData = $queryRest
            ->where('content_index', '=', 3)
            ->get();

        $restCount = count($restData);
        $restTimeTotal = 0;
        if ($restCount > 1){
            // 休憩ありの時は休憩時間合計を計算
            $restTimeTotal = 0;
            for ($i = 0; $i < $restCount-1; $i+=2) {
                $restStart = strtotime($restData[$i]['created_at']);
                $restEnd = strtotime($restData[$i+1]['created_at']);
                $restTimeTotal += ($restEnd - $restStart);
            }
        } 

        // 現在休憩中かどうかのフラグ（※本日のデータの表示用にしか使っていない）
        $isRest = ($restCount % 2 == 1);
        
        // 休憩時間合計をhh:mm:ssで表す
        $restTimeHours = floor($restTimeTotal/60/60);
        $restTimeMinutes = floor(($restTimeTotal - $restTimeHours * 60 * 60) / 60);
        $restTimeSeconds = $restTimeTotal - $restTimeHours * 60 * 60 - $restTimeMinutes * 60;
        $restTimeTotalStr = sprintf('%02d', $restTimeHours). ":". sprintf('%02d', $restTimeMinutes). ":". sprintf('%02d', $restTimeSeconds) ;

        // 1行分のデータをまとめる
        $attendance = [
            'name' => $name,
            'startTime' =>  $startTimeStr,
            'endTime' => $endTimeStr,
            'restTimeTotal' => $restTimeTotalStr,
            'workingTime' => $workingTimeTotalStr,
            'workState' => $workState,
            'isRest' => $isRest,
        ];

        return $attendance;
    }
}
