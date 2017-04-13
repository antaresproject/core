<?php

/* antares/widgets::templates.layouts.template */
class __TwigTemplate_0b0b03ecada456148413afb785300cddd40f1076507c9fd8112ac3ae83f81e6c extends TwigBridge\Twig\Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        if (call_user_func_array($this->env->getFunction('isAjaxRequest')->getCallable(), array())) {
            // line 2
            echo "    <div class=\"widget-ajax-response\">
    ";
        }
        // line 3
        echo "    
    ";
        // line 4
        echo call_user_func_array($this->env->getFunction('event')->getCallable(), array(("widgets:render.before.template." . ($context["template"] ?? null))));
        echo "
    ";
        // line 5
        $this->displayBlock('content', $context, $blocks);
        // line 6
        echo " 
    ";
        // line 7
        echo call_user_func_array($this->env->getFunction('event')->getCallable(), array(("widgets:render.after.template." . ($context["template"] ?? null))));
        echo "
    ";
        // line 8
        if (call_user_func_array($this->env->getFunction('isAjaxRequest')->getCallable(), array())) {
            echo " 
    </div>
";
        }
        // line 10
        echo "    
";
        // line 11
        if ((($context["enlargeable"] ?? null) &&  !call_user_func_array($this->env->getFunction('isAjaxRequest')->getCallable(), array()))) {
            // line 12
            echo "    <div class=\"is-hidden\">
        <div class=\"card card--";
            // line 13
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('str_snake')->getCallable(), array("snake", ($context["name"] ?? null))), "html", null, true);
            echo " card--enlarged ";
            echo twig_escape_filter($this->env, (($this->getAttribute(($context["modal"] ?? null), "width", array())) ? ((("w" . $this->getAttribute(($context["modal"] ?? null), "width", array())) . "p")) : ("w800")), "html", null, true);
            echo " ";
            echo twig_escape_filter($this->env, (($this->getAttribute(($context["modal"] ?? null), "height", array())) ? ((("h" . $this->getAttribute(($context["modal"] ?? null), "height", array())) . "p")) : ("h600")), "html", null, true);
            echo " ";
            echo twig_escape_filter($this->env, ((($context["card_class"] ?? null)) ? (($context["card_class"] ?? null)) : ("")), "html", null, true);
            echo "\"  data-width=\"";
            echo twig_escape_filter($this->env, $this->getAttribute(($context["modal"] ?? null), "width", array()), "html", null, true);
            echo "\"  data-height=\"";
            echo twig_escape_filter($this->env, $this->getAttribute(($context["modal"] ?? null), "height", array()), "html", null, true);
            echo "\"  data-widget-name=\"card--";
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('str_snake')->getCallable(), array("snake", ($context["name"] ?? null))), "html", null, true);
            echo "\"  data-preview-url=\"";
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('handles')->getCallable(), array(("antares::widgets/show/" . ($context["id"] ?? null)))), "html", null, true);
            echo "\"  data-id=\"";
            echo twig_escape_filter($this->env, ($context["id"] ?? null), "html", null, true);
            echo "\">
            <div class=\"card__header\">
                <div class=\"card__header-left\">
                    <span>";
            // line 16
            echo twig_escape_filter($this->env, ($context["name"] ?? null), "html", null, true);
            echo "</span>
                </div>
                <div class=\"card__header-right\">

                    ";
            // line 20
            if ((twig_length_filter($this->env, ($context["actions"] ?? null)) > 0)) {
                // line 21
                echo "                        <div class=\"ddown\">
                            <div class=\"ddown__init ddown__init--clean btn-more mdl-js-button mdl-js-ripple-effect\"><i class=\"zmdi zmdi-more-vert\"></i></div>
                            <div class=\"ddown__content\">
                                <div class=\"ddown__arrow\"></div>
                                <ul class=\"ddown__menu\">
                                    ";
                // line 26
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(($context["actions"] ?? null));
                foreach ($context['_seq'] as $context["_key"] => $context["action"]) {
                    // line 27
                    echo "                                        <li>
                                            <a class=\"mdl-js-button ";
                    // line 28
                    echo twig_escape_filter($this->env, $this->getAttribute($context["action"], "class", array()), "html", null, true);
                    echo "\" href=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["action"], "url", array()), "html", null, true);
                    echo "\" title=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["action"], "title", array()), "html", null, true);
                    echo "\" ";
                    echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('stringify')->getCallable(), array($this->getAttribute($context["action"], "attributes", array()))), "html", null, true);
                    echo ">
                                                ";
                    // line 29
                    if ((twig_length_filter($this->env, $this->getAttribute($context["action"], "icon", array())) > 0)) {
                        // line 30
                        echo "                                                    <i class=\"zmdi zmdi-";
                        echo twig_escape_filter($this->env, $this->getAttribute($context["action"], "icon", array()), "html", null, true);
                        echo "\"></i>
                                                ";
                    }
                    // line 32
                    echo "                                                <span>";
                    echo $this->getAttribute($context["action"], "title", array());
                    echo "</span>
                                            </a>
                                        </li>
                                    ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['action'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 35
                echo "                                                
                                </ul>
                            </div>
                        </div>
                    ";
            }
            // line 40
            echo "                </div>
            </div>
            <div class=\"card__content ";
            // line 42
            echo twig_escape_filter($this->env, ((($context["card_content_class"] ?? null)) ? (($context["card_content_class"] ?? null)) : ("")), "html", null, true);
            echo "\" data-scrollable></div>
        </div>
    </div>
