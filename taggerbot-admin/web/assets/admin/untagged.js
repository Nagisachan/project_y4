$.ajax(SERVICE_URL, {
        dataType: 'json'
    })
    .done(function(data) {
        $untagged = $('#untagged');
        for (var i = 0; i < data.data.length; i++) {
            $untagged.append(htmlFromDoc(data.data[i], i));
        }
    })

function htmlFromDoc(doc, i) {
    html = `<div class="item" title="` + doc.content + `">
                <div class="right floated content">
                    <div class="ui button blue tiny">Auto</div>
                    <a href="` + FILE_URL.replace('FILEID', doc.file_id) + `"><div class="ui button gray tiny">Tag</div></a>
                    <div class="ui button red tiny">Remove</div>
                </div>
                <div class="content">
                    <span>` + (i + 1) + `</span>
                    <div class="ui label large">
                        ` + doc.name + `
                    </div>
                </div>
            </div>`;
    return html;
}