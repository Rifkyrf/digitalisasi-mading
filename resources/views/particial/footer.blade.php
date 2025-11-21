    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>SMK Bakti Nusantara 666</h5>
                    <p>Mencetak generasi unggul, kreatif, dan berkarakter untuk masa depan Indonesia.</p>
                </div>
                <div class="col-md-3">
                    <h5>Quick Links</h5>
                    <ul class="footer-links">
                        <li><a href="/">Beranda</a></li>
                        <li><a href="{{ route('osis.index') }}">OSIS</a></li>
                        @if(Auth::check())
                            <li><a href="/dashboard">Dashboard</a></li>
                        @else
                            <li><a href="{{ route('login') }}">Login</a></li>
                        @endif
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Kontak</h5>
                    <ul class="footer-links">
                        <li><i class="fas fa-map-marker-alt me-2"></i> Jl. Pendidikan No. 666</li>
                        <li><i class="fas fa-phone me-2"></i> (021) 1234-5678</li>
                        <li><i class="fas fa-envelope me-2"></i> info@smkbn666.sch.id</li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="footer-bottom">
                <p>&copy; 2025 SMK Bakti Nusantara 666. Semua karya siswa dilindungi.</p>
            </div>
        </div>
    </footer>