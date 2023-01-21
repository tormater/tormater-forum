function search() {
    let searchPath = new URL(baseURL + '/search/');
    var searchBox = document.getElementById('searchbox');
    window.location.href = searchPath + "?search=" + encodeURIComponent(searchBox.value);
}
