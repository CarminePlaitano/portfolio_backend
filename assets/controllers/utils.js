export function escapeHtml(s) {
    if (s === null || s === undefined) return '';
    return String(s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

export function debounce(fn, wait = 250) {
    let t;
    return (...args) => {
        clearTimeout(t);
        t = setTimeout(() => fn(...args), wait);
    };
}

export function renderTablePagination(page, perPage, total) {
    if (!this.hasPaginationTarget) return;
    const totalPages = Math.max(1, Math.ceil(total / perPage));
    let html = '';
    for (let p = 1; p <= totalPages; p++) {
        html += `<li class="page-item ${p === page ? 'active' : ''}"><a class="page-link" href="?page=${p}">${p}</a></li>`;
    }
    this.paginationTarget.innerHTML = html;
}
