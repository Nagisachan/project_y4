CURRENT_SELECTED_FID = -1;
CURRENT_SELECTED_PID = -1;

function getUntaggedList() {
    $untagged = $('tbody');
    $untagged.prepend(`<tr><td colspan="10"><div class="ui active centered inline loader"></div></td></tr>`);
    $.ajax(SERVICE_URL.replace('FILEID', FILE_ID), {
            dataType: 'json'
        })
        .done(function(data) {
            $untagged.html("");
            $count = 0;
            for (var i = 0; i < data.data.length; i++) {
                // if ((data.data[i]).tags == null) {
                //     $untagged.append(htmlFromParagraph(data.data[i], i));
                //     $count++;
                // }
                $untagged.append(htmlFromParagraph(data.data[i], i));
                $count++;
            }

            if ($count == 0) {
                $untagged.append(`
            <tr>
                <td colspan="10">
                    <div class="table-error-msg ui negative message">
                        <div class="header aligned center">
                            No untagged paragraph
                        </div>
                        <p>You shouldn't see this document in untagged document list now</p>
                    </div>
                </td>
            </tr>`);
            }
        })
}

function addTag(fid, pid, tags) {
    CURRENT_SELECTED_FID = fid;
    CURRENT_SELECTED_PID = pid;
    $('#save-tag-err').hide();
    $('.category-tag').dropdown('clear');
    $('.ui.modal').modal('show');

    if (tags) {
        loadValue(tags.split(","));
    }
}

function htmlFromParagraph(paragraph, i) {
    html = `<tr>
                <td>
                    <div class="ui center aligned">` + paragraph.paragraph_id + `</div>
                </td>
                <td title="` + (paragraph.tags ? paragraph.tags : '') + `">
                    ` + paragraph.content + `
                </td>
                <td class="right aligned">
                    <div class="ui left aligned">` + (paragraph.tags ? paragraph.tag_texts.join(",<br/>") : '') + `</div>
                </td>
                <td>
                    <div class="single line content ui center aligned">
                        <div class="ui button ` + (paragraph.tags ? 'blue' : 'gray') + ` tiny" title="` + (paragraph.tags ? 'Has tag(s)' : 'No tag') + `" onclick="addTag(` + paragraph.file_id + `,` + paragraph.paragraph_id + `,'` + paragraph.tags + `');">` + (paragraph.tags ? 'Edit' : 'Tag') + `</div>
                        <div class="ui button red tiny" onclick="removeParagraph(` + paragraph.file_id + `,` + paragraph.paragraph_id + `,this)">Remove</div>
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
            //allowAdditions: true,
            className: { label: 'ui label ' + $dropdown.attr('color') }
        });
    }
}

function updateTag() {
    tags = []
    $caterogies = $('.category-tag');
    $caterogies.each(function(i, e) {
        $e = $(e);
        if ($e.dropdown('get value') != null) {
            $e.dropdown('get value').forEach(function(tag) {
                tags.push(tag);
            });
        }
    });

    updateTagWithServer(CURRENT_SELECTED_FID, CURRENT_SELECTED_PID, tags);
}

function updateTagWithServer(fid, pid, tags) {
    $('#save-tag').addClass('loading');
    $('#save-tag-err').hide();
    $.ajax(SERVICE_URL_UPDATE.replace('FILEID', fid).replace('PARAGRAPHID', pid), {
            dataType: 'json',
            type: 'post',
            data: {
                tags: tags,
            }
        })
        .done(function(data) {
            $('.ui.modal').modal('hide');
        })
        .error(function(err) {
            $('#save-tag-err').css('display', 'inline');
        })
        .always(function() {
            $('#save-tag').removeClass('loading');
            getUntaggedList();
        })
}

function removeParagraph(fid, pid, e) {
    $(e).addClass('loading');
    $.ajax(REMOVE_URL.replace('FILEID', fid).replace('PARAGRAPHID', pid), {
            dataType: 'json',
        })
        .done(function(data) {
            getUntaggedList();
        })
        .error(function(err) {})
        .always(function() {
            $(e).removeClass('loading');
        })
}

function loadValue(tags) {
    $caterogies = $('.category-tag');
    $caterogies.each(function(i, e) {
        $e = $(e);
        $e.dropdown('set exactly', tags)
    });
}

getUntaggedList();
initDropDown();