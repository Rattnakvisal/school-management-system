<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            margin: 0;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        h1 {
            margin-top: 0;
        }

        .user {
            color: #4b5563;
            margin-bottom: 20px;
        }

        button {
            background: #111827;
            color: #fff;
            border: 0;
            border-radius: 6px;
            padding: 10px 14px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>{{ $title }}</h1>
        <p class="user">
            Logged in as <strong>{{ auth()->user()->name }}</strong>
            ({{ auth()->user()->email }}) - role: <strong>{{ auth()->user()->role }}</strong>
        </p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </div>
</body>

</html>
