document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('menu-toggle');
    const nav = document.getElementById('main-menu');
    const layer = document.getElementById('menu-layer');

    toggle.addEventListener('click', () => {
        nav.classList.toggle('open');
        layer.classList.toggle('visible');
        toggle.classList.toggle('active');
    });

    layer.addEventListener('click', () => {
        nav.classList.remove('open');
        layer.classList.remove('visible');
        toggle.classList.remove('active');
    });

    const submenuLinks = document.querySelectorAll('.has-submenu > a');
    submenuLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                link.parentElement.classList.toggle('open');
            }
        });
    });
});
