@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/index.css') }}" />
@endsection

@section('content')
<div class="contact-form__content">
  <div class="contact-form__heading">
    <h2>Contact</h2>
  </div>

  <form class="form" action="/confirm" method="post">
    @csrf
    <table class="contact-table">
      <tr>
        <th>
          <div class="form__group-title">
            <span class="form__label--item">お名前</span>
            <span class="form__label--required">※</span>
          </div>
        </th>
        <td>
          <div class="form__group-content">
            <div class="form__input--text">
              <input type="text" name="last_name" placeholder="例）山田" value="{{ old('last_name') }}"/>
              <input type="text" name="first_name" placeholder="例）太郎" value="{{ old('first_name') }}"/>
              <!-- <input type="text" name="last_name" placeholder="例）山田" value="山田"/> -->
              <!-- <input type="text" name="first_name" placeholder="例）太郎" value="太郎"/> -->
            </div>
            <div class="form__error">
              @error('last_name')
                {{ $message }}
              @enderror
              @error('first_name')
                {{ $message }}
              @enderror
            </div>
          </div>
        </td>
      </tr>

      <tr>
        <th>
          <div class="form__group-title">
            <span class="form__label--item">性別</span>
            <span class="form__label--required">※</span>
          </div>
        </th>
        <td>
          <div class="form__group-content">
            <div class="form__input--radio">
              <input type="radio" name="gender" id="male" value="1" {{ old('gender','1') == '1' ? 'checked' : '' }}/>
              <label for="male">男性</label>

              <input type="radio" name="gender" id="female" value="2" {{ old('gender') == '2' ? 'checked' : '' }}/>
              <label for="female">女性</label>

              <input type="radio" name="gender" id="other" value="3" {{ old('gender') == '3' ? 'checked' : '' }}/>
              <label for="other">その他</label>
            </div>
            <div class="form__error">
              @error('gender')
                {{ $message }}
              @enderror
            </div>
          </div>
        </td>
      </tr>

      <tr>
        <th>
          <div class="form__group-title">
            <span class="form__label--item">メールアドレス</span>
            <span class="form__label--required">※</span>
          </div>
        </th>
        <td>
          <div class="form__group-content">
            <div class="form__input--text">
              <input type="email" name="email" placeholder="例）test@example.com" value="{{ old('email') }}"/>
              <!-- <input type="email" name="email" placeholder="例）test@example.com" value="test@example.com"/> -->
            </div>
            <div class="form__error">
              @error('email')
                {{ $message }}
              @enderror
            </div>
          </div>
        </td>
      </tr>

      <tr>
        <th>
          <div class="form__group-title">
            <span class="form__label--item">電話番号</span>
            <span class="form__label--required">※</span>
          </div>
        </th>
        <td>
          <div class="form__group-content">
            <div class="form__input--text">
              <input type="tel" name="tel" placeholder="080" value="{{ old('tel') }}"/>
              <input type="tel" name="tel_middle" placeholder="1234" value="{{ old('tel-middle') }}"/>
              <input type="tel" name="tel_bottom" placeholder="5678" value="{{ old('tel-bottom') }}"/>
             
              <!-- <input type="tel" name="tel" placeholder="080" value="080"/> -->
              <!-- <input type="tel" name="tel_middle" placeholder="1234" value="1234"/> -->
              <!-- <input type="tel" name="tel_bottom" placeholder="5678" value="5678"/> -->
            </div>
            <div class="form__error">
              @error('tel')
                {{ $message }}
              @enderror
              @error('tel_middle')
                {{ $message }}
              @enderror
              @error('tel_bottom')
                {{ $message }}
              @enderror
            </div>
          </div>
        </td>
      </tr>

      <tr>
        <th>
          <div class="form__group-title">
            <span class="form__label--item">住所</span>
            <span class="form__label--required">※</span>
          </div>
        </th>
        <td>
          <div class="form__group-content">
            <div class="form__input--text">
              <input type="text" name="address" placeholder="例）東京都渋谷区千駄ヶ谷1-2-3" value="{{ old('address') }}"/>
              <!-- <input type="text" name="address" placeholder="例）東京都渋谷区千駄ヶ谷1-2-3" value="東京都渋谷区千駄ヶ谷1-2-3"/> -->
            </div>
            <div class="form__error">
              @error('address')
                {{ $message }}
              @enderror
            </div>
          </div>
        </td>
      </tr>

      <tr>
        <th>
          <div class="form__group-title">
            <span class="form__label--item">建物名</span>
          </div>
        </th>
        <td>
          <div class="form__group-content">
            <div class="form__input--text">
              <input type="text" name="building" placeholder="例）建物名" value="{{ old('building') }}"/>
            </div>
            <div class="form__error">
              @error('building')
                {{ $message }}
              @enderror
            </div>
          </div>
        </td>
      </tr>

      <tr>
        <th>
          <div class="form__group-title">
            <span class="form__label--item">お問い合わせの種類</span>
            <span class="form__label--required">※</span>
          </div>
        </th>
        <td>
          <div class="form__group-content">
            <div class="form__input--text">
              <select class="search-form__item-select" name="category_id" >
                  <option value="" {{ old('category_id','') == '' ? 'selected' : '' }}>--選択してください--</option>
                  @foreach($categories as $category)
                      <option value="{{$category['id']}}" {{ old('category_id') == $category['id'] ? 'selected' : '' }}>
                        {{$category['content']}}
                      </option>
                  @endforeach
              </select>
            </div>
            <div class="form__error">
              @error('category_id')
                {{ $message }}
              @enderror
            </div>
          </div>
        </td>
      </tr>

      <tr>
        <th>
          <div class="form__group-title">
            <span class="form__label--item">お問い合わせ内容</span>
            <span class="form__label--required">※</span>
          </div>
        </th>
        <td>
          <div class="form__group-content">
            <div class="form__input--text">
              <textarea name="detail" placeholder="お問い合わせ内容をご記載ください">{{old('detail')}}</textarea>
              <!-- <textarea name="detail" placeholder="お問い合わせ内容をご記載ください">送ってください</textarea> -->
            </div>
            <div class="form__error">
                @error('detail')
                  {{ $message }}
                @enderror
              </div>
          </div>
        </td>
      </tr>
    </table>

    <div class="form__button">
      <button class="form__button-submit" type="submit">確認画面</button>
    </div>

  </form>
</div>
@endsection

