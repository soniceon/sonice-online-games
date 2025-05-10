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

/* pages/category.twig */
class __TwigTemplate_9e8ca710e1eb3769b7fe89bd7c3bdbeed61322f715b8cfa1db9f749c7d573b66 extends Template
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
        $this->parent = $this->loadTemplate("layouts/base.twig", "pages/category.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 3
        yield "<h2 class=\"text-3xl font-bold mb-6 text-white\">";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["category"] ?? null), "name", [], "any", false, false, false, 3), "html", null, true);
        yield "</h2>
<p class=\"mb-4 text-gray-300\">";
        // line 4
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["category"] ?? null), "description", [], "any", false, false, false, 4), "html", null, true);
        yield "</p>
<div class=\"grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4\">
    ";
        // line 6
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["games"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["game"]) {
            // line 7
            yield "    <div class=\"card relative overflow-hidden\">
        <a href=\"/game/";
            // line 8
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "slug", [], "any", false, false, false, 8), "html", null, true);
            yield "\" class=\"block relative\">
            <img src=\"";
            // line 9
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "thumbnail", [], "any", false, false, false, 9), "html", null, true);
            yield "\" alt=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "title", [], "any", false, false, false, 9), "html", null, true);
            yield "\" class=\"card-img h-60 object-cover w-full rounded-2xl rounded-b-none\">
            <div class=\"absolute top-3 right-3 z-10\">
                <button class=\"favorite-btn p-2 bg-black bg-opacity-50 rounded-full hover:bg-opacity-75 transition-colors shadow\" data-game-id=\"";
            // line 11
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "id", [], "any", false, false, false, 11), "html", null, true);
            yield "\">
                    <i class=\"fas fa-heart ";
            // line 12
            if (CoreExtension::getAttribute($this->env, $this->source, $context["game"], "is_favorite", [], "any", false, false, false, 12)) {
                yield "text-red-500";
            } else {
                yield "text-white";
            }
            yield "\"></i>
                </button>
            </div>
            <div class=\"absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-black/80 to-transparent z-0\"></div>
        </a>
        <div class=\"absolute bottom-0 left-0 right-0 p-5 z-10\">
            <h3 class=\"text-xl font-bold mb-2 text-white\">
                <a href=\"/game/";
            // line 19
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "slug", [], "any", false, false, false, 19), "html", null, true);
            yield "\" class=\"hover:text-blue-400 transition-colors\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "title", [], "any", false, false, false, 19), "html", null, true);
            yield "</a>
            </h3>
            <div class=\"flex items-center justify-between text-base text-gray-200\">
                <span>";
            // line 22
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "category", [], "any", false, false, false, 22), "html", null, true);
            yield "</span>
                <span>";
            // line 23
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "plays", [], "any", false, false, false, 23), "html", null, true);
            yield " plays</span>
            </div>
        </div>
    </div>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['game'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 28
        yield "</div>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "pages/category.twig";
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
        return array (  120 => 28,  109 => 23,  105 => 22,  97 => 19,  83 => 12,  79 => 11,  72 => 9,  68 => 8,  65 => 7,  61 => 6,  56 => 4,  51 => 3,  47 => 2,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends \"layouts/base.twig\" %}
{% block content %}
<h2 class=\"text-3xl font-bold mb-6 text-white\">{{ category.name }}</h2>
<p class=\"mb-4 text-gray-300\">{{ category.description }}</p>
<div class=\"grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4\">
    {% for game in games %}
    <div class=\"card relative overflow-hidden\">
        <a href=\"/game/{{ game.slug }}\" class=\"block relative\">
            <img src=\"{{ game.thumbnail }}\" alt=\"{{ game.title }}\" class=\"card-img h-60 object-cover w-full rounded-2xl rounded-b-none\">
            <div class=\"absolute top-3 right-3 z-10\">
                <button class=\"favorite-btn p-2 bg-black bg-opacity-50 rounded-full hover:bg-opacity-75 transition-colors shadow\" data-game-id=\"{{ game.id }}\">
                    <i class=\"fas fa-heart {% if game.is_favorite %}text-red-500{% else %}text-white{% endif %}\"></i>
                </button>
            </div>
            <div class=\"absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-black/80 to-transparent z-0\"></div>
        </a>
        <div class=\"absolute bottom-0 left-0 right-0 p-5 z-10\">
            <h3 class=\"text-xl font-bold mb-2 text-white\">
                <a href=\"/game/{{ game.slug }}\" class=\"hover:text-blue-400 transition-colors\">{{ game.title }}</a>
            </h3>
            <div class=\"flex items-center justify-between text-base text-gray-200\">
                <span>{{ game.category }}</span>
                <span>{{ game.plays }} plays</span>
            </div>
        </div>
    </div>
    {% endfor %}
</div>
{% endblock %} ", "pages/category.twig", "C:\\xampp\\htdocs\\sonice-online-games-new\\templates\\pages\\category.twig");
    }
}
