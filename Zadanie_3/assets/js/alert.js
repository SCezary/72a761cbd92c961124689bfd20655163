const Alert  = {
  alertTimeout: 5000,

  showInfo: (message) => {
    Alert.showAlert(message, 'info')
  },
  showWarning: (message) => {
    Alert.showAlert(message, 'warning')
  },
  showError: (message) => {
    Alert.showAlert(message, 'danger')
  },
  showSuccess: (message) => {
    Alert.showAlert(message, 'success')
  },
  showAlert: (message, type) => {
    const alertComponent = $('#custom-alert');
    if (!alertComponent?.length) return;

    alertComponent.attr('class', `alert alert-${type}`);
    alertComponent.html(message);
    alertComponent.removeClass('hidden');

    setTimeout(() => {
      alertComponent.addClass('hidden');
    }, Alert.alertTimeout);
  }
};

export default Alert;