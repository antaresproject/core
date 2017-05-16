$(document).ready(function () {
    if ($('.richtext').length) {
        CKEDITOR.editorConfig = function (config) {
            config.protectedSource.push(/<\?[\s\S]*?\?>/g);
        };
        $('.richtext').each(function (index, item) {
            CKEDITOR.replace($(item).attr('name'), {
                height: 500,
                width: '99%',
                allowedContent: true
            });
        });
    }
    $('#template-source a').click(function (e) {
        e.preventDefault();
        ckInstance = CKEDITOR.instances['content-rich'];
        if ($(this).attr('data-target') === '#template-wysiwyg') {
            ckInstance.setData($('#template-source-code').val());
        }
        if ($(this).attr('data-target') === '#template-html') {
            $('#template-source-code').val(ckInstance.getData());
        }
        $(this).tab('show');
    });
    function Inserter(textarea) {
        this.textarea = textarea;
        this.isHtmlTab = function () {
            return $('#template-source li.active a').attr('data-target') == "#template-html";
        }
        this.appendTextarea = function (data) {
            var cursorPos = textarea.prop('selectionStart');
            var v = textarea.val();
            var textBefore = v.substring(0, cursorPos);
            var textAfter = v.substring(cursorPos, v.length);
            textarea.val(textBefore + data + textAfter);
        }
        this.toggleInstruction = function (handler) {
            if (this.isHtmlTab()) {
                this.appendTextarea(handler.attr('title'));
            } else {
                var name = handler.closest('.mdl-tabs').find('textarea').attr('name');
                CKEDITOR.instances[name].insertText(handler.attr('title'));
            }
        }
        this.toggleVariable = function (handler) {
            if (this.isHtmlTab()) {
                this.appendTextarea('[[ ' + handler.attr('title') + ' ]]');
            } else {
                var name = handler.closest('.mdl-tabs').find('textarea').attr('name');
                CKEDITOR.instances[name].insertHtml('<p>[[ ' + handler.attr('title') + ' ]]</p>');
            }
        }
    }
    inserter = new Inserter($('#template-source-code'));
    $('.insert-instruction').click(function (e) {
        e.preventDefault();
        inserter.toggleInstruction($(this));
        return false;
    });
    $('.insert-variable').click(function (e) {
        e.preventDefault();
        inserter.toggleVariable($(this));
        return false;
    });
});