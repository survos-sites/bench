import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['button', 'panel'];

    toggle() {
        const open = this.panelTarget.classList.toggle('is-open');
        this.buttonTarget.setAttribute('aria-expanded', String(open));
    }
}
