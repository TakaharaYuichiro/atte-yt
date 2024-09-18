@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}"/>
@endsection

@section('utilities')
@if (Auth::check())
<form class="utilitiy-form" action="/logout" method="post">
    @csrf
    <button class="utility-button">logout</button>
</form>
@endif
@endsection

@section('content')
<div class="form__content">
    <div class="form__heading">
        <h2>Admin</h2>
    </div>

    <div class="category__alert">
        @if(session('message'))
        <div class="category__alert--success">{{session('message')}}</div>
        @endif

        @if($errors->any())
        <div class="category__alert--danger">
            <ul >
                @foreach ($errors->all() as $error)
                <li >{{$error}}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <div class="search-section">        
        <form  class="search-form" action="/admin/search" method="get">
            @csrf
            <input class="search-form__item search-form__item--keyword" type="text" name="keyword" placeholder="名前やメールアドレスを入力してください" value="{{old('keyword')}}"/>

            <select class="search-form__item search-form__item--gender" name="gender" >
                <option value="" selected>性別</option>
                <option value="1">男性</option>
                <option value="2">女性</option>
                <option value="3">その他</option>
            </select>

            <select class="search-form__item search-form__item--category" name="category_id" >
                <option value="" selected>お問い合わせの種類</option>
                @foreach($categories as $category)
                    <option value="{{$category['id']}}">{{$category['content']}}</option>
                @endforeach
            </select>

            <input type="date" name="date" class="search-form__item search-form__item--date"/>
            <button class="search-form__button search-form__item--submit" type="submit" name="submit">検索</button>
            <button class="search-form__button search-form__item--reset" type="submit" name="reset">リセット</button>
        </form>
    </div>

    <div class="optional-section">
        <div class="optional-section__export">
            <form class="export" method="get" action="/admin/export" >
                @csrf
                <div class="export__button">
                    <button class="export__button-submit" type="submit" name="submit">エクスポート</button>
                </div>
            </form>
        </div>

        <div class="optional-section__pagination">
            <!-- 以下のウェブ記事を参考 -->
            <!-- https://qiita.com/panax/items/fca6e39d6731899e0c7a -->
            <!-- https://qiita.com/wbraver/items/b95814d6383172b07a58 -->
            {{$contacts->appends(request()->query())->links('vendor.pagination.original_pagination_view')}}
        </div>
    </div>
 
    <div class="list-section">
        <table class="contact-table">
            <tr>
                <th class="contact-table__header contact-table__header--name">お名前</th>
                <th class="contact-table__header contact-table__header--gender">性別</th>
                <th class="contact-table__header contact-table__header--email">メールアドレス</th>
                <th class="contact-table__header contact-table__header--category">お問い合わせの種類</th>
                <th class="contact-table__header contact-table__header--detail"></th>
            </tr>

            @foreach($contacts as $contact)
            <tr class="contact-table__row">
                <td>
                    <span>{{ $contact['last_name']. '　'.  $contact['first_name'] }} </span>
                    <input type="hidden" name="first_name" value="{{ $contact['first_name'] }}"/>
                    <input type="hidden" name="last_name" value="{{ $contact['last_name'] }}"/>
                </td> 

                <td>
                    @switch($contact['gender'])
                        @case(1)
                            <span>男性</span>
                            @break
                        @case(2)
                            <span>女性</span>
                            @break
                        @default
                            <span>その他</span>
                            @break
                    @endswitch
                    <input type="hidden" name="gender" value="{{ $contact['gender']}}"/>
                </td> 

                <td>
                    <span>{{ $contact['email'] }}</span>
                    <input type="hidden" name="email" value="{{ $contact['email'] }}"/>
                </td>

                <td>
                    <span>{{$contact['category']['content']}}</span>
                    <input type="hidden" name="category_id" value="{{ $contact['category_id'] }}"/>
                </td> 

                <td>
                    <input type="hidden" name="id" value="{{ $contact['id'] }}">
                    <button class="contact-table__button-detail" type="button" value="{{ $contact['id'] }}">詳細</button>
                </td>
            </tr>
            @endforeach
        </table>
    </div> 
</div>

{{-- モーダルウィンドウ --}}
<div id="modal-window" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <span class="modal-close"></span>
      </div>
      <div class="modal-body">
        <div id="modal-table"></div>
      </div>

      <form action="/admin/delete_data" method="post" class="modal-delete">
        @method('DELETE')
        @csrf
        <input id="modal-delete__input" type="hidden" name="id" value="">
        <div class="modal-delete__button">
            <button type="submit"
                onclick='return confirm("このデータを削除してもよろしいですか？")'>削除
            </button>
        </div>
      </form>
    </div>
</div>


<script>
    // 詳細画面のデータ表示用のjavascriptです
    const buttonOpen = document.getElementsByClassName('contact-table__button-detail');
    const modal = document.getElementById('modal-window');
    const modalDeleteInput = document.getElementById('modal-delete__input');
    const buttonClose = document.getElementsByClassName('modal-close')[0];

    for(var button of buttonOpen){
        const id = button.value;
        // 全ての詳細ボタンにリスナーを設定
        button.addEventListener('click', () => {
            modalDeleteInput.value = id;
            modal.style.display = 'block';

            const contacts = @json($contacts);
            const target = contacts['data'].find(x => x.id == id);
            
            const name = target['last_name'] + " " + target['first_name'];
            var gender = "";
            switch (target['gender']){
                case 1:
                    gender = "男性";
                    break;
                case 2:
                    gender = "女性";
                    break;
                default:
                    gender = "その他";
                    break;
            }

            const email = target['email'];
            const tel = target['tel'] + target['tel_middle'] + target['tel_bottom'] ; 
            const address = target['address'];
            const building = target['building'] == null? "": target['building'];
            const category_id = target['category_id'];
            const category = target['category']['content'];
            const detail = target['detail'];

            var tb = "<table>";
            tb += `<tr><th>お名前</th><td>${name}</td></tr>`;
            tb += `<tr><th>性別</th><td>${gender}</td></tr>`;
            tb += `<tr><th>メールアドレス</th><td>${email}</td></tr>`;
            tb += `<tr><th>電話番号</th><td>${tel}</td></tr>`;
            tb += `<tr><th>住所</th><td>${address}</td></tr>`;
            tb += `<tr><th>建物名</th><td>${building}</td></tr>`;
            tb += `<tr><th>お問い合わせの種類</th><td>${category}</td></tr>`;
            tb += `<tr><th>お問い合わせ内容</th><td>${detail}</td></tr>`;
            tb += "</table>";

            document.getElementById('modal-table').innerHTML = tb;
        });
    }

    // バツ印がクリックされた時
    buttonClose.addEventListener('click', ()=>{
        modal.style.display = 'none';
    });

    // モーダルコンテンツ以外がクリックされた時
    addEventListener('click', (e)=>{
        if (e.target == modal) {
            modal.style.display = 'none';
        }
    });
</script>

@endsection