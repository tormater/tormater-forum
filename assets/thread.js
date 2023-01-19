function quotePost(id) {
    let ajaxPath = new URL(baseURL + '/pages/thread.ajax.php');
    var Field = document.getElementById('textbox1');
    var val = Field.value;
    var selected_txt = val.substring(Field.selectionStart, Field.selectionEnd);
    var before_txt = val.substring(0, Field.selectionStart);
    var after_txt = val.substring(Field.selectionEnd, val.length);
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            Field.value += this.responseText;;
            Field.focus();
            Field.setSelectionRange(Field.selectionStart,Field.selectionEnd);
       }
    };
    xhttp.open('POST', ajaxPath, true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhttp.send('postid=' + id); 
}
