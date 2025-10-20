// Modal login admin
document.getElementById('adminLoginBtn').onclick = function(e) {
    e.preventDefault();
    document.getElementById('adminLoginModal').style.display = 'flex';
};
document.getElementById('adminLoginBtnFooter').onclick = function(e) {
    e.preventDefault();
    document.getElementById('adminLoginModal').style.display = 'flex';
};
window.onclick = function(event) {
    var modal = document.getElementById('adminLoginModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
};

