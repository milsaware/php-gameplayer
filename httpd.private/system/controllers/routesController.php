<?php
class routesController {

	public static function get($route, $action){
		if($route == $_GET['route']){
			$actionArray = explode('@', $action);
			$controller = $actionArray[0];
			$function = $actionArray[1];
			$controllerName = $controller.'Controller';
			include_controller($controller);
			return $controllerName::$function();
		}
	}
	
	public static function error($data){
		view::build('head', $data).
		view::build('error', $data).
		view::build('foot', $data);
	}

}