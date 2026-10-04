<!DOCTYPE html>
<html>
<head>
@include('partials.font')
    <title>RakanKampus - Grok Test</title>
</head>

<body>

    <h2>RakanKampus - Grok Test</h2>

    <form method="POST" action="/chatbot">
        @csrf

        <input
            type="text"
            name="message"
            placeholder="Ask something..."
            style="width: 400px;"
        >

        <button type="submit">
            Ask Grok
        </button>
    </form>

</body>
</html>