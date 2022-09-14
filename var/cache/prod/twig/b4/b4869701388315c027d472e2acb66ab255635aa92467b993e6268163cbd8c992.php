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
class __TwigTemplate_6d4469d11cc2677a7132fb1abdb0b1f9ff071c9c3099f4a236fb22d7ecb0fbd4 extends \Twig\Template
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
        // line 19
        echo "
";
        // line 20
        if ( !($context["nativeStatsModulesEnabled"] ?? null)) {
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
    }

    // line 21
    public function block_content($context, array $blocks = [])
    {
        // line 22
        echo "      <div id=\"metrics-app\"></div>
  ";
    }

    // line 25
    public function block_stylesheets($context, array $blocks = [])
    {
        // line 26
        echo "    <link rel=\"stylesheet\" href=\"";
        echo twig_escape_filter($this->env, ($context["pathMetricsAssets"] ?? null), "html", null, true);
        echo "\" type=\"text/css\" media=\"all\">

    <style>
      /** Hide native multistore module activation panel, because of visual regressions on non-bootstrap content */
      #content.nobootstrap div.bootstrap.panel {
        display: none;
      }
    </style>
  ";
    }

    // line 36
    public function block_javascripts($context, array $blocks = [])
    {
        // line 37
        echo "    <script>
      var oAuthGoogleErrorMessage = '";
        // line 38
        echo ($context["oAuthGoogleErrorMessage"] ?? null);
        echo "';
      var fullscreen = ";
        // line 39
        if (($context["fullscreen"] ?? null)) {
            echo " true ";
        } else {
            echo " false ";
        }
        echo ";
      var contextPsAccounts = ";
        // line 40
        echo twig_jsonencode_filter(($context["contextPsAccounts"] ?? null));
        echo ";
      var metricsApiUrl = '";
        // line 41
        echo ($context["metricsApiUrl"] ?? null);
        echo "';
      var metricsModule = ";
        // line 42
        echo twig_jsonencode_filter(($context["metricsModule"] ?? null));
        echo ";
      var eventBusModule = ";
        // line 43
        echo twig_jsonencode_filter(($context["eventBusModule"] ?? null));
        echo ";
      var accountsModule = ";
        // line 44
        echo twig_jsonencode_filter(($context["accountsModule"] ?? null));
        echo ";
      var graphqlEndpoint = '";
        // line 45
        echo ($context["graphqlEndpoint"] ?? null);
        echo "';
      var isoCode = '";
        // line 46
        echo ($context["isoCode"] ?? null);
        echo "';
      var currencyIsoCode = '";
        // line 47
        echo ($context["currencyIsoCode"] ?? null);
        echo "';
      var currentPage = '";
        // line 48
        echo ($context["currentPage"] ?? null);
        echo "';
      var adminToken = '";
        // line 49
        echo ($context["adminToken"] ?? null);
        echo "';
    </script>

    <script type=\"module\" src=\"";
        // line 52
        echo twig_escape_filter($this->env, ($context["pathMetricsApp"] ?? null), "html", null, true);
        echo "\" async defer></script>

    ";
        // line 54
        if ( !(null === ($context["pathMetricsAppSourceMap"] ?? null))) {
            // line 55
            echo "      <script type=\"application/json\" src=\"";
            echo twig_escape_filter($this->env, ($context["pathMetricsAppSourceMap"] ?? null), "html", null, true);
            echo "\" async defer></script>
    ";
        }
        // line 57
        echo "  ";
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
        return array (  152 => 57,  146 => 55,  144 => 54,  139 => 52,  133 => 49,  129 => 48,  125 => 47,  121 => 46,  117 => 45,  113 => 44,  109 => 43,  105 => 42,  101 => 41,  97 => 40,  89 => 39,  85 => 38,  82 => 37,  79 => 36,  65 => 26,  62 => 25,  57 => 22,  54 => 21,  49 => 36,  46 => 35,  44 => 25,  41 => 24,  38 => 21,  36 => 20,  33 => 19,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "@Modules/ps_metrics/views/templates/hook/HookBlockLegacyPages.html.twig", "C:\\xampp\\htdocs\\prestashop\\modules\\ps_metrics\\views\\templates\\hook\\HookBlockLegacyPages.html.twig");
    }
}
