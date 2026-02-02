<div id="createPostModal" class="modal-overlay" data-alumni-id="{{ $alumni->id ?? '' }}">
    <div class="create-post-popup">
        <div class="modal-body">
            <div class="">
                <h2>Create New Post</h2>
                <button class="modal-close-btn" onclick="closeCreatePostModal()">&times;</button>
            </div>
            <form id="createPostForm">
                <!-- Title Field -->
                <div class="form-group">
                    <label>Post Title <span style="color: #dc2626;">*</span></label>
                    <input type="text" class="form-input" placeholder="Enter post title" name="title" maxlength="100" required oninput="clearFieldError(this)">
                    <small class="error-message" style="color: #dc2626; font-size: 12px; display: none;"></small>
                </div>

                <!-- Description Field with Quill Editor -->
                <div class="form-group">
                    <div class="editor-container">
                        <label>Post Description <span style="color: #dc2626;">*</span></label>
                        <div id="editorToolbar" class="editor-toolbar">
                            <button type="button" class="ql-bold" title="Bold"></button>
                            <button type="button" class="ql-italic" title="Italic"></button>
                            <button type="button" class="ql-underline" title="Underline"></button>
                            <button type="button" class="ql-list" value="bullet" title="Bullet List"></button>
                            <button type="button" class="ql-link" title="Link"></button>
                        </div>
                        <div id="editor" class="quill-editor form-input" data-error-field="true"></div>
                    </div>
                    <small class="error-message" style="color: #dc2626; font-size: 12px; display: none; margin-top: 8px;"></small>
                </div>

                <!-- Label/Tags Field -->
                <div class="form-group">
                    <label>Labels <span style="color: #dc2626;">*</span></label>
                    <div class="multi-select-container" id="labelsContainer">
                        <div class="multi-select-display" onclick="toggleLabelsDropdown()">
                            <span class="placeholder">Select labels</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="multi-select-dropdown" id="labelsDropdown">
                            <!-- Options will be loaded here -->
                        </div>
                        <input type="hidden" name="labels" id="selectedLabelsInput" required>
                    </div>
                    <div class="selected-labels-display" id="selectedLabelsDisplay">
                        <!-- Selected labels will be shown here -->
                    </div>
                    <small class="error-message" style="color: #dc2626; font-size: 12px; display: none;"></small>
                </div>
            </form>
        </div>
        <div class="modal-footer border-0 bg-white">
            <button class="btn-submit" onclick="submitPost()">Submit Post</button>
        </div>
    </div>
</div>

