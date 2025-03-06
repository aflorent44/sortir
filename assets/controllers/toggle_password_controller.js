import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["password", "toggleIcon"];

    toggle() {
        const type = this.passwordTarget.type === "password" ? "text" : "password";
        this.passwordTarget.type = type;

        // Changer l'icône du bouton (optionnel)
        this.toggleIconTarget.classList.toggle('hidden');
        this.toggleIconTarget.nextElementSibling.classList.toggle('hidden');
    }
}
