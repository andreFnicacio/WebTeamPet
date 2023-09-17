<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1, maximum-scale=0.85">
    <title>Lifepet Saúde -
        Avaliação de atendimento
    </title>
    <style>
        @font-face {
            font-family: 'Source Sans Pro';
            font-style: normal;
            font-weight: 400;
            font-display: swap;
            src: local('Source Sans Pro Regular'), local('SourceSansPro-Regular'), url(https://fonts.gstatic.com/s/sourcesanspro/v12/6xK3dSBYKcSV-LCoeQqfX1RYOo3qOK7g.ttf) format('truetype');
        }
        html {
            height: 100%;
        }
        body {
            background-color: #ffffff;
            background-image: url('https://www.lifepet.com.br/wp-content/uploads/2017/04/simule6.jpg');
            background-position: center center;
            background-repeat: no-repeat;
            padding-top:25px;
            padding-right:20px;
            padding-bottom:85px;
            padding-left:20px;
            margin-bottom: 0px;
            margin-top: 0px;
            -webkit-background-size:cover;
            -moz-background-size:cover;
            -o-background-size:cover;
            background-size:cover;
        }
        .box-avaliacao {
            max-width: 300px;
            width: 100%;
            margin: 50px auto;
            font-family: "Source Sans Pro";
            text-align: center;
            border-radius: 20px;
            box-shadow: 0px 3px 4px 2px #d8d8d8;
        }
        .box-avaliacao div.img-wrapper {
            background: white;
            padding-top: 20px;
            border-radius: 20px 20px 0 0;
            padding-bottom: 20px;
            margin-bottom: -25px;
        }
        .box-avaliacao div.avaliacao-wrapper {
            background: #009be3;
            padding-bottom: 50px;
            border-radius: 0px 0px 20px 20px;
            color: white;
        }
        .box-avaliacao img.logo {
            text-align: center;
            display: block;
            margin: 0 auto;
            max-width: 240px;
            width: 90%;
        }
        .box-avaliacao h1 {
            padding-top: 50px;
        }
        .box-avaliacao ul.avaliacao-nota {
            list-style: none;
            padding-left: 0;
            padding-top: 50px;
        }
        .box-avaliacao ul.avaliacao-nota li {
            display: inline-block;
        }
        .box-avaliacao ul.avaliacao-nota li a {
            color: #ccc;
        }
        .box-avaliacao ul.avaliacao-nota li a i {
            font-size: 28px;
            letter-spacing: 18px;
        }
        .box-avaliacao ul.avaliacao-nota li.selected a {
            color: #ffc100 !important;
        }
    </style>
</head>
<body>
<div class="box-avaliacao">
    <div class="img-wrapper">
        <img src="https://www.lifepet.com.br/wp-content/uploads/2019/07/logo.png" alt="" class="logo">
    </div>
    <div class="avaliacao-wrapper">
        <h1>Obrigado!</h1>
        <h5>Agradecemos pela sua contribuição.</h5>
        <h5>
            Você será redirecionado em...
            <span id="countdown"></span>
        </h5>
    </div>
</div>
<script>
    var seconds = 5;

    function countdown() {
        seconds = seconds - 1;
        if (seconds < 0) {
            // Chnage your redirection link here
            window.location = "https://www.lifepet.com.br";
        } else {
            // Update remaining seconds
            document.getElementById("countdown").innerHTML = seconds;
            // Count down using javascript
            window.setTimeout("countdown()", 1000);
        }
    }

    // Run countdown function
    countdown();
</script>
</body>
</html>