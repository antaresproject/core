<?php

/* C:\Projekty\dupa\src\core\testing\src/../fixture\resources/views/foo/bar.twig */
class __TwigTemplate_5b0dfa414b66c4c364fe9730a7b4c3684daadae7a3b61753a1f5e8e1d5e2ffae extends TwigBridge\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "C:\\Projekty\\dupa\\src\\core\\testing\\src/../fixture\\resources/views/foo/bar.twig";
    }

    public function getDebugInfo()
    {
        return array ();
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# empty Twig template #}
", "C:\\Projekty\\dupa\\src\\core\\testing\\src/../fixture\\resources/views/foo/bar.twig", "");
    }
}
