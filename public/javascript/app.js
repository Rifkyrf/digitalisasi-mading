document.addEventListener('DOMContentLoaded', function () {
    // Cek apakah elemen pencarian ada & di mobile
    const searchTab = document.getElementById('search-tab');
    const searchModal = document.getElementById('searchModal');
    const closeSearch = document.getElementById('closeSearch');
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');

    if (!searchTab || window.innerWidth >= 768) return;

    // === Buka/Tutup Modal ===
    function openSearchModal(e) {
        e.preventDefault();
        searchModal.classList.add('active');
        document.body.style.overflow = 'hidden';
        setTimeout(() => searchInput.focus(), 100);
    }

    function closeSearchModal() {
        searchModal.classList.remove('active');
        document.body.style.overflow = '';
        searchInput.value = '';
        searchResults.innerHTML = '';
    }

    searchTab.addEventListener('click', openSearchModal);
    closeSearch?.addEventListener('click', closeSearchModal);
    searchModal?.addEventListener('click', function (e) {
        if (e.target === searchModal) closeSearchModal();
    });

    // === LIVE SEARCH ===
    let searchTimeout;

    searchInput?.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            searchResults.innerHTML = '';
            return;
        }

        // Tampilkan loading
        searchResults.innerHTML = '<p class="text-center py-3">Mencari...</p>';

        searchTimeout = setTimeout(() => {
            // 🔑 Gunakan window.Laravel.assetUrl untuk path storage
            const searchUrl = `/search/users?q=${encodeURIComponent(query)}`;

            fetch(searchUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Networks error');
                return response.json();
            })
            .then(users => {
                if (!Array.isArray(users) || users.length === 0) {
                    searchResults.innerHTML = '<p class="text-muted text-center py-3">Tidak ada pengguna ditemukan</p>';
                    return;
                }

                let html = '';
                users.forEach(user => {
                    // ✅ Gunakan window.Laravel.assetUrl jika tersedia
                    const baseUrl = window.Laravel?.assetUrl || '/';
                    const avatar = user.profile_photo
                        ? baseUrl + 'storage/' + user.profile_photo
                        : `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=0d47a1&color=fff&size=128`;

                    html += `
                        <a href="${baseUrl}profile/${user.id}" class="user-item">
                            <img src="${avatar}" alt="${escapeHtml(user.name)}" loading="lazy">
                            <span>${escapeHtml(user.name)}</span>
                        </a>
                    `;
                });
                searchResults.innerHTML = html;
            })
            .catch(err => {
                console.error('Pencarian gagal:', err);
                searchResults.innerHTML = '<p class="text-danger text-center py-3">Gagal memuat hasil</p>';
            });
        }, 400);
    });

    // === Helper: Escape HTML ===
    function escapeHtml(text) {
        if (typeof text !== 'string') return '';
        const map = {
            '&': '&amp;',
            '<': '<',
            '>': '>',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
});



// Animasi untuk layout
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar Toggle dengan animasi
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const mainContent = document.getElementById('mainContent');

    function toggleSidebar() {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('show');
        document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
    }

    menuToggle.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', toggleSidebar);

    // Animasi untuk menu items
    const menuItems = document.querySelectorAll('.menu-item');
    menuItems.forEach((item, index) => {
        item.style.animationDelay = `${index * 0.1}s`;
        item.classList.add('slide-in-left');
    });

    // Load saved theme
    const savedTheme = localStorage.getItem('theme') || 'blue';
    document.documentElement.setAttribute('data-theme', savedTheme);

    // Animasi untuk cards
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('fade-in-up');
    });

    // Hover effects untuk interactive elements
    const interactiveElements = document.querySelectorAll('.btn, .card, .nav-item');
    interactiveElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        element.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Parallax effect untuk background
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const parallax = document.querySelector('.content::before');
        if (parallax) {
            parallax.style.transform = `translateY(${scrolled * 0.5}px)`;
        }
    });
});

// Loading animation
window.addEventListener('load', function() {
    document.body.style.opacity = '0';
    document.body.style.transition = 'opacity 0.5s ease';

    setTimeout(() => {
        document.body.style.opacity = '1';
    }, 100);
});

