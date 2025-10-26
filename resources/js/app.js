// import './bootstrap';
// import './bundle';

import './bootstrap';
import './bundle';

import Alpine from 'alpinejs';
import alertComponent from './components/alerts';

// Register the component BEFORE starting Alpine
Alpine.data('alertComponent', alertComponent);




import toastr from './libs/toastr';

// Optional: Add to window if you need global access
window.toastr = toastr;



// Then initialize your store and start Alpine
Alpine.store('alert', {
    show: false,
    type: 'success',
    message: '',
    flash(message, type = 'success') {
        this.message = message;
        this.type = type;
        this.show = true;
        setTimeout(() => this.show = false, 3000);
    }
});

window.Alpine = Alpine;
Alpine.start();