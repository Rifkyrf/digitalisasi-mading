document.addEventListener("DOMContentLoaded", function () {
    // Ambil elemen navbar, body, dan collapse
    const navbar = document.querySelector('.navbar');
    const navbarCollapse = document.getElementById('navbarNav');
    const body = document.body;

    // Animasi awal (seperti sebelumnya)
    const heroElements = document.querySelectorAll('.hero-title, .hero-description, .hero-stats');
    heroElements.forEach((el, index) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.8s ease-out, transform 0.8s ease-out';
        setTimeout(() => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, 200 + index * 200);
    });

    const filterBtns = document.querySelectorAll('.filter-btn');
    filterBtns.forEach((btn, index) => {
        btn.style.opacity = '0';
        btn.style.transform = 'translateY(-20px)';
        btn.style.transition = 'all 0.5s ease';
        setTimeout(() => {
            btn.style.opacity = '1';
            btn.style.transform = 'translateY(0)';
        }, 100 + index * 150);
    });

    const cards = document.querySelectorAll('.work-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'scale(0.95)';
        card.style.transition = 'opacity 0.7s ease, transform 0.7s ease';
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'scale(1)';
        }, 100 + index * 120);
    });

    // Animasi footer
    const footer = document.querySelector('.footer');
    const observer = new IntersectionObserver(([entry]) => {
        if (entry.isIntersecting) {
            footer.classList.add('visible');
        }
    }, { threshold: 0.1 });
    observer.observe(footer);

    // --- SOLUSI UNTUK HAMBURGER MENU ---
    // Event listener untuk tombol navbar-toggler
    document.querySelector('.navbar-toggler').addEventListener('click', function() {
        // Cek apakah menu sedang terbuka atau tertutup
        if (navbarCollapse.classList.contains('show')) {
            // Jika sedang ditutup, kembalikan padding body ke normal
            setTimeout(() => {
                 body.style.paddingTop = `${getComputedStyle(navbar).height}`;
            }, 300); // Delay sebentar agar transisi collapse selesai
        } else {
            // Jika akan dibuka, sesuaikan padding body dengan tinggi navbar + tinggi menu
            const navbarHeight = navbar.offsetHeight;
            const menuHeight = navbarCollapse.scrollHeight; // Ambil tinggi konten menu
            body.style.paddingTop = `${navbarHeight + menuHeight}px`;
        }
    });

    // Juga atur ulang padding jika ukuran layar berubah (misalnya rotasi hp)
    window.addEventListener('resize', function() {
        if (!navbarCollapse.classList.contains('show')) {
             // Jika menu tertutup saat resize, pastikan padding hanya tinggi navbar
             body.style.paddingTop = `${getComputedStyle(navbar).height}`;
        } else {
             // Jika menu terbuka saat resize, sesuaikan padding
             const navbarHeight = navbar.offsetHeight;
             const menuHeight = navbarCollapse.scrollHeight;
             body.style.paddingTop = `${navbarHeight + menuHeight}px`;
        }
    });
});


