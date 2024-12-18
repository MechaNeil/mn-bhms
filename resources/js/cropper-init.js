document.addEventListener('DOMContentLoaded', () => {
    // Apply cropper functionality when Livewire DOM updates
    Livewire.hook('element.updated', (el) => {
        initializeCropper(el);
    });

    // Initial setup
    initializeCropper(document);
});

function initializeCropper(context) {
    const fileInputs = context.querySelectorAll('[crop-after-change]');

    fileInputs.forEach((input) => {
        const previewImage = input.closest('x-file').querySelector('img');

        input.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (!file || !file.type.startsWith('image/')) return;

            const reader = new FileReader();
            reader.onload = () => {
                previewImage.src = reader.result;

                // Destroy existing Cropper instance if it exists
                if (previewImage.cropper) {
                    previewImage.cropper.destroy();
                }

                // Initialize Cropper.js
                previewImage.cropper = new Cropper(previewImage, {
                    aspectRatio: 1,
                    viewMode: 1,
                    autoCropArea: 0.8,
                    crop() {
                        const croppedData = previewImage.cropper.getCroppedCanvas().toDataURL();
                        const hiddenInput = input.closest('x-file').querySelector('input[type="hidden"]');
                        if (hiddenInput) hiddenInput.value = croppedData;
                    },
                });
            };
            reader.readAsDataURL(file);
        });
    });
}
