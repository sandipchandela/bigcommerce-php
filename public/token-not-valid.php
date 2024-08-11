<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expiry</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f8ff;
            margin: 0;
        }
        .container {
            text-align: center;
        }
        .countdown {
            font-size: 2rem;
            color: #333;
        }
        .expired {
            font-size: 2.5rem;
            color: blue;
            margin-top: 20px;
        }
       
    </style>
</head>
<body>
    <div class="container"> 
        <div class="expired">Oh no! The token has expired!</div>
    </div>
</body>
</html>