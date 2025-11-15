@php
$results = session('results');
@endphp

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8" />
<title>Гео</title>
</head>
<body>
<h1>Поиск по адресу</h1>

<form method="POST" action="{{ route('show') }}">
    @csrf
    <input type="text" name="address" placeholder="Введите адрес..." required value="{{ $address ?? '' }}"/>
    <button type="submit">Найти</button>
</form>

@if(session('error'))
    <p style="color: red;">{{ session('error') }}</p>
@endif

@if(isset($results))
    @if(count($results) > 0)
        <ul>
        @foreach($results as $result)
            <li style="margin-bottom:30px;">
                <strong>Полный адрес:</strong> {{ $result['fullAddress'] ?? 'Нет данных' }}<br/>
                <strong>Улица:</strong> {{ $result['street'] ?? 'Нет данных' }}<br/>
                <strong>Дом:</strong> {{ $result['house'] ?? 'Нет данных' }}<br/>
                <strong>Ближайшее метро:</strong> {{ $result['metro'] ?? 'Нет данных' }}<br/>
            </li>
        @endforeach
        </ul>
    @else
        <p>Нет результатов.</p>
    @endif
@endif
</body>
</html>

