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

/* @PrestaShop/Admin/Sell/Order/Order/index.html.twig */
class __TwigTemplate_4bb0b0b4039328b893dec924c55bde6f59bf9960a3080ec7d3395fd289d59adf extends \Twig\Template
{
    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->blocks = [
            'orders_kpi' => [$this, 'block_orders_kpi'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 20
        return "PrestaShopBundle:Admin/Sell/Order/Order:index.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $this->parent = $this->loadTemplate("PrestaShopBundle:Admin/Sell/Order/Order:index.html.twig", "@PrestaShop/Admin/Sell/Order/Order/index.html.twig", 20);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 22
    public function block_orders_kpi($context, array $blocks = [])
    {
        // line 23
        echo "    ";
        if (( !array_key_exists("nativeStatsModulesEnabled", $context) || ($context["nativeStatsModulesEnabled"] ?? null))) {
            // line 24
            echo "        ";
            $this->displayParentBlock("orders_kpi", $context, $blocks);
            echo "
    ";
        }
    }

    public function getTemplateName()
    {
        return "@PrestaShop/Admin/Sell/Order/Order/index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  45 => 24,  42 => 23,  39 => 22,  29 => 20,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "@PrestaShop/Admin/Sell/Order/Order/index.html.twig", "C:\\xampp\\htdocs\\prestashop\\modules\\ps_metrics\\views\\PrestaShop\\Admin\\Sell\\Order\\Order\\index.html.twig");
    }
}
