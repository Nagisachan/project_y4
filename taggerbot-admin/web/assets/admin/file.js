CURRENT_SELECTED_FID = -1;
CURRENT_SELECTED_PID = -1;

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
    CURRENT_SELECTED_FID = fid;
    CURRENT_SELECTED_PID = pid;
    $('#save-tag-err').hide();
    $('.category-tag').dropdown('clear');
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
        })
}

initDropDown();