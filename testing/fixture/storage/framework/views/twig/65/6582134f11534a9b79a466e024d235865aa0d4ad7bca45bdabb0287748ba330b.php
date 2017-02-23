<?php

/* C:\Projekty\project\src\core\testing\src/../fixture\resources/views/foo/bar.twig */
class __TwigTemplate_7ff3cc050243f1e5819012c342567ae094d9ca1bbab9c0f144a13eafe5e4d1a5 extends TwigBridge\Twig\Template
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
        return "C:\\Projekty\\project\\src\\core\\testing\\src/../fixture\\resources/views/foo/bar.twig";
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
", "C:\\Projekty\\project\\src\\core\\testing\\src/../fixture\\resources/views/foo/bar.twig", "");
    }
}