<style>
    /* Modal Overlay */
    #createPostModal.modal-overlay {
        display: none;
        position: fixed;
        z-index: 2000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        align-items: center;
        justify-content: center;
    }

    #createPostModal.modal-overlay.open {
        display: flex;
    }

    /* Modal Popup */
    #createPostModal .create-post-popup {
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
        width: 90%;
        max-width: 500px;
        max-height: 86vh;
        display: flex;
        flex-direction: column;
        animation: slideIn 0.3s ease;
        overflow: hidden;
    }

    @keyframes slideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Modal Body */
    #createPostModal .modal-body {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
    }

    #createPostModal .modal-body>div:first-child {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        padding-bottom: 16px;
    }

    #createPostModal .modal-body>div:first-child h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: #111827;
    }

    #createPostModal .modal-close-btn {
        background: none;
        border: none;
        font-size: 28px;
        color: #6b7280;
        cursor: pointer;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #createPostModal .modal-close-btn:hover {
        color: #111827;
    }

    /* Form Group */
    #createPostModal .form-group {
        margin-bottom: 6px;
    }

    #createPostModal .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #111827;
        font-size: 14px;
    }

    /* Form Inputs */
    #createPostModal .form-input {
        width: 100%;
        padding: 6px;
        border: 2px solid #e5e7eb;
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
        box-sizing: border-box;
        transition: border-color 0.3s;
    }

    #createPostModal .form-input:focus {
        outline: none;
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }

    #createPostModal .form-input::placeholder {
        color: #9ca3af;
    }

    /* Error Message */
    #createPostModal .error-message {
        display: block;
        margin-top: 6px;
        font-size: 12px;
        color: #dc2626;
        font-weight: 500;
    }

    #createPostModal .form-input.input-error {
        border-color: #dc2626;
        background-color: #fef2f2;
    }

    /* Quill Editor */
    #createPostModal .editor-toolbar {
        background: #f9fafb;
        border: 2px solid #e5e7eb;
        border-bottom: 1px solid #e5e7eb;
        border-radius: 6px 6px 0 0;
        padding: 8px;
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        transition: border-color 0.3s;
    }

    #createPostModal .editor-toolbar button {
        width: 32px;
        height: 32px;
        padding: 4px 8px;
        border: 1px solid #d1d5db;
        background: white;
        border-radius: 4px;
        cursor: pointer;
        color: #374151;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }

    #createPostModal .editor-toolbar button:hover {
        background: #f3f4f6;
        border-color: #9ca3af;
    }

    #createPostModal .editor-toolbar button.ql-active {
        background: #dc2626;
        color: white;
        border-color: #dc2626;
    }

    #createPostModal .quill-editor {
        background: white;
        border: 2px solid #e5e7eb;
        border-top: none;
        border-radius: 0 0 6px 6px;
        min-height: 120px;
        font-size: 14px;
        transition: border-color 0.3s;
    }

    /* Error state for editor and toolbar */
    #createPostModal .quill-editor.editor-error {
        border-color: #dc2626;
        background-color: #fef2f2;
    }

    #createPostModal .editor-container.editor-error .editor-toolbar {
        border-color: #dc2626;
        background-color: #fef2f2;
    }

    #createPostModal .editor-container.editor-error .editor-toolbar button {
        border-color: #dc2626;
        background-color: #fef2f2;
    }

    #createPostModal .editor-container.editor-error .editor-toolbar button:hover {
        background-color: #fecaca;
        border-color: #b91c1c;
    }

    /* Focus state for editor container */
    #createPostModal .editor-container.editor-focused .editor-toolbar {
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }

    #createPostModal .editor-container.editor-focused .quill-editor {
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }

    #createPostModal .ql-editor {
        padding: 12px;
        line-height: 1.6;
    }

    #createPostModal .ql-editor.ql-blank::before {
        color: #9ca3af;
        font-style: italic;
    }

    /* Multi-select styles */
    #createPostModal .multi-select-container {
        position: relative;
        width: 100%;
    }

    #createPostModal .multi-select-display {
        width: 100%;
        padding: 10px;
        border: 2px solid #e5e7eb;
        border-radius: 6px;
        font-size: 14px;
        background: white;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: border-color 0.3s;
    }

    #createPostModal .multi-select-display:hover {
        border-color: #d1d5db;
    }

    #createPostModal .multi-select-display.active {
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }

    #createPostModal .multi-select-display .placeholder {
        color: #9ca3af;
        flex: 1;
    }

    #createPostModal .multi-select-display .placeholder.has-selection {
        color: #374151;
    }


    #createPostModal .multi-select-option {
        padding: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background-color 0.2s;
        border-bottom: 1px solid #f3f4f6;
    }

    #createPostModal .multi-select-option:last-child {
        border-bottom: none;
    }

    #createPostModal .multi-select-option:hover {
        background-color: #f9fafb;
    }

    #createPostModal .multi-select-option input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: #dc2626;
        cursor: pointer;
    }

    #createPostModal .multi-select-option label {
        flex: 1;
        font-size: 14px;
        color: #374151;
        cursor: pointer;
        margin: 0;
        font-weight: 500;
    }

    #createPostModal .selected-labels-display {
        display: none;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
        min-height: 24px;
        align-items: center;
    }

    #createPostModal .selected-label-tag {
        background: #fef2f2;
        color: #dc2626;
        padding: 6px 12px;
        border-radius: 16px;
        border: 1px solid #fecaca;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    #createPostModal .selected-label-tag:hover {
        border-color: #f87171;
    }

    #createPostModal .selected-label-tag .remove {
        cursor: pointer;
        font-weight: bold;
        font-size: 16px;
        line-height: 1;
        padding: 0 4px;
        border-radius: 50%;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 18px;
        height: 18px;
    }

    #createPostModal .selected-label-tag .remove:hover {
        color: #a00909;
        scale: 1.1;
    }

    /* Error state for multi-select */
    #createPostModal .multi-select-container.input-error .multi-select-display {
        border-color: #dc2626;
        background-color: #fef2f2;
    }

    /* Modal Footer */
    #createPostModal .modal-body>div:last-child {
        padding-top: 4px;
        display: flex;
        justify-content: flex-end;
    }

    /* Submit Button */
    #createPostModal .btn-submit {
        background: #dc2626;
        color: white;
        border: none;
        width: 100%;
        padding: 12px 32px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    #createPostModal .btn-submit:hover {
        background: #b91c1c;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }

    #createPostModal .btn-submit:active {
        transform: scale(0.98);
    }

    #createPostModal .btn-submit:disabled {
        background: #9ca3af;
        cursor: not-allowed;
    }

    /* Responsive Design */
    @media (max-width: 768px) {

        #createPostModal .modal-body>div:first-child {
            margin-bottom: 8px;
            padding-bottom: 12px;
        }

        #createPostModal .modal-body>div:first-child h2 {
            font-size: 18px;
        }

        #createPostModal .modal-close-btn {
            font-size: 24px;
            width: 28px;
            height: 28px;
        }

        #createPostModal .form-group {
            margin-bottom: 12px;
        }

        #createPostModal .form-group label {
            font-size: 13px;
            margin-bottom: 6px;
        }

        #createPostModal .editor-toolbar {
            padding: 6px;
            gap: 3px;
        }

        #createPostModal .editor-toolbar button {
            width: 28px;
            height: 28px;
            padding: 2px 4px;
            font-size: 12px;
        }

        #createPostModal .quill-editor {
            min-height: 100px;
            font-size: 16px; /* Prevents zoom on iOS */
        }

        #createPostModal .ql-editor {
            padding: 0px;
        }


        #createPostModal .error-message {
            font-size: 11px;
        }
    }

    @media (max-width: 480px) {

        #createPostModal .modal-body>div:first-child h2 {
            font-size: 16px;
        }

        #createPostModal .editor-toolbar {
            padding: 4px;
            gap: 2px;
        }

        #createPostModal .editor-toolbar button {
            width: 24px;
            height: 24px;
            font-size: 11px;
        }

        #createPostModal .quill-editor {
            min-height: 80px;
        }

        #createPostModal .ql-editor {
            padding: 0px;
        }

        #createPostModal .form-input {
            font-size: 10px;
        }

    }