// =============== DESKTOP & MOBILE LIVE SEARCH ===============
document.addEventListener('DOMContentLoaded', function () {

    // === DESKTOP SEARCH ===
    const desktopSearchInput = document.getElementById('desktopSearchInput');
    const desktopSearchResults = document.getElementById('desktopSearchResults');
    const desktopSearchSpinner = document.getElementById('desktopSearchSpinner');
    const desktopSearchButton = document.getElementById('desktopSearchButton');

    if (desktopSearchInput) {
        let debounceTimer;

        desktopSearchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            const query = this.value.trim();

            if (query.length < 2) {
                desktopSearchResults.style.display = 'none';
                desktopSearchButton.disabled = true;
                return;
            }

            desktopSearchButton.disabled = false;

            // Tampilkan loading
            desktopSearchSpinner.classList.remove('d-none');
            desktopSearchResults.innerHTML = '<div class="p-3 text-center">Mencari...</div>';
            desktopSearchResults.style.display = 'block';

            debounceTimer = setTimeout(() => {
                const searchUrl = `/search/all?q=${encodeURIComponent(query)}`;

                fetch(searchUrl, {
                    headers: { 'Accept': 'application/json' }
                })
                .then(response => response.json())
                .then(data => {
                    desktopSearchSpinner.classList.add('d-none');

                    let html = '';

                    // Render Hasil Pengguna
                    if (data.users && data.users.length > 0) {
                        html += `
                            <div class="px-3 py-2 bg-light border-bottom">
                                <strong>Pengguna</strong>
                            </div>`;
                        data.users.forEach(user => {
                            const baseUrl = window.Laravel?.assetUrl || '/';
                            const avatar = user.profile_photo
                                ? baseUrl + 'storage/' + user.profile_photo
                                : `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=0d47a1&color=fff&size=128`;

                            html += `
                                <a href="${baseUrl}profile/${user.id}" class="user-item d-flex align-items-center p-3 text-decoration-none text-dark border-bottom">
                                    <img src="${avatar}" alt="${escapeHtml(user.name)}" width="40" height="40" class="rounded-circle me-3">
                                    <div>
                                        <div class="fw-semibold">${escapeHtml(user.name)}</div>
                                        <div class="text-muted small">@${user.id}</div>
                                    </div>
                                </a>`;
                        });
                    }

                    // Render Hasil works
                    if (data.works && data.works.length > 0) {
                        html += `
                            <div class="px-3 py-2 bg-light border-bottom mt-2">
                                <strong>Konten / works</strong>
                            </div>`;
                        data.works.forEach(works => {
                            const baseUrl = window.Laravel?.assetUrl || '/';
                            const thumbnail = works.thumbnail_path
                                ? baseUrl + 'storage/' + works.thumbnail_path
                                : getThumbnailPlaceholder(works.file_type);

                            html += `
                                <a href="${baseUrl}works/${works.id}" class="user-item d-flex align-items-center p-3 text-decoration-none text-dark border-bottom">
                                    <img src="${thumbnail}" width="40" height="40" class="rounded me-3" style="object-fit: cover;">
                                    <div>
                                        <div class="fw-semibold">${escapeHtml(works.title)}</div>
                                        <div class="text-muted small">${escapeHtml(works.description.substring(0, 50))}...</div>
                                    </div>
                                </a>`;
                        });
                    }

                    // Jika tidak ada hasil sama sekali
                    if (!html) {
                        html = '<div class="p-3 text-muted text-center">Tidak ada hasil ditemukan</div>';
                    }

                    desktopSearchResults.innerHTML = html;
                })
                .catch(err => {
                    console.error('Pencarian gagal:', err);
                    desktopSearchSpinner.classList.add('d-none');
                    desktopSearchResults.innerHTML = '<div class="p-3 text-danger text-center">Gagal memuat hasil</div>';
                });
            }, 300);
        });

        // Sembunyikan dropdown saat klik di luar
        document.addEventListener('click', function (e) {
            if (
                !desktopSearchInput.contains(e.target) &&
                !desktopSearchResults.contains(e.target)
            ) {
                desktopSearchResults.style.display = 'none';
            }
        });
    }

    // === MOBILE SEARCH MODAL ===
    const searchTab = document.getElementById('search-tab');
    const searchModal = document.getElementById('searchModal');
    const closeSearch = document.getElementById('closeSearch');
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');

    if (searchTab && searchModal && closeSearch && searchInput && searchResults) {

        // Buka modal
        searchTab.addEventListener('click', function(e) {
            e.preventDefault();
            searchModal.classList.add('active');
            document.body.style.overflow = 'hidden';
            setTimeout(() => searchInput.focus(), 100);
        });

        // Tutup modal
        closeSearch.addEventListener('click', function() {
            searchModal.classList.remove('active');
            document.body.style.overflow = '';
            searchInput.value = '';
            searchResults.innerHTML = '';
        });

        // Tutup jika klik luar
        searchModal.addEventListener('click', function(e) {
            if (e.target === searchModal) {
                closeSearch.click();
            }
        });

        // Live search di mobile
        let mobileDebounce;

        searchInput.addEventListener('input', function() {
            clearTimeout(mobileDebounce);
            const query = this.value.trim();

            if (query.length < 2) {
                searchResults.innerHTML = '';
                return;
            }

            searchResults.innerHTML = '<div class="p-3 text-center">Mencari...</div>';

            mobileDebounce = setTimeout(() => {
                const searchUrl = `/search/all?q=${encodeURIComponent(query)}`;

                fetch(searchUrl, {
                    headers: { 'Accept': 'application/json' }
                })
                .then(response => response.json())
                .then(data => {
                    let html = '';

                    // Render Hasil Pengguna
                    if (data.users && data.users.length > 0) {
                        html += `
                            <div class="px-3 py-2 bg-light border-bottom">
                                <strong>Pengguna</strong>
                            </div>`;
                        data.users.forEach(user => {
                            const baseUrl = window.Laravel?.assetUrl || '/';
                            const avatar = user.profile_photo
                                ? baseUrl + 'storage/' + user.profile_photo
                                : `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=0d47a1&color=fff&size=128`;

                            html += `
                                <a href="${baseUrl}profile/${user.id}" class="user-item d-flex align-items-center p-3 text-decoration-none text-dark border-bottom">
                                    <img src="${avatar}" alt="${escapeHtml(user.name)}" width="40" height="40" class="rounded-circle me-3">
                                    <div>
                                        <div class="fw-semibold">${escapeHtml(user.name)}</div>
                                        <div class="text-muted small">@${user.id}</div>
                                    </div>
                                </a>`;
                        });
                    }

                    // Render Hasil works
                    if (data.works && data.works.length > 0) {
                        html += `
                            <div class="px-3 py-2 bg-light border-bottom mt-2">
                                <strong>Konten / works</strong>
                            </div>`;
                        data.works.forEach(works => {
                            const baseUrl = window.Laravel?.assetUrl || '/';
                            const thumbnail = works.thumbnail_path
                                ? baseUrl + 'storage/' + works.thumbnail_path
                                : getThumbnailPlaceholder(works.file_type);

                            html += `
                                <a href="${baseUrl}works/${works.id}" class="user-item d-flex align-items-center p-3 text-decoration-none text-dark border-bottom">
                                    <img src="${thumbnail}" width="40" height="40" class="rounded me-3" style="object-fit: cover;">
                                    <div>
                                        <div class="fw-semibold">${escapeHtml(works.title)}</div>
                                        <div class="text-muted small">${escapeHtml(works.description.substring(0, 50))}...</div>
                                    </div>
                                </a>`;
                        });
                    }

                    // Jika tidak ada hasil
                    if (!html) {
                        html = '<div class="p-3 text-muted text-center">Tidak ada hasil ditemukan</div>';
                    }

                    searchResults.innerHTML = html;
                })
                .catch(err => {
                    console.error('Pencarian gagal:', err);
                    searchResults.innerHTML = '<div class="p-3 text-danger text-center">Gagal memuat hasil</div>';
                });
            }, 400);
        });
    }

    // Helper: Escape HTML
    function escapeHtml(text) {
        if (typeof text !== 'string') return '';
        const map = {
            '&': '&amp;',
            '<': '<',
            '>': '>',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    // Helper: Ambil placeholder thumbnail berdasarkan file_type
    function getThumbnailPlaceholder(fileType) {
        const ext = fileType.toLowerCase();
        const baseUrl = window.Laravel?.assetUrl || '/';

        const map = {
            'jpg': 'thumbnails/image.png',
            'jpeg': 'thumbnails/image.png',
            'png': 'thumbnails/image.png',
            'gif': 'thumbnails/image.png',
            'pdf': 'thumbnails/pdf.png',
            'doc': 'thumbnails/doc.png',
            'docx': 'thumbnails/doc.png',
            'zip': 'thumbnails/archive.png',
            'rar': 'thumbnails/archive.png',
            'mp4': 'thumbnails/video.png',
            'py': 'thumbnails/code.png',
            'js': 'thumbnails/code.png',
            'html': 'thumbnails/code.png',
            'txt': 'thumbnails/code.png',
        };

        const imagePath = map[ext] || 'thumbnails/file.png';
        return baseUrl + 'storage/' + imagePath;
    }
});

