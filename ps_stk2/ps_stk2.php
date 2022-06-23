<?php
/**
*  @author    StudioK
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class Ps_stk2 extends Module implements WidgetInterface
{
    const STK2_NBR_DEFAULT = 10; 
	
	private $templateFile;
	
    public function __construct()
    {
        $this->name = 'ps_stk2';
        $this->author = 'StudioK';
        $this->version = '0.0.3';
        $this->need_instance = 0;
		
		$this->tab = 'front_office_features';

        $this->ps_versions_compliancy = [
            'min' => '1.7.1.0',
            'max' => _PS_VERSION_,
        ];

        $this->bootstrap = true;
		
        parent::__construct();

        $this->displayName = $this->trans('Stk2 Waynet Task', [], 'Modules.Stk2.Admin');
        $this->description = $this->trans('Stk2 Waynet Task: create a module that will be displaying three blocks with products', [], 'Modules.Stk2.Admin');

        $this->templateFile = 'module:ps_stk2/views/templates/hook/ps_stk2.tpl';
    }
	
    public function install()
    {
        $this->_clearCache('*');

        Configuration::updateValue('STK2_NBR', self::STK2_NBR_DEFAULT);
        Configuration::updateValue('STK2_CAT', (int) Context::getContext()->shop->getCategory());
		Configuration::updateValue('STK2_CAT_2', (int) Context::getContext()->shop->getCategory());
		Configuration::updateValue('STK2_CAT_3', (int) Context::getContext()->shop->getCategory());
        
        return parent::install()
            && $this->registerHook('actionProductAdd')
            && $this->registerHook('actionProductUpdate')
            && $this->registerHook('actionProductDelete')
            && $this->registerHook('displayHome')
            && $this->registerHook('displayOrderConfirmation2')
            && $this->registerHook('displayCrossSellingShoppingCart')
            && $this->registerHook('actionCategoryUpdate')
            && $this->registerHook('actionAdminGroupsControllerSaveAfter')
			&& $this->registerHook('header')
        ;
		
    }

    public function uninstall()
    {
        $this->_clearCache('*');

        return parent::uninstall();
    }

    public function hookActionProductAdd($params)
    {
        $this->_clearCache('*');
    }

    public function hookActionProductUpdate($params)
    {
        $this->_clearCache('*');
    }

    public function hookActionProductDelete($params)
    {
        $this->_clearCache('*');
    }

    public function hookActionCategoryUpdate($params)
    {
        $this->_clearCache('*');
    }

    public function hookActionAdminGroupsControllerSaveAfter($params)
    {
        $this->_clearCache('*');
    }

    public function _clearCache($template, $cache_id = null, $compile_id = null)
    {
        parent::_clearCache($this->templateFile);
    }
    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }	
    public function getContent()
    {
        $output = '';
        $errors = [];

        if (Tools::isSubmit('submitStk2')) {
			
            $nbr = Tools::getValue('STK2_NBR');
            if (!Validate::isInt($nbr) || $nbr <= 0) {
                $errors[] = $this->trans('The number of products is invalid. Please enter a positive number.', [], 'Modules.Stk2.Admin');
            }

            $cat = Tools::getValue('STK2_CAT');
            if (!Validate::isInt($cat) || $cat <= 0) {
                $errors[] = $this->trans('The category ID for column 1 is invalid. Please choose an existing category ID.', [], 'Modules.Stk2.Admin');
            }
			
            $cat_2 = Tools::getValue('STK2_CAT_2');
            if (!Validate::isInt($cat_2) || $cat_2 <= 0) {
                $errors[] = $this->trans('The category ID for column 2 is invalid. Please choose an existing category ID.', [], 'Modules.Stk2.Admin');
            }
			
            $cat_3 = Tools::getValue('STK2_CAT_3');
            if (!Validate::isInt($cat_3) || $cat_3 <= 0) {
                $errors[] = $this->trans('The category ID for column 3 is invalid. Please choose an existing category ID.', [], 'Modules.Stk2.Admin');
            }							

            if (count($errors)) {
                $output = $this->displayError(implode('<br />', $errors));
            } else {
                Configuration::updateValue('STK2_NBR', (int) $nbr);
                Configuration::updateValue('STK2_CAT', (int) $cat);
				Configuration::updateValue('STK2_CAT_2', (int) $cat_2);
				Configuration::updateValue('STK2_CAT_3', (int) $cat_3);

                $this->_clearCache('*');

                $output = $this->displayConfirmation($this->trans('The settings have been updated.', [], 'Admin.Notifications.Success'));
            }
        }

        return $output . $this->renderForm();
    }
	
    public function renderForm()
    {
        
		$root = Category::getRootCategory();
		
		//Generating the tree for the first column
		$tree = new HelperTreeCategories('stk2_category'); //The string in param is the ID used by the generated tree
		$tree->setUseCheckBox(false)
			->setAttribute('is_category_filter', $root->id)
			->setRootCategory($root->id)
			->setSelectedCategories(array((int)Configuration::get('STK2_CAT')))
			->setInputName('STK2_CAT'); //Set the name of input. The option "name" of $fields_form doesn't seem to work with "categories_select" type
		$categoryTreeCol1 = $tree->render();
		
		//Generating the tree for the second column
		$tree = new HelperTreeCategories('stk2_category_2');
		$tree->setUseCheckBox(false)
			 ->setAttribute('is_category_filter', $root->id)
			 ->setRootCategory($root->id)
			 ->setSelectedCategories(array((int)Configuration::get('STK2_CAT_2')))
			 ->setInputName('STK2_CAT_2');
		$categoryTreeCol2 = $tree->render();
		
		//Generating the tree for the second column
		$tree = new HelperTreeCategories('stk2_category_3');
		$tree->setUseCheckBox(false)
			 ->setAttribute('is_category_filter', $root->id)
			 ->setRootCategory($root->id)
			 ->setSelectedCategories(array((int)Configuration::get('STK2_CAT_3')))
			 ->setInputName('STK2_CAT_3');
		$categoryTreeCol3 = $tree->render();				
		
		$fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Settings', [], 'Admin.Global'),
                    'icon' => 'icon-cogs',
                ],

                'description' => $this->trans('To add products to Stk2 Waynet Task Module, simply add them to the corresponding product category (default: "Home").', [], 'Modules.Stk2.Admin'),
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->trans('Number of products to be displayed', [], 'Modules.Stk2.Admin'),
                        'name' => 'STK2_NBR',
                        'class' => 'fixed-width-xs',
                        'desc' => $this->trans('Set the number of products that you would like to display on each column (default: ' . self::STK2_NBR_DEFAULT . ').', [], 'Modules.Stk2.Admin'),
                    ],
                    [
                        'type' => 'categories_select',
                        'tree' => [
                          'id' => 'stk2_category',
                          'selected_categories' => [Configuration::get('STK2_CAT')],
                        ],
                        'label' => $this->trans('Category from which to pick products to be displayed on column 1', [], 'Modules.Stk2.Admin'),
                        'name' => 'STK2_CAT',
						'category_tree'  => $categoryTreeCol1
                    ],
                    [
                        'type' => 'categories_select',
                        'tree' => [
                          'id' => 'stk2_category_2',
                          'selected_categories' => [Configuration::get('STK2_CAT_2')],
                        ],
                        'label' => $this->trans('Category from which to pick products to be displayed on column 2', [], 'Modules.Stk2.Admin'),
                        'name' => 'STK2_CAT_2',
						'category_tree'  => $categoryTreeCol2
                    ],
                    [
                        'type' => 'categories_select',
                        'tree' => [
                          'id' => 'stk2_category_3',
                          'selected_categories' => [Configuration::get('STK2_CAT_3')],
                        ],
                        'label' => $this->trans('Category from which to pick products to be displayed on column 3', [], 'Modules.Stk2.Admin'),
                        'name' => 'STK2_CAT_3',
						'category_tree'  => $categoryTreeCol3
                    ],										
                ],
                'submit' => [
                    'title' => $this->trans('Save', [], 'Admin.Actions'),
                ],
            ],
        ];

        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitStk2';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$fields_form]);
    }

    public function getConfigFieldsValues()
    {
        return [
            'STK2_NBR' => Tools::getValue('STK2_NBR', (int) Configuration::get('STK2_NBR')),
            'STK2_CAT' => Tools::getValue('STK2_CAT', (int) Configuration::get('STK2_CAT')),
			'STK2_CAT_2' => Tools::getValue('STK2_CAT_2', (int) Configuration::get('STK2_CAT_2')),
			'STK2_CAT_3' => Tools::getValue('STK2_CAT_3', (int) Configuration::get('STK2_CAT_3'))
        ];
    }
	
    public function renderWidget($hookName = null, array $configuration = [])
    {
        if (!$this->isCached($this->templateFile, $this->getCacheId('ps_stk2'))) {
            $variables = $this->getWidgetVariables($hookName, $configuration);

            if (empty($variables)) {
                return false;
            }

            $this->smarty->assign($variables);
        }

        return $this->fetch($this->templateFile, $this->getCacheId('ps_stk2'));
    }
	
    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        $category_id = (int) Configuration::get('STK2_CAT');
		$products = $this->getProducts($category_id);
		$category = new Category($category_id, $this->context->language->id);
		
		$category_id_2 = (int) Configuration::get('STK2_CAT_2');
		$products_2 = $this->getProducts($category_id_2);
		$category_2 = new Category($category_id_2, $this->context->language->id);
		
		$category_id_3 = (int) Configuration::get('STK2_CAT_3');
		$products_3 = $this->getProducts($category_id_3);
		$category_3 = new Category($category_id_3, $this->context->language->id);
		
		$display = !empty($products) || !empty($products_2)  || !empty($products_3);
		
		if (!$display) {
			return false;	
		}
		
		return [
			'columns' => [
				[
					'products' => $products,
					'allProductsLink' => Context::getContext()->link->getCategoryLink($this->getConfigFieldsValues()['STK2_CAT']),
					'category' => $category
				],
				[
					'products' => $products_2,
					'allProductsLink' => Context::getContext()->link->getCategoryLink($this->getConfigFieldsValues()['STK2_CAT_2']),
					'category' => $category_2
				],
				[
					'products' => $products_3,
					'allProductsLink' => Context::getContext()->link->getCategoryLink($this->getConfigFieldsValues()['STK2_CAT_3']),
					'category' => $category_3
				]				
			]
		];
        

    }

    protected function getProducts($category_id)
    {
        $nProducts = Configuration::get('STK2_NBR');
        if ($nProducts < 0) {
            $nProducts = 12;
        }
		
		$id_shop = Context::getContext()->shop->id;
		$id_shop_group = Context::getContext()->shop->getContextShopGroupID();
		$id_lang = Context::getContext()->language->id;
		
		require_once dirname(__FILE__) . "/stk2_category.php";
		
		$category = new Stk2_Category($category_id);

        $searchProvider = new CategoryProductSearchProvider(
            $this->context->getTranslator(),
            $category
        );

        $context = new ProductSearchContext($this->context);

        $query = new ProductSearchQuery();

        $nProducts = Configuration::get('STK2_NBR');
        if ($nProducts < 0) {
            $nProducts = 12;
        }

        $query
            ->setResultsPerPage($nProducts)
            ->setPage(1)
        ;

	    $query->setSortOrder(new SortOrder('product', 'position', 'asc'));
        
        $result = $searchProvider->runQuery(
            $context,
            $query
        );

        $assembler = new ProductAssembler($this->context);

        $presenterFactory = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = $presenterFactory->getPresenter();

        $products_for_template = [];

        foreach ($result->getProducts() as $rawProduct) {
			
            $products_for_template[] = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct($rawProduct),
                $this->context->language
            );
        }

        return $products_for_template;
    }
	
    protected function getCacheId($name = null)
    {
        $cacheId = parent::getCacheId($name);
        if (!empty($this->context->customer->id)) {
            $cacheId .= '|' . $this->context->customer->id;
        }

        return $cacheId;
    }					
		
}