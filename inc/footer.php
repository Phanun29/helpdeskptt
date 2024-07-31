<footer class="main-footer">
    <strong>Copyright &copy;<span id="year"></span> </strong>
    All rights reserved By Intern.

</footer>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var yearSpan = document.getElementById('year');
        var currentYear = new Date().getFullYear();
        yearSpan.textContent = currentYear;
    });
</script>