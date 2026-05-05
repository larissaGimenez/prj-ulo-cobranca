<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'ULO Matriz') }}</title>

    <script src="{{ asset('vendors/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="{{ asset('vendors/simplebar/simplebar.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link href="{{ asset('assets/css/theme.min.css') }}" type="text/css" rel="stylesheet" id="style-default">
    <link href="{{ asset('assets/css/user.min.css') }}" type="text/css" rel="stylesheet" id="user-style-default">

    <style>
        .loader-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 999999;
        }

        .loader-content {
            text-align: center;
            color: #ffffff;
        }

        .modern-spinner {
            width: 50px;
            height: 50px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #3874ff;
            animation: spin 0.8s ease-in-out infinite;
            margin: 0 auto 15px;
        }

        .loader-text {
            font-family: 'Nunito Sans', sans-serif;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin: 0;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Global Loading Overlay -->
    <div id="global-loader" class="loader-container">
        <div class="loader-content">
            <div class="modern-spinner"></div>
            <p class="loader-text">Carregando...</p>
        </div>
    </div>

    <main class="main" id="top">
        @include('layouts.partials.sidebar')

        @include('layouts.partials.navbar')

        <div class="content">
            @yield('content')

            @include('layouts.partials.footer')
        </div>
    </main>

    <script src="{{ asset('vendors/popper/popper.min.js') }}"></script>
    <script src="{{ asset('vendors/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendors/anchorjs/anchor.min.js') }}"></script>
    <script src="{{ asset('vendors/is/is.min.js') }}"></script>
    <script src="{{ asset('vendors/fontawesome/all.min.js') }}"></script>
    <script src="{{ asset('vendors/lodash/lodash.min.js') }}"></script>
    <script src="{{ asset('vendors/list.js/list.min.js') }}"></script>
    <script src="{{ asset('vendors/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('vendors/dayjs/dayjs.min.js') }}"></script>
    <script src="{{ asset('vendors/echarts/echarts.min.js') }}"></script>
    <script src="{{ asset('assets/js/phoenix.js') }}"></script>
    <script src="{{ asset('vendors/glightbox/glightbox.min.js') }}"></script>
    <script src="{{ asset('vendors/sortablejs/Sortable.min.js') }}"></script>



    @stack('scripts')

    <script>
        const loader = document.getElementById('global-loader');

        /**
         * Exibe o loader manualmente
         */
        function showLoader() {
            if (loader) {
                loader.style.display = 'flex';
            }
        }

        /**
         * Oculta o loader manualmente
         */
        function hideLoader() {
            if (loader) {
                loader.style.display = 'none';
            }
        }

        // Gatilho Automático: Dispara ao navegar para outra página ou atualizar
        window.addEventListener('beforeunload', function() {
            showLoader();
        });

        // Reset para o caso do usuário cancelar a saída ou usar o botão "Voltar" do browser
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                hideLoader();
            }
        });

        // Opcional: Integrar com formulários automaticamente
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                if (this.checkValidity()) {
                    showLoader();
                }
            });
        });
    </script>
</body>

</html>