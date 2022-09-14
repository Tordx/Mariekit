<?php
/**
 * Module Front Controller
 *
 * @author   Twispay
 * @version  1.0.1
 */

class TwispayRedirectModuleFrontController extends ModuleFrontController
{
    /** Method that provides mechanisms to process the IPN REQUESTS */
    public function postProcess()
    {
        $this->context->controller->display_column_left = false;
        $this->context->controller->display_column_right = false;
        /** Check for errors. */
        if (Tools::getValue('action') == 'error') {
            return $this->displayError($this->l('An error occurred while trying to redirect the customer'));
        } else {
            $this->context->smarty->assign(
                $this->module->getPaymentVars()
            );
            $this->context->controller->addJs($this->module->getPath().'/views/js/redirect.js');
            $this->context->smarty->assign('module_path', _PS_MODULE_DIR_.'twispay/');

            return $this->setTemplate('redirect.tpl');
        }
    }

    public function l($message)
    {
        return $this->module->l($message);
    }
}