</style>

<!-- Quill Rich Text Editor CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    let quill;
    const MAX_DESCRIPTION_LENGTH = 250;
    let availableLabels = [];
    let selectedLabels = [];

    function openCreatePostModal() {
        const modal = document.getElementById('createPostModal');
        if (modal) {
            modal.classList.add('open');
            clearAllPostFormErrors();
            loadLabels(); // Load labels when opening modal
            if (!quill) {
                quill = new Quill('#editor', {
                    theme: 'snow',
                    placeholder: 'Enter post description',
                    modules: {
                        toolbar: '#editorToolbar'
                    }
                });

                // Add focus and blur event handlers for red border
                quill.on('selection-change', function(range) {
                    const editorContainer = document.querySelector('.editor-container');
                    if (range) {
                        // Editor is focused
                        editorContainer.classList.add('editor-focused');
                    } else {
                        // Editor lost focus
                        editorContainer.classList.remove('editor-focused');
                    }
                });

                // Clear error when user starts typing in editor
                quill.on('text-change', function() {
                    const text = quill.getText().trim();
                    if (text.length > MAX_DESCRIPTION_LENGTH) {
                        quill.deleteText(MAX_DESCRIPTION_LENGTH);
                    }

                    // Clear error styling
                    const editorElement = document.getElementById('editor');
                    if (text.length > 0) {
                        editorElement.classList.remove('editor-error');
                        // Also remove error class from editor container
                        const editorContainer = editorElement.closest('.editor-container');
                        if (editorContainer) {
                            editorContainer.classList.remove('editor-error');
                        }
                        const errorMsg = editorElement.closest('.form-group').querySelector('.error-message');
                        if (errorMsg) {
                            errorMsg.textContent = '';
                            errorMsg.style.display = 'none';
                        }
                    }
                });
            } else {
                quill.setContents([]);
            }
        }
    }

    function loadLabels() {
        fetch("{{ route('alumni.forums.labels') }}")
            .then(res => res.json())
            .then(data => {
                availableLabels = data;
                populateLabelsDropdown();
            })
            .catch(error => {
                console.error('Error loading labels:', error);
                showToast('Failed to load labels', 'error');
            });
    }

    function populateLabelsDropdown() {
        const dropdown = document.getElementById('labelsDropdown');
        dropdown.innerHTML = '';
        
        availableLabels.forEach(label => {
            const isSelected = selectedLabels.some(selected => selected.id === label.id);
            const optionHtml = `
                <div class="multi-select-option" data-value="${label.id}">
                    <input type="checkbox" id="label_${label.id}" value="${label.id}" ${isSelected ? 'checked' : ''} onchange="handleLabelSelection(${label.id}, '${label.name}', this.checked)">
                    <label for="label_${label.id}">${label.name}</label>
                </div>
            `;
            dropdown.innerHTML += optionHtml;
        });
    }

    function toggleLabelsDropdown() {
        const dropdown = document.getElementById('labelsDropdown');
        const display = document.querySelector('#labelsContainer .multi-select-display');
        
        dropdown.classList.toggle('active');
        display.classList.toggle('active');
    }

    function handleLabelSelection(labelId, labelName, isChecked) {
        if (isChecked) {
            // Add label to selected list
            if (!selectedLabels.some(label => label.id === labelId)) {
                selectedLabels.push({ id: labelId, name: labelName });
            }
        } else {
            // Remove label from selected list
            selectedLabels = selectedLabels.filter(label => label.id !== labelId);
        }
        
        updateLabelsDisplay();
        updateLabelsInput();
        clearLabelsError();
    }

    function updateLabelsDisplay() {
        const display = document.querySelector('#labelsContainer .placeholder');
        const selectedDisplay = document.getElementById('selectedLabelsDisplay');

        if (selectedLabels.length > 0) {
            selectedDisplay.style.display = 'flex';
        } else {
            selectedDisplay.style.display = 'none';
        }
        
        // Update placeholder text
        if (selectedLabels.length > 0) {
            display.textContent = `${selectedLabels.length} label${selectedLabels.length > 1 ? 's' : ''} selected`;
            display.classList.add('has-selection');
        } else {
            display.textContent = 'Select labels';
            display.classList.remove('has-selection');
        }
        
        // Update selected labels display
        selectedDisplay.innerHTML = '';
        selectedLabels.forEach(label => {
            const tag = document.createElement('div');
            tag.className = 'selected-label-tag';
            tag.innerHTML = `
                <span>${label.name}</span>
                <span class="remove" onclick="removeLabelTag(${label.id})">&times;</span>
            `;
            selectedDisplay.appendChild(tag);
        });
    }

    function removeLabelTag(labelId) {
        // Remove from selected labels
        selectedLabels = selectedLabels.filter(label => label.id !== labelId);
        
        // Uncheck the checkbox
        const checkbox = document.getElementById(`label_${labelId}`);
        if (checkbox) {
            checkbox.checked = false;
        }
        
        updateLabelsDisplay();
        updateLabelsInput();
        clearLabelsError();
    }

    function updateLabelsInput() {
        const input = document.getElementById('selectedLabelsInput');
        input.value = selectedLabels.map(label => label.id).join(',');
    }

    function clearLabelsError() {
        const container = document.getElementById('labelsContainer');
        const errorMsg = container.parentElement.querySelector('.error-message');
        
        container.classList.remove('input-error');
        if (errorMsg) {
            errorMsg.textContent = '';
            errorMsg.style.display = 'none';
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const container = document.getElementById('labelsContainer');
        if (container && !container.contains(event.target)) {
            const dropdown = document.getElementById('labelsDropdown');
            const display = document.querySelector('#labelsContainer .multi-select-display');
            dropdown.classList.remove('active');
            display.classList.remove('active');
        }
    });

    function closeCreatePostModal() {
        const modal = document.getElementById('createPostModal');
        if (modal) {
            modal.classList.remove('open');
            document.getElementById('createPostForm').reset();
            clearAllPostFormErrors();
            
            // Reset labels selection
            selectedLabels = [];
            updateLabelsDisplay();
            updateLabelsInput();
            
            // Close dropdown
            const dropdown = document.getElementById('labelsDropdown');
            const display = document.querySelector('#labelsContainer .multi-select-display');
            dropdown.classList.remove('active');
            display.classList.remove('active');
            
            if (quill) {
                quill.setContents([]);
            }
        }
    }

    function submitPost() {
        const form = document.getElementById('createPostForm');
        const titleInput = form.querySelector('input[name="title"]');
        const title = titleInput.value.trim();
        const description = quill.getText().trim();
        const selectedLabelsInput = document.getElementById('selectedLabelsInput');
        const labels = selectedLabels.map(l => l.id);

        // Clear previous errors
        clearAllPostFormErrors();

        // Validation
        let hasError = false;

        if (!title) {
            showFieldError(titleInput, 'Post title is required');
            hasError = true;
        }

        const invalidPattern = /[^A-Za-z0-9 ]/;
        if (invalidPattern.test(title)) {
            showFieldError(titleInput, 'Special characters are not allowed in the title');
            hasError = true;
        }

        if (!description || description.length === 0) {
            showFieldError(document.getElementById('editor'), 'Post description is required');
            hasError = true;
        } else if (description.length > MAX_DESCRIPTION_LENGTH) {
            showFieldError(document.getElementById('editor'), `Description must be less than ${MAX_DESCRIPTION_LENGTH} characters`);
            hasError = true;
        }

        if (selectedLabels.length === 0) {
            showFieldError(document.getElementById('labelsContainer'), 'At least one label is required');
            hasError = true;
        }

        if (hasError) {
            return;
        }

        const formData = new FormData();
        const modal = document.getElementById('createPostModal');
        const alumniId = modal.getAttribute('data-alumni-id');

        formData.append('title', title);
        formData.append('description', quill.root.innerHTML);
        formData.append('labels', JSON.stringify(labels));
        formData.append('alumni_id', alumniId);
        formData.append('status', 'pending');

        if (!alumniId) {
            showToast('Alumni ID not found', 'error');
            return;
        }

        const submitBtn = document.querySelector('.btn-submit');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Submitting...';
        submitBtn.disabled = true;

        fetch(`{{ route('alumni.create.post') }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Post submitted successfully. Awaiting Admin approval.');
                    closeCreatePostModal();
                    window.location.reload();   
                } else {
                    showToast(data.message || 'Failed to create post', 'error');
                }
            })
            .catch(err => {
                showToast('Error creating post', 'error');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
    }

    function showFieldError(element, message) {
        let errorEl;
        
        if (element.id === 'editor') {
            // For editor, find error message in the form-group
            errorEl = element.closest('.form-group').querySelector('.error-message');
            element.classList.add('editor-error');
            // Also add error class to the editor container for toolbar styling
            const editorContainer = element.closest('.editor-container');
            if (editorContainer) {
                editorContainer.classList.add('editor-error');
            }
        } else if (element.id === 'labelsContainer') {
            // For labels multi-select container
            errorEl = element.parentElement.querySelector('.error-message');
            element.classList.add('input-error');
        } else {
            // For regular inputs
            errorEl = element.parentElement.querySelector('.error-message');
            element.classList.add('input-error');
        }
        
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.style.display = 'block';
        }
    }

    function clearFieldError(element) {
        // Remove error styling from input
        element.classList.remove('input-error');

        // Clear error message
        const errorMsg = element.parentElement.querySelector('.error-message');
        if (errorMsg) {
            errorMsg.textContent = '';
            errorMsg.style.display = 'none';
        }
    }

    function clearAllPostFormErrors() {
        const form = document.getElementById('createPostForm');
        form.querySelectorAll('.form-input').forEach(el => {
            el.classList.remove('input-error');
        });
        form.querySelectorAll('.error-message').forEach(el => {
            el.textContent = '';
            el.style.display = 'none';
        });
        
        // Clear editor errors
        const editor = document.getElementById('editor');
        editor.classList.remove('editor-error');
        const editorContainer = editor.closest('.editor-container');
        if (editorContainer) {
            editorContainer.classList.remove('editor-error');
            editorContainer.classList.remove('editor-focused');
        }
        
        // Clear labels container errors
        const labelsContainer = document.getElementById('labelsContainer');
        if (labelsContainer) {
            labelsContainer.classList.remove('input-error');
        }
    }


    // Close modal when clicking outside - DISABLED
    // Uncomment below to enable closing modal by clicking outside
    // window.addEventListener('click', function(event) {
    //     const modal = document.getElementById('createPostModal');
    //     if (event.target === modal) {
    //         closeCreatePostModal();
    //     }
    // });
</script>