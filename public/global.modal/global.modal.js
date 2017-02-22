// error message
//0=OK,1=error,2=info
function error_message(target, message, status, timeout)
{
    var status_class = new Array();
    status_class[0] = "alert-succes";
    status_class[1] = "alert-danger";
    status_class[2] = "alert-info";
    target.html("<div class='alert " + status_class[status] + "'>" + message + "</div>");
    if (timeout > 0)
    {
        if (timeout < 500)
        {
            timeout = 3000;
        }
        setTimeout(function() {
            target.children('.alert').fadeOut(300, function() {
                $(this).remove();
            });
        }, timeout);
    }

}
var $msgModal = {};
$msgModal = $('#globalModal').modal({
                  backdrop: 'static',
                  show: false,
                  keyboard: true
            });
function modalbox(url, options)
{
    
    var defaults = {
        header: "Response Required",
        noclose: false,
        content: false,
        button: true,
        buttons: [{
                text: "OK",
                type: "success",
                click: function() {
                    alert(1);
                }
            }, {
                text: "Close",
                type: "danger",
                click: function() {
                    closeModalbox();
                }
            }]

    }
    var opts = $.extend({}, defaults, options);

    //set header
    $msgModal.find('.modal-header > h4').html(opts.header);

    //close button on the title bar
    if (opts.noclose) {
        $msgModal.find('button.close').css("display", "none");
    } else {
        $msgModal.find('button.close').css("display", "inline");
        // $('.close').click(function(){
            // location.reload();
        // });
    }

    //buttons
    var btns = opts.buttons;
    var footer = $msgModal.find(".modal-footer");
    //initiate
    footer.empty();
    if (btns.length == 0 || opts.button == false) {
        footer.hide();
    }
    else {
        footer.show();
    }
    for (var i = 0; i < btns.length; i++) {
        btns[i].type = btns[i].type || 'default';
        btn = $('<button type="button" class="btn btn-' + btns[i].type + '">' + btns[i].text + '</button>');
        btn.click(btns[i].click);
        footer.append(btn);
    }

    if (url == "" && options.content === false) {
        return;
    }

    if (url === false && options.content !== false) {
        $msgModal
                .find('.modal-body').html(options.content).end()
                .modal('show');
    } else {
         $msgModal
                .find('.modal-body').html("<h3 style='text-align:center;'><span class='fa fa-spin fa-spinner'></span></h3>").end()
                .modal('show');
        $.get(url, function(data) {
            $msgModal
                    .find('.modal-body').html(data).end()
                    .modal('show');
        }, "text");
    }
}

function closeModalbox() {
    $('#globalModal').modal('hide');
}