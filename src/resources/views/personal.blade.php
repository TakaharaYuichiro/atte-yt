@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/personal.css') }}" />
@endsection

@section('utilities')
  @include('layouts.utility')
@endsection

@section('content')
<div class="main-content">
  <div class="user-section">

    <div class="user-section__message">
      <span class="user-section__name" id="user-name">{{$condition['name']}}</span>
      <span>{{'さんの勤務データ一覧'}}</span>
    </div>

    <div class="user-section__select">
      <a href="#selectUser">会員選択</a>
    </div>
  </div>

  <div class="list-section">
    <?php $week = ['日', '月', '火', '水', '木', '金', '土']; ?>

    <table class="atte-table">
      <tr>
        <th class="atte-table__header atte-table__header--name">日付</th>
        <th class="atte-table__header atte-table__header--time">勤務開始</th>
        <th class="atte-table__header atte-table__header--time">勤務終了</th>
        <th class="atte-table__header atte-table__header--time">休憩時間</th>
        <th class="atte-table__header atte-table__header--time">勤務時間</th>
      </tr>

      @foreach($data as $attendance)
      <tr class="atte-table__row">
        <?php
          $day = date('w', strtotime($attendance['date'])); 
          $dateStr = $attendance['date'] . ' ('. $week[$day]. ')';
        ?>
        <td><span>{{ $dateStr }}</span></td> 
        <td><span>{{ $attendance['startTime'] }}</span></td> 
        <td><span>{{ $attendance['endTime'] }}</span></td> 
        <td><span>{{ $attendance['restTimeTotal'] }}</span></td> 
        <td><span>{{ $attendance['workingTime'] }}</span></td> 
      </tr>
      @endforeach
    </table>
  </div> 

  <div class="optional-section">
    <div class="optional-section__pagination">
        {{-- $data->appends(request()->query())->links('vendor.pagination.original_pagination_view') --}}
        {{ $data->links('vendor.pagination.original_pagination_view') }}
    </div>
  </div>
</div>

<!-- ユーザー選択用のモーダルウインドウ -->
<div class="modal" id="selectUser">
  <a href="#!" class="modal-overlay"></a>
  <div class="modal__inner">
    <div class="modal__header">
        <a href="#" class="modal__close-btn"></a>
    </div>
    <div class="modal__content">
      <form class="modal__detail-form" action="/personal" method="get">
        @csrf
        <p class="modal-form__message">会員を選択してください</p>
        <div class="modal-form__input">
          <select class="search-form__item-select" name="user_id" >
            <option value="" {{ old('user_id','') == '' ? 'selected' : '' }}>--選択してください--</option>
            @foreach($users as $user)
              <option value="{{$user['id']}}" {{ old('user_id') == $user['id'] ? 'selected' : '' }}>
                {{$user['name']}}
              </option>
            @endforeach
          </select>
        </div>
        <div  class="modal-form__submit-button">
          <button type="submit">OK</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
