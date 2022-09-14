<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* @Modules/ps_metrics/views/templates/hook/HookBlockLegacyPages.html.twig */
class __TwigTemplate_a669a4cde09ef29c4d518280cf6d48ae733848cd2086ba75f20545b2715f1019 extends \Twig\Template
{
    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
            'content' => [$this, 'block_content'],
            'stylesheets' => [$this, 'block_stylesheets'],
            'javascripts' => [$this, 'block_javascripts'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->env->getExtension("Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension");
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@Modules/ps_metrics/views/templates/hook/HookBlockLegacyPages.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@Modules/ps_metrics/views/templates/hook/HookBlockLegacyPages.html.twig"));

        // line 19
        echo "
";
        // line 20
        if ( !($context["nativeStatsModulesEnabled"] ?? $this->getContext($context, "nativeStatsModulesEnabled"))) {
            // line 21
            echo "  ";
            $this->displayBlock('content', $context, $blocks);
            // line 24
            echo "
  ";
            // line 25
            $this->displayBlock('stylesheets', $context, $blocks);
            // line 35
            echo "
  ";
            // line 36
            $this->displayBlock('javascripts', $context, $blocks);
        }
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    // line 21
    public function block_content($context, array $blocks = [])
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->env->getExtension("Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension");
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "content"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "content"));

        // line 22
        echo "      <div id=\"metrics-app\"></div>
  ";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    // line 25
    public function block_stylesheets($context, array $blocks = [])
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->env->getExtension("Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension");
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "stylesheets"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "stylesheets"));

        // line 26
        echo "    <link rel=\"stylesheet\" href=\"";
        echo twig_escape_filter($this->env, ($context["pathMetricsAssets"] ?? $this->getContext($context, "pathMetricsAssets")), "html", null, true);
        echo "\" type=\"text/css\" media=\"all\">

    <style>
      /** Hide native multistore module activation panel, because of visual regressions on non-bootstrap content */
      #content.nobootstrap div.bootstrap.panel {
        display: none;
      }
    </style>
  ";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    // line 36
    public function block_javascripts($context, array $blocks = [])
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->env->getExtension("Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension");
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "javascripts"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "javascripts"));

        // line 37
        echo "    <script>
      var oAuthGoogleErrorMessage = '";
        // line 38
        echo ($context["oAuthGoogleErrorMessage"] ?? $this->getContext($context, "oAuthGoogleErrorMessage"));
        echo "';
      var fullscreen = ";
        // line 39
        if (($context["fullscreen"] ?? $this->getContext($context, "fullscreen"))) {
            echo " true ";
        } else {
            echo " false ";
        }
        echo ";
      var contextPsAccounts = ";
        // line 40
        echo twig_jsonencode_filter(($context["contextPsAccounts"] ?? $this->getContext($context, "contextPsAccounts")));
        echo ";
      var metricsApiUrl = '";
        // line 41
        echo ($context["metricsApiUrl"] ?? $this->getContext($context, "metricsApiUrl"));
        echo "';
      var metricsModule = ";
        // line 42
        echo twig_jsonencode_filter(($context["metricsModule"] ?? $this->getContext($context, "metricsModule")));
        echo ";
      var eventBusModule = ";
        // line 43
        echo twig_jsonencode_filter(($context["eventBusModule"] ?? $this->getContext($context, "eventBusModule")));
        echo ";
      var accountsModule = ";
        // line 44
        echo twig_jsonencode_filter(($context["accountsModule"] ?? $this->getContext($context, "accountsModule")));
        echo ";
      var graphqlEndpoint = '";
        // line 45
        echo ($context["graphqlEndpoint"] ?? $this->getContext($context, "graphqlEndpoint"));
        echo "';
      var isoCode = '";
        // line 46
        echo ($context["isoCode"] ?? $this->getContext($context, "isoCode"));
        echo "';
      var currencyIsoCode = '";
        // line 47
        echo ($context["currencyIsoCode"] ?? $this->getContext($context, "currencyIsoCode"));
        echo "';
      var currentPage = '";
        // line 48
        echo ($context["currentPage"] ?? $this->getContext($context, "currentPage"));
        echo "';
      var adminToken = '";
        // line 49
        echo ($context["adminToken"] ?? $this->getContext($context, "adminToken"));
        echo "';
    </script>

    <script type=\"module\" src=\"";
        // line 52
        echo twig_escape_filter($this->env, ($context["pathMetricsApp"] ?? $this->getContext($context, "pathMetricsApp")), "html", null, true);
        echo "\" async defer></script>

    ";
        // line 54
        if ( !(null === ($context["pathMetricsAppSourceMap"] ?? $this->getContext($context, "pathMetricsAppSourceMap")))) {
            // line 55
            echo "      <script type=\"application/json\" src=\"";
            echo twig_escape_filter($this->env, ($context["pathMetricsAppSourceMap"] ?? $this->getContext($context, "pathMetricsAppSourceMap")), "html", null, true);
            echo "\" async defer></script>
    ";
        }
        // line 57
        echo "  ";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function getTemplateName()
    {
        return "@Modules/ps_metrics/views/templates/hook/HookBlockLegacyPages.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  194 => 57,  188 => 55,  186 => 54,  181 => 52,  175 => 49,  171 => 48,  167 => 47,  163 => 46,  159 => 45,  155 => 44,  151 => 43,  147 => 42,  143 => 41,  139 => 40,  131 => 39,  127 => 38,  124 => 37,  115 => 36,  95 => 26,  86 => 25,  75 => 22,  66 => 21,  55 => 36,  52 => 35,  50 => 25,  47 => 24,  44 => 21,  42 => 20,  39 => 19,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("{#**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *#}

{% if not nativeStatsModulesEnabled %}
  {% block content %}
      <div id=\"metrics-app\"></div>
  {% endblock %}

  {% block stylesheets %}
    <link rel=\"stylesheet\" href=\"{{ pathMetricsAssets }}\" type=\"text/css\" media=\"all\">

    <style>
      /** Hide native multistore module activation panel, because of visual regressions on non-bootstrap content */
      #content.nobootstrap div.bootstrap.panel {
        display: none;
      }
    </style>
  {% endblock %}

  {% block javascripts %}
    <script>
      var oAuthGoogleErrorMessage = '{{ oAuthGoogleErrorMessage|raw }}';
      var fullscreen = {% if fullscreen %} true {% else %} false {% endif %};
      var contextPsAccounts = {{ contextPsAccounts|json_encode|raw }};
      var metricsApiUrl = '{{ metricsApiUrl|raw }}';
      var metricsModule = {{ metricsModule|json_encode|raw }};
      var eventBusModule = {{ eventBusModule|json_encode|raw }};
      var accountsModule = {{ accountsModule|json_encode|raw }};
      var graphqlEndpoint = '{{ graphqlEndpoint|raw }}';
      var isoCode = '{{ isoCode|raw }}';
      var currencyIsoCode = '{{ currencyIsoCode|raw }}';
      var currentPage = '{{ currentPage|raw }}';
      var adminToken = '{{ adminToken|raw }}';
    </script>

    <script type=\"module\" src=\"{{ pathMetricsApp }}\" async defer></script>

    {% if pathMetricsAppSourceMap is not null %}
      <script type=\"application/json\" src=\"{{ pathMetricsAppSourceMap }}\" async defer></script>
    {% endif %}
  {% endblock %}
{% endif %}
", "@Modules/ps_metrics/views/templates/hook/HookBlockLegacyPages.html.twig", "C:\\xampp\\htdocs\\prestashop\\modules\\ps_metrics\\views\\templates\\hook\\HookBlockLegacyPages.html.twig");
    }
}
