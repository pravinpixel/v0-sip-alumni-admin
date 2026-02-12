function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = 'toast-notification';
    toast.style.background = type === 'success' ? '#10b981' : '#ef4444';
    toast.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} text-white"></i>
            <span>${message}</span>
        `;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideOutDown 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

function initCustomSelectArrows() {
    document.querySelectorAll('.custom-select select').forEach(select => {
        const wrapper = select.parentElement;

        select.addEventListener('mousedown', () => {
            wrapper.classList.toggle('open');
        });

        select.addEventListener('blur', () => {
            wrapper.classList.remove('open');
        });

        select.addEventListener('change', () => {
            wrapper.classList.remove('open');
        });
    });
}

