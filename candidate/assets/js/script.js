document.addEventListener('DOMContentLoaded', function () {
    var sidebarCollapse = document.getElementById('sidebarCollapse');
    var sidebar = document.getElementById('sidebar');

    if(sidebarCollapse) {
        sidebarCollapse.addEventListener('click', function () {
            sidebar.classList.toggle('show');
        });
    }
});
