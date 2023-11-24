function formatText(tag, num) {
        var Field = document.getElementById('textbox' + num);
        var val = Field.value;
        var selected_txt = val.substring(Field.selectionStart, Field.selectionEnd);
        var before_txt = val.substring(0, Field.selectionStart);
        var after_txt = val.substring(Field.selectionEnd, val.length);
        open_tag = '[' + tag + ']';
        end_tag = '[/' + tag + ']';
        new_selection_start = Field.selectionStart + open_tag.length;
        new_selection_end = Field.selectionEnd + open_tag.length;
        Field.value = before_txt + open_tag + selected_txt + end_tag + after_txt;
        Field.setSelectionRange(new_selection_start, new_selection_end);
        Field.focus();
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
        open_tag = '[' + tag + '=' + detailsVal + ']';
        end_tag = '[/' + tag + ']';
        new_selection_start = Field.selectionStart + open_tag.length;
        new_selection_end = Field.selectionEnd + open_tag.length;
        Field.value = before_txt + open_tag + selected_txt + end_tag + after_txt;
        Field.setSelectionRange(new_selection_start, new_selection_end);
        Field.focus();
}
