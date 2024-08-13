document.addEventListener("DOMContentLoaded", function () {
    // Auto-close alert after 5 seconds
    setTimeout(function () {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function (alert) {
            alert.classList.remove('show');
            alert.classList.add('hide');
            setTimeout(function () {
                alert.remove();
            }, 500); // Remove alert after transition
        });
    }, 5000); // 5000 milliseconds = 5 seconds
});