<!DOCTYPE html>

<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <title>Sign Up | Blog Fusion</title>
  <!-- Tailwind CSS CDN with Plugins -->
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <!-- BEGIN: Tailwind Configuration -->
  <script data-purpose="tailwind-config">
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#7C3AED',
            secondary: '#4F46E5',
            accent: '#EC4899',
            customBg: '#F3F4F6',
            customText: '#111827',
          }
        }
      }
    }
  </script>
  <!-- END: Tailwind Configuration -->
  <!-- BEGIN: Custom Styles -->
  <style data-purpose="custom-css-variables">
    :root {
      --primary: #7C3AED;
      --secondary: #4F46E5;
      --accent: #EC4899;
      --background: #F3F4F6;
      --text: #111827;
    }
  </style>
  <style data-purpose="ui-components">
    /* Custom toggle switch styling */
    .role-toggle-checkbox:checked+.role-toggle-label {
      background-color: var(--primary);
    }

    .role-toggle-checkbox:checked+.role-toggle-label .toggle-dot {
      transform: translateX(100%);
    }

    .glass-effect {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
    }
  </style>
  <!-- END: Custom Styles -->
  <link
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
    rel="stylesheet" />
</head>

<body class="bg-customBg text-customText min-h-screen flex items-center justify-center p-4">
  <!-- BEGIN: Signup Card Container -->

  <main class="max-w-md w-full glass-effect rounded-2xl shadow-xl overflow-hidden border border-white/20"
    data-purpose="signup-card">
    <!-- BEGIN: Header Section -->
    <?php if (isset($_GET['msg']) && $_GET['msg'] == "exists"){ ?>

            <div id="alertBox" class="fixed top-4 left-1/2 -translate-x-1/2 z-[100] w-full max-w-sm px-4">
                <div
                    class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl shadow-lg">
                    <span class="material-symbols-outlined text-green-500">check_circle</span>
                    <p class="text-sm font-semibold">Email already exists</p>
                </div>
            </div>

            <script>
                setTimeout(() => {
                    document.getElementById("alertBox")?.remove();
                }, 2000);

                if (window.history.replaceState) {
                  const url = new URL(window.location);
                  url.searchParams.delete("msg"); // remove msg parameter
                  window.history.replaceState({}, document.title, url.pathname);
                }
            </script>
            

        <?php } ?>

    <header class="p-8 text-center bg-gradient-to-br from-primary to-secondary text-white">
      <h1 class="text-3xl font-bold tracking-tight">Blog Fusion</h1>
      <p class="mt-2 text-indigo-100">Join our creative community today</p>
    </header>
    <!-- END: Header Section -->
    <!-- BEGIN: Signup Form -->
    <form action="../actions/login_signup.php" class="p-8 space-y-6" data-purpose="signup-form" method="POST"
      enctype="multipart/form-data">
      <!-- Full Name Field -->
      <div>
        <label class="block text-sm font-semibold mb-2" for="full-name">Full Name</label>
        <input
          class="w-full px-4 py-3 rounded-lg border-gray-300 focus:ring-primary focus:border-primary transition duration-200"
          id="full-name" name="name" placeholder="John Doe" required="" type="text" />
      </div>
      <!-- Email Field -->
      <div>
        <label class="block text-sm font-semibold mb-2" for="email">Email Address</label>
        <input
          class="w-full px-4 py-3 rounded-lg border-gray-300 focus:ring-primary focus:border-primary transition duration-200"
          id="email" name="email" placeholder="hello@example.com" required="" type="email" />
      </div>
      <!-- Password Fields Row -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-semibold mb-2" for="password">Password</label>
          <div class="relative">
            <input
              class="w-full px-4 py-3 rounded-lg border-gray-300 focus:ring-primary focus:border-primary transition duration-200 pr-10"
              id="password" name="password" required="" type="password" />
            <button
              class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-primary transition-colors"
              onclick="togglePasswordVisibility('password', this)" type="button">
              <span class="material-symbols-outlined text-xl">visibility</span>
            </button>
          </div>
        </div>
        <div>
          <label class="block text-sm font-semibold mb-2" for="confirm-password">Confirm Password</label>
          <div class="relative">
            <input
              class="w-full px-4 py-3 rounded-lg border-gray-300 focus:ring-primary focus:border-primary transition duration-200 pr-10"
              id="confirm-password" name="confirm-password" required="" type="password" />
            <button
              class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-primary transition-colors"
              onclick="togglePasswordVisibility('confirm-password', this)" type="button">
              <span class="material-symbols-outlined text-xl">visibility</span>
            </button>
          </div>
        </div>
      </div>
      <!-- Role Selection Toggle -->
      <div class="space-y-3" data-purpose="role-selector">
        <label class="block text-sm font-semibold mb-2">Join as <span class="text-primary transition-all duration-300"
            id="role-text">User</span></label>
        <div class="relative flex p-1 bg-gray-100 rounded-xl border border-gray-200 w-full">
          <!-- Sliding Background Highlight -->
          <div
            class="absolute top-1 left-1 bottom-1 w-[calc(50%-4px)] bg-white rounded-lg shadow-sm transition-transform duration-300 ease-out transform translate-x-0"
            id="role-highlight"></div>
          <!-- Buttons -->
          <button class="relative flex-1 py-2 text-sm font-bold transition-colors duration-300 text-primary z-10"
            id="btn-user" type="button">
            USER
          </button>
          <button class="relative flex-1 py-2 text-sm font-bold transition-colors duration-300 text-gray-500 z-10"
            id="btn-author" type="button">
            AUTHOR
          </button>
          <!-- Hidden input to store value -->
          <input id="role-input" name="role" type="hidden" value="user" />
        </div>
      </div>
      <!-- Profile Picture Upload -->
      <div>
        <label class="block text-sm font-semibold mb-2">Profile Picture</label>
        <div
          class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-accent transition duration-200">
          <div class="space-y-1 text-center w-full relative group">
            <!-- Placeholder UI -->
            <div class="block" id="upload-placeholder">
              <svg aria-hidden="true" class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                viewbox="0 0 48 48">
                <path
                  d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
              </svg>
              <div class="flex text-sm text-gray-600 justify-center">
                <label
                  class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-indigo-500"
                  for="file-upload">
                  <span>Upload a file</span>
                  <input accept="image/*" class="sr-only" id="file-upload" name="image"
                    onchange="handleImagePreview(event)" type="file" />
                </label>
                <p class="pl-1">or drag and drop</p>
              </div>
              <p class="text-xs text-gray-500">PNG, JPG up to 10MB</p>
            </div>
            <!-- Image Preview UI -->
            <div class="hidden relative inline-block mx-auto" id="preview-container">
              <img alt="Profile Preview"
                class="h-32 w-32 object-cover rounded-full border-4 border-white shadow-md mx-auto" id="image-preview"
                src="" />
              <button
                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-lg hover:bg-red-600 transition-colors"
                onclick="removeImage()" type="button">
                <span class="material-symbols-outlined text-sm">close</span>
              </button>
            </div>
          </div>
        </div>
      </div>
      <!-- Submit Button -->
      <button
        class="w-full bg-primary hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg transform active:scale-[0.98] transition duration-200"
        type="submit" name="signup">
        Create Account
      </button>
      <!-- Footer Links -->
      <p class="text-center text-sm text-gray-500 mt-4">
        Already have an account?
        <a class="text-accent font-semibold hover:underline" href="login.php">Log in</a>
      </p>
    </form>

    <!-- END: Signup Form -->
  </main>



  <!-- END: Signup Card Container -->
  <!-- BEGIN: Interactive Logic -->
  <script data-purpose="form-interactivity">
    const roleTextDisplay = document.getElementById('role-text');
    const btnUser = document.getElementById('btn-user');
    const btnAuthor = document.getElementById('btn-author');
    const highlight = document.getElementById('role-highlight');
    const roleInput = document.getElementById('role-input');

    function setRole(role) {
      if (role === 'user') {
        roleTextDisplay.textContent = 'User';
        roleInput.value = 'user';
        highlight.style.transform = 'translateX(0)';
        btnUser.classList.add('text-primary');
        btnUser.classList.remove('text-gray-500');
        btnAuthor.classList.add('text-gray-500');
        btnAuthor.classList.remove('text-primary');
      } else {
        roleTextDisplay.textContent = 'Author';
        roleInput.value = 'author';
        highlight.style.transform = 'translateX(100%)';
        btnAuthor.classList.add('text-primary');
        btnAuthor.classList.remove('text-gray-500');
        btnUser.classList.add('text-gray-500');
        btnUser.classList.remove('text-primary');
      }
    }

    btnUser.addEventListener('click', () => setRole('user'));
    btnAuthor.addEventListener('click', () => setRole('author'));

    // Handle file upload preview or name display
    const fileInput = document.getElementById('file-upload');
    if (fileInput) {
      fileInput.addEventListener('change', function () {
        if (this.files && this.files[0]) {
          console.log('File selected:', this.files[0].name);
        }
      });
    }
  </script>
  <!-- END: Interactive Logic -->
  <script>
    function togglePasswordVisibility(inputId, button) {
      const input = document.getElementById(inputId);
      const icon = button.querySelector('.material-symbols-outlined');
      if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = 'visibility_off';
      } else {
        input.type = 'password';
        icon.textContent = 'visibility';
      }
    }
  </script>
  <script>
    function handleImagePreview(event) {
      const input = event.target;
      const placeholder = document.getElementById('upload-placeholder');
      const previewContainer = document.getElementById('preview-container');
      const previewImg = document.getElementById('image-preview');

      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
          previewImg.src = e.target.result;
          placeholder.classList.add('hidden');
          previewContainer.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
      }
    }

    function removeImage() {
      const input = document.getElementById('file-upload');
      const placeholder = document.getElementById('upload-placeholder');
      const previewContainer = document.getElementById('preview-container');
      const previewImg = document.getElementById('image-preview');

      input.value = '';
      previewImg.src = '';
      previewContainer.classList.add('hidden');
      placeholder.classList.remove('hidden');
    }
  </script>
</body>

</html>