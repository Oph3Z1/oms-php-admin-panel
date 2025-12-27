document.addEventListener('DOMContentLoaded', function() {
    function initDropdowns() {
        const dropdowns = document.querySelectorAll('.relative');

        dropdowns.forEach(dropdown => {
            const button = dropdown.querySelector('button');
            const menu = dropdown.querySelector('[class*="absolute"]');

            if (button && menu) {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    closeAllDropdowns(menu);
                    menu.classList.toggle('hidden');
                });
            }
        });

        document.addEventListener('click', function() {
            closeAllDropdowns();
        });

        document.querySelectorAll('[class*="absolute"]').forEach(menu => {
            menu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
    }

    function closeAllDropdowns(except = null) {
        document.querySelectorAll('[class*="absolute"][class*="bg-gray-700"]').forEach(menu => {
            if (menu !== except) {
                menu.classList.add('hidden');
            }
        });
    }


    // MOBILE NAVIGATION
    function initMobileNav() {
        const mobileMenuToggle = document.querySelector('[data-mobile-menu-toggle]');
        const mobileMenuClose = document.querySelector('[data-mobile-menu-close]');
        const mobileSidebar = document.querySelector('[data-mobile-sidebar]');
        const mobileOverlay = document.querySelector('[data-mobile-sidebar-overlay]');

        if (mobileMenuToggle && mobileSidebar && mobileOverlay) {
            mobileMenuToggle.addEventListener('click', function() {
                mobileSidebar.classList.remove('hidden');
                mobileOverlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });

            function closeMobileMenu() {
                mobileSidebar.classList.add('hidden');
                mobileOverlay.classList.add('hidden');
                document.body.style.overflow = '';
            }

            if (mobileMenuClose) {
                mobileMenuClose.addEventListener('click', closeMobileMenu);
            }

            mobileOverlay.addEventListener('click', closeMobileMenu);

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !mobileSidebar.classList.contains('hidden')) {
                    closeMobileMenu();
                }
            });
        }
    }


    // MODALS
    function initModals() {
        document.querySelectorAll('[data-modal-target]').forEach(trigger => {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                const modalId = this.getAttribute('data-modal-target');
                const modal = document.getElementById(modalId);

                if (modal) {
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
            });
        });

        document.querySelectorAll('[data-modal-close]').forEach(closeBtn => {
            closeBtn.addEventListener('click', function() {
                const modal = this.closest('[id*="modal"]');
                if (modal) {
                    modal.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            });
        });

        document.querySelectorAll('[id*="modal"]').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            });
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('[id*="modal"]').forEach(modal => {
                    if (!modal.classList.contains('hidden')) {
                        modal.classList.add('hidden');
                        document.body.style.overflow = '';
                    }
                });
            }
        });
    }

    // ALERT BANNERS
    function initAlerts() {
        document.querySelectorAll('[class*="border-emerald-800"] button, [class*="border-red-800"] button, [class*="border-amber-800"] button').forEach(closeBtn => {
            closeBtn.addEventListener('click', function() {
                const alert = this.closest('[class*="border-emerald-800"], [class*="border-red-800"], [class*="border-amber-800"]');
                if (alert) {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    alert.style.transition = 'all 0.3s ease-in-out';

                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                }
            });
        });
    }

    // TABLE ROW ACTIONS
    function initTableActions() {
        document.querySelectorAll('[data-actions-menu]').forEach(trigger => {
            trigger.addEventListener('click', function(e) {
                e.stopPropagation();
                const menu = this.nextElementSibling;

                if (menu) {
                    document.querySelectorAll('[data-actions-dropdown]').forEach(dropdown => {
                        if (dropdown !== menu) {
                            dropdown.classList.add('hidden');
                        }
                    });

                    menu.classList.toggle('hidden');
                }
            });
        });
         
        document.addEventListener('click', function() {
            document.querySelectorAll('[data-actions-dropdown]').forEach(dropdown => {
                dropdown.classList.add('hidden');
            });
        });
    }

    initDropdowns();
    initMobileNav();
    initModals();
    initAlerts();
    initTableActions();

    console.log('OMS Admin Panel initialized successfully'); // remove this later, its just test
});
