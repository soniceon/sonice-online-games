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

/* pages/game-detail.twig */
class __TwigTemplate_8ed00f8aea78f09184b141b7887b68028278b676756ede81873526d0d76edbb0 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'content' => [$this, 'block_content'],
            'extra_scripts' => [$this, 'block_extra_scripts'],
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
        $this->parent = $this->loadTemplate("layouts/base.twig", "pages/game-detail.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        yield "<div class=\"content-wrapper\" style=\"margin-top: 104px;\">
  <div class=\"flex justify-center\">
    <main class=\"main-content flex-1 p-6\">
      <!-- 大卡片包裹区 -->
      <div class=\"game-container\">
        <div class=\"title-section flex flex-col items-center justify-center mb-8\">
          <div class=\"inline-block px-8 py-2 rounded-xl font-bold text-3xl md:text-4xl text-white bg-blue-400/90 shadow-lg mb-3\" style=\"box-shadow: 0 4px 16px rgba(14,165,233,0.15);\">";
        // line 10
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["game"] ?? null), "title", [], "any", false, false, false, 10), "html", null, true);
        yield "</div>
          <div class=\"flex flex-wrap justify-center gap-2 mb-3\">
            ";
        // line 12
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, ($context["game"] ?? null), "categories", [], "any", false, false, false, 12));
        foreach ($context['_seq'] as $context["_key"] => $context["category"]) {
            // line 13
            yield "              <span class=\"inline-block px-4 py-1 rounded-full text-white text-base font-semibold bg-purple-500/90 shadow\" style=\"box-shadow: 0 2px 8px rgba(124,58,237,0.15);\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["category"], "html", null, true);
            yield "</span>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['category'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 15
        yield "          </div>
          ";
        // line 16
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["game"] ?? null), "subtitle", [], "any", false, false, false, 16)) {
            // line 17
            yield "            <div class=\"text-center text-blue-100 text-lg mb-1\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["game"] ?? null), "subtitle", [], "any", false, false, false, 17), "html", null, true);
            yield "</div>
          ";
        }
        // line 19
        yield "          <div class=\"text-center text-blue-200 text-base\">";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["game"] ?? null), "description", [], "any", false, false, false, 19), "html", null, true);
        yield "</div>
        </div>
        <div class=\"game-wrapper\" style=\"background: rgba(0,0,0,0.5); border-radius: 12px; overflow: hidden; position: relative; width: 100%; padding-bottom: 56.25%; margin: 2rem 0;\">
          <iframe id=\"game-frame\" src=\"";
        // line 22
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["game"] ?? null), "iframe_url", [], "any", false, false, false, 22), "html", null, true);
        yield "\" style=\"position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none; border-radius: 12px;\" allowfullscreen></iframe>
          <div id=\"game-loading-overlay\" class=\"absolute inset-0 bg-black/80 flex flex-col items-center justify-center z-10 rounded-lg\">
            <div class=\"w-16 h-16 border-t-4 border-blue-500 border-solid rounded-full animate-spin mb-4\"></div>
            <p class=\"text-xl text-white\">Loading Game...</p>
            <p class=\"text-sm text-gray-400 mt-2\">Please wait while the game loads</p>
            <div class=\"w-64 h-2 bg-gray-700 rounded-full mt-4 overflow-hidden\">
              <div id=\"loading-progress\" class=\"h-full bg-blue-500 rounded-full\" style=\"width: 0%\"></div>
            </div>
          </div>
        </div>
        <div class=\"flex justify-between items-center mb-6\">
          <div class=\"flex space-x-3\">
            <button id=\"fullscreen-btn\" class=\"bg-blue-primary hover:bg-blue-secondary text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors\">
              <i class=\"fas fa-expand\"></i>
              <span>Fullscreen</span>
            </button>
            <button id=\"favorite-btn\" class=\"favorite-btn bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors\">
              <i class=\"fas fa-heart\"></i>
              <span>Add to Favorites</span>
            </button>
          </div>
          <button id=\"report-btn\" class=\"text-gray-400 hover:text-white flex items-center space-x-1\">
            <i class=\"fas fa-flag\"></i>
            <span>Report Issue</span>
          </button>
        </div>
        <!-- Controls 区块 -->
        <div class=\"bg-blue-600/80 p-6 rounded-lg mb-6\">
          <h2 class=\"text-2xl font-bold mb-4 text-white\">Game Controls</h2>
          <ul class=\"text-white space-y-2 pl-4\">
            <li class=\"flex items-start\"><span class=\"mr-2\">•</span><span>Mouse Click - Click to earn points or currency</span></li>
            <li class=\"flex items-start\"><span class=\"mr-2\">•</span><span>Number Keys or Letter Keys - Purchase upgrades or activate special abilities</span></li>
            <li class=\"flex items-start\"><span class=\"mr-2\">•</span><span>Left/Right arrow keys - Navigate through menus (in some games)</span></li>
            <li class=\"flex items-start\"><span class=\"mr-2\">•</span><span>Spacebar - Pause game or activate primary function</span></li>
            <li class=\"flex items-start\"><span class=\"mr-2\">•</span><span>Z, X, C - Special actions or combo moves</span></li>
          </ul>
        </div>
        <!-- Overview 区块 -->
        <div class=\"bg-blue-600/80 p-6 rounded-lg mb-6\">
          <h2 class=\"text-2xl font-bold mb-4 text-white\">Game Overview</h2>
          <p class=\"text-white leading-relaxed mb-4\">This is a clicker/incremental game where you start small and gradually build up your resources. Click to earn points, invest in upgrades, and watch your earnings grow exponentially. The game features automatic income generation, various upgrades and achievements to unlock, and increasingly rewarding milestones to reach.</p>
          <p class=\"text-white leading-relaxed mb-6\">As you progress, you'll unlock new features and mechanics that add depth to the gameplay. Strategize your investments to maximize your earnings and compete for the highest scores. Special events and bonuses appear randomly to boost your progress!</p>
          <div class=\"grid grid-cols-1 md:grid-cols-2 gap-4 mb-6\">
            <div class=\"bg-white/10 p-4 rounded-lg\">
              <h3 class=\"text-lg font-semibold mb-2 text-white\">Key Features</h3>
              <ul class=\"text-white space-y-1 pl-4\">
                <li class=\"flex items-start\"><span class=\"mr-2\">•</span><span>Auto-clickers and passive income generators</span></li>
                <li class=\"flex items-start\"><span class=\"mr-2\">•</span><span>Multiple upgrade paths and strategies</span></li>
                <li class=\"flex items-start\"><span class=\"mr-2\">•</span><span>Achievement system with rewards</span></li>
              </ul>
            </div>
            <div class=\"bg-white/10 p-4 rounded-lg\">
              <h3 class=\"text-lg font-semibold mb-2 text-white\">Game Progress</h3>
              <ul class=\"text-white space-y-1 pl-4\">
                <li class=\"flex items-start\"><span class=\"mr-2\">•</span><span>Automatic saving of your progress</span></li>
                <li class=\"flex items-start\"><span class=\"mr-2\">•</span><span>Prestige system for advanced players</span></li>
                <li class=\"flex items-start\"><span class=\"mr-2\">•</span><span>Special weekend events with bonus rewards</span></li>
              </ul>
            </div>
          </div>
          <div class=\"flex flex-wrap gap-2\">
            <span class=\"bg-white/20 px-3 py-1 rounded-full text-sm font-medium text-white\">Clicker</span>
            <span class=\"bg-white/20 px-3 py-1 rounded-full text-sm font-medium text-white\">Incremental</span>
            <span class=\"bg-white/20 px-3 py-1 rounded-full text-sm font-medium text-white\">Idle</span>
            <span class=\"bg-white/20 px-3 py-1 rounded-full text-sm font-medium text-white\">Strategy</span>
            <span class=\"bg-white/20 px-3 py-1 rounded-full text-sm font-medium text-white\">Resource Management</span>
            <span class=\"bg-white/20 px-3 py-1 rounded-full text-sm font-medium text-white\">Casual</span>
          </div>
        </div>
        <!-- 推荐和评分区块 -->
        <div class=\"game-container glass-container\">
          <div class=\"recommended-games mb-10\">
            <h2 class=\"text-2xl font-bold mb-4 text-white\">You May Also Like</h2>
            <div class=\"grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6\" id=\"related-games\">
              <div class=\"skeleton-loader\">Loading recommended games...</div>
            </div>
          </div>
          <div class=\"rating-section bg-blue-600 p-6 rounded-lg text-center\">
            <h2 class=\"text-2xl font-bold mb-4 text-white\">Rate This Game</h2>
            <div class=\"rating-stars flex justify-center space-x-4 mb-4\">
              <span class=\"star text-4xl cursor-pointer text-white opacity-70 hover:opacity-100\" data-rating=\"1\">★</span>
              <span class=\"star text-4xl cursor-pointer text-white opacity-70 hover:opacity-100\" data-rating=\"2\">★</span>
              <span class=\"star text-4xl cursor-pointer text-white opacity-70 hover:opacity-100\" data-rating=\"3\">★</span>
              <span class=\"star text-4xl cursor-pointer text-white opacity-70 hover:opacity-100\" data-rating=\"4\">★</span>
              <span class=\"star text-4xl cursor-pointer text-white opacity-70 hover:opacity-100\" data-rating=\"5\">★</span>
            </div>
            <div class=\"rating-count text-white\">
              Average Rating: <span id=\"avgRating\">";
        // line 109
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["game"] ?? null), "rating", [], "any", true, true, false, 109)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["game"] ?? null), "rating", [], "any", false, false, false, 109), "4.5")) : ("4.5")), "html", null, true);
        yield "</span>/5
              (<span id=\"ratingCount\">";
        // line 110
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["game"] ?? null), "ratingCount", [], "any", true, true, false, 110)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["game"] ?? null), "ratingCount", [], "any", false, false, false, 110), "128")) : ("128")), "html", null, true);
        yield "</span> votes)
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
";
        return; yield '';
    }

    // line 120
    public function block_extra_scripts($context, array $blocks = [])
    {
        $macros = $this->macros;
        yield "<script>
document.addEventListener('DOMContentLoaded', function() {
  var iframe = document.getElementById('game-frame');
  var overlay = document.getElementById('game-loading-overlay');
  if (iframe && overlay) {
    var hideOverlay = function() {
      overlay.style.display = 'none';
    };
    iframe.onload = hideOverlay;
    setTimeout(hideOverlay, 10000);
  }
});
</script>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "pages/game-detail.twig";
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
        return array (  204 => 120,  190 => 110,  186 => 109,  96 => 22,  89 => 19,  83 => 17,  81 => 16,  78 => 15,  69 => 13,  65 => 12,  60 => 10,  52 => 4,  48 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends \"layouts/base.twig\" %}

{% block content %}
<div class=\"content-wrapper\" style=\"margin-top: 104px;\">
  <div class=\"flex justify-center\">
    <main class=\"main-content flex-1 p-6\">
      <!-- 大卡片包裹区 -->
      <div class=\"game-container\">
        <div class=\"title-section flex flex-col items-center justify-center mb-8\">
          <div class=\"inline-block px-8 py-2 rounded-xl font-bold text-3xl md:text-4xl text-white bg-blue-400/90 shadow-lg mb-3\" style=\"box-shadow: 0 4px 16px rgba(14,165,233,0.15);\">{{ game.title }}</div>
          <div class=\"flex flex-wrap justify-center gap-2 mb-3\">
            {% for category in game.categories %}
              <span class=\"inline-block px-4 py-1 rounded-full text-white text-base font-semibold bg-purple-500/90 shadow\" style=\"box-shadow: 0 2px 8px rgba(124,58,237,0.15);\">{{ category }}</span>
            {% endfor %}
          </div>
          {% if game.subtitle %}
            <div class=\"text-center text-blue-100 text-lg mb-1\">{{ game.subtitle }}</div>
          {% endif %}
          <div class=\"text-center text-blue-200 text-base\">{{ game.description }}</div>
        </div>
        <div class=\"game-wrapper\" style=\"background: rgba(0,0,0,0.5); border-radius: 12px; overflow: hidden; position: relative; width: 100%; padding-bottom: 56.25%; margin: 2rem 0;\">
          <iframe id=\"game-frame\" src=\"{{ game.iframe_url }}\" style=\"position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none; border-radius: 12px;\" allowfullscreen></iframe>
          <div id=\"game-loading-overlay\" class=\"absolute inset-0 bg-black/80 flex flex-col items-center justify-center z-10 rounded-lg\">
            <div class=\"w-16 h-16 border-t-4 border-blue-500 border-solid rounded-full animate-spin mb-4\"></div>
            <p class=\"text-xl text-white\">Loading Game...</p>
            <p class=\"text-sm text-gray-400 mt-2\">Please wait while the game loads</p>
            <div class=\"w-64 h-2 bg-gray-700 rounded-full mt-4 overflow-hidden\">
              <div id=\"loading-progress\" class=\"h-full bg-blue-500 rounded-full\" style=\"width: 0%\"></div>
            </div>
          </div>
        </div>
        <div class=\"flex justify-between items-center mb-6\">
          <div class=\"flex space-x-3\">
            <button id=\"fullscreen-btn\" class=\"bg-blue-primary hover:bg-blue-secondary text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors\">
              <i class=\"fas fa-expand\"></i>
              <span>Fullscreen</span>
            </button>
            <button id=\"favorite-btn\" class=\"favorite-btn bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors\">
              <i class=\"fas fa-heart\"></i>
              <span>Add to Favorites</span>
            </button>
          </div>
          <button id=\"report-btn\" class=\"text-gray-400 hover:text-white flex items-center space-x-1\">
            <i class=\"fas fa-flag\"></i>
            <span>Report Issue</span>
          </button>
        </div>
        <!-- Controls 区块 -->
        <div class=\"bg-blue-600/80 p-6 rounded-lg mb-6\">
          <h2 class=\"text-2xl font-bold mb-4 text-white\">Game Controls</h2>
          <ul class=\"text-white space-y-2 pl-4\">
            <li class=\"flex items-start\"><span class=\"mr-2\">•</span><span>Mouse Click - Click to earn points or currency</span></li>
            <li class=\"flex items-start\"><span class=\"mr-2\">•</span><span>Number Keys or Letter Keys - Purchase upgrades or activate special abilities</span></li>
            <li class=\"flex items-start\"><span class=\"mr-2\">•</span><span>Left/Right arrow keys - Navigate through menus (in some games)</span></li>
            <li class=\"flex items-start\"><span class=\"mr-2\">•</span><span>Spacebar - Pause game or activate primary function</span></li>
            <li class=\"flex items-start\"><span class=\"mr-2\">•</span><span>Z, X, C - Special actions or combo moves</span></li>
          </ul>
        </div>
        <!-- Overview 区块 -->
        <div class=\"bg-blue-600/80 p-6 rounded-lg mb-6\">
          <h2 class=\"text-2xl font-bold mb-4 text-white\">Game Overview</h2>
          <p class=\"text-white leading-relaxed mb-4\">This is a clicker/incremental game where you start small and gradually build up your resources. Click to earn points, invest in upgrades, and watch your earnings grow exponentially. The game features automatic income generation, various upgrades and achievements to unlock, and increasingly rewarding milestones to reach.</p>
          <p class=\"text-white leading-relaxed mb-6\">As you progress, you'll unlock new features and mechanics that add depth to the gameplay. Strategize your investments to maximize your earnings and compete for the highest scores. Special events and bonuses appear randomly to boost your progress!</p>
          <div class=\"grid grid-cols-1 md:grid-cols-2 gap-4 mb-6\">
            <div class=\"bg-white/10 p-4 rounded-lg\">
              <h3 class=\"text-lg font-semibold mb-2 text-white\">Key Features</h3>
              <ul class=\"text-white space-y-1 pl-4\">
                <li class=\"flex items-start\"><span class=\"mr-2\">•</span><span>Auto-clickers and passive income generators</span></li>
                <li class=\"flex items-start\"><span class=\"mr-2\">•</span><span>Multiple upgrade paths and strategies</span></li>
                <li class=\"flex items-start\"><span class=\"mr-2\">•</span><span>Achievement system with rewards</span></li>
              </ul>
            </div>
            <div class=\"bg-white/10 p-4 rounded-lg\">
              <h3 class=\"text-lg font-semibold mb-2 text-white\">Game Progress</h3>
              <ul class=\"text-white space-y-1 pl-4\">
                <li class=\"flex items-start\"><span class=\"mr-2\">•</span><span>Automatic saving of your progress</span></li>
                <li class=\"flex items-start\"><span class=\"mr-2\">•</span><span>Prestige system for advanced players</span></li>
                <li class=\"flex items-start\"><span class=\"mr-2\">•</span><span>Special weekend events with bonus rewards</span></li>
              </ul>
            </div>
          </div>
          <div class=\"flex flex-wrap gap-2\">
            <span class=\"bg-white/20 px-3 py-1 rounded-full text-sm font-medium text-white\">Clicker</span>
            <span class=\"bg-white/20 px-3 py-1 rounded-full text-sm font-medium text-white\">Incremental</span>
            <span class=\"bg-white/20 px-3 py-1 rounded-full text-sm font-medium text-white\">Idle</span>
            <span class=\"bg-white/20 px-3 py-1 rounded-full text-sm font-medium text-white\">Strategy</span>
            <span class=\"bg-white/20 px-3 py-1 rounded-full text-sm font-medium text-white\">Resource Management</span>
            <span class=\"bg-white/20 px-3 py-1 rounded-full text-sm font-medium text-white\">Casual</span>
          </div>
        </div>
        <!-- 推荐和评分区块 -->
        <div class=\"game-container glass-container\">
          <div class=\"recommended-games mb-10\">
            <h2 class=\"text-2xl font-bold mb-4 text-white\">You May Also Like</h2>
            <div class=\"grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6\" id=\"related-games\">
              <div class=\"skeleton-loader\">Loading recommended games...</div>
            </div>
          </div>
          <div class=\"rating-section bg-blue-600 p-6 rounded-lg text-center\">
            <h2 class=\"text-2xl font-bold mb-4 text-white\">Rate This Game</h2>
            <div class=\"rating-stars flex justify-center space-x-4 mb-4\">
              <span class=\"star text-4xl cursor-pointer text-white opacity-70 hover:opacity-100\" data-rating=\"1\">★</span>
              <span class=\"star text-4xl cursor-pointer text-white opacity-70 hover:opacity-100\" data-rating=\"2\">★</span>
              <span class=\"star text-4xl cursor-pointer text-white opacity-70 hover:opacity-100\" data-rating=\"3\">★</span>
              <span class=\"star text-4xl cursor-pointer text-white opacity-70 hover:opacity-100\" data-rating=\"4\">★</span>
              <span class=\"star text-4xl cursor-pointer text-white opacity-70 hover:opacity-100\" data-rating=\"5\">★</span>
            </div>
            <div class=\"rating-count text-white\">
              Average Rating: <span id=\"avgRating\">{{ game.rating|default('4.5') }}</span>/5
              (<span id=\"ratingCount\">{{ game.ratingCount|default('128') }}</span> votes)
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
{% endblock %}

{% block extra_scripts %}
<script>
document.addEventListener('DOMContentLoaded', function() {
  var iframe = document.getElementById('game-frame');
  var overlay = document.getElementById('game-loading-overlay');
  if (iframe && overlay) {
    var hideOverlay = function() {
      overlay.style.display = 'none';
    };
    iframe.onload = hideOverlay;
    setTimeout(hideOverlay, 10000);
  }
});
</script>
{% endblock %} ", "pages/game-detail.twig", "C:\\xampp\\htdocs\\sonice-online-games-new\\templates\\pages\\game-detail.twig");
    }
}
