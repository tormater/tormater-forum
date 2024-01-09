function search() {
    let searchPath = new URL(baseURL + '/search/', window.location.origin);
    var searchBox = document.getElementById('searchbox');
    window.location.href = searchPath + "?search=" + encodeURIComponent(searchBox.value);
}
