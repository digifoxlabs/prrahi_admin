export default {
    success(message, title = 'Success') {
        toastr.success(message, title);
    },
    error(message, title = 'Error') {
        toastr.error(message, title);
    },
    warning(message, title = 'Warning') {
        toastr.warning(message, title);
    },
    info(message, title = 'Info') {
        toastr.info(message, title);
    }
};