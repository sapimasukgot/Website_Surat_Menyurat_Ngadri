import './bootstrap';
import 'bootstrap';
import TomSelect from 'tom-select';

window.TomSelect = TomSelect;

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.tom-select').forEach((el) => {
        new TomSelect(el, {
            create: false,
            allowEmptyOption: true,
            placeholder: el.dataset.placeholder || 'Cari...',
        });
    });
});
