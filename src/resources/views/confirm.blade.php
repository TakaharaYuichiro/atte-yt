@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/confirm.css') }}" />
@endsection

@section('content')
<div class="confirm__content">
  <div class="confirm__heading">
    <h2>Confirm</h2>
  </div>


  <form class="form" action="/confirm/store" method="post">
    @csrf

    <table class="confirm-table">
      <tr class="confirm-table__row">
        <th class="confirm-table__header">お名前</th>
        <td class="confirm-table__text">
          <span>{{ $contact['last_name']. '　'.  $contact['first_name'] }} </span>
          <input type="hidden" name="first_name" value="{{ $contact['first_name'] }}"/>
          <input type="hidden" name="last_name" value="{{ $contact['last_name'] }}"/>
        </td>
      </tr>

      <tr class="confirm-table__row">
        <th class="confirm-table__header">性別</th>
        <td class="confirm-table__text">
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
      </tr>

      <tr class="confirm-table__row">
        <th class="confirm-table__header">メールアドレス</th>
        <td class="confirm-table__text">
          <input type="email" name="email" value="{{ $contact['email'] }}" readonly/>
        </td>
      </tr>

      <tr class="confirm-table__row">
        <th class="confirm-table__header">電話番号</th>
        <td class="confirm-table__text">
          <span>{{ $contact['tel'].$contact['tel_middle'].$contact['tel_bottom'] }}</span>

          <input type="hidden" name="tel" value="{{ $contact['tel'] }}" />
          <input type="hidden" name="tel_middle" value="{{ $contact['tel_middle'] }}" />
          <input type="hidden" name="tel_bottom" value="{{ $contact['tel_bottom'] }}" />
        </td>
      </tr>

      <tr class="confirm-table__row">
        <th class="confirm-table__header">住所</th>
        <td class="confirm-table__text">
          <input type="text" name="address" value="{{ $contact['address'] }}" readonly/>
        </td>
      </tr>

      <tr class="confirm-table__row">
        <th class="confirm-table__header">建物名</th>
        <td class="confirm-table__text">
          <input type="text" name="building" value="{{ $contact['building'] }}" readonly/>
        </td>
      </tr>

      <tr class="confirm-table__row">
        <th class="confirm-table__header">お問い合わせの種類</th>
        <td class="confirm-table__text">
          <span>{{$contact['category_content']}}</span>
          <input type="hidden" name="category_id" value="{{ $contact['category_id'] }}" />
        </td>
      </tr>

      <tr class="confirm-table__row">
        <th class="confirm-table__header">お問い合わせ内容</th>
        <td class="confirm-table__text">
          <input type="text" name="detail" value="{{ $contact['detail'] }}" readonly/>
        </td>
      </tr>
    </table>

    <div class="form__button">
      <button class="form__button-submit" type="submit" name="submin">送信</button>
      <button class="form__button-submit form__button-cancel" type="submit" name="cancel">修正</button>
     
    </div>
  </form>


</div>
@endsection