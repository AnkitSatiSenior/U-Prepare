<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg" />
    <link rel="shortcut icon" href="/favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="U-Prepare" />
    <link rel="manifest" href="/favicon/site.webmanifest" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('asset/vendors/bootstrap/dist/css/bootstrap.min.css') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('asset/vendors/font-awesome/css/font-awesome.min.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">

    <!-- jQuery UI CSS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('asset/build/css/custom.min.css') }}?ver=1.5.1">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.41.0/dist/apexcharts.min.js"></script>

    <!-- Optional Export Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <!-- jQuery (needed for some export plugins or future use) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- jQuery UI JS -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
        integrity="sha256-T0Vest4WQxCJ27o6c2XmqKk8zCD3pKk6aa0GTV8o60k=" crossorigin="anonymous"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- TinyMCE -->




    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
    <script src="{{ asset('asset/build/js/custom.js') }}?ver=1.5.1"></script>

     @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body class="nav-md">

    <x-loader></x-loader>

    <div class=" container body">
        <div class="main_container" style="min-height: 100vh;">

            <x-admin.sidebar></x-admin.sidebar>
            <x-admin.header></x-admin.header>

            <div class="right_col" role="main" style="min-height: 100vh;">
                @if (session('success'))
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <x-alert type="success" :message="session('success')" dismissible />
                        </div>
                    </div>
                @endif
                @if (session('error'))
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <x-alert type="danger" :message="session('error')" dismissible />
                        </div>
                    </div>
                @endif
                {{ $slot }}
                {{-- Toast container (fixed bottom right) --}}
                <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100"></div>

            </div>



            <!-- Chat Icon -->
            <div style="position: fixed; bottom: 20px; right: 20px; z-index: 999;">
                <button id="chat-toggle" class="btn btn-primary rounded-circle" style="width: 60px; height: 60px;">
                    <i class="fas fa-comments fa-lg"></i>
                </button>
            </div>

            <!-- Chat Modal -->
            <div id="chat-modal" class="card shadow"
                style="position: fixed; bottom: 90px; right: 20px; width: 350px; max-height: 500px; display: none;">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span>Chat</span>
                    <button id="chat-close" class="btn btn-sm btn-light">&times;</button>
                </div>
                <div class="card-body">
                    @if (isset($conversationId))
                        @livewire('chat-component', ['conversationId' => $conversationId])
                    @else
                        <div class="text-center text-muted">
                            Select a conversation from <a href="{{ route('admin.chat.index') }}" class="fw-bold">chat
                                list</a>
                        </div>
                    @endif
                </div>
            </div>


        </div>
        <x-admin.footer></x-admin.footer>
        <x-admin.loader></x-admin.loader>
    </div>

    <link rel="preload" href="{{ asset('assets/img/svg/img_loader.svg') }}" as="image" />
    <!-- jQuery (only once) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="{{ asset('asset/vendors/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Popper (needed for Bootstrap if not bundled) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <!-- Other libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <!-- Your custom scripts -->
    <script src="{{ asset('asset/build/js/custom.js') }}"></script>
    <script src="{{ asset('asset/custom.js') }}?ver=1.1.0"></script>
    <script src="{{ asset('assets/js/mis/scripts.js') }}?ver=1.6.2"></script>

    <!-- Livewire -->
    @livewireScripts

    @stack('modals')
    @yield('script')
</body>

</html>
