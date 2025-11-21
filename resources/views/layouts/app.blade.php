<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SpeedyIndex App') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS (CDN for simplicity, or use Vite) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        /* Sidebar Styling */
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #212529;
            padding-top: 1rem;
            color: white;
            z-index: 1000;
            transition: all 0.3s;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.8rem 1.5rem;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar .brand {
            font-size: 1.5rem;
            font-weight: bold;
            padding: 0 1.5rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 1rem;
            display: block;
            color: white;
            text-decoration: none;
        }
        /* Main Content Wrapper */
        .main-content {
            margin-left: 250px; /* Match sidebar width */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        /* Topbar Styling */
        .topbar {
            height: 60px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
        }
        /* Page Content */
        .page-content {
            padding: 2rem;
            flex: 1;
        }
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }
            .sidebar.active {
                margin-left: 0;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <nav class="sidebar" id="sidebar">
        <a href="{{ route('dashboard') }}" class="brand">
            <i class="bi bi-rocket-takeoff-fill me-2"></i> SpeedyIndex
        </a>
        <div class="nav flex-column">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <div class="text-uppercase small text-light fw-bold px-4 mt-3 mb-2">Tasks Management</div>
            
            <a href="{{ route('tasks.create') }}" class="nav-link {{ request()->routeIs('tasks.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle"></i> Create New Task
            </a>
            <a href="{{ route('tasks.index') }}" class="nav-link {{ request()->routeIs('tasks.index') ? 'active' : '' }}">
                <i class="bi bi-list-task"></i> All Tasks
            </a>

            <div class="text-uppercase small text-light fw-bold px-4 mt-3 mb-2">Monitoring</div>

            <a href="#" class="nav-link"> <!-- Placeholder for future route -->
                <i class="bi bi-graph-up"></i> Indexing Reports
            </a>
            <a href="{{ route('api-logs.index') }}" class="nav-link {{ request()->routeIs('api-logs.index') ? 'active' : '' }}">
                <i class="bi bi-terminal"></i> API Logs
            </a>
            
            <div class="mt-auto p-3 border-top border-secondary">
                 <a href="#" class="nav-link text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-left"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </nav>

    <!-- MAIN SECTION -->
    <div class="main-content" id="main-content">
        
        <!-- TOPBAR -->
        <header class="topbar">
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-secondary d-md-none me-3" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="m-0 text-secondary">@yield('header', 'Dashboard')</h5>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <!-- Balance Badge -->
                <div class="badge bg-primary bg-opacity-10 text-primary p-2 px-3 border border-primary-subtle rounded-pill">
                    <i class="bi bi-wallet2 me-1"></i> Balance: 
                    <span class="fw-bold">
                        {{-- Balance passed via View Composer or shared variable --}}
                        {{ $sharedBalance['indexer'] ?? '0' }} Credits
                    </span>
                </div>

                <!-- User Profile Dropdown -->
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none text-dark dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="bg-dark text-white rounded-circle d-flex justify-content-center align-items-center me-2" style="width: 35px; height: 35px;">
                            {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                        </div>
                        <span class="d-none d-sm-inline fw-medium">{{ Auth::user()->name ?? 'User' }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end text-small shadow" aria-labelledby="dropdownUser1">
                        <li><a class="dropdown-item" href="#">Profile</a></li>
                        <li><a class="dropdown-item" href="#">Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sign out</a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- CONTENT AREA -->
        <main class="page-content">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Page Content Injection -->
            @yield('content')
        </main>

        <!-- FOOTER -->
        <footer class="bg-white text-center py-3 border-top mt-auto">
            <small class="text-muted">&copy; {{ date('Y') }} SpeedyIndex App. All rights reserved.</small>
        </footer>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Sidebar Toggle Script -->
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
    </script>
    
    @stack('scripts')
</body>
</html>
