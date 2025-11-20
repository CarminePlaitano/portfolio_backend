import { Controller } from "@hotwired/stimulus";
import { escapeHtml, debounce, renderTablePagination } from "./utils.js";

export default class extends Controller {
    static targets = ["search", "contactBy", "tbody", "pagination"];
    static values = { url: String }

    connect() {
        this.onInput = debounce(this.fetch.bind(this), 300);

        if (this.hasSearchTarget) this.searchTarget.addEventListener('input', this.onInput);
        if (this.hasContactByTarget) this.contactByTarget.addEventListener('change', () => this.fetch());
    }

    async fetch(opts = {}) {
        const params = new URLSearchParams();

        if (this.hasSearchTarget) params.set('search', this.searchTarget.value || '');
        if (this.hasContactByTarget) params.set('contact_by', this.contactByTarget.value || '');
        if (opts.page) params.set('page', opts.page);

        const perPage = opts.perPage || 15;

        params.set('perPage', perPage);

        const url = `${this.urlValue}?${params.toString()}`;

        try {
            const res = await fetch(url, { credentials: 'same-origin' });

            if (!res.ok) throw new Error('Network error');

            const json = await res.json();

            this.renderTable(json.data);
            renderTablePagination(json.page, json.perPage, json.total);

            const newUrl = new URL(window.location.pathname, window.location.origin);

            json.page && newUrl.searchParams.set('page', json.page);
            this.searchTarget && newUrl.searchParams.set('search', this.searchTarget.value || '');
            this.contactByTarget && newUrl.searchParams.set('contact_by', this.contactByTarget.value || '');

            window.history.replaceState({}, '', newUrl.toString());
        } catch (err) {
            console.error(err);
        }
    }

    renderTable(rows) {
        if (!this.hasTbodyTarget) return;

        if (!rows.length) {
            this.tbodyTarget.innerHTML = `<tr><td colspan="5" class="text-center">No contacts found.</td></tr>`;
            return;
        }

        this.tbodyTarget.innerHTML = rows.map(r => `
      <tr>
        <td>${escapeHtml(r.type)}</td>
        <td>${escapeHtml(r.value)}</td>
        <td>${escapeHtml(r.contactBy)}</td>
        <td>${escapeHtml(r.label ?? '')}</td>
        <td class="text-end">
          <a class="btn btn-sm btn-primary" href="/contacts/${r.id}/edit">Edit</a>
          <a class="btn btn-sm btn-danger" href="/contacts/${r.id}/delete">Delete</a>
        </td>
      </tr>
    `).join('');
    }
}
