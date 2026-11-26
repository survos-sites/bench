import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    switch(event) {
        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        event.preventDefault();
        const url = new URL(window.location.href);
        url.searchParams.set('backend', event.currentTarget.dataset.backend);
        url.searchParams.set('page', '1');
        window.location.assign(url.toString());
    }
}
