// CKEditor 5 Custom Configuration
ClassicEditor
    .create(document.querySelector('#address'), {
        toolbar: [
            'heading', '|',
            'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|',
            'undo', 'redo', '|',
            'customUpload'
        ],
        heading: {
            options: [
                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' }
            ]
        },
        extraPlugins: [ MyCustomUploadPlugin ]
    })
    .catch(error => {
        console.error(error);
    });

// Define the custom upload plugin
function MyCustomUploadPlugin(editor) {
    editor.ui.componentFactory.add('customUpload', locale => {
        const view = new editor.ui.button.ButtonView(locale);

        view.set({
            label: 'Upload File',
            icon: '<svg>...</svg>', // Replace with actual SVG icon
            tooltip: true
        });

        view.on('execute', () => {
            alert('Custom upload button clicked!');
            // Add your file upload logic here
        });

        return view;
    });
}
