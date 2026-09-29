<div>
    <label>Название</label>
    <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}">
</div>

<div>
    <label>Цена</label>
    <input type="number" step="0.01" name="price" value="{{ old('price', $product->price ?? '') }}">
</div>

<div>
    <label>Количество</label>
    <input type="number" name="stock" value="{{ old('stock', $product->stock ?? '') }}">
</div>

<div>
    <label>Артикул (SKU)</label>
    <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}">
</div>

<div>
    <label>Статус</label>
    <select name="status">
        <option value="active" {{ old('status', $product->status ?? '') === 'active' ? 'selected' : '' }}>Активен</option>
        <option value="inactive" {{ old('status', $product->status ?? '') === 'inactive' ? 'selected' : '' }}>Неактивен</option>
    </select>
</div>

<div>
    <label>Изображение</label>
    <input type="file" name="image">
</div>
