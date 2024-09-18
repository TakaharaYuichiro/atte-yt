<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>FashionablyLate</title>
  <link href='https://fonts.googleapis.com/css?family=Inika' rel='stylesheet'>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/common.css') }}" />
  @yield('css')
</head>

<body>
  <header class="header">
    <div class="header__inner">
      <div class="header-logo">
        <a  href="/">
            <h1>FashionablyLate</h1>
        </a>
      </div>

      <div class="header-utilities">
        @yield('utilities')
      </div>
        
    </div>
  </header>

  <main>
    @yield('content')
  </main>
</body>

</html>