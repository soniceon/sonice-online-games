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

/* pages/favorites.twig */
class __TwigTemplate_c21a00bb4d50cb715d75ddf0a7c950a899c21be897174cc994b8911bca47bc6f extends Template
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
        $this->parent = $this->loadTemplate("layouts/base.twig", "pages/favorites.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 3
        yield "<h2 class=\"text-3xl font-bold mb-6 text-white\">My Favorites</h2>
<div class=\"grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4\">
    ";
        // line 5
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["favorites"] ?? null));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["game"]) {
            // line 6
            yield "    <div class=\"card relative overflow-hidden\">
        <a href=\"/game/";
            // line 7
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "slug", [], "any", false, false, false, 7), "html", null, true);
            yield "\" class=\"block relative\">
            <img src=\"";
            // line 8
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "thumbnail", [], "any", false, false, false, 8), "html", null, true);
            yield "\" alt=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "title", [], "any", false, false, false, 8), "html", null, true);
            yield "\" class=\"card-img h-60 object-cover w-full rounded-2xl rounded-b-none\">
            <div class=\"absolute top-3 right-3 z-10\">
                <button class=\"favorite-btn p-2 bg-black bg-opacity-50 rounded-full hover:bg-opacity-75 transition-colors shadow is-favorite\" data-game-id=\"";
            // line 10
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "id", [], "any", false, false, false, 10), "html", null, true);
            yield "\">
                    <i class=\"fas fa-heart text-red-500\"></i>
                </button>
            </div>
            <div class=\"absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-black/80 to-transparent z-0\"></div>
        </a>
        <div class=\"absolute bottom-0 left-0 right-0 p-5 z-10\">
            <h3 class=\"text-xl font-bold mb-2 text-white\">
                <a href=\"/game/";
            // line 18
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "slug", [], "any", false, false, false, 18), "html", null, true);
            yield "\" class=\"hover:text-blue-400 transition-colors\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "title", [], "any", false, false, false, 18), "html", null, true);
            yield "</a>
            </h3>
            <div class=\"flex items-center justify-between text-base text-gray-200\">
                <span>";
            // line 21
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "category", [], "any", false, false, false, 21), "html", null, true);
            yield "</span>
                <span>";
            // line 22
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "plays", [], "any", false, false, false, 22), "html", null, true);
            yield " plays</span>
            </div>
        </div>
    </div>
    ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 27
            yield "    <div class=\"col-span-full text-center py-16 text-gray-400 text-xl\">No favorites yet.</div>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['game'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 29
        yield "</div>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "pages/favorites.twig";
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
        return array (  114 => 29,  107 => 27,  97 => 22,  93 => 21,  85 => 18,  74 => 10,  67 => 8,  63 => 7,  60 => 6,  55 => 5,  51 => 3,  47 => 2,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends \"layouts/base.twig\" %}
{% block content %}
<h2 class=\"text-3xl font-bold mb-6 text-white\">My Favorites</h2>
<div class=\"grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4\">
    {% for game in favorites %}
    <div class=\"card relative overflow-hidden\">
        <a href=\"/game/{{ game.slug }}\" class=\"block relative\">
            <img src=\"{{ game.thumbnail }}\" alt=\"{{ game.title }}\" class=\"card-img h-60 object-cover w-full rounded-2xl rounded-b-none\">
            <div class=\"absolute top-3 right-3 z-10\">
                <button class=\"favorite-btn p-2 bg-black bg-opacity-50 rounded-full hover:bg-opacity-75 transition-colors shadow is-favorite\" data-game-id=\"{{ game.id }}\">
                    <i class=\"fas fa-heart text-red-500\"></i>
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
    {% else %}
    <div class=\"col-span-full text-center py-16 text-gray-400 text-xl\">No favorites yet.</div>
    {% endfor %}
</div>
{% endblock %} ", "pages/favorites.twig", "C:\\xampp\\htdocs\\sonice-online-games-new\\templates\\pages\\favorites.twig");
    }
}
