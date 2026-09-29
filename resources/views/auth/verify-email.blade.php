<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Подтверждение Email</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f3f4f6; margin: 0; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 400px; text-align: center; }
        h1 { color: #1f2937; font-size: 24px; margin-bottom: 15px; }
        p { color: #4b5563; line-height: 1.5; margin-bottom: 25px; }
        button { background: #2563eb; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 14px; }
        button:hover { background: #1d4ed8; }
        .success { color: #16a34a; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="card">
    <h1>Подтвердите ваш Email</h1>
    <p>Спасибо за регистрацию! Мы отправили ссылку для подтверждения на вашу почту. Пожалуйста, проверьте ящик и кликните по ней.</p>

    @if (session('message'))
        <div class="success">Ссылка успешно отправлена повторно!</div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit">
            Отправить письмо повторно
        </button>
    </form>
</div>

</body>
</html>
