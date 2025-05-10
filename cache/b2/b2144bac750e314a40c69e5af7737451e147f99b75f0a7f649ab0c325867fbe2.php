<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* pages/404.twig */
class __TwigTemplate_8eed4fdce3f8b2565bfe096f9ad5c4ae63673c9a9dc58fa80fe92d8d70b1b642 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "layouts/base.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $this->parent = $this->loadTemplate("layouts/base.twig", "pages/404.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        yield "<div class=\"text-center py-32\">
    <h1 class=\"text-6xl font-bold text-blue-500 mb-4\">404</h1>
    <p class=\"text-xl text-gray-300 mb-8\">Page Not Found</p>
    <a href=\"/\" class=\"btn-primary\">Back to Home</a>
</div>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "pages/404.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable()
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  47 => 2,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends \"layouts/base.twig\" %}
{% block content %}
<div class=\"text-center py-32\">
    <h1 class=\"text-6xl font-bold text-blue-500 mb-4\">404</h1>
    <p class=\"text-xl text-gray-300 mb-8\">Page Not Found</p>
    <a href=\"/\" class=\"btn-primary\">Back to Home</a>
</div>
{% endblock %} ", "pages/404.twig", "C:\\xampp\\htdocs\\sonice-online-games-new\\templates\\pages\\404.twig");
    }
}
