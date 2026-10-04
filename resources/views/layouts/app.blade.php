<!DOCTYPE html>
<html lang="en">

<head>
@include('partials.font')

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>RakanKampus</title>

@include('partials.no-hscroll')

@vite([
'resources/css/app.css',
'resources/js/app.js'
])

</head>


<body>

@yield('content')

</body>

</html>