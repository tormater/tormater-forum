function search() {
    let searchPath = new URL(baseURL + '/search/', window.location.origin);
    var searchBox = document.getElementById('searchbox').value;
    if (searchBox.length > 0) {
        window.location.href = searchPath + "?search=" + encodeURIComponent(searchBox);
    }
}
