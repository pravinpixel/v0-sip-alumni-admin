var Commonstep =function(){
    
    $(".toggle-password").click(function() {
  
    $(this).toggleClass("fa-eye fa-eye-slash");
    var input = $($(this).attr("toggle"));
    if (input.attr("type") == "password") {
      input.attr("type", "text");
    } else {
      input.attr("type", "password");
    }
  });

        // Script to prevent Password Copy/Paste
      //   $('input[type="password"]').on('paste', function(e) {
      //         e.preventDefault();
      //   }).on('copy', function(e) {
      //         e.preventDefault();
      //   }).on('cut', function(e) {
      //         e.preventDefault();
      //   }).on('contextmenu', function(e) {
      //         e.preventDefault();
      //   });

  }();

// function showProgressBar() {
//   var progressBar = document.getElementById('progress-bar');
//   if (progressBar) {
//     progressBar.style.display = 'block';
//   } else {
//     console.error('Element with ID "progress-bar" not found.');
//   }
// }

// function hideProgressBar() {
//   var progressBar = document.getElementById('progress-bar');
//   if (progressBar) {
//     progressBar.style.display = 'none';
//   } else {
//     console.error('Element with ID "progress-bar" not found.');
//   }
// }

function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        toast.style.background = type === 'success' ? '#10b981' : '#ef4444';
        toast.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} text-white"></i>
            <span>${message}</span>
        `;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'slideOutDown 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
