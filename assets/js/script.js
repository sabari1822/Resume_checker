document.addEventListener('DOMContentLoaded', () => {
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('resume_file');
    const selectedFileDiv = document.getElementById('selected-file');
    const fileNameDisplay = document.getElementById('file-name-display');
    const removeFileBtn = document.getElementById('remove-file');
    const form = document.getElementById('upload-form');
    const analyzeBtn = document.getElementById('analyze-btn');

    if (!dropzone) return; // For result page where this might not exist

    // Drag and drop functionality
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => {
            dropzone.classList.add('dragover');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => {
            dropzone.classList.remove('dragover');
        }, false);
    });

    dropzone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length) {
            fileInput.files = files;
            updateFileDisplay();
        }
    }, false);

    fileInput.addEventListener('change', updateFileDisplay);

    function updateFileDisplay() {
        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            
            // Basic validation
            const validTypes = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            const validExtensions = ['.pdf', '.docx'];
            
            const fileExtension = file.name.substring(file.name.lastIndexOf('.')).toLowerCase();
            
            if (!validTypes.includes(file.type) && !validExtensions.includes(fileExtension)) {
                alert('Please upload a PDF or DOCX file.');
                fileInput.value = '';
                return;
            }

            dropzone.style.display = 'none';
            selectedFileDiv.style.display = 'flex';
            fileNameDisplay.textContent = file.name;
        } else {
            dropzone.style.display = 'block';
            selectedFileDiv.style.display = 'none';
            fileNameDisplay.textContent = '';
        }
    }

    removeFileBtn.addEventListener('click', () => {
        fileInput.value = '';
        updateFileDisplay();
    });

    form.addEventListener('submit', (e) => {
        if (fileInput.files.length === 0) {
            e.preventDefault();
            alert('Please select a resume file to upload.');
            return;
        }
        
        // Show loading state
        analyzeBtn.innerHTML = '<span>Analyzing...</span>';
        analyzeBtn.style.opacity = '0.8';
        analyzeBtn.disabled = true;
        // Proceed with standard form submission
        form.submit();
    });
});
