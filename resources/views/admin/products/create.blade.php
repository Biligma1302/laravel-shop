<div class="form-container">
    <h1 class="form-title">Создание товара</h1>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @include('admin.products._form')

        <div class="form-actions">
            <a href="{{ route('admin.products.index') }}" class="btn-back">Назад</a>
            <button type="submit" class="btn-submit">Создать</button>
        </div>
    </form>
</div>

<style>
    .form-container {
        max-width: 600px;
        margin: 20px auto;
        padding: 30px;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        font-family: 'Segoe UI', system-ui, sans-serif;
    }

    .form-title {
        margin-bottom: 24px;
        color: #1a202c;
        font-size: 24px;
        font-weight: 600;
    }

    .form-container input[type="text"],
    .form-container input[type="number"],
    .form-container textarea,
    .form-container select {
        width: 100%;
        padding: 10px 14px;
        margin-top: 6px;
        margin-bottom: 18px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 14px;
        box-sizing: border-box;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-container input:focus,
    .form-container textarea:focus,
    .form-container select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }

    .form-container label {
        display: block;
        font-size: 14px;
        font-weight: 500;
        color: #475569;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 6px;
    }

    .btn-submit, .btn-back {
        flex: 1;
        padding: 12px;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        text-align: center;
        text-decoration: none;
        box-sizing: border-box;
        transition: background-color 0.2s, border-color 0.2s, color 0.2s;
    }

    .btn-submit {
        background-color: #2563eb;
        color: white;
        border: none;
    }

    .btn-submit:hover {
        background-color: #1d4ed8;
    }

    .btn-back {
        background-color: #ffffff;
        color: #64748b;
        border: 1px solid #cbd5e1;
    }

    .btn-back:hover {
        background-color: #f8fafc;
        color: #334155;
        border-color: #94a3b8;
    }
</style>
