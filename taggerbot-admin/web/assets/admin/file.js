$.ajax(SERVICE_URL.replace('FILEID', FILE_ID), {
        dataType: 'json'
    })
    .done(function(data) {
        $untagged = $('tbody');
        for (var i = 0; i < data.data.length; i++) {
            $untagged.append(htmlFromParagraph(data.data[i], i));
        }
    })

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
                        <div class="ui button gray tiny">Tag</div>
                        <div class="ui button red tiny">Remove</div>
                    </div>
                </td>
            </tr>`;
    return html;
}