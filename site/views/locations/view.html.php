<?php
/**
 * @package    DD_GMaps_Locations
 *
 * @author     HR-IT-Solutions Florian Häusler <info@hr-it-solutions.com>
 * @copyright  Copyright (C) 2011 - 2019 HR-IT-Solutions GmbH
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 **/

defined('_JEXEC') or die;

class DD_GMaps_LocationsViewLocations extends JViewLegacy
{
	protected $app;

	protected $items;

	protected $params;

	protected $sef_rewrite;

	protected $active_alias;

	protected $overridePath;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse
	 *
	 * @return boolean | mixed
	 *
	 * @since Version 1.1.0.0
	 * @throws  Exception
	 */
	public function display($tpl = null)
	{
		$this->app = JFactory::getApplication();

		$this->items = $this->get('Items');

		$this->params = $this->app->getParams();

		$this->sef_rewrite  = JFactory::getConfig()->get('sef_rewrite');
		$this->active_alias = $this->app->getMenu()->getActive()->alias;

		$this->overridePath = JPATH_ROOT . '/templates/' . JFactory::getApplication()->getTemplate() . '/html/com_dd_gmaps_locations/locations/default_items.php';

		foreach ($this->items as $key => $item){
		// Get custom fields
			JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
			$fields = FieldsHelper::getFields('com_dd_gmaps_locations.location', $item, true);

			// Assigne custom fields to $item->jcfields
			if($fields)
			{
				foreach ($fields as $field)
				{
					if($field->value != '')
					{
						$this->items[$key]->jcfields[$field->id] = $field;
					}
				}
			}
		}

		// Active menu
		$activeMenu = $this->app->getMenu()->getItem($this->app->getMenu()->getActive()->id);

		if (method_exists($activeMenu, 'getParams')) // Joomla 3.7.xx
		{
			// Active menu params
			$activeMenuParams   = $activeMenu->getParams();

			// Get meta data from menu
			$metadesc = $activeMenuParams->get('menu-meta_description');
			$metakey  = $activeMenuParams->get('menu-meta_keywords');
			$robots = $activeMenuParams->get('robots');
		}
		else // Joomla 3.5.xx
		{
			// Get meta data from menu
			$metadesc = $activeMenu->{'menu-meta_description'};
			$metakey  = $activeMenu->{'menu-meta_keywords'};
			$robots = $activeMenu->robots;
		}

		// Set meta data haeder from menu : default from item
		$doc = JFactory::getDocument();
		$doc->setMetaData('description', $metadesc);
		$doc->setMetaData('keywords', $metakey);
		$doc->setMetaData('robots', $robots);

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		return parent::display($tpl);
	}
}
