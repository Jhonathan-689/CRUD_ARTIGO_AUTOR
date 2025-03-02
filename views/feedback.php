<?php if (!empty($message)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var toastEl = document.getElementById('feedbackToast');
            var toast = new bootstrap.Toast(toastEl);
            toast.show();
        });
    </script>
<?php endif; ?>
