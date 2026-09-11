{{-- @author Jose Rodriguez <jrpcone@gmail.com> --}}
{{-- @license MIT --}}
{{-- @link https://github.com/jorodriguezpr/ --}}

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel - Micro Computer Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #0099FF;
            --dark: #0D1B2E;
            --secondary: #1a3a4a;
        }

        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .navbar {
            background: linear-gradient(135deg, var(--dark) 0%, var(--secondary) 100%);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.3rem;
            color: var(--primary) !important;
        }

        .sidebar {
            background-color: var(--dark);
            min-height: calc(100vh - 60px);
            padding: 2rem 0;
            position: sticky;
            top: 60px;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1.5rem;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover {
            color: var(--primary);
            border-left-color: var(--primary);
            background-color: rgba(0, 153, 255, 0.1);
        }

        .sidebar .nav-link.active {
            color: var(--primary);
            border-left-color: var(--primary);
            background-color: rgba(0, 153, 255, 0.1);
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: #0077cc;
            border-color: #0077cc;
        }

        .badge {
            font-size: 0.85rem;
            padding: 0.35rem 0.65rem;
        }

        .table th {
            background-color: #f8f9fa;
            border-top: 2px solid #dee2e6;
            font-weight: 600;
            color: var(--dark);
        }

        .main-content {
            padding: 2rem;
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }
        }
    </style>
</head>

<body>
    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.live-chat.index') }}">
                <i class="bi bi-shield-lock me-2"></i>Admin Panel
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link">Micro Computer Services</span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/">Back to Site</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="#" onclick="alert('Logout functionality not implemented'); return false;">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            {{-- Sidebar --}}
            <div class="col-md-3 col-lg-2 sidebar d-none d-md-block">
                <ul class="nav flex-column">
                    <li class="nav-item mb-2">
                        <a class="nav-link {{ request()->routeIs('admin.live-chat*') ? 'active' : '' }}"
                            href="{{ route('admin.live-chat.index') }}">
                            <i class="bi bi-chat-dots me-2"></i>Live Chat
                            @php
                                $pendingCount = \Microrepairnet\ChatWidget\Models\LiveChat::where('status', 'pending')->count();
                            @endphp
                            @if($pendingCount > 0)
                                <span class="badge bg-danger float-end">{{ $pendingCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link {{ request()->routeIs('admin.ai-settings*') ? 'active' : '' }}"
                            href="{{ route('admin.ai-settings.index') }}">
                            <i class="bi bi-robot me-2"></i>AI Settings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/">
                            <i class="bi bi-house me-2"></i>Home
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Main Content --}}
            <div class="col-md-9 col-lg-10 main-content">
                {{-- Flash Messages --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Content --}}
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
