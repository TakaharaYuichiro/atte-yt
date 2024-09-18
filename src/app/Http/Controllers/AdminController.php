<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Category;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function index(){
        $contacts = Contact::with('category')->paginate(7);
        $categories = Category::all();

        // csvダウンロードに使う検索条件の記録用セッションを初期化
        session() -> put('search_parameters',[
            'category_id'=>"",
            'keyword'=>"",
            'gender'=>"",
            'date'=>"",
        ]);

        return view('/admin', compact('contacts', 'categories'));
    }

    // 検索
    public function search(Request $request){
        if (isset($_GET['reset'])){
            // 「リセット」ボタンが押された時の処理
            $contacts = Contact::with('category')->paginate(7);
            $categories = Category::all();

            // csvダウンロードに使う検索条件の記録用セッションを初期化
            session() -> put('search_parameters',[
                'category_id'=>"",
                'keyword'=>"",
                'gender'=>"",
                'date'=>"",
            ]);

            return view('/admin', compact('contacts', 'categories'));
        }
        else
        {
            // 検索実行
            $contacts = Contact::with('category')
                        ->KeywordSearch($request->keyword)  
                        ->CategorySearch($request->category_id)
                        ->GenderSearch($request->gender)
                        ->DateSearch($request->date)
                        ->paginate(7);

            $categories = Category::all();

            // csvダウンロードに使う記録用セッションに、今回の検索条件を記録
            $request ->session() -> put('search_parameters',[
                'category_id'=>$request->category_id,
                'keyword'=>$request->keyword,
                'gender'=>$request->gender,
                'date'=>$request->date,
            ]);

            return view('/admin', compact('contacts', 'categories'));
        }
    }

    // csv出力関数
    public function export(){
        // csvダウンロード用の記録用セッションから、最後に検索した時の検索条件を取得
        $param = session('search_parameters', [
            'category_id'=>"",
            'keyword'=>"",
            'gender'=>"",
            'date'=>""]);

        $param_category_id = isset($param['category_id'])? $param['category_id']: "";
        $param_keyword = isset($param['keyword'])? $param['keyword']: "";
        $param_gender = isset($param['gender'])? $param['gender']: "";
        $param_date = isset($param['date'])? $param['date']: "";

        $contacts = Contact::with('category')
                        ->KeywordSearch($param_keyword)
                        ->CategorySearch($param_category_id)
                        ->GenderSearch($param_gender)
                        ->DateSearch($param_date)
                        ->get();

        $csvHeader = ['お名前', '性別','メールアドレス','電話番号','住所','建物名','お問い合わせの種類','お問い合わせ内容', '作成日', '更新日'];
        $csvData = $contacts->toArray();
        
        $response = new StreamedResponse(function () use ($csvHeader, $csvData) {
            $handle = fopen('php://output', 'w');
            mb_convert_variables('SJIS-win', 'UTF-8', $csvHeader);
            fputcsv($handle, $csvHeader);

            foreach ($csvData as $row) {
                $name = $row['last_name']. " ". $row['first_name'];
                $gender = "";
                switch($row['gender']){
                    case 1:
                        $gender="男性";
                        break;
                    case 2:
                        $gender="女性";
                        break;
                    default:
                        $gender = "その他";
                        break;
                }

                $email = $row['email'];
                $tel = strval($row['tel']). strval($row['tel_middle']) .strval($row['tel_bottom']);
                $address = $row['address'];
                $building = isset($row['building'])? $row['building']: "";
                $category = $row['category']['content'];
                $detail = $row['detail'];
                $created_at = $row['created_at'];
                $updated_at = $row['updated_at'];

                $data = [$name, $gender, $email, $tel, $address, $building, $category,$detail,$created_at,$updated_at];

                mb_convert_variables('SJIS-win', 'UTF-8', $data);
                fputcsv($handle, $data);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users.csv"',
        ]);

        return $response;
    }

    // 詳細画面(モーダル)表示
    public function showDetail(Request $request){
        $selected_id=$request->id;
        return view('/admin', compact('selected_id'));
    }

    // 詳細画面(モーダル)から「削除」をクリックした時に使う
    public function deleteData(Request $request){
        Contact::find($request->id) ->delete();
        return redirect('/admin') -> with('message', 'お問い合わせデータを削除しました');
    }   
}
