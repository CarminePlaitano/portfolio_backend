import { Controller } from '@hotwired/stimulus';

export default class SidebarController extends Controller {
  static targets = ['links'];

  connect() {
    this._anchors = [];
    this._paths = [];

    this.populatePathPages();
    this.updateActive();
  }

  // Build arrays of anchors and their normalized paths
  populatePathPages() {
    const listElements = Array.from(this.linksTarget.children);

    listElements.forEach((li) => {
      const a = li.querySelector('a');
      if (!a) return;

      // Store anchor element
      this._anchors.push(a);

      // Build a normalized path relative to current origin
      const href = a.href;
      const origin = window.location.origin;
      let path;
      try {
        // Use URL to get pathname reliably (handles absolute and relative href)
        const url = new URL(href, origin);
        path = url.pathname;
      } catch (e) {
        // fallback: use attribute
        path = a.getAttribute('href') || '/';
      }

      path = this._normalizePath(path);
      this._paths.push(path);
    });
  }

  // Main public method to compute and set the active link
  updateActive() {
    const currentPath = this._normalizePath(window.location.pathname);

    // 1) Try exact match
    let bestIndex = this._paths.findIndex(p => p === currentPath);

    // 2) If no exact match, try longest-prefix match (ignoring root '/')
    if (bestIndex === -1) {
      let bestLen = 0;
      this._paths.forEach((p, idx) => {
        if (p === '/') return; // skip root as prefix candidate
        if (currentPath.startsWith(p) && p.length > bestLen) {
          bestLen = p.length;
          bestIndex = idx;
        }
      });
    }

    // 3) fallback: if nothing matched, prefer root ('/') if present, else first anchor
    if (bestIndex === -1) {
      const rootIndex = this._paths.findIndex(p => p === '/');
      bestIndex = (rootIndex !== -1) ? rootIndex : 0;
    }

    // Apply active class to matched anchor + parent li; remove from others
    this._setActiveByIndex(bestIndex);
  }

  // Remove old active classes and set the new one
  _setActiveByIndex(index) {
    if (!this._anchors || !this._anchors.length) return;

    this._anchors.forEach((a, i) => {
      // anchor classes
      a.classList.remove('active');
      a.removeAttribute('aria-current');

      // parent li classes
      const li = a.closest('li');
      if (li) li.classList.remove('active');
    });

    const picked = this._anchors[index];
    if (!picked) return;

    picked.classList.add('active');
    picked.setAttribute('aria-current', 'page');

    const parentLi = picked.closest('li');
    if (parentLi) parentLi.classList.add('active');
  }

  // Normalize path: decode, remove trailing slash (except root), lowercase
  _normalizePath(path) {
    try {
      let p = decodeURIComponent(path || '/');
      if (p.length > 1 && p.endsWith('/')) p = p.slice(0, -1);
      return p.toLowerCase();
    } catch (e) {
      // fallback simple normalization
      let p = (path || '/');
      if (p.length > 1 && p.endsWith('/')) p = p.slice(0, -1);
      return p.toLowerCase();
    }
  }
}
