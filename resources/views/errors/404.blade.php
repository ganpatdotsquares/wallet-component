<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            text-align: center;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }

        .container h1 {
            font-size: 100px;
            margin: 0;
            color: #dc3545;
        }

        .container p {
            font-size: 24px;
            margin: 20px 0;
            color: #333;
        }

        .container a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        .container a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>404</h1>
        <p>Sorry, the page you are looking for does not exist.</p>
        <p><a href="{{ url()->previous() }}">Go back to the previous page</a></p>
    </div>
</body>

</html>