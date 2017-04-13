<?php

/* C:\Projekty\dupa\src\components\widgets/resources/views/templates/foo/index.twig */
class __TwigTemplate_5def888a27fd739f7bdd15da47060af843e845fb0d5af10f533b7452a5048192 extends TwigBridge\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("antares/widgets::templates.layouts.template", "C:\\Projekty\\dupa\\src\\components\\widgets/resources/views/templates/foo/index.twig", 1);
        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "antares/widgets::templates.layouts.template";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_content($context, array $blocks = array())
    {
        // line 3
        echo "    <div style=\"background-color:red;padding:10px;height:100%;\">
        ";
        // line 4
        echo ($context["content"] ?? null);
        echo "
    </div>
";
    }

    public function getTemplateName()
    {
        return "C:\\Projekty\\dupa\\src\\components\\widgets/resources/views/templates/foo/index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  34 => 4,  31 => 3,  28 => 2,  11 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("{% extends 'antares/widgets::templates.layouts.template' %}
{% block content %}
    <div style=\"background-color:red;padding:10px;height:100%;\">
        {{ content|raw }}
    </div>
{% endblock %}", "C:\\Projekty\\dupa\\src\\components\\widgets/resources/views/templates/foo/index.twig", "");
    }
}
