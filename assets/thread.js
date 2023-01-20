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

function previewPost(num) {
    let ajaxPath = new URL(baseURL + '/pages/thread.ajax.php');
    var Field = document.getElementById('textbox' + num);
    var PreviewBox = document.getElementById("previewbox" + num)
    var PreviewButton = document.getElementById("showpreview" + num)
    var HidePreviewButton = document.getElementById("hidepreview" + num)
    var val = Field.value;

    if (PreviewBox.innerHTML == "") {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                Field.setSelectionRange(Field.selectionStart,Field.selectionEnd);
                PreviewButton.style = "display: none";
                HidePreviewButton.style = "";
                PreviewBox.innerHTML = this.responseText;
                PreviewBox.style = "";
                PreviewButton.blur();
                HidePreviewButton.scrollIntoView();

           }
        };
    
        xhttp.open('POST', ajaxPath, true);
        xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhttp.send('postRaw=' + Field.value); 
    }
    else {
        PreviewButton.style = "";
        HidePreviewButton.style = "display: none";
        PreviewBox.style = "display: none";
        PreviewBox.innerHTML = ""
        HidePreviewButton.blur();
        PreviewButton.scrollIntoView();
    }
}
