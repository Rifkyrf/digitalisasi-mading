{{-- <main class="content" id="content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mx-3 mx-md-0" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mx-3 mx-md-0" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</main>

<style>
    /* Google Font Poppins — modern & mudah dibaca */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f8f9fa;
    }

    .content {
        padding: 20px;
        transition: margin-left 0.3s ease;
        min-height: calc(100vh - 60px);
    }

    /* Alert dengan ikon & spacing lebih baik */
    .alert {
        border-radius: 12px;
        padding: 1rem 1.5rem;
        font-weight: 500;
        font-size: 0.95rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    /* Floating Upload Button — tetap muncul di semua halaman */
    .fixed-upload-btn {
        position: fixed;
        bottom: 24px;
        right: 24px;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #0d47a1, #0b3c85);
        color: white;
        border: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        z-index: 1050;
        box-shadow: 0 4px 12px rgba(13, 71, 161, 0.3);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .fixed-upload-btn:hover {
        transform: scale(1.1) rotate(8deg);
        box-shadow: 0 8px 20px rgba(13, 71, 161, 0.4);
    }

    .fixed-upload-btn::after {
        content: "Upload Karya";
        position: absolute;
        bottom: 72px;
        right: 50%;
        transform: translateX(50%);
        background: rgba(0,0,0,0.85);
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.85rem;
        white-space: nowrap;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }

    .fixed-upload-btn:hover::after {
        opacity: 1;
    }

    /* Responsif untuk alert dan konten */
    @media (max-width: 576px) {
        .content {
            padding: 15px;
        }
        .alert {
            margin: 0 10px;
            font-size: 0.9rem;
        }
    }
</style>
 --}}
