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

/* @PrestaShop/Admin/Module/Includes/sorting.html.twig */
class __TwigTemplate_506dc99af390bbec8d74940b193c7a2d5a2ecb20d81b19b93a4d024666791449 extends \Twig\Template
{
    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->env->getExtension("Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension");
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PrestaShop/Admin/Module/Includes/sorting.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "@PrestaShop/Admin/Module/Includes/sorting.html.twig"));

        // line 25
        echo "<div class=\"module-sorting-menu\">
  <div class=\"row\">
    <div class=\"col-lg-6\">
      <div class=\"module-sorting-search-wording\">
        <span id=\"selected_modules\" class=\"module-search-result-title module-search-result-wording\">";
        // line 29
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("%nbModules% modules and services selected for you", ["%nbModules%" => ($context["totalModules"] ?? $this->getContext($context, "totalModules"))], "Admin.Modules.Feature"), "html", null, true);
        echo "</span>
        ";
        // line 30
        $this->loadTemplate("@Common/HelpBox/helpbox.html.twig", "@PrestaShop/Admin/Module/Includes/sorting.html.twig", 30)->display(twig_array_merge($context, ["content" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Selection", [], "Admin.Modules.Feature"), "title" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Customize your store with this selection of modules recommended for your shop, based on your country, language and version of PrestaShop. It includes the most popular modules from our Addons marketplace, and free partner modules.", [], "Admin.Modules.Help")]));
        // line 31
        echo "      </div>
    </div>
    <div class=\"col-lg-6\">
      <div class=\"module-sorting module-sorting-author float-right\">
        <select id=\"sort_module\" class=\"custom-select sort-component\">
          <option value=\"\" disabled selected>- ";
        // line 36
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Sort by", [], "Admin.Actions"), "html", null, true);
        echo " -</option>
          <option value=\"name\">";
        // line 37
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Name", [], "Admin.Global"), "html", null, true);
        echo "</option>
          <option value=\"price\">";
        // line 38
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Increasing Price", [], "Admin.Modules.Feature"), "html", null, true);
        echo "</option>
          <option value=\"price-desc\">";
        // line 39
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Decreasing Price", [], "Admin.Modules.Feature"), "html", null, true);
        echo "</option>
          <option value=\"scoring-desc\">";
        // line 40
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Popularity", [], "Admin.Modules.Feature"), "html", null, true);
        echo "</option>
        </select>
      </div>
    </div>
  </div>
</div>
";
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    public function getTemplateName()
    {
        return "@PrestaShop/Admin/Module/Includes/sorting.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  71 => 40,  67 => 39,  63 => 38,  59 => 37,  55 => 36,  48 => 31,  46 => 30,  42 => 29,  36 => 25,);
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
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *#}
<div class=\"module-sorting-menu\">
  <div class=\"row\">
    <div class=\"col-lg-6\">
      <div class=\"module-sorting-search-wording\">
        <span id=\"selected_modules\" class=\"module-search-result-title module-search-result-wording\">{{ '%nbModules% modules and services selected for you'|trans({'%nbModules%' : totalModules}, 'Admin.Modules.Feature') }}</span>
        {% include '@Common/HelpBox/helpbox.html.twig' with { 'content' : \"Selection\"|trans({}, 'Admin.Modules.Feature'), 'title' : \"Customize your store with this selection of modules recommended for your shop, based on your country, language and version of PrestaShop. It includes the most popular modules from our Addons marketplace, and free partner modules.\"|trans({}, 'Admin.Modules.Help') } %}
      </div>
    </div>
    <div class=\"col-lg-6\">
      <div class=\"module-sorting module-sorting-author float-right\">
        <select id=\"sort_module\" class=\"custom-select sort-component\">
          <option value=\"\" disabled selected>- {{ 'Sort by'|trans({}, 'Admin.Actions') }} -</option>
          <option value=\"name\">{{ 'Name'|trans({}, 'Admin.Global') }}</option>
          <option value=\"price\">{{ 'Increasing Price'|trans({}, 'Admin.Modules.Feature') }}</option>
          <option value=\"price-desc\">{{ 'Decreasing Price'|trans({}, 'Admin.Modules.Feature') }}</option>
          <option value=\"scoring-desc\">{{ 'Popularity'|trans({}, 'Admin.Modules.Feature') }}</option>
        </select>
      </div>
    </div>
  </div>
</div>
", "@PrestaShop/Admin/Module/Includes/sorting.html.twig", "C:\\xampp\\htdocs\\prestashop\\src\\PrestaShopBundle\\Resources\\views\\Admin\\Module\\Includes\\sorting.html.twig");
    }
}
