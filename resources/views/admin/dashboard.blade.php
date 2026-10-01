<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админка</title>
    <!-- Наш локальный красивый стиль -->
    <link href="{{ asset('admin.css') }}" rel="stylesheet">
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <!-- Боковое меню (Sidebar) -->
        <div class="sidebar-panel">
            <a href="{{ url('/products') }}" target="_blank" style="color: #ffffff; text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">
                🛍️ Магазин
            </a>
            <hr>
            <ul class="nav-menu">
                <li>
                    <a class="nav-link active" href="{{ route('admin.dashboard') }}">📊 Главная</a>
                </li>
                <li>
                    <a class="nav-link" href="{{ route('admin.products.index') }}">📦 Товары</a>
                </li>
                <li>
                    <a class="nav-link" href="{{ route('admin.orders.index') }}">🛒 Заказы</a>
                </li>
                <li>
                    <a class="nav-link" href="{{ route('admin.users.index') }}">👥 Пользователи</a>
                </li>
            </ul>
            <hr>
            <!-- Форма выхода -->
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">🚪 Выйти</button>
            </form>
        </div>

        <!-- Основной контент панели -->
        <div class="main-content">
            <h1>Панель управления</h1>
            <p>Добро пожаловать обратно! Вот актуальные данные вашего проекта на сегодня.</p>

            <p style="color: #64748b; font-size: 0.9rem; margin-top: -5px; margin-bottom: 25px; font-weight: 500;">
                Последнее обновление:
                @if($lastReport && $lastReport->calculated_at)
                    {{ $lastReport->calculated_at->format('d.m.Y H:i') }}
                @else
                    <span style="color: #94a3b8; font-style: italic;">данные еще не рассчитывались</span>
                @endif
            </p>

            <!-- Сетка красивых карточек -->
            <div class="stats-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
                <div class="stat-card">
                    <span class="card-title">Всего товаров</span>
                    <h3 class="card-value">{{ $productsCount }}</h3>
                </div>
                <div class="stat-card">
                    <span class="card-title">Новых заказов</span>
                    <h3 class="card-value">{{ $ordersCount }}</h3>
                </div>
                <div class="stat-card">
                    <span class="card-title">⌛ Ожидают оплаты</span>
                    <h3 class="card-value">{{ $pendingOrdersCount }}</h3>
                </div>
                <div class="stat-card">
                    <span class="card-title">Пользователи</span>
                    <h3 class="card-value">{{ $usersCount }}</h3>
                </div>
            </div>

            <!-- Блок недельного отчета -->
            <div style="margin-top: 30px; padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                <h2 style="margin-top: 0; color: #1e293b;">📊 Отчёт за неделю:</h2>

                @if(!empty($report['calculated_at']))
                    <p style="color: #64748b; font-size: 0.875rem; margin-top: -10px; margin-bottom: 20px;">
                        Данные актуальны на: {{ is_string($report['calculated_at']) ? \Carbon\Carbon::parse($report['calculated_at'])->format('d.m.Y H:i') : $report['calculated_at']->format('d.m.Y H:i') }}
                    </p>
                @endif

                <p>Всего заказов: <strong>{{ $report['orders_count'] }}</strong></p>
                <p>Продаж: <strong>{{ $report['sales_count'] }}</strong></p>
                <p>Отменено заказов: <strong>{{ $report['canceled_count'] }}</strong></p>
                <p>Выручка: <strong style="color: #10b981;">{{ number_format((float) $report['revenue'], 2, ',', ' ') }} ₽</strong></p>

                <!-- Детализация по дням -->
                <div style="margin-top: 30px; padding: 20px; background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <h3 style="margin-top: 0; color: #1e293b; margin-bottom: 15px; font-size: 1.25rem;">Детализация по дням</h3>

                    <table class="table table-striped align-middle mt-4" style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead>
                        <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                            <th style="padding: 12px 10px; color: #64748b; font-weight: 600;">Дата</th>
                            <th style="padding: 12px 10px; color: #64748b; font-weight: 600;">Заказов</th>
                            <th style="padding: 12px 10px; color: #64748b; font-weight: 600;">Продаж</th>
                            <th style="padding: 12px 10px; color: #64748b; font-weight: 600;">Выручка</th>
                            <th style="padding: 12px 10px; color: #64748b; font-weight: 600;">Средний чек</th>
                            <th style="padding: 12px 10px; color: #64748b; font-weight: 600;">Отменено</th>
                            <th style="padding: 12px 10px; color: #64748b; font-weight: 600;">Пересчитано</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($report['daily_reports'] as $dailyReport)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 10px; font-weight: bold; color: #334155;">
                                    {{ is_string($dailyReport->report_date) ? \Carbon\Carbon::parse($dailyReport->report_date)->format('d.m.Y') : $dailyReport->report_date->format('d.m.Y') }}
                                </td>
                                <td style="padding: 12px 10px; color: #334155;">
                                    {{ $dailyReport->orders_count }}
                                </td>
                                <td style="padding: 12px 10px; color: #334155;">
                                    {{ $dailyReport->sales_count }}
                                </td>
                                <td style="padding: 12px 10px; color: #10b981; font-weight: bold;">
                                    {{ number_format((float) $dailyReport->revenue, 2, ',', ' ') }} ₽
                                </td>
                                <td style="padding: 12px 10px; color: #2563eb; font-weight: bold;">
                                    {{ number_format((float) ($dailyReport->average_order_value ?? 0), 2, ',', ' ') }} ₽
                                </td>
                                <td style="padding: 12px 10px; color: #ef4444;">
                                    {{ $dailyReport->canceled_count }}
                                </td>
                                <td style="padding: 12px 10px; color: #64748b; font-size: 0.875rem;">
                                    {{ is_string($dailyReport->calculated_at) ? \Carbon\Carbon::parse($dailyReport->calculated_at)->format('d.m.Y H:i') : $dailyReport->calculated_at?->format('d.m.Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding: 30px; text-align: center; color: #64748b; background: #f8fafc;">
                                    Отчёты ещё не сформированы.<br>
                                    <small style="color: #94a3b8; font-size: 0.875rem;">Запустите Scheduler и queue worker в терминале Docker.</small>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
</div>

</body>
</html>
