document.addEventListener('DOMContentLoaded', function () {
    // Animasi staggered untuk card
    const cards = document.querySelectorAll('.work-card');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                // Delay hanya di desktop untuk efek bertahap
                const delay = window.innerWidth >= 768 ? index * 100 : 0;
                setTimeout(() => {
                    entry.target.classList.add('animate');
                }, delay);
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px' // mulai animasi sebelum card fully visible
    });

    cards.forEach(card => {
        // Reset class animate jika kembali (misal: SPA-like navigation)
        card.classList.remove('animate');
        observer.observe(card);
    });

    // Opsional: tambahkan efek halus saat scroll (untuk floating button)
    let lastScrollTop = 0;
    const uploadBtn = document.querySelector('.fixed-upload-btn');
    if (uploadBtn) {
        window.addEventListener('scroll', () => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            if (scrollTop > lastScrollTop && scrollTop > 200) {
                uploadBtn.style.transform = 'scale(0.9) rotate(0deg)';
                uploadBtn.style.opacity = '0.8';
            } else {
                uploadBtn.style.transform = '';
                uploadBtn.style.opacity = '1';
            }
            lastScrollTop = scrollTop;
        }, { passive: true });
    }
});