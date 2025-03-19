class Form
{
  constructor(selector) {
    this.component = $(selector);
  }

  /**
   * Errors -> {[property]: [message]}
   * @param errors
   */
  handleFormErrors(errors = {})
  {
    Object.keys(errors).forEach((key) => {
      const errorComponent = this.component.find(`[name=${key}]`);

      console.log('Error Key: ', key, errorComponent);
      if (errorComponent?.length && !errorComponent?.hasClass('is-invalid')) {
        errorComponent.addClass('is-invalid');
      }

      const errorMessageComponent = this.component.find(`#error-${key}`);
      if (errorMessageComponent?.length) {
        errorMessageComponent.html(errors[key]);
      }
    })
  }

  clearFormErrors()
  {
    this.component.find('.error-message').each(function () {
      $(this).html('')
    })
  }

  getFormData()
  {
    return Object.fromEntries(this.component.serializeArray().map(el => [el.name, el.value]));
  }
}

export default Form;