// =============== MOBILE LIVE SEARCH ===============
document.addEventListener('DOMContentLoaded', function () {
    const searchTab = document.getElementById('search-tab');
    const searchModal = document.getElementById('searchModal');
    const closeSearch = document.getElementById('closeSearch');
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');

    if (!searchTab || !searchModal || !closeSearch || !searchInput || !searchResults) return;

    // Buka modal
    searchTab.addEventListener('click', function(e) {
        e.preventDefault();
        searchModal.classList.add('active');
        document.body.style.overflow = 'hidden';
        setTimeout(() => searchInput.focus(), 100);
    });

    // Tutup modal
    closeSearch.addEventListener('click', function() {
        searchModal.classList.remove('active');
        document.body.style.overflow = '';
        searchInput.value = '';
        searchResults.innerHTML = '';
    });

    // Tutup jika klik luar
    searchModal.addEventListener('click', function(e) {
        if (e.target === searchModal) {
            closeSearch.click();
        }
    });

    // Live search di mobile
    let mobileDebounce;

    searchInput.addEventListener('input', function() {
        clearTimeout(mobileDebounce);
        const query = this.value.trim();

        if (query.length < 2) {
            searchResults.innerHTML = '';
            return;
        }

        searchResults.innerHTML = '<div class="p-3 text-center">Mencari...</div>';

        mobileDebounce = setTimeout(() => {
            const searchUrl = `/search/all?q=${encodeURIComponent(query)}`;

            fetch(searchUrl, {
                headers: { 'Accept': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                let html = '';

                // Render Hasil Pengguna
                if (data.users && data.users.length > 0) {
                    html += `
                        <div class="px-3 py-2 bg-light border-bottom">
                            <strong>Pengguna</strong>
                        </div>`;
                    data.users.forEach(user => {
                        const baseUrl = window.Laravel?.assetUrl || '/';
                        const avatar = user.profile_photo
                            ? baseUrl + 'storage/' + user.profile_photo
                            : `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=0d47a1&color=fff&size=128`;

                        html += `
                            <a href="${baseUrl}profile/${user.id}"
                               class="d-flex align-items-center p-3 text-decoration-none text-dark border-bottom"
                               style="text-decoration: none;">
                                <img src="${avatar}" width="40" height="40" class="rounded-circle me-3" style="object-fit: cover;">
                                <div>
                                    <div class="fw-semibold">${escapeHtml(user.name)}</div>
                                    <div class="text-muted small">@${user.id}</div>
                                </div>
                            </a>`;
                    });
                }

                // Render Hasil works
                if (data.works && data.works.length > 0) {
                    html += `
                        <div class="px-3 py-2 bg-light border-bottom mt-2">
                            <strong>Konten / works</strong>
                        </div>`;
                    data.works.forEach(works => {
                        const baseUrl = window.Laravel?.assetUrl || '/';
                        const thumbnail = works.thumbnail_path
                            ? baseUrl + 'storage/' + works.thumbnail_path
                            : getThumbnailPlaceholder(works.file_type);

                        html += `
                            <a href="${baseUrl}works/${works.id}"
                               class="d-flex align-items-center p-3 text-decoration-none text-dark border-bottom"
                               style="text-decoration: none;">
                                <img src="${thumbnail}" width="40" height="40" class="rounded me-3" style="object-fit: cover;">
                                <div>
                                    <div class="fw-semibold">${escapeHtml(works.title)}</div>
                                    <div class="text-muted small">${escapeHtml(works.description.substring(0, 50))}...</div>
                                </div>
                            </a>`;
                    });
                }

                // Jika tidak ada hasil
                if (!html) {
                    html = '<div class="p-3 text-muted text-center">Tidak ada hasil ditemukan</div>';
                }

                searchResults.innerHTML = html;
            })
            .catch(err => {
                console.error('Pencarian gagal:', err);
                searchResults.innerHTML = '<div class="p-3 text-danger text-center">Gagal memuat hasil</div>';
            });
        }, 400);
    });
});

// Helper: Escape HTML
function escapeHtml(text) {
    if (typeof text !== 'string') return '';
    const map = {
        '&': '&amp;',
        '<': '<',
        '>': '>',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

// Helper: Ambil placeholder thumbnail berdasarkan file_type
function getThumbnailPlaceholder(fileType) {
    const ext = fileType.toLowerCase();
    const baseUrl = window.Laravel?.assetUrl || '/';

    const map = {
        'jpg': 'thumbnails/image.png',
        'jpeg': 'thumbnails/image.png',
        'png': 'thumbnails/image.png',
        'gif': 'thumbnails/image.png',
        'pdf': 'thumbnails/pdf.png',
        'doc': 'thumbnails/doc.png',
        'docx': 'thumbnails/doc.png',
        'zip': 'thumbnails/archive.png',
        'rar': 'thumbnails/archive.png',
        'mp4': 'thumbnails/video.png',
        'py': 'thumbnails/code.png',
        'js': 'thumbnails/code.png',
        'html': 'thumbnails/code.png',
        'txt': 'thumbnails/code.png',
    };

    const imagePath = map[ext] || 'thumbnails/file.png';
    return baseUrl + 'storage/' + imagePath;
}

// Pastikan escapeHtml tetap tersedia (jika belum ada di scope global)
function escapeHtml(text) {
    if (typeof text !== 'string') return '';
    const map = {
        '&': '&amp;',
        '<': '<',
        '>': '>',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}



//============SIDEBAR MOBILE===============
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const menuToggle = document.getElementById('menuToggle');

    // Optional: jika elemen tidak ada, jangan error fatal
    if (menuToggle && sidebar && overlay) {
        menuToggle.addEventListener('click', () => {
            sidebar.classList.add('open');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
            document.body.style.overflow = 'auto';
        });

        document.querySelectorAll('.sidebar a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    setTimeout(() => {
                        sidebar.classList.remove('open');
                        overlay.classList.remove('show');
                        document.body.style.overflow = 'auto';
                    }, 300);
                }
            });
        });
    }

    // Theme switcher
    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        console.log(`Theme changed to: ${theme}`);
    }

    // Ambil tema dari localStorage, atau gunakan 'blue' sebagai default
    const savedTheme = localStorage.getItem('theme') || 'blue';
    applyTheme(savedTheme);

    // Pasang event listener ke setiap opsi tema
    document.querySelectorAll('.theme-option').forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            const theme = this.getAttribute('data-theme');
            applyTheme(theme);
        });
    });
});

