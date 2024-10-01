<div class="header-utilities">
    <form class="utilitiy-form" action="/" method="get">
        @csrf
        <button class="utility-button">ホーム</button>
    </form>
    <form class="utilitiy-form" action="/attendance" method="get">
        @csrf
        <button class="utility-button">日付一覧</button>
    </form>
    <form class="utilitiy-form" action="/logout" method="post">
        @csrf
        <button class="utility-button">ログアウト</button>
    </form>
</div>