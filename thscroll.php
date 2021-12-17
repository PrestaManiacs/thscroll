<?php
/**
 * 2006-2021 THECON SRL
 *
 * NOTICE OF LICENSE
 *
 * DISCLAIMER
 *
 * YOU ARE NOT ALLOWED TO REDISTRIBUTE OR RESELL THIS FILE OR ANY OTHER FILE
 * USED BY THIS MODULE.
 *
 * @author    THECON SRL <contact@thecon.ro>
 * @copyright 2006-2021 THECON SRL
 * @license   Commercial
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Thscroll extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'thscroll';
        $this->tab = 'front_office_features';
        $this->version = '1.0.1';
        $this->author = 'Presta Maniacs';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Customizable Scroll to Top Button');
        $this->description = $this->l('Allow your shop customers to easily scroll back to the top of your page with one click of the button.');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        if (!$this->registerHooks()) {
            return false;
        }

        $this->installDemo();

        return true;
    }

    private function installDemo()
    {
        Configuration::updateValue('THSCROLL_LIVE_MODE', false);
        Configuration::updateValue('THSCROLL_WIDTH', 50);
        Configuration::updateValue('THSCROLL_HEIGHT', 50);
        Configuration::updateValue('THSCROLL_BORDER_RADIUS', 200);
        Configuration::updateValue('THSCROLL_SHOW_AFTER', 500);
        Configuration::updateValue('THSCROLL_TEXT_COLOR', '#ffffff');
        Configuration::updateValue('THSCROLL_BACK_COLOR', '#000000');
        Configuration::updateValue('THSCROLL_BORDER_COLOR', '#000000');
        Configuration::updateValue('THSCROLL_H_TEXT_COLOR', '#ffffff');
        Configuration::updateValue('THSCROLL_H_BACK_COLOR', '#000000');
        Configuration::updateValue('THSCROLL_H_BORDER_COLOR', '#000000');
        Configuration::updateValue('THSCROLL_TEXT_SHOW', true);
        Configuration::updateValue('THSCROLL_TEXT_SIZE', '12');
        Configuration::updateValue('THSCROLL_TEXT_LH', '14');
        Configuration::updateValue('THSCROLL_ICON_SHOW', true);
        Configuration::updateValue('THSCROLL_ICON_SIZE', '14');
        Configuration::updateValue('THSCROLL_ICON_COLOR', '#ffffff');
        Configuration::updateValue('THSCROLL_H_ICON_COLOR', '#ffffff');
        Configuration::updateValue('THSCROLL_ALLIGN', 'right');
        Configuration::updateValue('THSCROLL_BOTTOM_DISTANCE', '30');
        Configuration::updateValue('THSCROLL_SIDE_DISTANCE', '30');
        Configuration::updateValue('THSCROLL_SPEED', '900');

        if ($this->getPsVersion() == '7') {
            Configuration::updateValue('THSCROLL_ICON_LIBRARY', 'material_icons');
        } else {
            Configuration::updateValue('THSCROLL_ICON_LIBRARY', 'font_awesome');
        }

        $languages = Language::getLanguages(false);
        $lang_values = array();
        foreach ($languages as $lang) {
            $lang_values[$lang['id_lang']] = 'Top';
        }
        Configuration::updateValue('THSCROLL_TEXT_CONTENT', $lang_values);

        return true;
    }

    public function registerHooks()
    {
        if (
            !$this->registerHook('backOfficeHeader') ||
            !$this->registerHook('header') ||
            !$this->registerHook('actionFrontControllerSetMedia')
        ) {
            return false;
        }

        if ($this->getPsVersion() == '7') {
            if (!$this->registerHook('displayBeforeBodyClosingTag')) {
                return false;
            }
        } else {
            if (!$this->registerHook('displayFooter')) {
                return false;
            }
        }

        return true;
    }

    public function uninstall()
    {
        $form_values = $this->getConfigFormValues();
        foreach (array_keys($form_values) as $key) {
            Configuration::deleteByName($key);
        }

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $message = '';

        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitThscrollModule')) == true) {
            $this->postProcess();

            if (count($this->_errors)) {
                $message = $this->displayError($this->_errors);
            } else {
                $message = $this->displayConfirmation($this->l('Successfully saved!'));
            }
        }

        $this->context->smarty->assign('module_dir', $this->_path);
        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$message.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitThscrollModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    private function getIconTypes()
    {
        $icon_types = array();
        if ($this->getPsVersion() == '7') {
            $icon_types[] = array(
                'option_value' => 'material_icons',
                'option_title' => $this->l('Material Icons')
            );
        }

        $icon_types[] = array(
            'option_value' => 'font_awesome',
            'option_title' => $this->l('Font Awesome')
        );
        $icon_types[] = array(
            'option_value' => 'custom',
            'option_title' => $this->l('Custom Html')
        );

        return $icon_types;
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show Go to Top Button:'),
                        'name' => 'THSCROLL_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'th_title',
                        'label' => '',
                        'name' => $this->l('Button Design'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Width:'),
                        'name' => 'THSCROLL_WIDTH',
                        'col' => 1,
                        'suffix' => 'px',
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Height:'),
                        'name' => 'THSCROLL_HEIGHT',
                        'col' => 1,
                        'suffix' => 'px',
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Border Radius:'),
                        'name' => 'THSCROLL_BORDER_RADIUS',
                        'col' => 1,
                        'suffix' => 'px'
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Speed Scroll:'),
                        'name' => 'THSCROLL_SPEED',
                        'col' => 1,
                        'suffix' => 'ms',
                        'required' => true,
                        'hint' => $this->l('Miliseconds Value. 1sec => 1000ms')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Show button after:'),
                        'name' => 'THSCROLL_SHOW_AFTER',
                        'col' => 1,
                        'suffix' => 'px',
                        'hint' => $this->l('Leave the field blank so that the button is always displayed, or fill in the px number for scrolling, after which it will be displayed.')
                    ),
                    array(
                        'type' => 'th_sub_title',
                        'label' => '',
                        'name' => $this->l('Colors'),
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Background Color:'),
                        'name' => 'THSCROLL_BACK_COLOR',
                        'required' => true,
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Border Color:'),
                        'name' => 'THSCROLL_BORDER_COLOR',
                        'required' => true,
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Hover Background Color:'),
                        'name' => 'THSCROLL_H_BACK_COLOR',
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Hover Border Color:'),
                        'name' => 'THSCROLL_H_BORDER_COLOR',
                    ),
                    array(
                        'type' => 'th_title',
                        'label' => '',
                        'name' => $this->l('Button Position'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Button Position:'),
                        'name' => 'THSCROLL_ALLIGN',
                        'options' => array(
                            'query' => array(
                                array(
                                    'option_value' => 'left',
                                    'option_title' => $this->l('Left')
                                ),
                                array(
                                    'option_value' => 'right',
                                    'option_title' => $this->l('Right')
                                )
                            ),
                            'id' => 'option_value',
                            'name' => 'option_title'
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Bottom Distance:'),
                        'name' => 'THSCROLL_BOTTOM_DISTANCE',
                        'col' => 1,
                        'suffix' => 'px',
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Side Distance:'),
                        'name' => 'THSCROLL_SIDE_DISTANCE',
                        'col' => 1,
                        'suffix' => 'px',
                        'required' => true,
                    ),
                    array(
                        'type' => 'th_title',
                        'label' => '',
                        'name' => $this->l('Button Text'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show Text:'),
                        'name' => 'THSCROLL_TEXT_SHOW',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Text Content:'),
                        'name' => 'THSCROLL_TEXT_CONTENT',
                        'lang' => true,
                        'col' => 3,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Font Size:'),
                        'name' => 'THSCROLL_TEXT_SIZE',
                        'col' => 1,
                        'suffix' => 'px'
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Line Height:'),
                        'name' => 'THSCROLL_TEXT_LH',
                        'col' => 1,
                        'suffix' => 'px'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Text Color:'),
                        'name' => 'THSCROLL_TEXT_COLOR',
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Hover Text Color:'),
                        'name' => 'THSCROLL_H_TEXT_COLOR',
                    ),
                    array(
                        'type' => 'th_title',
                        'label' => '',
                        'name' => $this->l('Button Icon'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show Icon:'),
                        'name' => 'THSCROLL_ICON_SHOW',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Icon Font Size:'),
                        'name' => 'THSCROLL_ICON_SIZE',
                        'col' => 1,
                        'suffix' => 'px'
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Text Color:'),
                        'name' => 'THSCROLL_ICON_COLOR',
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Hover Text Color:'),
                        'name' => 'THSCROLL_H_ICON_COLOR',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Icon Library:'),
                        'name' => 'THSCROLL_ICON_LIBRARY',
                        'options' => array(
                            'query' => $this->getIconTypes(),
                            'id' => 'option_value',
                            'name' => 'option_title'
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Icon Custom HTML:'),
                        'name' => 'THSCROLL_ICON_HTML',
                        'col' => 3,
                        'desc' => $this->l('To use this field, select "Custom Html" value from "Icon Library" input.')
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        $values =  array(
            'THSCROLL_LIVE_MODE' => Tools::getValue('THSCROLL_LIVE_MODE', Configuration::get('THSCROLL_LIVE_MODE')),
            'THSCROLL_WIDTH' => Tools::getValue('THSCROLL_WIDTH', Configuration::get('THSCROLL_WIDTH')),
            'THSCROLL_HEIGHT' => Tools::getValue('THSCROLL_HEIGHT', Configuration::get('THSCROLL_HEIGHT')),
            'THSCROLL_BORDER_RADIUS' => Tools::getValue('THSCROLL_BORDER_RADIUS', Configuration::get('THSCROLL_BORDER_RADIUS')),
            'THSCROLL_SHOW_AFTER' => Tools::getValue('THSCROLL_SHOW_AFTER', Configuration::get('THSCROLL_SHOW_AFTER')),
            'THSCROLL_TEXT_COLOR' => Tools::getValue('THSCROLL_TEXT_COLOR', Configuration::get('THSCROLL_TEXT_COLOR')),
            'THSCROLL_BACK_COLOR' => Tools::getValue('THSCROLL_BACK_COLOR', Configuration::get('THSCROLL_BACK_COLOR')),
            'THSCROLL_BORDER_COLOR' => Tools::getValue('THSCROLL_BORDER_COLOR', Configuration::get('THSCROLL_BORDER_COLOR')),
            'THSCROLL_H_TEXT_COLOR' => Tools::getValue('THSCROLL_H_TEXT_COLOR', Configuration::get('THSCROLL_H_TEXT_COLOR')),
            'THSCROLL_H_BACK_COLOR' => Tools::getValue('THSCROLL_H_BACK_COLOR', Configuration::get('THSCROLL_H_BACK_COLOR')),
            'THSCROLL_H_BORDER_COLOR' => Tools::getValue('THSCROLL_H_BORDER_COLOR', Configuration::get('THSCROLL_H_BORDER_COLOR')),
            'THSCROLL_TEXT_SHOW' => Tools::getValue('THSCROLL_TEXT_SHOW', Configuration::get('THSCROLL_TEXT_SHOW')),
            'THSCROLL_TEXT_SIZE' => Tools::getValue('THSCROLL_TEXT_SIZE', Configuration::get('THSCROLL_TEXT_SIZE')),
            'THSCROLL_TEXT_LH' => Tools::getValue('THSCROLL_TEXT_LH', Configuration::get('THSCROLL_TEXT_LH')),
            'THSCROLL_ICON_SHOW' => Tools::getValue('THSCROLL_ICON_SHOW', Configuration::get('THSCROLL_ICON_SHOW')),
            'THSCROLL_ICON_SIZE' => Tools::getValue('THSCROLL_ICON_SIZE', Configuration::get('THSCROLL_ICON_SIZE')),
            'THSCROLL_ICON_COLOR' => Tools::getValue('THSCROLL_ICON_COLOR', Configuration::get('THSCROLL_ICON_COLOR')),
            'THSCROLL_H_ICON_COLOR' => Tools::getValue('THSCROLL_H_ICON_COLOR', Configuration::get('THSCROLL_H_ICON_COLOR')),
            'THSCROLL_ICON_LIBRARY' => Tools::getValue('THSCROLL_ICON_LIBRARY', Configuration::get('THSCROLL_ICON_LIBRARY')),
            'THSCROLL_ICON_HTML' => Tools::getValue('THSCROLL_ICON_HTML', Configuration::get('THSCROLL_ICON_HTML')),
            'THSCROLL_ALLIGN' => Tools::getValue('THSCROLL_ALLIGN', Configuration::get('THSCROLL_ALLIGN')),
            'THSCROLL_BOTTOM_DISTANCE' => Tools::getValue('THSCROLL_BOTTOM_DISTANCE', Configuration::get('THSCROLL_BOTTOM_DISTANCE')),
            'THSCROLL_SIDE_DISTANCE' => Tools::getValue('THSCROLL_SIDE_DISTANCE', Configuration::get('THSCROLL_SIDE_DISTANCE')),
            'THSCROLL_SPEED' => Tools::getValue('THSCROLL_SPEED', Configuration::get('THSCROLL_SPEED')),
        );

        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $values['THSCROLL_TEXT_CONTENT'][$lang['id_lang']] = Tools::getValue('THSCROLL_TEXT_CONTENT_'.$lang['id_lang'], Configuration::get('THSCROLL_TEXT_CONTENT', $lang['id_lang']));
        }

        return $values;
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            if ($key == 'THSCROLL_TEXT_CONTENT') {
                $languages = Language::getLanguages(false);
                $values = array();
                foreach ($languages as $lang) {
                    $values['THSCROLL_TEXT_CONTENT'][$lang['id_lang']] = Tools::getValue('THSCROLL_TEXT_CONTENT_'.$lang['id_lang']);
                }

                Configuration::updateValue('THSCROLL_TEXT_CONTENT', $values['THSCROLL_TEXT_CONTENT']);
            } elseif($key == 'THSCROLL_ICON_HTML') {
                Configuration::updateValue($key, Tools::getValue($key), true);
            } else {
                $update_value = 1;

                if ($key == 'THSCROLL_WIDTH') {
                    if (!Validate::isInt(Tools::getValue($key)) || Tools::getValue($key) <= 0) {
                        $this->_errors[] = 'Button Design Width value it\'s not ok';
                        $update_value = 0;
                    }
                } elseif($key == 'THSCROLL_HEIGHT') {
                    if (!Validate::isInt(Tools::getValue($key)) || Tools::getValue($key) <= 0) {
                        $this->_errors[] = 'Button Design Height value it\'s not ok';
                        $update_value = 0;
                    }
                } elseif($key == 'THSCROLL_BORDER_RADIUS') {
                    if (Tools::getValue($key) && (!Validate::isInt(Tools::getValue($key)) || Tools::getValue($key) <= 0)) {
                        $this->_errors[] = 'Button Design Border Radius value it\'s not ok';
                        $update_value = 0;
                    }
                } elseif($key == 'THSCROLL_SPEED') {
                    if (Tools::getValue($key) && (!Validate::isInt(Tools::getValue($key)) || Tools::getValue($key) <= 0)) {
                        $this->_errors[] = 'Button Design Scroll Speed value it\'s not ok';
                        $update_value = 0;
                    }
                } elseif($key == 'THSCROLL_SHOW_AFTER') {
                    if (Tools::getValue($key) && (!Validate::isInt(Tools::getValue($key)) || Tools::getValue($key) <= 0)) {
                        $this->_errors[] = 'Button Design Show After value it\'s not ok';
                        $update_value = 0;
                    }
                } elseif($key == 'THSCROLL_TEXT_SIZE') {
                    if (Tools::getValue($key) && (!Validate::isInt(Tools::getValue($key)) || Tools::getValue($key) <= 0)) {
                        $this->_errors[] = 'Button Text Font Size value it\'s not ok';
                        $update_value = 0;
                    }
                } elseif($key == 'THSCROLL_TEXT_LH') {
                    if (Tools::getValue($key) && (!Validate::isInt(Tools::getValue($key)) || Tools::getValue($key) <= 0)) {
                        $this->_errors[] = 'Button Text Line Height value it\'s not ok';
                        $update_value = 0;
                    }
                } elseif($key == 'THSCROLL_BOTTOM_DISTANCE') {
                    if (!Validate::isInt(Tools::getValue($key))) {
                        $this->_errors[] = 'Button Position Bottom Distance value it\'s not ok';
                        $update_value = 0;
                    }
                } elseif($key == 'THSCROLL_SIDE_DISTANCE') {
                    if (!Validate::isInt(Tools::getValue($key))) {
                        $this->_errors[] = 'Button Position Side Distance value it\'s not ok';
                        $update_value = 0;
                    }
                } elseif($key == 'THSCROLL_ICON_SIZE') {
                    if (Tools::getValue($key) && (!Validate::isInt(Tools::getValue($key)) || Tools::getValue($key) <= 0)) {
                        $this->_errors[] = 'Button Icon Font Size value it\'s not ok';
                        $update_value = 0;
                    }
                }

                if ($update_value) {
                    Configuration::updateValue($key, Tools::getValue($key));
                }
            }
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    public function hookActionFrontControllerSetMedia($params)
    {
        if (!Configuration::get('THSCROLL_LIVE_MODE')) {
            return false;
        }

        $this->context->controller->addJS(_MODULE_DIR_.$this->name.'/views/js/front.js');

        if ($this->getPsVersion() == '7') {
            Media::addJsDef(array(
                'THSCROLL_SPEED' => Configuration::get('THSCROLL_SPEED') ? Configuration::get('THSCROLL_SPEED') : '800',
                'THSCROLL_SHOW_AFTER' => Configuration::get('THSCROLL_SHOW_AFTER') ? Configuration::get('THSCROLL_SHOW_AFTER') : false
            ));
        }
    }

    public function hookHeader($params)
    {
        if (!Configuration::get('THSCROLL_LIVE_MODE')) {
            return false;
        }

        $this->assignHeaderVariables();

        $output = '';
        if ($this->getPsVersion() == '6') {
            $this->context->smarty->assign(
                array(
                    'THSCROLL_SPEED' => Configuration::get('THSCROLL_SPEED') ? Configuration::get('THSCROLL_SPEED') : '800',
                    'THSCROLL_SHOW_AFTER' => Configuration::get('THSCROLL_SHOW_AFTER') ? Configuration::get('THSCROLL_SHOW_AFTER') : false
                )
            );

            $output = $this->context->smarty->fetch($this->local_path.'views/templates/hook/header16.tpl');
        }

        return $output.$this->display(__FILE__, 'header.tpl');
    }

    public function assignHeaderVariables()
    {
        $data = array(
            'THSCROLL_WIDTH' => Configuration::get('THSCROLL_WIDTH'),
            'THSCROLL_HEIGHT' => Configuration::get('THSCROLL_HEIGHT'),
            'THSCROLL_BORDER_RADIUS' => Configuration::get('THSCROLL_BORDER_RADIUS'),
            'THSCROLL_TEXT_COLOR' => Configuration::get('THSCROLL_TEXT_COLOR'),
            'THSCROLL_BACK_COLOR' => Configuration::get('THSCROLL_BACK_COLOR'),
            'THSCROLL_BORDER_COLOR' => Configuration::get('THSCROLL_BORDER_COLOR'),
            'THSCROLL_H_TEXT_COLOR' => Configuration::get('THSCROLL_H_TEXT_COLOR'),
            'THSCROLL_H_BACK_COLOR' => Configuration::get('THSCROLL_H_BACK_COLOR'),
            'THSCROLL_H_BORDER_COLOR' => Configuration::get('THSCROLL_H_BORDER_COLOR'),
            'THSCROLL_ALLIGN' => Configuration::get('THSCROLL_ALLIGN'),
            'THSCROLL_BOTTOM_DISTANCE' => Configuration::get('THSCROLL_BOTTOM_DISTANCE'),
            'THSCROLL_SIDE_DISTANCE' => Configuration::get('THSCROLL_SIDE_DISTANCE'),
            'THSCROLL_ICON_SIZE' => Configuration::get('THSCROLL_ICON_SIZE'),
            'THSCROLL_ICON_COLOR' => Configuration::get('THSCROLL_ICON_COLOR'),
            'THSCROLL_H_ICON_COLOR' => Configuration::get('THSCROLL_H_ICON_COLOR'),
            'THSCROLL_TEXT_SIZE' => Configuration::get('THSCROLL_TEXT_SIZE'),
            'THSCROLL_TEXT_LH' => Configuration::get('THSCROLL_TEXT_LH'),
            'THSCROLL_SPEED' => Configuration::get('THSCROLL_SPEED'),
        );

        $this->context->smarty->assign($data);
    }

    public function assignFooterVariables()
    {
        $data = array(
            'THSCROLL_TEXT_SHOW' => Configuration::get('THSCROLL_TEXT_SHOW'),
            'THSCROLL_TEXT_CONTENT' => Configuration::get('THSCROLL_TEXT_CONTENT', $this->context->language->id),
            'THSCROLL_ICON_SHOW' => Configuration::get('THSCROLL_ICON_SHOW'),
            'THSCROLL_ICON_LIBRARY' => Configuration::get('THSCROLL_ICON_LIBRARY'),
            'THSCROLL_ICON_HTML' => Configuration::get('THSCROLL_ICON_HTML'),
            'THSCROLL_SPEED' => Configuration::get('THSCROLL_SPEED'),
            'THSCROLL_SHOW_AFTER' => Configuration::get('THSCROLL_SHOW_AFTER') ? Configuration::get('THSCROLL_SHOW_AFTER') : false,
            'THSCROLL_PS_VERSION' => $this->getPsVersion()
        );

        $this->context->smarty->assign($data);
    }

    public function hookDisplayFooter($params)
    {
        if (!Configuration::get('THSCROLL_LIVE_MODE') || $this->getPsVersion() == '7') {
            return false;
        }

        $this->assignFooterVariables();

        return $this->display(__FILE__, 'footer.tpl');
    }

    public function hookDisplayBeforeBodyClosingTag($params)
    {
        if (!Configuration::get('THSCROLL_LIVE_MODE') || $this->getPsVersion() == '6') {
            return false;
        }

        $this->assignFooterVariables();

        return $this->display(__FILE__, 'footer.tpl');
    }

    public function getPsVersion()
    {
        $full_version = _PS_VERSION_;
        return explode(".", $full_version)[1];
    }
}
