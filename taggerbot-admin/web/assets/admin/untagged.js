$.ajax(SERVICE_URL, {
        dataType: 'json'
    })
    .done(function(data) {
        $untagged = $('#untagged');
        for (var i = 0; i < data.data.length; i++) {
            $untagged.append(htmlFromDoc(data.data[i], i));
        }
        $('.ui.dropdown').dropdown();
    })

function htmlFromDoc(doc, i) {
    html = `<div class="item" title="` + doc.content + `">
                <div class="right floated content">
                    <div class="ui teal buttons tiny">
                        <div class="ui button">Auto</div>
                        <div class="ui floating dropdown icon button">
                            <i class="dropdown icon"></i>
                            <div class="menu">
                                <div class="item" onclick="javascript:filePage('` + FILE_URL.replace('FILEID', doc.file_id) + `')"><i class="edit icon green"></i> Tag</div>
                                <div class="item"><a><i class="delete icon red"></i> Remove</a></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content">
                    <span>` + (i + 1) + `</span>
                    <a href="` + FILE_URL.replace('FILEID', doc.file_id) + `"><div class="ui label large">
                        ` + doc.name + `
                    </div></a>
                </div>
            </div>`;
    return html;
}

function filePage(url) {
    location = url;
}