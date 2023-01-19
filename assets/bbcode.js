function formatText(tag, num) {
        var Field = document.getElementById('textbox' + num);
        var val = Field.value;
        var selected_txt = val.substring(Field.selectionStart, Field.selectionEnd);
        var before_txt = val.substring(0, Field.selectionStart);
        var after_txt = val.substring(Field.selectionEnd, val.length);
        Field.value += '[' + tag + ']' + '[/' + tag + ']';
        var endTag = '[/' + tag + ']'
        Field.focus();
        Field.setSelectionRange(Field.selectionStart - endTag.length,Field.selectionEnd - endTag.length);
    }
    function formatTextWithDetails(tag, input, num) {
        var Field = document.getElementById('textbox' + num);
        var val = Field.value;

        var Details = document.getElementById(input + num );
        if (Details.nodeName == 'SELECT')
        {
            var detailsVal = Details.options[Details.selectedIndex].value;
        }
        else {
            var detailsVal = Details.value;
        }

        var selected_txt = val.substring(Field.selectionStart, Field.selectionEnd);
        var before_txt = val.substring(0, Field.selectionStart);
        var after_txt = val.substring(Field.selectionEnd, val.length);
        Field.value += '[' + tag + '=' + detailsVal + ']' + '[/' + tag + ']';
        var endTag = '[/' + tag + ']'
        Field.focus();
        Field.setSelectionRange(Field.selectionStart - endTag.length,Field.selectionEnd - endTag.length);
    }
