<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добро пожаловать!</title>
</head>
<body style="font-family: sans-serif; background-color: #f6f9fc; padding: 20px;">
<div style="background-color: #ffffff; padding: 30px; border-radius: 8px; max-width: 600px; margin: 0 auto;">
    <h1 style="color: #333333;">Рады видеть вас, {{ $user->first_name }}! 👋</h1>
    <p style="color: #666666; line-height: 1.6;">
        Ваш аккаунт успешно активирован. Теперь вам открыт полный доступ к личному кабинету, истории заказов и корзине нашего магазина.
    </p>
    <p style="color: #666666; line-height: 1.6;">
        В честь успешной регистрации мы дарим вам промокод на первую покупку: <strong>WELCOME10</strong>
    </p>
    <a href="{{ url('/products') }}" style="display: inline-block; background-color: #2563eb; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin-top: 20px;">
        Перейти к покупкам
    </a>
</div>
</body>
</html>
