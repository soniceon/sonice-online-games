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

/* pages/home.twig */
class __TwigTemplate_043803173877f98231afe7772eed8ccf84e991a64168b0a3712c8a51ce820f44 extends Template
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
        // line 2
        $context["categories"] = ($context["categories"] ?? null);
        // line 1
        $this->parent = $this->loadTemplate("layouts/base.twig", "pages/home.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 4
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 5
        yield "<!-- Featured Games -->
<section class=\"mb-12\">
    <h2 class=\"text-3xl font-bold mb-6 text-white\">Featured Games</h2>
    <div class=\"grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 xl:grid-cols-6 gap-4\">
        ";
        // line 9
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["featured_games"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["game"]) {
            // line 10
            yield "        <div class=\"card relative overflow-hidden\">
            <a href=\"";
            // line 11
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_url"] ?? null), "html", null, true);
            yield "/game/";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "slug", [], "any", false, false, false, 11), "html", null, true);
            yield "\" class=\"block relative\">
                <img src=\"";
            // line 12
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "thumbnail", [], "any", false, false, false, 12), "html", null, true);
            yield "\" alt=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "title", [], "any", false, false, false, 12), "html", null, true);
            yield "\" class=\"card-img h-60 object-cover w-full rounded-2xl rounded-b-none\">
                <div class=\"absolute top-3 right-3 z-10\">
                    <button class=\"favorite-btn p-2 bg-black bg-opacity-50 rounded-full hover:bg-opacity-75 transition-colors shadow\" data-game-id=\"";
            // line 14
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "id", [], "any", false, false, false, 14), "html", null, true);
            yield "\">
                        <i class=\"fas fa-heart ";
            // line 15
            if (CoreExtension::getAttribute($this->env, $this->source, $context["game"], "is_favorite", [], "any", false, false, false, 15)) {
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
                    <a href=\"";
            // line 22
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_url"] ?? null), "html", null, true);
            yield "/game/";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "slug", [], "any", false, false, false, 22), "html", null, true);
            yield "\" class=\"hover:text-blue-400 transition-colors\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "title", [], "any", false, false, false, 22), "html", null, true);
            yield "</a>
                </h3>
                <div class=\"flex items-center justify-between text-base text-gray-200\">
                    <span>";
            // line 25
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "category", [], "any", false, false, false, 25), "html", null, true);
            yield "</span>
                    <span>";
            // line 26
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "plays", [], "any", false, false, false, 26), "html", null, true);
            yield " plays</span>
                </div>
            </div>
        </div>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['game'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 31
        yield "    </div>
</section>

<!-- New Games -->
<section class=\"mb-12\">
    <h2 class=\"text-3xl font-bold mb-6 text-white\">New Games</h2>
    <div class=\"grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 xl:grid-cols-6 gap-4\">
        ";
        // line 38
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["new_games"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["game"]) {
            // line 39
            yield "        <div class=\"card relative overflow-hidden\">
            <a href=\"";
            // line 40
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_url"] ?? null), "html", null, true);
            yield "/game/";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "slug", [], "any", false, false, false, 40), "html", null, true);
            yield "\" class=\"block relative\">
                <img src=\"";
            // line 41
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "thumbnail", [], "any", false, false, false, 41), "html", null, true);
            yield "\" alt=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "title", [], "any", false, false, false, 41), "html", null, true);
            yield "\" class=\"card-img h-60 object-cover w-full rounded-2xl rounded-b-none\">
                <div class=\"absolute top-3 right-3 z-10\">
                    <button class=\"favorite-btn p-2 bg-black bg-opacity-50 rounded-full hover:bg-opacity-75 transition-colors shadow\" data-game-id=\"";
            // line 43
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "id", [], "any", false, false, false, 43), "html", null, true);
            yield "\">
                        <i class=\"fas fa-heart ";
            // line 44
            if (CoreExtension::getAttribute($this->env, $this->source, $context["game"], "is_favorite", [], "any", false, false, false, 44)) {
                yield "text-red-500";
            } else {
                yield "text-white";
            }
            yield "\"></i>
                    </button>
                </div>
                <div class=\"absolute top-3 left-3 z-10\">
                    <span class=\"px-2 py-1 bg-blue-600 text-white text-xs rounded-full shadow\">NEW</span>
                </div>
                <div class=\"absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-black/80 to-transparent z-0\"></div>
            </a>
            <div class=\"absolute bottom-0 left-0 right-0 p-5 z-10\">
                <h3 class=\"text-xl font-bold mb-2 text-white\">
                    <a href=\"";
            // line 54
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_url"] ?? null), "html", null, true);
            yield "/game/";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "slug", [], "any", false, false, false, 54), "html", null, true);
            yield "\" class=\"hover:text-blue-400 transition-colors\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "title", [], "any", false, false, false, 54), "html", null, true);
            yield "</a>
                </h3>
                <div class=\"flex items-center justify-between text-base text-gray-200\">
                    <span>";
            // line 57
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "category", [], "any", false, false, false, 57), "html", null, true);
            yield "</span>
                    <span>";
            // line 58
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["game"], "plays", [], "any", false, false, false, 58), "html", null, true);
            yield " plays</span>
                </div>
            </div>
        </div>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['game'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 63
        yield "    </div>
</section>

<!-- Popular Categories -->
<section class=\"mb-12\">
    <h2 class=\"text-3xl font-bold mb-6 text-white\">Popular Categories</h2>
    <div class=\"grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 xl:grid-cols-6 gap-4\">
        ";
        // line 70
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["popular_categories"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["category"]) {
            // line 71
            yield "        <a href=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_url"] ?? null), "html", null, true);
            yield "/category/";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["category"], "slug", [], "any", false, false, false, 71), "html", null, true);
            yield "\" class=\"card flex flex-col items-center justify-center p-6 text-center hover:bg-sidebar-blue transition-colors rounded-2xl\">
            <div class=\"icon-";
            // line 72
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["category"], "slug", [], "any", false, false, false, 72), "html", null, true);
            yield " w-14 h-14 mx-auto mb-3 flex items-center justify-center\">
                <i class=\"";
            // line 73
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["category"], "icon", [], "any", false, false, false, 73), "html", null, true);
            yield " text-3xl\"></i>
            </div>
            <h3 class=\"font-bold text-white\">";
            // line 75
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["category"], "name", [], "any", false, false, false, 75), "html", null, true);
            yield "</h3>
            <p class=\"text-base text-gray-200\">";
            // line 76
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["category"], "count", [], "any", false, false, false, 76), "html", null, true);
            yield " games</p>
        </a>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['category'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 79
        yield "    </div>
</section>

<!-- 头像选择弹窗 -->
<div id=\"avatar-picker-modal\" class=\"modal-backdrop hidden\">
  <div class=\"modal-content max-w-lg mx-auto p-8 rounded-2xl bg-white text-center relative\">
    <button onclick=\"hideAvatarPickerModal()\" class=\"absolute right-4 top-4 text-gray-400 hover:text-blue-600 text-2xl\"><i class=\"fas fa-times\"></i></button>
    <h2 class=\"text-2xl font-bold mb-4 text-blue-700 flex items-center justify-center\"><i class=\"fas fa-random mr-2\"></i>选择你的新头像</h2>
    <div id=\"avatar-picker-list\" class=\"grid grid-cols-3 gap-4 mb-6\"></div>
    <button onclick=\"refreshAvatarPicker()\" class=\"btn-secondary mb-2\"><i class=\"fas fa-sync-alt mr-2\"></i>再换一批</button>
    <div class=\"text-gray-400 text-xs\">头像由 DiceBear API 随机生成</div>
  </div>
</div>

<script>
function showAvatarPickerModal() {
    document.getElementById('avatar-picker-modal').classList.remove('hidden');
    refreshAvatarPicker();
}
function hideAvatarPickerModal() {
    document.getElementById('avatar-picker-modal').classList.add('hidden');
}
function refreshAvatarPicker() {
    const list = document.getElementById('avatar-picker-list');
    list.innerHTML = '';
    for (let i = 0; i < 9; i++) {
        const seed = Math.random().toString(36).substring(2, 12);
        const url = `https://api.dicebear.com/7.x/adventurer/svg?seed=\${seed}`;
        const btn = document.createElement('button');
        btn.className = 'rounded-xl overflow-hidden border-2 border-transparent hover:border-blue-500 transition-all aspect-square bg-white';
        btn.style.padding = '0';
        btn.onclick = function() { selectAvatarFromPicker(url); };
        const img = document.createElement('img');
        img.src = url;
        img.alt = 'avatar';
        img.className = 'w-20 h-20 object-cover';
        btn.appendChild(img);
        list.appendChild(btn);
    }
}
function selectAvatarFromPicker(avatar) {
    fetch('api/change_avatar.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        credentials: 'same-origin',
        body: JSON.stringify({avatar})
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            hideAvatarPickerModal();
            location.reload();
        } else {
            alert(data.message || '换头像失败');
        }
    })
    .catch(() => {
        alert('网络错误，换头像失败');
    });
}
window.showAvatarPickerModal = showAvatarPickerModal;
window.refreshAvatarPicker = refreshAvatarPicker;
window.hideAvatarPickerModal = hideAvatarPickerModal;
</script>
";
        // line 143
        yield from         $this->loadTemplate("partials/auth-modals.twig", "pages/home.twig", 143)->unwrap()->yield($context);
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "pages/home.twig";
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
        return array (  306 => 143,  240 => 79,  231 => 76,  227 => 75,  222 => 73,  218 => 72,  211 => 71,  207 => 70,  198 => 63,  187 => 58,  183 => 57,  173 => 54,  156 => 44,  152 => 43,  145 => 41,  139 => 40,  136 => 39,  132 => 38,  123 => 31,  112 => 26,  108 => 25,  98 => 22,  84 => 15,  80 => 14,  73 => 12,  67 => 11,  64 => 10,  60 => 9,  54 => 5,  50 => 4,  45 => 1,  43 => 2,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends \"layouts/base.twig\" %}
{% set categories = categories %}

{% block content %}
<!-- Featured Games -->
<section class=\"mb-12\">
    <h2 class=\"text-3xl font-bold mb-6 text-white\">Featured Games</h2>
    <div class=\"grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 xl:grid-cols-6 gap-4\">
        {% for game in featured_games %}
        <div class=\"card relative overflow-hidden\">
            <a href=\"{{ base_url }}/game/{{ game.slug }}\" class=\"block relative\">
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
                    <a href=\"{{ base_url }}/game/{{ game.slug }}\" class=\"hover:text-blue-400 transition-colors\">{{ game.title }}</a>
                </h3>
                <div class=\"flex items-center justify-between text-base text-gray-200\">
                    <span>{{ game.category }}</span>
                    <span>{{ game.plays }} plays</span>
                </div>
            </div>
        </div>
        {% endfor %}
    </div>
</section>

<!-- New Games -->
<section class=\"mb-12\">
    <h2 class=\"text-3xl font-bold mb-6 text-white\">New Games</h2>
    <div class=\"grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 xl:grid-cols-6 gap-4\">
        {% for game in new_games %}
        <div class=\"card relative overflow-hidden\">
            <a href=\"{{ base_url }}/game/{{ game.slug }}\" class=\"block relative\">
                <img src=\"{{ game.thumbnail }}\" alt=\"{{ game.title }}\" class=\"card-img h-60 object-cover w-full rounded-2xl rounded-b-none\">
                <div class=\"absolute top-3 right-3 z-10\">
                    <button class=\"favorite-btn p-2 bg-black bg-opacity-50 rounded-full hover:bg-opacity-75 transition-colors shadow\" data-game-id=\"{{ game.id }}\">
                        <i class=\"fas fa-heart {% if game.is_favorite %}text-red-500{% else %}text-white{% endif %}\"></i>
                    </button>
                </div>
                <div class=\"absolute top-3 left-3 z-10\">
                    <span class=\"px-2 py-1 bg-blue-600 text-white text-xs rounded-full shadow\">NEW</span>
                </div>
                <div class=\"absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-black/80 to-transparent z-0\"></div>
            </a>
            <div class=\"absolute bottom-0 left-0 right-0 p-5 z-10\">
                <h3 class=\"text-xl font-bold mb-2 text-white\">
                    <a href=\"{{ base_url }}/game/{{ game.slug }}\" class=\"hover:text-blue-400 transition-colors\">{{ game.title }}</a>
                </h3>
                <div class=\"flex items-center justify-between text-base text-gray-200\">
                    <span>{{ game.category }}</span>
                    <span>{{ game.plays }} plays</span>
                </div>
            </div>
        </div>
        {% endfor %}
    </div>
</section>

<!-- Popular Categories -->
<section class=\"mb-12\">
    <h2 class=\"text-3xl font-bold mb-6 text-white\">Popular Categories</h2>
    <div class=\"grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 xl:grid-cols-6 gap-4\">
        {% for category in popular_categories %}
        <a href=\"{{ base_url }}/category/{{ category.slug }}\" class=\"card flex flex-col items-center justify-center p-6 text-center hover:bg-sidebar-blue transition-colors rounded-2xl\">
            <div class=\"icon-{{ category.slug }} w-14 h-14 mx-auto mb-3 flex items-center justify-center\">
                <i class=\"{{ category.icon }} text-3xl\"></i>
            </div>
            <h3 class=\"font-bold text-white\">{{ category.name }}</h3>
            <p class=\"text-base text-gray-200\">{{ category.count }} games</p>
        </a>
        {% endfor %}
    </div>
</section>

<!-- 头像选择弹窗 -->
<div id=\"avatar-picker-modal\" class=\"modal-backdrop hidden\">
  <div class=\"modal-content max-w-lg mx-auto p-8 rounded-2xl bg-white text-center relative\">
    <button onclick=\"hideAvatarPickerModal()\" class=\"absolute right-4 top-4 text-gray-400 hover:text-blue-600 text-2xl\"><i class=\"fas fa-times\"></i></button>
    <h2 class=\"text-2xl font-bold mb-4 text-blue-700 flex items-center justify-center\"><i class=\"fas fa-random mr-2\"></i>选择你的新头像</h2>
    <div id=\"avatar-picker-list\" class=\"grid grid-cols-3 gap-4 mb-6\"></div>
    <button onclick=\"refreshAvatarPicker()\" class=\"btn-secondary mb-2\"><i class=\"fas fa-sync-alt mr-2\"></i>再换一批</button>
    <div class=\"text-gray-400 text-xs\">头像由 DiceBear API 随机生成</div>
  </div>
</div>

<script>
function showAvatarPickerModal() {
    document.getElementById('avatar-picker-modal').classList.remove('hidden');
    refreshAvatarPicker();
}
function hideAvatarPickerModal() {
    document.getElementById('avatar-picker-modal').classList.add('hidden');
}
function refreshAvatarPicker() {
    const list = document.getElementById('avatar-picker-list');
    list.innerHTML = '';
    for (let i = 0; i < 9; i++) {
        const seed = Math.random().toString(36).substring(2, 12);
        const url = `https://api.dicebear.com/7.x/adventurer/svg?seed=\${seed}`;
        const btn = document.createElement('button');
        btn.className = 'rounded-xl overflow-hidden border-2 border-transparent hover:border-blue-500 transition-all aspect-square bg-white';
        btn.style.padding = '0';
        btn.onclick = function() { selectAvatarFromPicker(url); };
        const img = document.createElement('img');
        img.src = url;
        img.alt = 'avatar';
        img.className = 'w-20 h-20 object-cover';
        btn.appendChild(img);
        list.appendChild(btn);
    }
}
function selectAvatarFromPicker(avatar) {
    fetch('api/change_avatar.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        credentials: 'same-origin',
        body: JSON.stringify({avatar})
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            hideAvatarPickerModal();
            location.reload();
        } else {
            alert(data.message || '换头像失败');
        }
    })
    .catch(() => {
        alert('网络错误，换头像失败');
    });
}
window.showAvatarPickerModal = showAvatarPickerModal;
window.refreshAvatarPicker = refreshAvatarPicker;
window.hideAvatarPickerModal = hideAvatarPickerModal;
</script>
{% include 'partials/auth-modals.twig' %}
{% endblock %} ", "pages/home.twig", "C:\\xampp\\htdocs\\sonice-online-games-new\\templates\\pages\\home.twig");
    }
}
