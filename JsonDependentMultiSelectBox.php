<?php

/**
 * @author Daniel Robenek
 * @license MIT
 */

namespace DependentSelectBox;

use Nette\Application\Responses\JsonResponse;
use Nette\Forms\Container as FormContainer;

// \Nette\Forms\FormContainer::extensionMethod("addJsonDependentSelectBox", "DependentSelectBox\JsonDependentSelectBox::formAddJsonDependentSelectBox");

class JsonDependentMultiSelectBox extends DependentMultiSelectBox
{

	public static $jsonResoponseItems = array();

	public function submitButtonHandler($button) {
		parent::submitButtonHandler($button);
		if ($this->getParent()->getPresenter()->isAjax())
			$this->addJsonResponseItem($this);
	}

	protected function addJsonResponseItem($selectBox) {
		self::$jsonResoponseItems[] = $selectBox;
		if($selectBox instanceof DependentSelectBox)
			foreach($selectBox->childs as $child)
				$child->addJsonResponseItem($child);
	}

	public static function getJsonResponse() {
		if(empty(self::$jsonResoponseItems))
			return;

		$payload = array(
			"type" => "JsonDependentSelectBoxResponse",
			"items" => array()
		);
		foreach(self::$jsonResoponseItems as $item) {
			$payload["items"][$item->getHtmlId()] = array(
				"selected" => $item->getValue(),
				"items" => $item->getItems()
			);
		}
		return new JsonResponse($payload);
	}


	/**
	 * @deprecated Alias for Container_prototype_addDependentSelectBox
	 */
	public static function formAddJsonDependentSelectBox($_this, $name, $label, $parents, $dataCallback) {
		return self::Container_prototype_addJsonDependentSelectBox($_this, $name, $label, $parents, $dataCallback);
	}

	public static function Container_prototype_addJsonDependentSelectBox(FormContainer $obj, $name, $label, $parents, $dataCallback) {
		return $obj[$name] = new JsonDependentSelectBox($label, $parents, $dataCallback);
	}

	public static function register($methodName = "addJsonDependentSelectBox") {
		if(PHP_VERSION_ID >= 50300)
			FormContainer::extensionMethod($methodName, "DependentSelectBox\JsonDependentSelectBox::Container_prototype_addJsonDependentSelectBox");
		else
			FormContainer::extensionMethod("FormContainer::$methodName", array("JsonDependentSelectBox", "Container_prototype_addJsonDependentSelectBox"));
	}

}