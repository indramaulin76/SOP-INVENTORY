<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'Auth') - Sae Bakery</title>
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            padding: 20px;
        }

        .auth-container {
            width: 100%;
            max-width: 400px;
            background: var(--white);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .auth-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            flex-shrink: 0;
        }

        .auth-logo .logo-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid var(--gold);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .auth-header h1 {
            font-size: 28px;
            color: var(--text-dark);
            margin-bottom: 5px;
            font-weight: 600;
        }

        .auth-header p {
            color: var(--text-light);
            font-size: 14px;
        }

        .auth-footer {
            text-align: center;
            margin-top: 20px;
            color: var(--text-light);
            font-size: 14px;
        }

        .auth-footer a {
            color: var(--gold);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .auth-container {
                padding: 25px;
            }

            .auth-logo {
                width: 60px;
                height: 60px;
                margin-bottom: 15px;
            }

            .auth-header h1 {
                font-size: 24px;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="auth-container">
        @yield('content')
    </div>

    @yield('scripts')
</body>
</html>
