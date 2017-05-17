<!DOCTYPE html>
<html>
<head>
  <title>@yield('title')</title>
</head>
<body>
<ul>
{{--
    @foreach (routes() as $r)
        <li>{{ $r }}</li>
    @endforeach
--}}
</ul>

@yield('content')
</body>
</html>