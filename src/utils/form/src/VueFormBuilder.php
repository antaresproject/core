<?php

namespace Antares\Form;

use App;
use Antares\Contracts\Html\Form\Factory;
use Antares\Html\Form\Grid as FormGrid;
use Antares\Html\Form\FormBuilder;
use Closure;
use Illuminate\Support\Str;
use InvalidArgumentException;

class VueFormBuilder {

    /**
     * @var string
     */
    protected $action;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var array
     */
    protected $dataProviders = [];

    /**
     * @var Factory|null
     */
    protected $form;

    /**
     * VueFormBuilder constructor.
     * @param string $action
     * @param string $method
     */
    public function __construct(string $action, string $method = 'POST') {
        $this->action   = $action;
        $this->method   = $method;
    }

    /**
     * @param string $variable
     * @param string $value
     * @throws InvalidArgumentException
     */
    public function addDataProvider(string $variable, string $value) {
        if($variable === '') {
            throw new InvalidArgumentException('The given variable is empty.');
        }

        $this->dataProviders[$variable] = $value;
    }

    /**
     * @param array $dataProviders
     * @throws InvalidArgumentException
     */
    public function setDataProviders(array $dataProviders) {
        foreach($dataProviders as $variable => $value) {
            $this->addDataProvider($variable, $value);
        }
    }

    /**
     * @param string $name
     * @param Closure $callback
     * @return FormBuilder
     */
    public function build(string $name, Closure $callback) : FormBuilder {
        publish('billevio_base', ['js/select2.js', 'js/form_errors.js', 'js/form_mixin.js', 'js/vue-the-mask.js', 'js/v-money.js']);

        $form = $this->form()->of($name, function(FormGrid $formGrid) use($name, $callback) {
            $formAttributes = [
                'id'        => $this->getIdOfName($name),
                'method'    => $this->method,
                '@keydown'  => 'errors.clear($event.target.name)',
            ];

            foreach($this->dataProviders as $variable => $value) {
                $formAttributes['data-provider-' . $variable] = $value;
            }

            $formGrid->simple($this->action, $formAttributes);
            $formGrid->name($name);

            $callback($formGrid);
        });

        if($form instanceof FormBuilder) {
            return $form;
        }
    }

    /**
     * @return Factory
     */
    public function form() : Factory {
        if($this->form === null) {
            $this->form = App::make(Factory::class);
        }

        return $this->form;
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getIdOfName(string $name) : string {
        $search     = ['.', ','];
        $replace    = ['-', '-'];

        return Str::slug('form-' . str_replace($search, $replace, $name));
    }

}
