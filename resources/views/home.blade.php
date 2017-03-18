<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Buy All Steam Games</title>

        <!-- Styles -->
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <style>
            #main p{
                font-size: 150%;
            }
            .red {
                color: #FF4136;
            }
            html {
                position: relative;
                min-height: 100%;
            }
            body {
                margin-bottom: 60px;
            }
            footer {
                position: absolute;
                bottom: 0;
                width: 100%;
                /* Set the fixed height of the footer here */
                height: 60px;
            }
        </style>
    </head>
    <body>
        <div class="container" id="main">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Buy All Steam Games</h1>
                    <p>It costs <span class="red">${{ $final }}</span> to buy all steam games which originally costs <span class="red">${{ $init }}</span>.</p>
                </div>
            </div>
        </div>
        <footer>
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <p>&copy {{ date('Y') }} <a href="https://seewang.me">seewang.me</a></p>
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>
