function init() {
    $('.train.dropdown').dropdown({
        on: 'hover',
        onChange: function(a, b, e) {
            $('#choose-tag-first').hide();
            tag = $(e).attr('tag');
            getTagParagraph(tag);
        },
    });
}

function getTagParagraph(tagId) {
    $.ajax(SERVICE_URL.replace('TAGID', tagId), {
            dataType: 'json',
        })
        .done(function(data) {
            $('tbody').html('');
            data.data.forEach(function(e) {
                $('tbody').append(
                    `<tr>
                        <td class="collapsing">
                            <div class="ui fitted slider checkbox">
                            <input type="checkbox"> <label></label>
                            </div>
                        </td>
                        <td class="single line">` + e.file_name + `</td>
                        <td>` + e.content + `</td>
                        <td class="single line">` + e.tags + `</td>
                    </tr>`
                );
            });

            if (data.data.length == 0) {
                $('tbody').append(
                    `<tr>
                        <td class="collapsing">
                            <div class="ui fitted slider checkbox input disabled">
                                <input type="checkbox"> <label></label>
                            </div>
                        </td>
                        <td colspan="10" style="text-align: center">
                            <div class="ui pointing red basic label large">
                                No associated paragraph.
                            </div>
                        </td>
                    </tr>`
                );
            }
        });
}

init();