<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Category;
use App\Http\Requests\ContactRequest;

class ContactController extends Controller
{
    public function index(){
        $categories = Category::all();
  
        return view('index', compact('categories'));
    }

    public function confirm(ContactRequest $request){
        // 入力されたcategory_idから、categoryを取得（もっと簡単に書けるはずだが）
        $categories = Category::all();
        $category_id = $request->category_id;
        $category_content = "";
        foreach($categories as $category){
            if($category['id'] == $category_id){
                $category_content = $category['content'];
            }
        }

        $contact = $request->only([
            'first_name',
            'last_name',
            'gender',
            'email',
            'tel',
            'tel_middle',
            'tel_bottom',
            'address',
            'building',
            'category_id',
            'detail',
        ]);

        $contact['category_content'] = $category_content;
        return view('confirm', compact('contact'));
    }

    public function store(ContactRequest $request){
        if (isset($_POST['cancel'])){
            // 「リセット」ボタンが押された時の処理
            return redirect('/') ->withInput();
        }
        else
        {
            // submitのとき
            $contact = $request->only([
                'first_name',
                'last_name',
                'gender',
                'email',
                'tel',
                'tel_middle',
                'tel_bottom',
                'address',
                'building',
                'category_id',
                'detail']);
            Contact::create($contact);
            return view('thanks');
        }
    }

    public function thanks(){
        return view('thanks');
    }
}
