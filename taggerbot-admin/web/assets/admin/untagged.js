NO_CATEGORY_HTML = `<div id="no-doc" class="inline field">
                        <div class="ui left pointing red basic label">
                            You don't have any document, go to "Upload" page first.</div>
                        </div>
                    </div>`;

function getUntaggedDoc() {
    $.ajax(SERVICE_URL, {
            dataType: 'json'
        })
        .done(function(data) {
            $untagged = $('#untagged');
            $untagged.html('');

            for (var i = 0; i < data.data.length; i++) {
                $untagged.append(htmlFromDoc(data.data[i], i));
            }
            $('.actions.ui.dropdown').dropdown({ on: 'hover' });

            if (data.data.length == 0) {
                $untagged.append(NO_CATEGORY_HTML);
            }
        })
}

function htmlFromDoc(doc, i) {
    html = `<div class="item" title="` + doc.content + `">
                <div class="right floated content">
                    <div class="ui teal buttons tiny">
                        <div class="ui button" onclick="javascript:predictDoc(` + doc.file_id + `,this)">Auto</div>
                        <div class="actions ui floating dropdown icon button">
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

function predictDoc(fileId, e) {
    $(e).addClass('loading');
    $.ajax(PREDICT_URL.replace('FILEID', fileId), {
            dataType: 'json',
        })
        .done(function(data) {
            console.log(data);
            getUntaggedDoc();
        })
        .always(function() {
            $(e).removeClass('loading');
        })
}

getUntaggedDoc();