// =============== DROPDOWN BERSARANG ===============
document.addEventListener('DOMContentLoaded', function () {
    // Toggle submenu dropdown
    document.querySelectorAll('.dropdown-submenu a.dropdown-toggle').forEach(function (element) {
        element.addEventListener('click', function (e) {
            e.preventDefault();
            const submenu = this.nextElementSibling;
            if (submenu) {
                submenu.classList.toggle('show');
            }
        });
    });

    // Close all submenus when clicking outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.dropdown-submenu')) {
            document.querySelectorAll('.dropdown-submenu .dropdown-menu').forEach(function (menu) {
                menu.classList.remove('show');
            });
        }
    });

    // Optional: Close submenu when opening another one in the same dropdown
    document.querySelectorAll('.dropdown-menu .dropdown-toggle').forEach(function (toggle) {
        toggle.addEventListener('click', function () {
            const parentMenu = this.closest('.dropdown-menu');
            parentMenu.querySelectorAll('.dropdown-menu').forEach(function (menu) {
                if (menu !== this.nextElementSibling) {
                    menu.classList.remove('show');
                }
            }.bind(this));
        });
    });
});



        // =============== NOTIFIKASI LOGIKA ===============
        function markAsRead(notificationId, element) {
            fetch(`/notifications/${notificationId}/read`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (response.ok) {
                    // Hapus badge 'Baru'
                    element.querySelector('.badge')?.remove();
                    // Tandai item sebagai sudah dibaca (hapus kelas 'unread' jika ada)
                    element.closest('.notification-item')?.classList.remove('unread');
                    // Kurangi jumlah notifikasi di ikon
                    updateNotificationBadge();
                }
            })
            .catch(error => console.error('Error marking notification as read:', error));
        }

        function updateNotificationBadge() {
            fetch('/notifications/unread-count', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                const badge = document.querySelector('#notificationDropdown .badge');
                if (data.count > 0) {
                    if (badge) {
                        badge.textContent = data.count;
                    } else {
                        const button = document.querySelector('#notificationDropdown');
                        button.insertAdjacentHTML('beforeend', '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">' + data.count + '<span class="visually-hidden">unread messages</span></span>');
                    }
                } else {
                    badge?.remove();
                }
            })
            .catch(error => console.error('Error fetching unread count:', error));
        }

        // Opsional: Perbarui badge saat dropdown dibuka
        document.getElementById('notificationDropdown')?.addEventListener('show.bs.dropdown', function () {
            // Panggil updateNotificationBadge() jika ingin memastikan data terbaru saat dibuka
            // updateNotificationBadge(); // Hanya jika kamu ingin selalu fetch ulang
        });