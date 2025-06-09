document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');

    if (menuToggle && sidebar && mainContent) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            mainContent.classList.toggle('shifted');
        });
    }

    // Settings dropdown toggle
    const settingsDropdown = document.getElementById('settingsDropdown');
    const cogIcon = document.getElementById('cogIcon');

    if (settingsDropdown && cogIcon) {
        cogIcon.addEventListener('click', function(event) {
            settingsDropdown.classList.toggle('active');
            event.stopPropagation();
        });

        document.addEventListener('click', function(event) {
            if (!settingsDropdown.contains(event.target) && event.target !== cogIcon) {
                settingsDropdown.classList.remove('active');
            }
        });
    }

    // Toggle balance visibility
    const toggleBalance = document.getElementById('toggleBalance');
    const actualBalance = document.getElementById('actualBalance');
    const maskedBalance = document.getElementById('maskedBalance');

    if (toggleBalance && actualBalance && maskedBalance) {
        if (actualBalance.classList.contains('hidden')) {
            toggleBalance.classList.remove('fa-eye-slash');
            toggleBalance.classList.add('fa-eye');
        } else {
            toggleBalance.classList.remove('fa-eye');
            toggleBalance.classList.add('fa-eye-slash');
        }

        toggleBalance.addEventListener('click', function() {
            const isCurrentlyHidden = actualBalance.classList.contains('hidden');

            if (isCurrentlyHidden) {
                actualBalance.classList.remove('hidden');
                actualBalance.classList.add('visible-inline');
                maskedBalance.classList.remove('visible-inline');
                maskedBalance.classList.add('hidden');
                toggleBalance.classList.remove('fa-eye');
                toggleBalance.classList.add('fa-eye-slash');
            } else {
                actualBalance.classList.remove('visible-inline');
                actualBalance.classList.add('hidden');
                maskedBalance.classList.remove('hidden');
                maskedBalance.classList.add('visible-inline');
                toggleBalance.classList.remove('fa-eye-slash');
                toggleBalance.classList.add('fa-eye');
            }
        });
    }

    // Show birthday reminder popup
    const birthdayPopup = document.getElementById('birthdayPopup');
    const closeBirthdayPopup = document.getElementById('closeBirthdayPopup');
    const goToSettingsBtn = document.getElementById('goToSettings');
    const overlay = document.getElementById('overlay');

    if (birthdayPopup && overlay) {
        birthdayPopup.style.display = 'block';
        overlay.style.display = 'block';

        if (closeBirthdayPopup) {
            closeBirthdayPopup.addEventListener('click', function() {
                birthdayPopup.style.display = 'none';
                overlay.style.display = 'none';
            });
        }

        if (goToSettingsBtn) {
            goToSettingsBtn.addEventListener('click', function() {
                window.location.href = 'settings.php';
            });
        }
    }

    // Deactivate account modal control
    const deactivateAccountBtn = document.getElementById('deactivateAccountBtn');
    const deactivateModal = document.getElementById('deactivateModal');
    const cancelDeactivateBtn = document.getElementById('cancelDeactivateBtn');

    if (deactivateAccountBtn && deactivateModal && cancelDeactivateBtn) {
        deactivateAccountBtn.addEventListener('click', function() {
            deactivateModal.classList.add('active');
        });

        cancelDeactivateBtn.addEventListener('click', function() {
            deactivateModal.classList.remove('active');
            const form = deactivateModal.querySelector('form');
            form.reset();
        });

        deactivateModal.addEventListener('click', function(event) {
            if (event.target === deactivateModal) {
                deactivateModal.classList.remove('active');
                const form = deactivateModal.querySelector('form');
                form.reset();
            }
        });
    }
});
