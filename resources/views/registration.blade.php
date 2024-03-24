<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

    <div class="container">
        <h3>
            Hello {{ $data['userName'] }},
        </h3>

        <p>
            Thank you for choosing PLUS PHARMACY. As part of our registration process,
        </p>
        <p>
            we have generated a default password for you. Please use the following information to log in to your account
        <p>
            Email : {{ $data['userEmail'] }}
        </p>
        <p>
            Password : {{ $data['password'] }}
        </p>

        <p>
            Best Regards,
            PLUS PHARMACY
        </p>
    </div>



</body>

</html>
