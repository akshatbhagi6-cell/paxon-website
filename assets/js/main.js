const form = document.getElementById('inquiryForm');

if (form) {
  form.addEventListener('submit', function (e) {
    let valid = true;

    form.querySelectorAll('[required]').forEach(function (field) {
      if (field.value.trim() === '') {
        field.classList.add('input-error');
        valid = false;
      } else {
        field.classList.remove('input-error');
      }
    });

    const email = document.getElementById('email');
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email.value.trim())) {
      email.classList.add('input-error');
      valid = false;
    }

    document.getElementById('formError').hidden = valid;

    if (!valid) {
      e.preventDefault();
    }
  });
}