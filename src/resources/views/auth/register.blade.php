@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/common_auth.css') }}">
@endsection


@section('content')
<div class="main-content">
  <div class="form__heading">
    <h2>会員登録</h2>
  </div>

  <form class="form" action="/register" method="post">
    @csrf
    <div class="form__group">
      <div class="form__group-content">
        <div class="form__input--text">
          <input type="text" name="name" placeholder="名前" value="{{ old('name') }}" />
        </div>
        <div class="form__error">
          @error('name')
          {{ $message }}
          @enderror
        </div>
      </div>
    </div>
    <div class="form__group">
      <div class="form__group-content">
        <div class="form__input--text">
          <input type="text" name="email" placeholder="メールアドレス" value="{{ old('email') }}" />
        </div>
        <div class="form__error">
          @error('email')
          {{ $message }}
          @enderror
        </div>
      </div>
    </div>
    <div class="form__group">
      <div class="form__group-content">
        <div class="form__input--text">
          <input type="password" name="password" placeholder="パスワード"/>
        </div>
        <div class="form__error">
          @error('password')
          {{ $message }}
          @enderror
        </div>
      </div>
    </div>
    <div class="form__group">
      <div class="form__group-content">
        <div class="form__input--text">
          <input type="password" name="password_confirmation" placeholder="確認用パスワード"/>
        </div>
      </div>
    </div>
    <div class="form__button">
      <button class="form__button-submit" type="submit">会員登録</button>
    </div>
  </form>

  <div class="login-register-switching">
    <p>
      <span>アカウントをお持ちの方はこちらから</span><br>
      <a href="/login">ログイン</a>
    </p>
  </div>


</div>
@endsection