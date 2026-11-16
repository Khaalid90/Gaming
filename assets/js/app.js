document.addEventListener('DOMContentLoaded', () => {
  // Small client-side enhancement: confirm before submitting a game upload.
  const uploadForm = document.querySelector('form[enctype="multipart/form-data"]');
  if (uploadForm) {
    uploadForm.addEventListener('submit', (event) => {
      const fileInput = uploadForm.querySelector('input[type="file"]');
      if (!fileInput || !fileInput.files.length) {
        event.preventDefault();
        alert('Please choose a game file first.');
      }
    });
  }
});
