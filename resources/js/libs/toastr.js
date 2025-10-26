import toastr from 'toastr';
import 'toastr/build/toastr.min.css';

// Configure Toastr
toastr.options = {
  closeButton: true,
  debug: false,
  newestOnTop: true,
  progressBar: true,
  positionClass: 'toast-bottom-right',
  preventDuplicates: false,
  showDuration: '300',
  hideDuration: '1000',
  timeOut: '5000',
  extendedTimeOut: '1000',
  showEasing: 'swing',
  hideEasing: 'linear',
  showMethod: 'fadeIn',
  hideMethod: 'fadeOut',
  // Custom classes to match Tailwind
  toastClass: 'bg-white border rounded shadow-lg overflow-hidden',
  containerId: 'toast-container',
};

// Make it available globally
window.toastr = toastr;

export default toastr;