";
        }
        // line 45
        echo "    ";
    }

    // line 5
    public function block_content($context, array $blocks = array())
    {
        // line 6
        echo "    ";
    }

    public function getTemplateName()
    {
        return "antares/widgets::templates.layouts.template";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  155 => 6,  152 => 5,  148 => 45,  141 => 42,  137 => 40,  130 => 35,  119 => 32,  113 => 30,  111 => 29,  101 => 28,  98 => 27,  94 => 26,  87 => 21,  85 => 20,  78 => 16,  56 => 13,  53 => 12,  51 => 11,  48 => 10,  42 => 8,  38 => 7,  35 => 6,  33 => 5,  29 => 4,  26 => 3,  22 => 2,  20 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("{% if isAjaxRequest() %}
    <div class=\"widget-ajax-response\">
    {% endif %}    
    {{ event('widgets:render.before.template.'~template)|raw }}
    {% block content %}
    {% endblock %} 
    {{ event('widgets:render.after.template.'~template)|raw }}
    {% if isAjaxRequest() %} 
    </div>
{% endif %}    
{% if enlargeable and not isAjaxRequest() %}
    <div class=\"is-hidden\">
        <div class=\"card card--{{ name|str_snake }} card--enlarged {{ modal.width?'w'~modal.width~'p':'w800' }} {{ modal.height?'h'~modal.height~'p':'h600' }} {{ card_class ? card_class : ''  }}\"  data-width=\"{{ modal.width }}\"  data-height=\"{{ modal.height}}\"  data-widget-name=\"card--{{ name|str_snake }}\"  data-preview-url=\"{{ handles('antares::widgets/show/'~id) }}\"  data-id=\"{{id}}\">
            <div class=\"card__header\">
                <div class=\"card__header-left\">
                    <span>{{ name }}</span>
                </div>
                <div class=\"card__header-right\">

                    {% if actions|length>0 %}
                        <div class=\"ddown\">
                            <div class=\"ddown__init ddown__init--clean btn-more mdl-js-button mdl-js-ripple-effect\"><i class=\"zmdi zmdi-more-vert\"></i></div>
                            <div class=\"ddown__content\">
                                <div class=\"ddown__arrow\"></div>
                                <ul class=\"ddown__menu\">
                                    {% for action in actions %}
                                        <li>
                                            <a class=\"mdl-js-button {{ action.class }}\" href=\"{{ action.url }}\" title=\"{{ action.title }}\" {{ action.attributes|stringify }}>
                                                {% if action.icon|length>0 %}
                                                    <i class=\"zmdi zmdi-{{ action.icon }}\"></i>
                                                {% endif %}
                                                <span>{{ action.title|raw }}</span>
                                            </a>
                                        </li>
                                    {% endfor %}                                                
                                </ul>
                            </div>
                        </div>
                    {% endif %}
                </div>
            </div>
            <div class=\"card__content {{ card_content_class ? card_content_class : ''  }}\" data-scrollable></div>
        </div>
    </div>
{% endif %}    ", "antares/widgets::templates.layouts.template", "");
    }
}
