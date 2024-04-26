<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite('resources/js/app.js')
    @inertiaHead
</head>

<body>

    <div id="pageLoading" class="loading-box">
        <div class="app-loading">
            <div class="app-loading__loader"></div>
        </div>
        <style>
            body {
                width: 100vw;
                height: 100vh;
                background: #0c0c0c url(/assets/imgs/login_bg.jpg) top center no-repeat;
                background-size: auto 100%;
                display: flex;
                position: relative;
                overflow: hidden;
                align-items: center;
                justify-content: center;
            }

            .app-loading {
                position: absolute;
                top: 0px;
                left: 0px;
                right: 0px;
                bottom: 0px;
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
            }

            .app-loading__loader {
                box-sizing: border-box;
                width: 88px;
                height: 88px;
                border: 5px solid transparent;
                border-top-color: #000;
                border-radius: 50%;
                animation: .5s loader linear infinite;
                position: relative;
            }

            .app-loading__loader:before {
                box-sizing: border-box;
                content: '';
                display: block;
                width: inherit;
                height: inherit;
                position: absolute;
                top: -5px;
                left: -5px;
                border: 5px solid #1D78FF;
                border-radius: 50%;
                opacity: .5;
            }

            @keyframes loader {
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
                }
            }
        </style>
    </div>
    @inertia
</body>

</html>
