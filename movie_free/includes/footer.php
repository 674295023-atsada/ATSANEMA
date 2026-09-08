    </div><!-- /.container -->
</div><!-- /.main-content -->

<footer class="mt-auto py-4 bg-dark text-secondary text-center border-top border-secondary-subtle">
    <div class="container">
        <p class="mb-1">&copy; <?php echo date('Y'); ?> <span class="text-danger fw-bold">MovieFree</span>. ระบบดูหนังออนไลน์ฟรีเพื่อการศึกษา (PHP + MySQL)</p>
        <small class="text-muted">พัฒนาด้วย Bootstrap 5, FontAwesome & PDO</small>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script สำหรับปุ่มรูปตาแสดง/ซ่อนรหัสผ่าน -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.toggle-password').forEach(function (button) {
        button.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        });
    });
});
</script>
</body>
</html>
