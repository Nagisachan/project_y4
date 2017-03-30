$.ajax(SERVICE_URL.replace('FILEID', FILE_ID), {
        dataType: 'json'
    })
    .done(function(data) {
        $untagged = $('tbody');
        for (var i = 0; i < data.data.length; i++) {
            $untagged.append(htmlFromParagraph(data.data[i], i));
        }
    })

function addTag(fid, pid) {
    $('.ui.modal').modal('show');
}

function htmlFromParagraph(paragraph, i) {
    html = `<tr>
                <td>
                    <div class="ui center aligned">` + paragraph.paragraph_id + `</div>
                </td>
                <td>
                    ` + paragraph.content + `
                </td>
                <td class="right aligned">
                    <div class="ui center aligned">` + (paragraph.content ? paragraph.content.trim().split(' ').length : '-') + `</div>
                </td>
                <td>
                    <div class="single line content ui center aligned">
                        <div class="ui button gray tiny" onclick="javascript:addTag(` + paragraph.file_id + `,` + paragraph.paragraph_id + `);">Tag</div>
                        <div class="ui button red tiny">Remove</div>
                    </div>
                </td>
            </tr>`;
    return html;
}

function initDropDown() {
    $dropdowns = $('.ui.dropdown');
    for (var i = 0; i < $dropdowns.length; i++) {
        $dropdown = $($dropdowns[i]);
        $dropdown.dropdown({
            allowAdditions: true,
            className: { label: 'ui label ' + $dropdown.attr('color') }
        });
    }
}

initDropDown();