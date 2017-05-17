<?php

/**
 * Part of the Antares package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Antares Core
 * @version    0.9.0
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


namespace Antares\Html\Control;

use Antares\Contracts\Html\Form\Field as FieldContract;
use Antares\Html\HtmlBuilder as BaseHtmlBuilder;
use Antares\Html\FormBuilder as BaseFormBuilder;
use Antares\Asset\Factory as AssetFactory;
use Antares\Asset\JavaScriptDecorator;
use Antares\Asset\JavaScriptExpression;
use RuntimeException;

class Dropzone
{

    /**
     * field instance
     *
     * @var FieldContract
     */
    protected $field;

    /**
     * Asset factory.
     *
     * @var \Antares\Asset\Factory
     */
    protected $asset;

    /**
     * Html builder.
     *
     * @var \Antares\Html\HtmlBuilder
     */
    protected $html;

    /**
     * Form builder.
     *
     * @var \Antares\Html\FormBuilder
     */
    protected $form;

    /**
     * constructing
     * 
     * @param FieldContract $field
     * @param AssetFactory $assetFactory
     * @param \Antares\Html\Control\BaseHtmlBuilder $html
     * @param \Antares\Html\Control\BaseFormBuilder $form
     */
    public function __construct(FieldContract $field, AssetFactory $assetFactory, BaseHtmlBuilder $html, BaseFormBuilder $form)
    {
        $this->field = $field;
        $this->html  = $html;
        $this->form  = $form;
        $this->asset = $assetFactory;
    }

    /**
     * create dropzone field type
     * 
     * @return type
     */
    public function render()
    {
        $dropzoneAttributes = array_diff_key($this->field->attributes, array_flip(['class']));

        if (!isset($dropzoneAttributes['container'])) {
            throw new RuntimeException('Invalid Dropzone Container Id');
        }
        $this->field->attributes = ['class' => $this->field->attributes['class']];

        $id             = $dropzoneAttributes['container'];
        $view           = isset($dropzoneAttributes['view']) ? $dropzoneAttributes['view'] : 'antares/foundation::widgets.forms.dropzone';
        $name           = $dropzoneAttributes['paramName'];
        $successDefault = <<<CBALL
        this.on("success", function(file,response) {        
            $('#$id').parent().find('input[name=$name]').val(response.path);
            %s 
            %s    
        }); 
CBALL;
        $afterLoad      = isset($dropzoneAttributes['previewsContainer']) ? $this->onSuccessDefault($id) : '';
        $onSuccess      = isset($dropzoneAttributes['onSuccess']) ? $dropzoneAttributes['onSuccess'] : '';
        $afterUpload    = sprintf($successDefault, $afterLoad, $onSuccess);


        $params = array_merge([
            'autoProcessQueue' => true,
            'uploadMultiple'   => false,
            'maxFiles'         => 100,
            'thumbnailWidth'   => 120,
            'thumbnailHeight'  => 120,
            'parallelUploads'  => 20,
            'init'             => new JavaScriptExpression(sprintf($this->onInit($dropzoneAttributes), $afterUpload))], $dropzoneAttributes);


        $cball        = <<<CBALL
(function(window,$){Dropzone.autoDiscover = false; Dropzone.options.%s = %s; %s })(window,jQuery);

CBALL;
        $attach       = <<<CBALL
                     $('#{$id}').dropzone(Dropzone.options.$id);
CBALL;
        $inlineScript = sprintf($cball, $id, JavaScriptDecorator::decorate(array_except($params, ['view', 'container', 'onSuccess'])), $attach);
        $this->scripts()->inlineScript('dropzone-' . $id, $inlineScript);
        $attributes   = $this->html->decorate($this->field->get('attributes'), ['class' => 'form-control']);
        $input        = $this->form->input('hidden', $params['paramName'], $this->field->get('value'), ['id' => $id . 'Container']);
        return view($view, ['field' => $this->field, 'attributes' => $attributes, 'params' => $params, 'input' => $input, 'id' => $id]);
    }

    /**
     * on init dropzone
     * 
     * @return String
     */
    protected function onInit(array $attributes = [])
    {
        $return = 'function() { ' . $this->onAddedFile() . (isset($attributes['previewsContainer']) ? $this->onButtonClick() : '' ) . ' %s }';
        return $return;
    }

    /**
     * on add file
     * 
     * @return String
     */
    protected function onAddedFile()
    {
        return <<<EOD
   var srcBase = [];
   this.on("addedfile", function (file) {                            
                                    var read = new FileReader();
                                    read.readAsDataURL(file);
                                    read.onloadend = function () {
                                        srcBase.push(read.result);
                                    }
                                    var image = $(this.element).find('.dz-image img');
                                    image.hide();
                                    setTimeout(function () {
                                        image.attr('src', srcBase[0]);
                                        image.show();
                                    }, 100);
                            });
EOD;
    }

    /**
     * when custom upload button available
     * 
     * @return type
     */
    protected function onButtonClick()
    {
        return <<<EOD
    var myDropzone = this;
    this.element.querySelector("button#DropZoneUploadButton").addEventListener("click", function(e) {                               
      e.preventDefault();
      e.stopPropagation();
      myDropzone.processQueue();
      return false;
    });
    this.on("addedfile", function(file) {
        $('#DropZoneUploadButton').removeClass('hidden');
        $('#DropZoneAfterUploadContainer').html("");
    });     
EOD;
    }

    /**
     * default on success upload
     * 
     * @param mixed $id
     * @return String
     */
    protected function onSuccessDefault($id)
    {
        return <<<CBALL
            if(response.html!==undefined){
                  $('#DropZoneAfterUploadContainer').html(response.html);              
            }
            $('#{$id}').find("button[type=submit]").removeClass('btn-disabled').removeAttr('disabled');        
CBALL;
    }

    /**
     * add dropzone scripts
     * 
     * @return \Antares\Asset\Asset
     */
    protected function scripts()
    {
        $container   = $this->asset->container('antares/foundation::scripts');
        $scriptsPath = config('antares/html::form.dropzone', []);
        foreach ($scriptsPath as $key => $script) {
            $container->add($key, $script);
        }
        return $container;
    }

}
