<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>TinyMCE Email Editor</title>
    <script src="tinymce/tinymce.min.js"></script>

    <style>
        body {
            font-family: Arial;
            background: #f3f4f6;
            margin: 0;
            padding: 20px;
        }

        .editor-container {
            max-width: 1000px;
            margin: 20px auto;
            background: white;
            border: 1px solid #ddd;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .editor-header {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            background: #f9fafb;
            font-weight: bold;
        }

        .editor-footer {
            padding: 10px;
            border-top: 1px solid #ddd;
            background: #f9fafb;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .tag-btn {
            padding: 6px 12px;
            background: #2563EB;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .submit-btn {
            padding: 10px 20px;
            background: #10B981;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 10px;
        }

        /* আউটপুট দেখানোর জায়গা */
        #output-area {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border: 2px dashed #ccc;
            min-height: 100px;
        }
    </style>
</head>

<body>

    <div class="editor-container">
        <div class="editor-header">Design Tools</div>

        <textarea id="editor">
        <h1>linxtest</h1>
        <p>Type your company slogan here</p>
    </textarea>

        <div class="editor-footer">
            <button class="tag-btn" onclick="insertTag('%%email%%')">Custom Fields</button>
            <button class="tag-btn" onclick="insertTag('%%name%%')">Dynamic Content</button>
            <button class="tag-btn" onclick="insertTag('[survey_link]')">Survey Link</button>
            <button class="submit-btn" onclick="showContent()">Submit & View Content</button>
        </div>
    </div>

    <h3 style="text-align:center;">Final Preview:</h3>
    <div id="output-area">আপনার লেখা কন্টেন্ট এখানে দেখা যাবে...</div>

    <script>
        tinymce.init({
            selector: '#editor',
            height: 400,
            license_key: 'gpl',
            plugins: 'image',
            toolbar: 'undo redo | bold italic | image',

            automatic_uploads: true,
            images_upload_url: 'upload.php'
        });
        // tinymce.init({
        //     selector: '#editor',
        //     height: 400,
        //     license_key: 'gpl',

        //     plugins: 'lists link image table code preview',
        //     toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | image',

        //     automatic_uploads: true,
        //     images_upload_url: 'test2.php', // 👈 main magic

        //     images_upload_handler: function (blobInfo, success, failure) {
        //         let formData = new FormData();
        //         formData.append('file', blobInfo.blob(), blobInfo.filename());

        //         fetch('test2.php', {
        //             method: 'POST',
        //             body: formData
        //         })
        //             .then(res => res.json())
        //             .then(data => {
        //                 success(data.location); // 👈 image URL return
        //             })
        //             .catch(() => {
        //                 failure('Upload failed');
        //             });
        //     }
        // });
        function showContent() {
            const content = tinymce.get('editor').getContent();
            console.log(content); // এডিটরের লেখা নেওয়া হচ্ছে
            document.getElementById('output-area').innerHTML = content; // আউটপুট বক্সে দেখানো হচ্ছে
        }
        function insertTag(tag) {
            tinymce.get('editor').insertContent(tag);
        }
    </script>

</body>

</html>