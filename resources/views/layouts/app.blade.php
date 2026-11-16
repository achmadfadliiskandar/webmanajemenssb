<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$judul}}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .wrapper { display: flex; align-items: stretch; min-height: 100vh; }
        #sidebar { min-width: 250px; max-width: 250px; background: #1e3c72; color: #fff; transition: all 0.3s; }
        #sidebar .sidebar-header { padding: 20px; background: #162d55; }
        #sidebar ul li a { padding: 15px 25px; display: block; color: rgba(255,255,255,0.8); text-decoration: none; }
        #sidebar ul li a:hover { background: rgba(255,255,255,0.1); color: #fff; }
        #sidebar ul li.active > a { background: rgba(255,255,255,0.2); border-left: 4px solid #ff2d20; }
        #content { width: 100%; padding: 20px; background: #f4f7f6; }
    </style>
    @livewireStyles
</head>
<body>

    @auth
        <div class="wrapper">
            <livewire:templates.sidebar />
            <div id="content">
                {{ $slot }}
            </div>
        </div>
    @else
        {{ $slot }}
    @endauth

    @livewireScripts
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>